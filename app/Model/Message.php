<?php
App::uses('AppModel', 'Model');
/**
 * Message Model
 *
 * @property Client $Client
 */
class Message extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	public function createMessage($user_id, $from, $message) {
		$this->create();
		$this->set('user_id', $user_id);
		$this->set('from', $from);
		$this->set('message', $message);
		$this->save();
	}
}
