<?php
App::uses('AppModel', 'Model');
/**
 * DevicesVisit Model
 *
 * @property Device $Device
 * @property Visit $Visit
 */
class DevicesVisit extends AppModel {


	public $actsAs = array( 'AuditLog.Auditable' );
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Device' => array(
			'className' => 'Device',
			'foreignKey' => 'device_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Visit' => array(
			'className' => 'Visit',
			'foreignKey' => 'visit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
