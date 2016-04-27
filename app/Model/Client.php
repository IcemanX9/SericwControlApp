<?php
App::uses('AppModel', 'Model');
/**
 * Client Model
 *
 * @property Company $Company
 * @property User $User
 * @property Document $Document
 */
class Client extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	public $actsAs = array( 'AuditLog.Auditable' ); //makes this model auditable
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
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
			'Device' => array(
					'className' => 'Device',
					'foreignKey' => 'client_id',
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
			'Visit' => array(
					'className' => 'Visit',
					'foreignKey' => 'client_id',
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
		'Document' => array(
			'className' => 'Document',
			'joinTable' => 'clients_documents',
			'foreignKey' => 'client_id',
			'associationForeignKey' => 'document_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Chemical' => array(
			'className' => 'Chemical',
			'joinTable' => 'chemicals_clients',
			'foreignKey' => 'client_id',
			'associationForeignKey' => 'chemical_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Policy' => array(
			'className' => 'Policy',
			'joinTable' => 'clients_policies',
			'foreignKey' => 'client_id',
			'associationForeignKey' => 'policy_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

	
	/**
	 * Calculates the great-circle distance between two points, with
	 * the Haversine formula.
	 * @param float $latitudeFrom Latitude of start point in [deg decimal]
	 * @param float $longitudeFrom Longitude of start point in [deg decimal]
	 * @param float $earthRadius Mean earth radius in [m]
	 * @return float Distance between points in [m] (same as earthRadius)
	*/
	public function distanceFromCurrent($latitudeFrom, $longitudeFrom, $earthRadius = 6371000) {
		if ($this->field('latitude') == null || $this->field('longitude') == null) {
			return -1;
		}
		else {
			$latitudeTo = $this->field('latitude');
			$longitudeTo = $this->field('longitude');
			// convert from degrees to radians
			$latFrom = deg2rad($latitudeFrom);
			$lonFrom = deg2rad($longitudeFrom);
			$latTo = deg2rad($latitudeTo);
			$lonTo = deg2rad($longitudeTo);

			$latDelta = $latTo - $latFrom;
			$lonDelta = $lonTo - $lonFrom;

			$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +	cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
			return $angle * $earthRadius;
		}
	}
	
	public function distanceFrom($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
		if ($latitudeTo == null || $longitudeTo == null) {
			return 99999999999;
		}
		else {
			// convert from degrees to radians
			$latFrom = deg2rad($latitudeFrom);
			$lonFrom = deg2rad($longitudeFrom);
			$latTo = deg2rad($latitudeTo);
			$lonTo = deg2rad($longitudeTo);
	
			$latDelta = $latTo - $latFrom;
			$lonDelta = $lonTo - $lonFrom;
	
			$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +	cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
			return $angle * $earthRadius;
		}
	}
	
	public function getClientCompanyLogo($clientId) {
		App::import('Model', 'Company');
		$this->id = $clientId;
		$Company = new Company();
		$Company->id = $this->field('company_id');
		return $Company->field('logoBlob');
	}
	
	public function getClientCompanyHeader($clientId) {
		App::import('Model', 'Company');
		$this->id = $clientId;
		$Company = new Company();
		$Company->id = $this->field('company_id');
		return $Company->field('headerBlob');
	}
}
