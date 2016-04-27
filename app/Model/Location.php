<?php
App::uses('AppModel', 'Model');
/**
 * Location Model
 *
 */
class Location extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	
	public $hasMany = array(
			'Device' => array(
					'className' => 'Device',
					'foreignKey' => 'location_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			)
	);
}
