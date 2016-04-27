<?php
App::uses('AppModel', 'Model');
/**
 * Visit Model
 *
 * @property Client $Client
 * @property Technician $Technician
 * @property ActionLog $ActionLog
 * @property ServiceReport $ServiceReport
 * @property Sighting $Sighting
 * @property Chemical $Chemical
 * @property Device $Device
 */
class Visit extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';
	public $actsAs = array( 'AuditLog.Auditable' ); //makes this model auditable


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Technician' => array(
			'className' => 'Technician',
			'foreignKey' => 'technician_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ActionLog' => array(
			'className' => 'ActionLog',
			'foreignKey' => 'visit_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ServiceReport' => array(
			'className' => 'ServiceReport',
			'foreignKey' => 'visit_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'AreaSummary' => array(
					'className' => 'AreaSummary',
					'foreignKey' => 'visit_id',
					'dependent' => false,
					'conditions' => '',
					'fields' => '',
					'order' => '',
					'limit' => '',
					'offset' => '',
					'exclusive' => '',
					'finderQuery' => '',
					'counterQuery' => ''
		),
		'Sighting' => array(
			'className' => 'Sighting',
			'foreignKey' => 'visit_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'DeviceService' => array(
			'className' => 'DeviceService',
			'foreignKey' => 'visit_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);


/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Chemical' => array(
			'className' => 'Chemical',
			'joinTable' => 'chemicals_visits',
			'foreignKey' => 'visit_id',
			'associationForeignKey' => 'chemical_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Device' => array(
			'className' => 'Device',
			'joinTable' => 'devices_visits',
			'foreignKey' => 'visit_id',
			'associationForeignKey' => 'device_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

}
