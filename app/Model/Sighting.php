<?php
App::uses('AppModel', 'Model');
/**
 * Visit Model
 *
 * @property Visit $Visit
 */
class Sighting extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Visit' => array(
			'className' => 'Visit',
			'foreignKey' => 'visit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
