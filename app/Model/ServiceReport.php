<?php
App::uses('AppModel', 'Model');
/**
 * ServiceReport Model
 *
 * @property Document $Document
 * @property Visit $Visit
 * @property ServiceReportProblem $ServiceReportProblem
 */
class ServiceReport extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'status';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Document' => array(
			'className' => 'Document',
			'foreignKey' => 'document_id',
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
		),
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'clientSignature',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Media' => array(
			'className' => 'Media',
			'foreignKey' => 'technicianSignature',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
