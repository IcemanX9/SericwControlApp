<?php
App::uses('AppController', 'Controller');
/**
 * Documents Controller
 *
 * @property Document $Document
 * @property PaginatorComponent $Paginator
*/
class DocumentsController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'RequestHandler');

	public function isAuthorized($user) {
		$user = $this->Auth->user();
		// Allows only users role who are logged in to access resources by default
		if ($this->Auth->loggedIn() && ($user['role']=='user' || $user['role']=='admin')) {
			return true;
		}
		if ($this->Auth->loggedIn() && $user['role']=='client' && ($this->action=='showFile' || $this->action=='download' || $this->action=='downloadIndexFile')) {
			return true;
		}
		if ($this->Auth->loggedIn() && $user['role']=='client') $this->redirect(array('controller' => 'Documents', 'action' => 'showFile'));
		// Default deny
		return false;
	}


	/**
	* showFile method - this is the main action for displaying a client's files
	*
	* @return void
	*/
	public function showFile($userid = null) {
		$this->loadModel('Sighting');
		$this->loadModel('Visit');
		$this->loadModel('Message');
		if ($this->request->is('post')) { //i.e. they are submitting a sighting report
			$this->Sighting->create();
			if ($this->Sighting->save($this->request->data)) {
				$this->Visit->create();
				$this->Visit->saveMany(array(array(
					"client_id" => $this->request->data['client_id'],
					"technician_id" => -1,
					"purpose" => "sighting",
					"timeRequested" => date("Y-m-d H:i:s"),
					"timeDue" => date('Y-m-d H:i:s', strtotime($date . ' + 1 days')))));
				$this->Sighting->set("visit_id", $this->Visit->field('id'));
				$this->Sighting->set("dateLogged", date("Y-m-d H:i:s"));
				$this->Sighting->save();
				$this->Session->setFlash(__('The sighting has been noted. A technician will attend to the problem within 24 hours.'));
				return $this->redirect(array('action' => 'showFile', $this->request->data['client_user_id']));
			} else {
				$this->Session->setFlash(__('The sighting could not be saved. Please, try again.'));
			}
		}
		//load the file layout
		$this->layout='clientfile';
		//ensure we have a userid to pull documents. clients always default to their own userid
		$user = $this->Auth->user();
		if ($user['role']=='client') $userid=$user['id'];
		if ($userid == null) $userid=$user['id'];

		$this->loadModel('Client');
		$client = $this->Client->find('first', array('fields' => array('Client.id', 'Client.company_id'), 'conditions' => array('Client.user_id =' => $userid)));
		$documentList = $this->Document->getAllDocumentsForClient($client['Client']['id'], $client['Client']['company_id']);
		$documentCategories = $this->Document->DocumentCategory->find('list', array('conditions' => array('DocumentCategory.archived' => '0'), 'order' => array('DocumentCategory.order' => 'asc')));
		$messages = $this->Message->find('all', array('conditions' => array('Message.seen' => '0', 'Message.user_id'=>$userid)));
		$this->set('clientUserId', $userid);
		$this->set('documentList', $documentList);
		$this->set('documentCategories', $documentCategories);
		$this->set('messages', $messages);
		$this->set('clientId', $client['Client']['id']);
		$this->set('companyId', $client['Client']['company_id']);
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index($showAll = false) {
		if ($showAll) $conditions = array('Document.archived =' => '0');
		else $conditions = array('Document.archived =' => '0', 'Document.policyDocument'=>0);
		$this->Paginator->settings = array(
				'conditions' => $conditions,
				'order' => array('Document.modified' => 'desc')
		);
		$this->Document->recursive = 0;
		$this->set('documents', $this->Paginator->paginate());
		$this->set('showAll', $showAll);
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Document->exists($id)) {
			throw new NotFoundException(__('Invalid document'));
		}
		$options = array('conditions' => array('Document.' . $this->Document->primaryKey => $id));
		$this->set('document', $this->Document->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			if ($this->request->data['Document']['templated'] == 1) { //create a file with the text from free text
				$filename = Configure::read('file_upload_prefix') . time() . "-DirectText.htm";
				file_put_contents("files/".$filename, $this->request->data['Document']['document_text']);
				$this->request->data['Document']['filename'] = substr($filename, strlen(Configure::read('file_upload_prefix')));
				$this->request->data['Document']['size'] = filesize("files/".$filename);
				$this->request->data['Document']['file_type'] = "Direct templated text";
				$this->request->data['Document']['mime'] = "text/html";
			}
			else {//move file to the correct permanent folder
				$filename = Configure::read('file_upload_prefix') . $this->request->data['Document']['filename'];
				rename("upload_tmp/$filename", "files/$filename");				
			}
			$this->Document->create();
			if ($this->Document->save($this->request->data)) {
				$this->Session->setFlash(__('The document has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The document could not be saved. Please, try again.'));
			}
		}
		$companies = $this->Document->Company->find('list', array('order'=>array('id'=>'asc')));
		$documentCategories = $this->Document->DocumentCategory->find('list', array("conditions"=>array("policyOnlyCategory"=>0)));
		$users = $this->Document->User->find('list');
		$this->set(compact('documentCategories', 'users', 'companies'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		if (!$this->Document->exists($id)) {
			throw new NotFoundException(__('Invalid document'));
		}
		$this->Document->id = $id;
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Document->field('editable') != 0) {
				if ($this->request->data['Document']['templated'] == 1) { //create a file with the text from free text
					$filename = Configure::read('file_upload_prefix') . time() . "-DirectText.htm";
					file_put_contents("files/".$filename, $this->request->data['Document']['document_text']);
					$this->request->data['Document']['filename'] = substr($filename, strlen(Configure::read('file_upload_prefix')));
					$this->request->data['Document']['size'] = filesize("files/".$filename);
					$this->request->data['Document']['file_type'] = "Direct templated text";
					$this->request->data['Document']['mime'] = "text/html";
				}
				else {
					//if file exists (i.e. a new one has been uploaded) move file to the correct permanent folder
					$filename = Configure::read('file_upload_prefix') . $this->request->data['Document']['filename'];
					if (file_exists("upload_tmp/$filename")) rename("upload_tmp/$filename", "files/$filename");
				}
				if ($this->Document->save($this->request->data)) {
					$this->Session->setFlash(__('The document has been saved.'));
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('The document could not be saved. Please, try again.'));
				}
			}
			else {
				$this->Document->saveField('notes', $this->request->data['Document']['notes']);
				$this->Session->setFlash(__('The document note has been saved.'));
				return $this->redirect(array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('Document.' . $this->Document->primaryKey => $id));
			$this->request->data = $this->Document->find('first', $options);
			if (!$this->request->data['Document']['editable']) {
				$this->Session->setFlash(__('You are not able to edit this document. You may only add/edit notes.'));
				//return $this->redirect(array('action' => 'index'));
			}
			if ($this->Document->field('editable') == 0) $this -> render('edit-notes');
		}
		$companies = $this->Document->Company->find('list', array('order'=>array('id'=>'asc')));
		$documentCategories = $this->Document->DocumentCategory->find('list', array("conditions"=>array("policyOnlyCategory"=>0)));
		$users = $this->Document->User->find('list');
		$this->set(compact('documentCategories', 'users', 'companies'));
	}


	/**
	 * toggle active method ajax call
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function toggleActive($id, $state) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->Document->id = $id;
		if ($state == "true") $bool = 1;
		else $bool = 0;
		$this->Document->saveField('active', $bool);
		echo $bool;
	}
	
	public function setMessageSeen($id) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->loadModel('Message');
		$this->Message->id = $id;
		$this->Message->saveField('seen', 1);
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Document->id = $id;
		if (!$this->Document->exists()) {
			throw new NotFoundException(__('Invalid document'));
		}
		$this->request->onlyAllow('post', 'delete');
		if($this->Document->field('policyDocument') != 1 && $this->Document->field('editable') == 1) {
			if ($this->Document->saveField('archived', 1)) {
				$this->Session->setFlash(__('The document has been deleted.'));
			} else {
				$this->Session->setFlash(__('The document could not be deleted. Please, try again.'));
			}
		}
		else {
			$this->Session->setFlash(__('This document is a policy document or important report and cannot be deleted. If not a Service Report, you may change the attached file by editing the document.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	/**
	 * purge method - cleans all documents which are no longer associated with a record in the db - thus removing all document history. Comment out if you don't want this (invisible) feature available. Irreversible.
	 *
	 * @throws NotFoundException
	 * @return void
	 */
	public function purge() {
		$this->autoRender = false;
		$data = $this->Document->find('all', array("fields"=>array("Document.filename")));
		$files = glob('files/' . Configure::read('file_upload_prefix') . '*.*');
		foreach ($files as $file) {
			$exists = false;
			foreach ($data as $document) 
				if ($file == "files/" . Configure::read('file_upload_prefix') . $document['Document']['filename']) $exists = true;
			if ($exists) echo "Keep $file <br/>";
			else {
				echo "Delete $file <br/>";
				unlink($file);
			}
		}
	}
	
	/**
	 * upload method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function upload() {
		$this->autoRender = false;
		App::uses('Folder', 'Utility');
		App::uses('File', 'Utility');
		$dir = 'upload_tmp/'.Configure::read('file_upload_prefix');
		$filedir = 'files/'.Configure::read('file_upload_prefix');
		// create new directory with 777 permissions if it does not exist yet
		// owner will be the user/group the PHP script is run under
		if (!file_exists($dir)) {
			mkdir ($dir, 0777);
		}
		if (!file_exists($filedir)) {
			mkdir ($filedir, 0777);
		}
		$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
		$timeNow = time() . "-";
		if ($fn) {
			file_put_contents(
			$dir . '/' . $timeNow . $fn,
			file_get_contents('php://input')
			);
			echo "!1! ". $timeNow . $fn;
			exit();
		}
		else {
			// form submit
			$files = $_FILES['thefile'];
			if ($files['error'] == UPLOAD_ERR_OK) {
				$fn = $files['name'];
				move_uploaded_file(
				$files['tmp_name'],
				$dir . $timeNow . $fn
				);
				echo "!1! ". $timeNow . $fn;
			}
			else echo "error marker in file uploaded data";
		}
	}


	/**
	 * download method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function download($id = null, $clientId = null) {
		$this->autoRender = false;
		App::uses('Folder', 'Utility');
		App::uses('File', 'Utility');
		$this->loadModel('Client');
		//ensure that the user has access to this document
		$user = $this->Auth->user();
		if ($this->Auth->loggedIn() && $user['role']=='client') {
			$client = $this->Client->find('first', array('fields' => array('Client.id'), 'conditions' => array('user_id =' => $user['id'])));
			if (!($this->Document->isDocumentVisibleToClient($client['Client']['id'], $id))) {
				$this->Session->setFlash(__("You do not have access to that document."));
				return $this->redirect(array('action' => 'showFile'));
			}
		}
		else { //if we are not a client, we're going to use a random first client for the sake of displaying a logo
			$client = $this->Client->find('first', array('fields' => array('Client.id')));
		}
		$this->Document->id = $id;
		if (!$this->Document->exists()) {
			throw new NotFoundException(__('Invalid document'));
		}
		if ($this->Document->field('templated') == 1) {
			if ($clientId == null) $clientId = $client['Client']['id'];
			$this->Document->outputTemplatedFile($clientId);
			exit();
		}
		$path = 'files/' . Configure::read('file_upload_prefix') . $this->Document->field('filename');
		if(!file_exists($path)) {
			$this->Session->setFlash(__('The document has been deleted from the filesystem and is not available for download!!! Please contact your system administator.'));
			return $this->redirect(array('action' => 'index'));
		}
		header('Content-type: ' . $this->Document->field('mime'));
		header('Content-Disposition: attachment; filename="' . substr($this->Document->field('filename'), 1+ strpos($this->Document->field('filename'), "-")) . '"');
		readfile($path);
	}
	
	/**
	 * download method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function downloadReport($id = null, $clientId = null) {
		$this->autoRender = false;
		$this->loadModel('Report');
		$this->Report->generateReport($id, $clientId);
	}

	/**
	 * download method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function downloadIndexFile($id = null) {
		$this->loadModel('Client');
		$user = $this->Auth->user();
		if ($user['role']=='user' || $user['role']=='admin' || $user['role']=='client') {
			$this->autoRender = false;
			if ($id == null) $client = $this->Client->find('first', array('fields' => array('Client.id', 'Client.company_id'), 'conditions' => array('Client.user_id =' => $user['id'])));
			else $client = $this->Client->find('first', array('fields' => array('Client.id', 'Client.company_id'), 'conditions' => array('Client.id =' => $id)));
			$this->Document->getIndexFile($id, $client['Client']['company_id']);
		}
		else {
			$this->Session->setFlash(__('You are not authorised to view this document.'));
			//	return $this->redirect(array('action' => 'index'));
		}
	}
}
