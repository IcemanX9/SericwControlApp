<?php
App::uses('AppController', 'Controller');
/**
 * Chemicals Controller
 *
 * @property Chemical $Chemical
 * @property PaginatorComponent $Paginator
 */
class ChemicalsController extends AppController {

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
				'conditions' => array('Chemical.archived =' => '0'),
		);
		$this->Chemical->recursive = 0;
		$this->set('chemicals', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Chemical->exists($id)) {
			throw new NotFoundException(__('Invalid chemical'));
		}
		$options = array('conditions' => array('Chemical.' . $this->Chemical->primaryKey => $id));
		$this->set('chemical', $this->Chemical->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->loadModel('Document');
		if ($this->request->is('post')) {
			//deal with the document
			if ($this->Document->saveMany(array(array(
					"name" => $this->request->data['Chemical']['name'] . ' Chemical Information Document',
					"filename" => $this->request->data['Chemical']['filename'],
					"file_type" => $this->request->data['Chemical']['file_type'],
					"document_category_id" => $this->request->data['Chemical']['document_category_id'],
					"mime" => $this->request->data['Chemical']['mime'],
					"size" => $this->request->data['Chemical']['size'],
					"user_id" => $this->request->data['Chemical']['admin_user_id'],
					"policyDocument" => 1,
					"meta" => $this->request->data['Chemical']['meta'])))) {
				$this->request->data['Chemical']['document_id'] = $this->Document->getId();
				$filename = Configure::read('file_upload_prefix') . $this->request->data['Chemical']['filename'];
				rename("upload_tmp/$filename", "files/$filename");		
			}
			$this->Chemical->create();
			if ($this->Chemical->save($this->request->data)) {
				$this->Session->setFlash(__('The chemical has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The chemical could not be saved. Please, try again.'));
			}
		}
		$documents = $this->Chemical->Document->find('list');
		$clients = $this->Chemical->Client->find('list');
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
		if (!$this->Chemical->exists($id)) {
			throw new NotFoundException(__('Invalid chemical'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->request->data['Chemical']['documentChanged'] == 1) {
				if ($this->Document->saveMany(array(array(
						"id" => $this->request->data['Chemical']['document_id'],
						"name" => $this->request->data['Chemical']['name'] . ' Chemical Information Document',
						"filename" => $this->request->data['Chemical']['filename'],
						"file_type" => $this->request->data['Chemical']['file_type'],
						"document_category_id" => $this->request->data['Chemical']['document_category_id'],
						"mime" => $this->request->data['Chemical']['mime'],
						"size" => $this->request->data['Chemical']['size'],
						"user_id" => $this->request->data['Chemical']['admin_user_id'],
						"policyDocument" => 1,
						"meta" => $this->request->data['Chemical']['meta'])))) {
						$filename = Configure::read('file_upload_prefix') . $this->request->data['Chemical']['filename'];
						rename("upload_tmp/$filename", "files/$filename");
				}
			}
			if ($this->Chemical->save($this->request->data)) {
				$this->Session->setFlash(__('The chemical has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The chemical could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Chemical.' . $this->Chemical->primaryKey => $id));
			$this->request->data = $this->Chemical->find('first', $options);
		}
		$documents = $this->Chemical->Document->find('list');
		$clients = $this->Chemical->Client->find('list');
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
		$this->Chemical->id = $id;
		if (!$this->Chemical->exists()) {
			throw new NotFoundException(__('Invalid Chemical'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Chemical->saveField('archived', 1)) {
			$chemical = $this->Chemical->find('first', array('conditions' => array('Chemical.id' => $id)));
			$this->Document->id = $chemical['Chemical']['document_id'];
			if ($this->Document->saveField('archived', 1)) {
				$this->Session->setFlash(__('The chemical has been deleted.' . $this->Chemical->document_id));
			} else {
				$this->Session->setFlash(__('The chemical could not be deleted. Please, try again.'));
			}
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function checkUniqueness($field, $value) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$conditions = array(
				$field => $value
		);
		if ($this->Chemical->hasAny($conditions)){
			echo "true";
		}
		else echo "false";
	}
}
