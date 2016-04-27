<?php
App::uses('AppController', 'Controller');
/**
 * Companies Controller
 *
 * @property Company $Company
 * @property PaginatorComponent $Paginator
 */
class CompaniesController extends AppController {

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
		$this->Company->recursive = 0;
		$this->Paginator->settings = array(
				'conditions' => array('Company.id >' => '0'),
		);
		$this->set('companies', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!($this->Company->exists($id) && $id > -1)) {
			throw new NotFoundException(__('Invalid company'));
		}
		$options = array('conditions' => array('Company.' . $this->Company->primaryKey => $id));
		$this->set('company', $this->Company->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$fp = fopen($this->data['Company']['imgFile']['tmp_name'], 'r');
			$fpHeader = fopen($this->data['Company']['headerFile']['tmp_name'], 'r');
			$content = fread($fp, filesize($this->data['Company']['imgFile']['tmp_name']));
			$contentHeader = fread($fpHeader, filesize($this->data['Company']['headerFile']['tmp_name']));
			$this->request->data['Company']['logoBlob'] = $content;
			$this->request->data['Company']['headerBlob'] = $contentHeader;
			$this->Company->create();
			if ($this->Company->save($this->request->data)) {
				$this->Session->setFlash(__('The company has been created.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The company could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!($this->Company->exists($id) && $id > -1)) {
			throw new NotFoundException(__('Invalid company'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->data['Company']['fileChanged'] == "true") {
				$fp = fopen($this->data['Company']['imgFile']['tmp_name'], 'r');
				$content = fread($fp, filesize($this->data['Company']['imgFile']['tmp_name']));
				$this->request->data['Company']['logoBlob'] = $content;
			}
			if ($this->data['Company']['headerFileChanged'] == "true") {
				$fp = fopen($this->data['Company']['headerFile']['tmp_name'], 'r');
				$content = fread($fp, filesize($this->data['Company']['headerFile']['tmp_name']));
				$this->request->data['Company']['headerBlob'] = $content;
			}
			if ($this->Company->save($this->request->data)) {
				$this->Session->setFlash(__('The company has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The company could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Company.' . $this->Company->primaryKey => $id));
			$this->request->data = $this->Company->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Company->id = $id;
		if (!($this->Company->exists($id) && $id > -1)) {
			throw new NotFoundException(__('Invalid company'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Company->delete()) {
			$this->Session->setFlash(__('The company has been deleted.'));
		} else {
			$this->Session->setFlash(__('The company could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	
	
	public function checkUniqueness($field, $value) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$conditions = array(
				$field => $value
		);
		if ($this->Company->hasAny($conditions)){
			echo "true";
		}
		else echo "false";
	}
	
	
	public function getLogo($id = null) {
		$this->autoRender = false;
		App::uses('Folder', 'Utility');
		App::uses('File', 'Utility');
		if (strpos($id, '.jpg')) {
			$id = substr($id, 0, -4);
		}
		//ensure that the user has access to this document
		$user = $this->Auth->user();
		$this->Company->id = $id;
		if (!$this->Company->exists()) {
			throw new NotFoundException(__('Invalid company'));
		}
		if ($this->Company->field('logoMime') == "image/jpeg") $ext = ".jpg";
		else $ext = '.png';
		$content = $this->Company->field('logoBlob');
		header('Accept-Ranges: bytes');
		header('Connection:Keep-Alive');
		header('Keep-Alive:timeout=1, max=100');
		header('Content-type: ' . $this->Company->field('logoMime'));
		header("Content-length: " . strlen($content));
		header("Cache-Control: no-cache");
		header('Content-Disposition: filename="logo' . $ext . '"');
		echo $content;
	}
	
	public function getHeader($id = null) {
		$this->autoRender = false;
		App::uses('Folder', 'Utility');
		App::uses('File', 'Utility');
		if (strpos($id, '.jpg')) {
			$id = substr($id, 0, -4);
		}
		//ensure that the user has access to this document
		$user = $this->Auth->user();
		$this->Company->id = $id;
		if (!$this->Company->exists()) {
			throw new NotFoundException(__('Invalid company'));
		}
		if ($this->Company->field('headerMime') == "image/jpeg") $ext = ".jpg";
		else $ext = '.png';
		$content = $this->Company->field('headerBlob');
		header('Accept-Ranges: bytes');
		header('Connection:Keep-Alive');
		header('Keep-Alive:timeout=1, max=100');
		header('Content-type: ' . $this->Company->field('headerMime'));
		header("Content-length: " . strlen($content));
		header("Cache-Control: no-cache");
		header('Content-Disposition: filename="header' . $ext . '"');
		echo $content;
	}
}
