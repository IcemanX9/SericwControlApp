<?php
App::uses('AppModel', 'Model');
/**
 * ChemicalsVisit Model
 *
 * @property Visit $Visit
 * @property Chemical $Chemical
 */
class ChemicalsVisit extends AppModel {


	public $actsAs = array( 'AuditLog.Auditable' );
	//The Associations below have been created with all possible keys, those that are not needed can be removed

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
		),
		'Chemical' => array(
			'className' => 'Chemical',
			'foreignKey' => 'chemical_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
