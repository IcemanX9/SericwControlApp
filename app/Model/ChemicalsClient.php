<?php
App::uses('AppModel', 'Model');
/**
 * Chemical Model
 *
*/
class ChemicalsClient extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
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
			'Chemical' => array(
					'className' => 'Chemical',
					'foreignKey' => 'chemical_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			)
	);
}