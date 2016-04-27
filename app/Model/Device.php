<?php
App::uses('AppModel', 'Model');
/**
 * Device Model
 *
 * @property Client $Client
*/
class Device extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'location';
	public $actsAs = array( 'AuditLog.Auditable' );


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
			'Location' => array(
					'className' => 'Location',
					'foreignKey' => 'location_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			),
			'DeviceType' => array(
					'className' => 'DeviceType',
					'foreignKey' => 'device_type_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			)
	);

	public $hasMany = array(
			'DeviceService' => array(
					'className' => 'DeviceService',
					'foreignKey' => 'device_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			),
			'DevicesVisit' => array(
					'className' => 'DevicesVisit',
					'foreignKey' => 'device_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			),
	);

	public function getBarcodeImage($id = null) {
		App::import('Vendor', 'tcpdf_min/tcpdf_barcodes_2d');
		if ($id == null) $id = $this->id;
		$barcode = new TCPDF2DBarcode($id, "QRCODE,Q");
		$barcode->getBarcodePNG(3, 3, array(0,0,0));
	}


}
