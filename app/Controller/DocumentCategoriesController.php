<?php
App::uses('AppController', 'Controller');
/**
 * Companies Controller
 *
 * @property DocumentCategory $DocumentCategory
 * @property PaginatorComponent $Paginator
*/
class DocumentCategoriesController extends AppController {

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
				'order' => array('DocumentCategory.order' => 'asc'),
				'conditions' => array('DocumentCategory.archived' => '0')
		);
		$this->DocumentCategory->recursive = 0;
		$this->set('documentCategories', $this->Paginator->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->DocumentCategory->exists($id)) {
			throw new NotFoundException(__('Invalid documentCategory'));
		}
		$options = array('conditions' => array('DocumentCategory.' . $this->DocumentCategory->primaryKey => $id));
		$this->set('documentCategory', $this->DocumentCategory->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->DocumentCategory->create();
			if ($this->DocumentCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The documentCategory has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The documentCategory could not be saved. Please, try again.'));
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
		if (!$this->DocumentCategory->exists($id)) {
			throw new NotFoundException(__('Invalid document category'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->DocumentCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The document category has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The document category could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('DocumentCategory.' . $this->DocumentCategory->primaryKey => $id));
			$this->request->data = $this->DocumentCategory->find('first', $options);
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
		$this->loadModel('Document');
		$this->DocumentCategory->id = $id;
		if (!$this->DocumentCategory->exists()) {
			throw new NotFoundException(__('Invalid document'));
		}
		if ($this->DocumentCategory->field('persistent') == 1) {
			$this->Session->setFlash(__('The category cannot not be deleted because it is the default category for certain types of files.'));
		}
		else {
			$docs = $this->Document->find('count', array('conditions'=>array("document_category_id = $id")));
			if ($docs == 0) {
				if ($this->DocumentCategory->saveField('archived', 1)) {
					$this->Session->setFlash(__('The category has been deleted.'));
				} else {
					$this->Session->setFlash(__('The category could not be deleted. Please, try again.'));
				}
			}
			else {
				$this->Session->setFlash(__('Their are still documents which belong to this category. It cannot be deleted.'));
			}
		}
		return $this->redirect(array('action' => 'index'));
	}

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index() {
		$this->DocumentCategory->recursive = 0;
		$this->set('documentCategories', $this->Paginator->paginate());
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null) {
		if (!$this->DocumentCategory->exists($id)) {
			throw new NotFoundException(__('Invalid documentCategory'));
		}
		$options = array('conditions' => array('DocumentCategory.' . $this->DocumentCategory->primaryKey => $id));
		$this->set('documentCategory', $this->DocumentCategory->find('first', $options));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->DocumentCategory->create();
			if ($this->DocumentCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The documentCategory has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The documentCategory could not be saved. Please, try again.'));
			}
		}
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null) {
		if (!$this->DocumentCategory->exists($id)) {
			throw new NotFoundException(__('Invalid document category'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->DocumentCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The document category has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The document category could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('DocumentCategory.' . $this->DocumentCategory->primaryKey => $id));
			$this->request->data = $this->DocumentCategory->find('first', $options);
		}
	}

	/**
	 * admin_delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null) {
		$this->DocumentCategory->id = $id;
		if (!$this->DocumentCategory->exists()) {
			throw new NotFoundException(__('Invalid document category'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->DocumentCategory->delete()) {
			$this->Session->setFlash(__('The document category has been deleted.'));
		} else {
			$this->Session->setFlash(__('The document category could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
