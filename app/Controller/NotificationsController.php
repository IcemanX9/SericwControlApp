<?php
App::uses('AppController', 'Controller');
/**
 * Notifications Controller
 *
 * @property Notifications $Notification
 * @property PaginatorComponent $Paginator
 */
class NotificationsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

	
	public function isAuthorized($user) {
		$user = $this->Auth->user();
		return parent::isAuthorized($user);
	}
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->isAdmin();
		$this->Notification->recursive = 0;
		$this->set('notifications', $this->Paginator->paginate());
	}
		
	public function isAdmin() {
		if ($this->Auth->user('role')=='admin') {}
		else {
			$this->Session->setFlash(__('You are not authorised to access this area.'));
			return $this->redirect('/');
		}
	}
	
	public function runAllNoFrequencyCheck() {
		echo "Running notifications - this might take a while, please do not leave this page.";
		$this->Notification->runAllNotifications(true);
		$this->Session->setFlash(__('Notifications have been sent successfully.'));
		$this->redirect(array('action' => 'index'));
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
		$this->Notification->id = $id;
		if ($state == "true") $bool = 1;
		else $bool = 0;
		$this->Notification->saveField('active', $bool);
		echo $bool;
	}
	
	/**
	 * save frequency method ajax call
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function saveFrequency($id, $frequency) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->Notification->id = $id;
		$this->Notification->saveField('runFrequency', $frequency);
		echo $frequency;
	}
	
	/**
	 * save frequency method ajax call
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function saveEmail($id, $email) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->Notification->id = $id;
		$this->Notification->saveField('email', $email);
		echo $email;
	}
}
