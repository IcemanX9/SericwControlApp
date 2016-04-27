<?php
App::uses('AppController', 'Controller');
/**
 * Bugs Controller
 */
class BugsController extends AppController {

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Bug->create();
			if ($this->Bug->save($this->request->data)) {
				$this->loadModel('Notification');
				$this->loadModel('Users.User');
				$this->User->id = $this->request->data['Bug']['user_id'];
				$name = $this->User->field('username');
				$this->Notification->sendMail("jonathan.haenen@gmail.com", "New bug raised by " . $name, $this->request->data['Bug']['description']);
				$this->Session->setFlash(__('The bug has been logged. You will be notified when it has been dealt with.'));
				return $this->redirect('/');
			} else {
				$this->Session->setFlash(__('The bug could not be logged. Please, try again.'));
			}
		}
	}
}
