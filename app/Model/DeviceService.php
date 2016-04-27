<?php
App::uses('AppModel', 'Model');
/**
 * Visit Model
 *
 * @property Device $Device
*/
class DeviceService extends AppModel {

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
