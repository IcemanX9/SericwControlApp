<?php
App::uses('AppController', 'Controller');
/**
 * Policies Controller
 *
 * @property Policy $Policy
 * @property PaginatorComponent $Paginator
 */
class PoliciesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Paginator->settings = array(
				'conditions' => array('Policy.archived' => '0')
		);
		$this->Policy->recursive = 0;
		$this->set('policies', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Policy->exists($id)) {
			throw new NotFoundException(__('Invalid policy'));
		}
		$options = array('conditions' => array('Policy.' . $this->Policy->primaryKey => $id));
		$this->set('policy', $this->Policy->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->loadModel('Document');
		if ($this->request->is('post')) {
			if ($this->request->data['Policy']['templated'] == 1) { //create a file with the text from free text
				$this->Document->create();
				$filename = Configure::read('file_upload_prefix') . time() . "-DirectText.htm";
				file_put_contents("files/".$filename, $this->request->data['Policy']['document_text']);
				$this->request->data['Document']['name'] = $this->request->data['Policy']['name'] . ' Policy Information Document';
				$this->request->data['Document']['filename'] = substr($filename, strlen(Configure::read('file_upload_prefix')));
				$this->request->data['Document']['size'] = filesize("files/".$filename);
				$this->request->data['Document']['file_type'] = "Direct templated text";
				$this->request->data['Document']['mime'] = "text/html";
				$this->request->data['Document']["user_id"] = $this->request->data['Policy']['admin_user_id'];
				$this->request->data['Document']["document_category_id"] = $this->request->data['Policy']['document_category_id'];
				$this->request->data['Document']["policyDocument"] = 1;
				$this->request->data['Document']["templated"] = 1;
				$this->Document->save($this->request->data);
			}
			else {
				$this->Document->saveMany(array(array(
					"name" => $this->request->data['Policy']['name'] . ' Policy Information Document',
					"filename" => $this->request->data['Policy']['filename'],
					"file_type" => $this->request->data['Policy']['file_type'],
					"document_category_id" => $this->request->data['Policy']['document_category_id'],
					"mime" => $this->request->data['Policy']['mime'],
					"size" => $this->request->data['Policy']['size'],
					"user_id" => $this->request->data['Policy']['admin_user_id'],
					"policyDocument" => 1,
					"meta" => $this->request->data['Policy']['meta'])));
				$filename = Configure::read('file_upload_prefix') . $this->request->data['Policy']['filename'];
				rename("upload_tmp/$filename", "files/$filename");
			}
			$this->request->data['Policy']['document_id'] = $this->Document->getId();
			$this->Policy->create();
			if ($this->Policy->save($this->request->data)) {
				$this->Session->setFlash(__('The policy has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The policy could not be saved. Please, try again.'));
			}
		}
		$documents = $this->Policy->Document->find('list');
		$clients = $this->Policy->Client->find('list');
		$this->set(compact('documents', 'clients'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->loadModel('Document');
		if (!$this->Policy->exists($id)) {
			throw new NotFoundException(__('Invalid policy'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->request->data['Policy']['documentChanged'] == 1) {
				if ($this->request->data['Policy']['templated'] == 1) { //create a file with the text from free text
					$this->Document->id =  $this->request->data['Policy']['document_id'];
					$filename = Configure::read('file_upload_prefix') . time() . "-DirectText.htm";
					file_put_contents("files/".$filename, $this->request->data['Policy']['document_text']);
					$this->request->data['Document']['name'] = $this->request->data['Policy']['name'] . ' Policy Information Document';
					$this->request->data['Document']['filename'] = substr($filename, strlen(Configure::read('file_upload_prefix')));;
					$this->request->data['Document']['size'] = filesize("files/".$filename);
					$this->request->data['Document']['file_type'] = "Direct templated text";
					$this->request->data['Document']['mime'] = "text/html";
					$this->request->data['Document']["user_id"] = $this->request->data['Policy']['admin_user_id'];
					$this->request->data['Document']["document_category_id"] = $this->request->data['Policy']['document_category_id'];
					$this->request->data['Document']["policyDocument"] = 1;
					$this->request->data['Document']["templated"] = 1;
					$this->Document->save($this->request->data);
				}
				else {
					$this->Document->saveMany(array(array(
						"id" => $this->request->data['Policy']['document_id'],
						"name" => $this->request->data['Policy']['name'] . ' Policy Information Document',
						"filename" => $this->request->data['Policy']['filename'],
						"file_type" => $this->request->data['Policy']['file_type'],
						"document_category_id" => $this->request->data['Policy']['document_category_id'],
						"mime" => $this->request->data['Policy']['mime'],
						"size" => $this->request->data['Policy']['size'],
						"user_id" => $this->request->data['Policy']['admin_user_id'],
						"policyDocument" => 1,
						"meta" => $this->request->data['Policy']['meta'])));
						$filename = Configure::read('file_upload_prefix') . $this->request->data['Policy']['filename'];
						rename("upload_tmp/$filename", "files/$filename");
				}
			}
			if ($this->Policy->save($this->request->data)) {
				$this->Session->setFlash(__('The policy has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The policy could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Policy.' . $this->Policy->primaryKey => $id));
			$this->request->data = $this->Policy->find('first', $options);
		}
		$documents = $this->Policy->Document->find('list');
		$clients = $this->Policy->Client->find('list');
		$this->set(compact('documents', 'clients'));
	}


	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->loadModel('Document');
		$this->Policy->id = $id;
		if (!$this->Policy->exists()) {
			throw new NotFoundException(__('Invalid document'));
		}
		$policy = $this->Policy->find('first', array('conditions'=>array("Policy.id = $id")));
		if (sizeof($policy['Client']) == 0) {
			if ($this->Policy->saveField('archived', 1)) {
				$this->Document->id = $this->Policy->field('document_id');
				$this->Document->saveField('archived', 1);
				$this->Session->setFlash(__('The policy has been deleted.'));
			} else {
				$this->Session->setFlash(__('The policy could not be deleted. Please, try again.'));
			}
		}
		else {
			$this->Session->setFlash(__('Their are still clients which belong to this policy. It cannot be deleted.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	

	public function checkUniqueness($field, $value) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$conditions = array(
				$field => $value
		);
		if ($this->Policy->hasAny($conditions)){
			echo "true";
		}
		else echo "false";
	}
}
