<?php
App::uses('AppModel', 'Model');
/**
 * Visit Model
 *
 * @property Visit $Visit
*/
class ActionLog extends AppModel {

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
			'Visit' => array(
					'className' => 'Visit',
					'foreignKey' => 'nextVisitId',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			),
			'ServiceReport' => array(
					'className' => 'ServiceReport',
					'foreignKey' => 'service_report_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			),
			'Sighting' => array(
					'className' => 'Sighting',
					'foreignKey' => 'sighting_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			)
	);
}
