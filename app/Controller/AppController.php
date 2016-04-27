<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller', 'Auth', 'Users.RememberMe');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
*/
class AppController extends Controller {

	var $globalSettings;
	
	public $components = array(
			'DebugKit.Toolbar', 'Session', 'Users.RememberMe',
			'Auth' => array());
	
	public function beforeRender() {
		$this->set('userData', $this->Auth->user());
	}

	public function beforeFilter() {
		parent::beforeFilter();
		$this->RememberMe->restoreLoginFromCookie();

		// Let's load some settings
		$this->loadModel("Setting");
		$settings = $this->Setting->find("all");
		$this->globalSettings = array();
		foreach ($settings as $setting) $this->globalSettings[$setting['Setting']['name']] = $setting['Setting']['value']; 
		$this->set('globalSettings', $this->globalSettings);
		
		/**
		 * Configure the file path for uploaded files
		 * The file path is always prefixed with a folder corresponding to the name of the database this client is using
		 */
		Configure::write('file_upload_prefix', 'default/');
		if (isset($_SESSION['sc_database'])) {
			$prefix = $_SESSION['sc_database'];
			Configure::write('file_upload_prefix', $prefix.'/');
		}
		$url = $_SERVER['REQUEST_URI'];
		$index = strpos($url, '/api');
		if (!($index == false)) {
			$index = strrpos($url, '/');
			$prefix = substr($url, $index + 1);
			Configure::write('file_upload_prefix', $prefix.'/');
		}		

		//lets set up login redirect here based on roles
		$user = $this->Auth->user();
		if (isset($user)) {
			if ($user['role']=='user' || $user['role']=='admin') $this->Auth->loginRedirect = '/';
			if($user['role']=='client') $this->Auth->loginRedirect = '/Documents/showFile/';
		}

		$this->Auth->deny(); //deny all by default
		$this->Auth->allow('login','logout', 'apiListing', 'apiLogin', 'apiListingByLocation', 'apiGetClientByCode', 'apiGetClientDevices', 'apiCreateVisit', 'generateServiceReport', 'apiStartPremadeVisit',
				'apiSendVisitAction', 'apiGetClientData', 'apiGetVisitSummary', 'apiCompleteVisitWithReport', 'apiSaveIncompleteReport', 'apiGetOpenVisitsAndLogs', 'apiSync', 'apiUpdateVisitData', 'apiUploadMedia',
				'apiAuthCode', 'apiDownloadMap', 'apiAddConversation', 'apiGetNewConversations', 'apiSendTrainData', 'apiGetTrainUpdates', 'apiGetTrainNotifications');

		$this->Auth->authorize = 'Controller';
		$this->Auth->fields = array('username' => 'username', 'password' => 'passwd');
		$this->Auth->loginAction = array('plugin' => 'users', 'controller' => 'users', 'action' => 'login', 'admin' => false);
		$this->Auth->logoutRedirect = '/';
		$this->Auth->authError = __('Sorry, but you need to login to access this location.', true);
		$this->Auth->loginError = __('Invalid username / password	combination.  Please try again', true);
		$this->Auth->autoRedirect = true;
		$this->Auth->userModel = 'User';
		$this->Auth->userScope = array('User.active' => 1);
		if ($this->Auth->user()) {
			$this->set('userData', $this->Auth->user());
			$this->set('isAuthorized', ($this->Auth->user('id') != ''));
		}
	}

	public function isAuthorized($user) {
		$user = $this->Auth->user();
		// Allows only users role who are logged in to access resources by default
		if ($this->Auth->loggedIn() && ($user['role']=='user' || $user['role']=='admin')) {
			return true;
		}
		// Default deny
		return false;
	}
	
	public function apiAuthCode($db) {
		$this->autoRender = false;
		$result = "invalid";
		$version = "regular";
		switch ($this->request->data['code']) {
			case "eyrtgms8yo1a":
				$result = "default";
				break;
			case "4kf3fmp84n5w":
				$result = "flick";
				break;
			case "eyrtgms8yo1al":
				$result = "default";
				$version = "lite";
				break;
			case "4kf3fmp84n5wl":
				$result = "flick";
				$version = "lite";
				break;
		}
		echo '{"result":"' . $result . '", "version":"'. $version .'"}';
	}
	
	
	public function apiSync($db) {
		$this->loadModel('Technician');
		$this->loadModel('Device');
		$this->loadModel('DeviceType');
		$this->loadModel('Client');
		$this->loadModel('Visit');
		$this->loadModel('Chemical');
		$this->loadModel('ChemicalsClient');
		$this->loadModel('Users.User');
		$this->loadModel('Location');
		$this->autoRender = false;
		$fullList = "[";
		//load technicians
		$data = $this->Technician->find('all', array(
				'fields' => array('Technician.id', 'Technician.name', 'Technician.user_id', 'User.username', 'User.password'),
				'conditions' => array('Technician.archived =' => '0', 'Technician.active =' => '1')));
		$fullList = $fullList . json_encode($data) . ',';

		//load clients
		$this->Client->unbindModel(array('hasMany'=>array('Device', 'Visit'), 'hasAndBelongsToMany' => array('Chemical', 'Policy', 'Document'), 'belongsTo' => array('User', 'Company')));
		$data = $this->Client->find('all', array(
				'fields' => array('Client.id', 'Client.name', 'Client.latitude', 'Client.longitude', 'Client.code'),
				'conditions' => array('Client.archived =' => '0', 'Client.active =' => '1')));
		$fullList = $fullList . json_encode($data) . ',';

		//load visits
		$this->Visit->unbindModel(array('hasMany'=>array('ActionLog', 'ServiceReport', 'Sighting'), 'hasAndBelongsToMany' => array('Chemical', 'Device'), 'belongsTo' => array('Client', 'Technician')));
		$data = $this->Visit->find('all', array(
				'fields' => array('Visit.id', 'Visit.client_id', 'Visit.technician_id', 'Visit.status', 'Visit.purpose', 'Visit.timeRequested', 'Visit.timeDue', 'Visit.timeStarted'),
				'conditions' => array('Visit.status !=' => 'complete', 'Visit.purpose !='=>'installation')));
		$fullList = $fullList . json_encode($data) . ',';

		//load chemicals
		$this->Chemical->unbindModel(array('hasAndBelongsToMany' => array('Client'), 'belongsTo' => array('Document')));
		$data = $this->Chemical->find('all', array(
				'fields' => array('Chemical.id', 'Chemical.name', 'Chemical.code'),
				'conditions' => array('Chemical.archived =' => '0')));
		$fullList = $fullList . json_encode($data) . ',';
		
		//load chemicals / client join table
		$this->ChemicalsClient->unbindModel(array('belongsTo' => array('Client', 'Chemical')));
		$data = $this->ChemicalsClient->find('all');
		$fullList = $fullList . json_encode($data) . ',';

		//load devices
		$this->Device->unbindModel(array('belongsTo' => array('Client'), 'hasMany'=>array('DevicesVisit', 'DeviceService')));
		$data = $this->Device->find('all', array('contain' => array('Location'),
				'fields' => array('Device.id', 'Location.name', 'DeviceType.name', 'DeviceType.screen', 'Device.installed', 'Device.missing', 'Device.damaged', 'Device.client_id', 'Device.label'),
				'conditions' => array('Device.archived =' => '0', 'Device.active =' => '1')));
		for ($i=0;$i!=sizeof($data); $i++) {
			$data[$i]['Device']['location'] = $data[$i]['Location']['name'];
			$data[$i]['Device']['typeScreen'] = $data[$i]['DeviceType']['screen'];
			$data[$i]['Device']['type'] = $data[$i]['DeviceType']['name'];
		}
		$fullList = $fullList . json_encode($data) . ',';
		
		//load locations
		$this->Location->unbindModel(array('hasMany'=>array('Device')));
		$data = $this->Location->find('all', array('order'=>array('order'=>'asc')));
		$fullList = $fullList . json_encode($data) . ',';
		
		//load devicetypes
		$this->DeviceType->unbindModel(array('hasMany'=>array('Device')));
		$data = $this->DeviceType->find('all', array('order'=>array('order'=>'asc')));
		$fullList = $fullList . json_encode($data);

		$fullList = $fullList . "]";
		echo gzencode($fullList);
		//echo $fullList;
	}
	
	public function apiUploadMedia($db) {
		$this->loadModel("Media");
		$this->autoRender = false;
		if ($this->request->header('Media-token') == "b1BoQXT7OVuPXFVbnWCLrbyBojUFmCl4Sde7jGTngbveK2JW") {
			$this->Media->create();
			$this->Media->set("data", file_get_contents($_FILES['uploadedfile']['tmp_name']));
			$this->Media->set("filename", $_FILES['uploadedfile']['name']);
			$this->Media->set("mime", "image/png");
			$this->Media->save();
			echo '[{"result":"success", "id":"' . $this->Media->field('id') . '"}]';
		}
		else echo "not authorised";
	}

	/*
	 * This is the main function for receiving and processing technician logs. It will handle multiple types of logs and multiple actions and process each by updating database records accordingly
	* Parameters:
	*      - visit_id (required)
	*
	*/
	public function apiSendVisitAction() {
		$this->loadModel('Visit');
		$this->loadModel('DevicesVisit');
		$this->loadModel('Device');
		$this->loadModel('Location');
		$this->loadModel('Users.User');
		$this->loadModel('DeviceService');
		$this->loadModel('ChemicalsVisit');
		$this->loadModel('Chemical');
		$this->loadModel('ServiceReport');
		$this->loadModel('AreaSummary');
		$this->autoRender = false;
		$visitCompleteStatus = "complete";
		$visitInProgressStatus = "in progress";
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'])) {
				$result = "failure";
				$actionId = "-1";
				$this->Visit->id = $this->request->data['visit_id'];
				$this->Visit->read();
				switch ($this->request->data['type']) {
					case 'device':
						//set the initial device log
						$this->Device->create();
						$this->Device->id = $this->request->data['device_id'];
						$this->Device->read();
						$this->DevicesVisit->create();
						$this->DevicesVisit->set("device_id", $this->request->data['device_id']);
						$this->DevicesVisit->set("visit_id", $this->request->data['visit_id']);
						if (isset($this->request->data['scanned'])) $this->DevicesVisit->set("barcodeScanned", $this->request->data['scanned']);
						else ($this->DevicesVisit->set("barcodeScanned", $this->request->data['scanned']) == 1);
						$this->DevicesVisit->set("time", $this->request->data['time']);
						$this->DevicesVisit->set("device_type", $this->Device->field('device_type_id'));
						$this->DevicesVisit->set("latitude", $this->request->data['latitude']);
						$this->DevicesVisit->set("longitude", $this->request->data['longitude']);
						$this->DevicesVisit->set("action", $this->request->data['action']);
						$this->DevicesVisit->set("results", $this->request->data['results']);
						if ($this->DevicesVisit->save()) $result = "success";
						$actionId = $this->DevicesVisit->field('id');
						//now we need to do some further things to the device model to update its state, depending on the action
						$date = date("Y-m-d");
						$this->Device->set("lastChecked", $date);
						$this->Device->set("adhocCheckRequired", null);
						switch ($this->request->data['action']) {
							case 'install':
								$this->Device->set("installed", 1);
								$this->Device->set("lastSpecialServiceDate", $date);
								$this->Device->set("installedDate", $date);
								$this->Visit->set("status", $visitCompleteStatus);
								break;
							case 'check':
								$this->Visit->set("status", $visitInProgressStatus);
								if ($this->request->data['damaged'] == "true") {
									$this->Device->set("damaged", 1);
									$this->Device->set("damagedDate", $date);
								}
								if ($this->request->data['obscured'] == "true") {
									$this->Device->set("obscured", 1);
									$this->Device->set("obscuredDate", $date);
								}
								if ($this->request->data['followupDays'] > 0) $this->Device->set("adhocCheckRequired", date('Y-m-d', strtotime($date. ' + ' . $this->request->data['followupDays'] . ' days')));
								$resultData = json_decode($this->request->data['results'], true);
								if (isset($resultData['activity']) && $resultData['activity'] == "yes") $pestActivity = 1; else $pestActivity = 0;
								if (isset($resultData['problems']['chemicalActionId']) && $resultData['problems']['chemicalActionId'] > 0) $treated = 1; else $treated = 0;
								if (isset($this->request->data['pestsObserved'])) $pestType = $this->request->data['pestsObserved']; else $pestType = "";
								$this->Location->create();
								$this->Location->id = $this->Device->field('location_id');
								$this->createAndConsolidateAreaSummary($this->request->data['visit_id'], $this->Location->field('name'), 1, $treated, $pestActivity, $pestType);
								break;
							case 'reportedMissing':
								$this->Visit->set("status", $visitInProgressStatus);
								$this->Device->set("missing", 1);
								$this->Device->set("missingDate", $date);
								break;
							case 'reportedDamaged':
								$this->Visit->set("status", $visitInProgressStatus);
								$this->Device->set("damaged", 1);
								$this->Device->set("damagedDate", $date);
								break;
							case 'replace':
								$this->Visit->set("status", $visitInProgressStatus);
								$this->Device->set("damaged", 0);
								$this->Device->set("missing", 0);
								$this->Device->set("installed", 1);
								break;
							case 'replaceEFKLight':
								$this->Visit->set("status", $visitInProgressStatus);
								$this->Device->set("lastSpecialServiceDate", $date);
								$this->DeviceService->create();
								$this->DeviceService->set('serviceDate', $date);
								$this->DeviceService->set('visit_id', $this->request->data['visit_id']);
								$this->DeviceService->set('device_id', $this->request->data['device_id']);
								$this->DeviceService->set('serviceType', "EFK Light Replacement");
								$this->DeviceService->save();
								break;
						}
						if (!($this->Device->save()) || !($this->Visit->save())) $result = "failure";
						else $result = "success";
						break;
					case 'chemical':
						$this->Visit->set("status", $visitInProgressStatus);
						$this->Visit->save();
						$this->ChemicalsVisit->create();
						$this->ChemicalsVisit->set("chemical_id", $this->request->data['chemical_id']);
						$this->ChemicalsVisit->set("visit_id", $this->request->data['visit_id']);
						$this->ChemicalsVisit->set("time", $this->request->data['time']);
						$this->ChemicalsVisit->set("batch", $this->request->data['batch']);
						$this->ChemicalsVisit->set("dosage", $this->request->data['dosage']);
						$this->ChemicalsVisit->set("dilution", $this->request->data['dilution']);
						$this->ChemicalsVisit->set("applicationType", $this->request->data['application']);
						$this->ChemicalsVisit->set("pestActivity", $this->request->data['activity']);
						$this->ChemicalsVisit->set("comments", $this->request->data['comments']);
						$this->ChemicalsVisit->set("areaUsed", $this->request->data['areaUsed']);
						$this->ChemicalsVisit->save();
						$result = "success";
						$actionId = $this->ChemicalsVisit->field('id');
						break;
					case 'report':
						//set the initial device log
						$this->ServiceReport->create();
						$this->ServiceReport->set("visit_id", $this->request->data['visit_id']);
						$this->ServiceReport->set("document_id", -1);
						//now we need to do some further things to the device model to update its state, depending on the action
						switch ($this->request->data['action']) {
							case 'technicianSignature':
								$this->ServiceReport->set("technicianSignature", $this->request->data['signatureData']);
								break;
						}
						if (!$this->ServiceReport->save()) $result = "failure";
						else $result = "success";
						break;
					case 'areaSummary':
						$summaries = json_decode($this->request->data['results']);
						foreach ($summaries as $summaryOb) {
							$summary = (array)$summaryOb;
							if ($summary['inspected'] == "Yes") $summary['inspected'] = 1;
							else $summary['inspected'] = 0;
							if ($summary['treated'] == "Yes") $summary['treated'] = 1;
							else $summary['treated'] = 0;
							if ($summary['pestActivity'] == "Yes") $summary['pestActivity'] = 1;
							else $summary['pestActivity'] = 0;
							$this->createAndConsolidateAreaSummary($this->request->data['visit_id'], $summary['area'], $summary['inspected'], $summary['treated'],
									$summary['pestActivity'], $summary['pestType'], $summary['comment'], true);
						}
						$result = "success";
						break;
				}
				echo "{'result': '$result', 'action_id': '$actionId'}";
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}
	
	public function createAndConsolidateAreaSummary($visit_id, $area, $inspected, $treated, $pestActivity, $pestType, $comments = null, $tokenize = false) {
		$this->loadModel('AreaSummary');	
		$this->AreaSummary->create();
		$existing = $this->AreaSummary->find('first', array('conditions'=>array('visit_id'=>$visit_id, 'area'=>$area)));
		if (sizeof($existing) == 0) {
			$this->AreaSummary->create();
			$this->AreaSummary->set("visit_id", $visit_id);
			$this->AreaSummary->set("area", $area);
			$this->AreaSummary->set("inspected", $inspected);
			$this->AreaSummary->set("treated", $treated);
			$this->AreaSummary->set("pestActivity", $pestActivity);
			if ($comments != null) $this->AreaSummary->set("comments", $comments);
			$this->AreaSummary->set("pestActivityType", $pestType);
			$this->AreaSummary->save();
		}
		else {
			$this->AreaSummary->create();
			$this->AreaSummary->id = $existing['AreaSummary']['id'];
			$this->AreaSummary->read();
			if ($existing['AreaSummary']['inspected'] == 0) $this->AreaSummary->set("inspected", $inspected);
			if ($existing['AreaSummary']['treated'] == 0) $this->AreaSummary->set("treated", $treated);
			if ($existing['AreaSummary']['pestActivity'] == 0) $this->AreaSummary->set("pestActivity", $pestActivity);
			if ($comments != null) $this->AreaSummary->set("comments", $comments);
			if ($tokenize) {
				$tokens = explode('; ', $pestType);
				foreach ($tokens as $pest) {
					if (strtolower($pest) != 'none') {
						if (strtolower($existing['AreaSummary']['pestActivityType']) == 'none' || $existing['AreaSummary']['pestActivityType'] == "" || $existing['AreaSummary']['pestActivityType'] == null) {
							$this->AreaSummary->set("pestActivityType", $pest);
							$existing['AreaSummary']['pestActivityType'] = $pest;
						}
						else if (strpos($existing['AreaSummary']['pestActivityType'], $pest) == false) {
							$this->AreaSummary->set("pestActivityType", $existing['AreaSummary']['pestActivityType'] . "; " . $pest);
							$existing['AreaSummary']['pestActivityType'] = $existing['AreaSummary']['pestActivityType'] . "; " . $pest;
						}
					}
				}
			}
			else {
				if (strtolower($pestType) != 'none') {
					if (strtolower($existing['AreaSummary']['pestActivityType']) == 'none' || $existing['AreaSummary']['pestActivityType'] == "" || $existing['AreaSummary']['pestActivityType'] == null)
						$this->AreaSummary->set("pestActivityType", $pestType);
					else if (strpos($existing['AreaSummary']['pestActivityType'], $pestType) == false) $this->AreaSummary->set("pestActivityType", $existing['AreaSummary']['pestActivityType'] . "; " . $pestType);
				}				
			}
			$this->AreaSummary->save();
		}
	}
}
