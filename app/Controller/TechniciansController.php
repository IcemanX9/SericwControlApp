<?php
App::uses('AppController', 'Controller');
/**
 * Technicians Controller
 *
 * @property Technician $Technician
 * @property PaginatorComponent $Paginator
 */
class TechniciansController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

	
	public function isAuthorized($user) {
		$user = $this->Auth->user();
		// Allows only users role who are logged in to access resources by default
		if ($this->action=='apiListing') {
			return true;
		}
		else return parent::isAuthorized($user);
	}
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Paginator->settings = array(
				'conditions' => array('Technician.archived =' => '0'),
		);
		$this->Technician->recursive = 0;
		$this->set('technicians', $this->Paginator->paginate());
	}
	
	/**
	 * gives a json list of technicians method as a web service
	 *
	 * @return void
	 */
	public function apiListing($db, $authkey = null) {
		$this->autoRender = false;
		$this->Paginator->settings = array(
				'fields' => array('Technician.name', 'Technician.id', 'User.id', 'User.username'),
				'conditions' => array('Technician.archived =' => '0', 'Technician.active =' => '1'));
		$data = $this->paginate('Technician');
		echo(gzencode(json_encode($data)));
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
		$this->loadModel('User');
		$this->Technician->id = $id;
		$data = $this->Technician->find('first', array('conditions' => array('Technician.id' => $id)));
		if ($state == "true") $bool = 1;
		else $bool = 0;
		$this->Technician->saveField('active', $bool);
		$this->User->id = $data['Technician']['user_id'];
		$this->User->saveField('active', $bool);
		echo $bool;
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Technician->exists($id)) {
			throw new NotFoundException(__('Invalid technician'));
		}
		$options = array('conditions' => array('Technician.' . $this->Technician->primaryKey => $id));
		$this->set('technician', $this->Technician->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->loadModel('User');
		$this->loadModel('Document');
		if ($this->request->is('post')) {
			//deal with the document
			if ($this->Document->saveMany(array(array(
						"name" => $this->request->data['Technician']['name'] . ' Certification Document',
						"filename" => $this->request->data['Technician']['filename'],
						"file_type" => $this->request->data['Technician']['file_type'],
						"document_category_id" => $this->request->data['Technician']['document_category_id'],
						"mime" => $this->request->data['Technician']['mime'],
						"size" => $this->request->data['Technician']['size'],
						"user_id" => $this->request->data['Technician']['admin_user_id'],
						"policyDocument" => 1,
						"meta" => $this->request->data['Technician']['meta'])))) {
				$this->request->data['Technician']['document_id'] = $this->Document->getId();
				$filename = Configure::read('file_upload_prefix') . $this->request->data['Technician']['filename'];
				rename("upload_tmp/$filename", "files/$filename");
				//create the user
				$this->User->create();
				if ($this->User->saveField("username", $this->request->data['Technician']['username']) &&
				$this->User->saveField("role", "technician") &&
				$this->User->saveField("active", 1) &&
				$this->User->saveField("email_verified", 1) &&
				$this->User->saveField("email", $this->request->data['Technician']['email']) &&
				$this->User->saveField("password", Security::hash($this->request->data['Technician']['password'], null, true))) {
					$this->request->data['Technician']['user_id'] = $this->User->getId();
					$this->Technician->create();
					if ($this->Technician->save($this->request->data)) {
						$this->Session->setFlash(__('The technician has been saved.'));
						return $this->redirect(array('action' => 'index'));
					} else {
						$this->Session->setFlash(__('The technician could not be saved. Please, try again.'));
					}
				}
			}
		}
		$users = $this->Technician->User->find('list');
		$documents = $this->Technician->Document->find('list');
		$this->set(compact('users', 'documents'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->loadModel('User');
		$this->loadModel('Document');
		if (!$this->Technician->exists($id)) {
			throw new NotFoundException(__('Invalid technician'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if (!$this->User->exists($this->request->data['Technician']['user_id'])) {
				throw new NotFoundException(__('Technician has not been created properly and has no user account. Cannot edit.'));
			}
			if (!$this->Document->exists($this->request->data['Technician']['document_id'])) {
				throw new NotFoundException(__('Technician has not been created properly and has no associated document. Cannot edit.'));
			}
			$this->User->id = $this->request->data['Technician']['user_id'];
			$this->User->saveField("username", $this->request->data['Technician']['username']);
			$this->User->saveField("active", $this->request->data['Technician']['active']);
			if ($this->request->data['Technician']['documentChanged'] == 1) {
				if ($this->Document->saveMany(array(array(
						"id" => $this->request->data['Technician']['document_id'],
						"name" => $this->request->data['Technician']['name'] . ' Certification Document',
						"filename" => $this->request->data['Technician']['filename'],
						"file_type" => $this->request->data['Technician']['file_type'],
						"document_category_id" => $this->request->data['Technician']['document_category_id'],
						"mime" => $this->request->data['Technician']['mime'],
						"size" => $this->request->data['Technician']['size'],
						"user_id" => $this->request->data['Technician']['admin_user_id'],
						"policyDocument" => 1,
						"meta" => $this->request->data['Technician']['meta'])))) {
						$filename = Configure::read('file_upload_prefix') . $this->request->data['Technician']['filename'];
						rename("upload_tmp/$filename", "files/$filename");
				}
			}
			if (strlen($this->request->data['Technician']['password']) > 0) $this->User->saveField("password", Security::hash($this->request->data['Technician']['password'], null, true));
			if ($this->Technician->save($this->request->data)) {
				$this->Session->setFlash(__('The technician has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The technician could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Technician.' . $this->Technician->primaryKey => $id));
			$this->request->data = $this->Technician->find('first', $options);
		}
		$users = $this->Technician->User->find('list');
		$documents = $this->Technician->Document->find('list');
		$this->set(compact('users', 'documents'));
	}

/**
 * delete method - just archives the guy
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->loadModel('Document');
		$this->Technician->id = $id;
		if (!$this->Technician->exists()) {
			throw new NotFoundException(__('Invalid Technician'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Technician->saveField('archived', 1)) {
			$this->Technician->saveField('active', 0);
			$technician = $this->Technician->find('first', array('conditions' => array('Technician.id' => $id)));
			$this->Document->id = $technician['Technician']['document_id'];
			if ($this->Document->saveField('archived', 1)) {
				$this->Session->setFlash(__('The technician has been deleted.' . $this->Technician->document_id));
			} else {
				$this->Session->setFlash(__('The technician could not be deleted. Please, try again.'));
			}
		}
		return $this->redirect(array('action' => 'index'));
	}
}
