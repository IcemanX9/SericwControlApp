<?php
App::uses('AppController', 'Controller');
/**
 * Visits Controller
 *
 * @property Visit $Visit
 * @property PaginatorComponent $Paginator
*/
class VisitsController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');

	public function listOpenVisits($overdueonly=false) {
		$this->Paginator->settings = array("conditions"=>array("Visit.status != 'complete'"), "order"=>array("Visit.status"=>"asc", "Visit.client_id"=>"asc"));
		if ($overdueonly) array_push($this->Paginator->settings['conditions'], "Visit.timeDue < '" . date('Y-m-d H:i:s') . "'");
		$this->set('visits', $this->Paginator->paginate());
	}
	
	public function listOverdueServices() {
		$this->loadModel('Client');
		$clientData = $this->Client->find("all", array("conditions"=>array("Client.nextService < '" . date('Y-m-d') . "'"), "order"=>array("Client.nextServiceScheduled"=>"asc")));
		$this->set('clients', $clientData);
	}

	public function viewSchedule($date = null) {
		$this->loadModel('Client');
		if ($date==null)  $upcomingVisits = $this->Visit->find('all', array("conditions"=>array("Visit.status != 'complete'",
				"Visit.timeDue > '" . date('Y-m-d') . " 00:00:00'", "Visit.timeDue < '" . date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)) . "'")));
		else $upcomingVisits = $this->Visit->find('all', array("conditions"=>array("Visit.status != 'complete'", "Visit.timeDue LIKE '" . date('Y-m-d', strtotime($date)) . "%'")));
		$this->Client->unbindModel(array('hasMany'=>array('Device', 'Visit'), 'hasAndBelongsToMany' => array('Chemical', 'Policy', 'Document'), 'belongsTo' => array('User', 'Company')));
		if ($date==null) $upcomingServices = $this->Client->find('all', array("conditions"=>array("Client.nextServiceScheduled >= '" . date('Y-m-d') . " 00:00:00'", "Client.nextServiceScheduled < '" . date('Y-m-d', time() + (7 * 24 * 60 * 60)) . " 23:59:59'")));
		else $upcomingServices = $this->Client->find('all', array("conditions"=>array("Client.nextServiceScheduled = '" . $date . "'")));
		for($i=0;$i!=sizeof($upcomingServices);$i++) {
			$upcomingServices[$i]['Visit']['purpose'] = "Routine Visit";
			$upcomingServices[$i]['Visit']['serviceDate'] = $upcomingServices[$i]['Client']['nextService'];
			$upcomingServices[$i]['Visit']['timeDue'] = $upcomingServices[$i]['Client']['nextServiceScheduled'];
			$upcomingServices[$i]['Visit']['timeRequested'] = "Routine: Automatic";
			$upcomingServices[$i]['Visit']['status'] = "n/a";
		}
		$upcomingVisits = array_merge($upcomingVisits, $upcomingServices);
		function sort_visits($a, $b) {	return strtotime($a['Visit']['timeDue']) - strtotime($b['Visit']['timeDue']); }
		usort($upcomingVisits, 'sort_visits');
		if ($date==null) {
			$overdueServices = $this->Client->find("all", array("conditions"=>array("Client.nextServiceScheduled < '" . date('Y-m-d') . "'"), "order"=>array("Client.nextServiceScheduled"=>"asc")));
			$lateAdhoc = $this->Visit->find("all", array("conditions"=>array("Visit.status != 'complete'", "Visit.timeDue < '" . date('Y-m-d H:i:s') . "'"), "order"=>array("Visit.status"=>"asc", "Visit.client_id"=>"asc")));
			$this->set('onedayonly', false);
		}
		else {
			$overdueServices = array();
			$lateAdhoc = array();
			$this->set('onedayonly', true);
		}
		$this->set('lateAdhocVisits', $lateAdhoc);
		$this->set('overdueServices', $overdueServices);
		$this->set('upcomingVisits', $upcomingVisits);
	}
	
	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Visit->create();
			if ($this->Visit->save($this->request->data)) {
				$this->Session->setFlash(__('The visit has been created.'));
				return $this->redirect(array('action' => 'listOpenVisits'));
			} else {
				$this->Session->setFlash(__('The visit could not be saved. Please, try again.'));
			}
		}
	}
	
	public function saveClientNextService($id, $date) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->loadModel("Client");
		if (strtotime($date) < time() - (24*60*60)	) echo "pastdate";
		else {
			$this->Client->id = $id;
			$this->Client->saveField('nextService', $date);
			echo $date;
		}
	}
	
	public function saveClientNextServiceScheduled($id, $date) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->loadModel("Client");
		if (strtotime($date) < time() - (24*60*60)) echo "pastdate";
		else {
			$this->Client->id = $id;
			$this->Client->saveField('nextServiceScheduled', $date);
			echo $date;
		}
	}
	
	public function skipToNextVisit($id = null, $schedule = false) {
		$this->loadModel('Client');
		$this->Client->id = $id;
		if (!$this->Client->exists()) {
			throw new NotFoundException(__('Invalid client'));
		}
		//update the next service date for the client
		if ($this->Client->field('nextService') == "0000-00-00") $this->Client->saveField('nextService', date("Y-m-d",time()));
		$nextDate = $this->getNextServiceDate($this->Client->field('nextService'), $this->Client->field('ServiceFrequency'));
		if ($this->Client->saveField('nextService',$nextDate) && $this->Client->saveField('nextServiceScheduled',$nextDate)) {
			$this->Session->setFlash(__('You have skipped the service period for selected client.'));
		} else {
			$this->Session->setFlash(__('The service could not be skipped.'));
		}
		if ($schedule) return $this->redirect(array('action' => 'viewSchedule'));
		else return $this->redirect(array('action' => 'listOverdueServices'));
	}
	
	public function getNextServiceDate($date, $frequency) {
		$date = strtotime($date);
		$nextMonth = (date('m', $date) == 12) ? 1: date('m', $date) + 1; 
		$nextMonthYear = ($nextMonth == 1) ? date('Y', $date) + 1 : date('Y', $date); 
		$nextMonthDays = cal_days_in_month (CAL_GREGORIAN, $nextMonth, $nextMonthYear);
		$nextNextMonth = (date('m', $date) > 10) ? date('m', $date) - 10: date('m', $date) + 2;
		$nextNextMonthYear = ($nextNextMonth < 3) ? date('Y', $date) + 1 : date('Y', $date);
		$nextNextMonthDays = cal_days_in_month (CAL_GREGORIAN, $nextNextMonth, $nextNextMonthYear );
		switch ($frequency) {
			case "Monthly":
				$days = $nextMonthDays;
				break;
			case "Every two weeks":
				$days = 14;
				break;
			case "Every week":
				$days = 7;
				break;
			case "Every six weeks":
				$days = 42;
				break;				
			case "Every two months":
				$days = $nextMonthDays + $nextNextMonthDays;
				break;
			default:
				$days = $nextMonthDays;
		}
		$nextDate = date("Y-m-d", strtotime("+ $days days",	$date));
		return $nextDate;
	}
	
	/**
	 * save due date method ajax call
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function saveDueDate($id, $date) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		if (strtotime($date) < time()) echo "pastdate";
		else {
			$this->Visit->id = $id;
			$this->Visit->saveField('timeDue', $date . " 23:00:00");
			$currentNote = $this->Visit->field('notes');
			if ($currentNote == "null") $currentNote = "";
			$this->Visit->saveField('notes', $currentNote . "Manually changed due date to " . $date . " on " . date("Y-m-d H:i:s", time()). ". ");
			echo $date;
		}
	}
	
	/**
	 * save due date method ajax call
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function saveStatus($id, $status) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->Visit->id = $id;
		if ($this->Visit->field("status") != "complete") {
			$currentNote = $this->Visit->field('notes');
			if ($currentNote == "null") $currentNote = "";
			$this->Visit->saveField('notes', $currentNote . "Manually changed status to " . $status . " on " . date("Y-m-d H:i:s", time()) . ". ");
			$this->Visit->saveField('status', $status);
		}
		echo $status;
	}

	//I can't remember why this function is here.
	public function nothing() {
		$this->autoRender = false;
		$this->loadModel('ServiceReport');
		$data = $this->ServiceReport->find('first', array('conditions'=>array('ServiceReport.id'=>82)));
		//echo $data['ServiceReport']['clientSignature'] . "'>";
		header('Content-type: image/jpeg');
		//header('Content-Disposition: attachment; filename="test.png"');
		echo $data['ServiceReport']['technicianSignature'];
	}

	public function delete($id = null, $schedule = false) {
		$this->Visit->id = $id;
		if (!$this->Visit->exists()) {
			throw new NotFoundException(__('Invalid visit'));
		}
		if ($this->Visit->field("status") != "complete") {
			if ($this->Visit->delete()) {
				$this->Session->setFlash(__('The visit has been deleted.'));
			} else {
				$this->Session->setFlash(__('The visit could not be deleted. Please, try again.'));
			}
		}
		else $this->Session->setFlash(__('You cannot delete completed visits.'));
		if ($schedule) return $this->redirect(array('action' => 'viewSchedule'));
		else return $this->redirect(array('action' => 'listOpenVisits'));
	}
	
	/**
	 * gives a json list of clients orderd by location
	 *
	 * @return void
	 */
	public function apiCreateVisit($db) {
		$this->loadModel('Users.User');
		$this->autoRender = false;
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'], $db)) {
				$this->Visit->unbindModel(array('hasMany'=>array('ActionLog', 'ServiceReport', 'Sighting', 'DeviceService'), 'hasAndBelongsToMany' => array('Chemical', 'Device', 'AreaSummary'), 'belongsTo' => array('Client')));
				$this->Visit->create();
				$this->Visit->set("client_id", $this->request->data['client_id']);
				$this->Visit->set("technician_id", $this->request->data['technician_id']);
				$this->Visit->set("purpose", $this->request->data['purpose']);
				if (isset($this->request->data['status'])) $this->Visit->set("status", $this->request->data['status']);
				if ($this->request->data['startNow'] == "yes") $this->Visit->set("timeStarted", date("Y-m-d H:i:s"));
				$this->Visit->set("timeRequested", date("Y-m-d H:i:s"));
				$this->Visit->set("timeDue", date('Y-m-d H:i:s'));
				$this->Visit->set("latitude", $this->request->data['latitude']);
				$this->Visit->set("longitude", $this->request->data['longitude']);
				$this->Visit->set("locationMeta", $this->request->data['locationMeta']);
				echo(json_encode($this->Visit->save()));
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}

	public function apiGetVisitSummary($db) {
		$this->loadModel('Users.User');
		$this->autoRender = false;
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'], $db)) {
				$this->Visit->id = $this->request->data['visit_id'];
				$data = $this->Visit->find('first', array(
						'conditions' => array('Visit.id =' => $this->request->data['visit_id'])));
				echo gzencode(json_encode($data));
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}

	public function apiCompleteVisitWithReport($db) {
		$this->loadModel('Users.User');
		$this->loadModel('Technician');
		$this->loadModel('Client');
		$this->loadModel("ServiceReport");
		$this->loadModel("Document");
		$this->loadModel('Sighting');
		$this->loadModel('ActionLog');
		$this->loadModel('Notification');
		$this->loadModel('Message');
		$this->autoRender = false;
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'], $db)) {
				$date = date('Y-m-d H:i:s');
				$this->Visit->id = $this->request->data['visit_id'];
				$this->Visit->read();
				$this->Visit->set("status", "complete");
				$this->Visit->set("clientRating", $this->request->data['clientRating']);
				$this->Visit->set("pestActivity", $this->request->data['pestActivity'] == "yes" ? 1:0);
				$this->Visit->set("clientRemarks", $this->request->data['clientRemarks']);
				$this->Visit->set("recommendationHousekeeping", $this->request->data['housekeepingRec']);
				$this->Visit->set("recommendationProofing", $this->request->data['proofingRec']);
				$this->Visit->set("recommendationStacking", $this->request->data['stackingRec']);
				$this->Visit->save();
				//if this is a routine visit, update the next service date for the client
				if ($this->Visit->field("purpose") == "routine_visit") {
					$this->Client->create();
					$this->Client->id = $this->Visit->field('client_id');
					if ($this->Client->field('nextService') == "0000-00-00") $this->Client->saveField('nextService', date("Y-m-d",time()));
					$nextDate = $this->getNextServiceDate($this->Client->field('nextService'), $this->Client->field('ServiceFrequency'));
					$this->Client->saveField('nextService', $nextDate);
					$this->Client->saveField('nextServiceScheduled', $nextDate);
				}
				//create the service report for the visit
				$this->ServiceReport->create();
				$this->ServiceReport->set("visit_id", $this->request->data['visit_id']);
				$this->ServiceReport->set("technicianSignature", $this->request->data['technicianSignature']);
				$this->Technician->id = $this->Visit->field('technician_id');
				if (isset($this->request->data['clientSignature'])) {
					$this->ServiceReport->set("clientSignature", $this->request->data['clientSignature']);
					$this->ServiceReport->set("document_id", $this->Document->generateServiceReport($this->request->data['visit_id'], $this->Visit->field('client_id'), $this->Technician->field('user_id'), $this->request->data['technicianSignature'], $this->request->data['clientSignature']));
				}
				else $this->ServiceReport->set("document_id", $this->Document->generateServiceReport($this->request->data['visit_id'], $this->Visit->field('client_id'), $this->Technician->field('user_id'), $this->request->data['technicianSignature'], null));
				if ($this->ServiceReport->save() && $this->Visit->save()) {
					$clientId = $this->Visit->field('client_id');
					//send a notification to client
					$this->Client->id = $clientId;
					$this->User->id = $this->Client->field('user_id');
					$this->Notification->newServiceReportNotification($this->User->field('email'), $this->ServiceReport->field("id"));
					//send a message to the client for when they log in					
					$this->Message->createMessage($this->Client->field('user_id'), "Service Control", "A new Service Report was created on ". date("j F Y", time()) ." and is ready for you to view.");
					//check that the visit took place in the right location
					$distance = $this->doesGPSMatch($this->request->data['visit_id']);
					if (!($distance == -1)) $this->Notification->checkServiceGPSMatch($this->Visit->field("id"), $distance);
					//create follow up visits if necessary
					$followUpVisitId = -1;
					if ($this->request->data['followUpDays'] < 100) {
						$this->Visit->create();
						$this->Visit->saveMany(array(array(
								"client_id" => $clientId,
								"technician_id" => -1,
								"purpose" => "follow_up",
								"timeRequested" => date("Y-m-d H:i:s"),
								"timeDue" => date('Y-m-d H:i:s', strtotime($date . ' + ' . $this->request->data['followUpDays'] . ' days')))));
						$followUpVisitId = $this->Visit->field('id');
					}
					$this->Visit->create();
					$this->Visit->id = $this->request->data['visit_id'];
					if ($this->Visit->field("purpose") == "sighting") {
						//update sighting
						$sighting = $this->Sighting->find("first", array("conditions"=>array("visit_id"=>$this->request->data['visit_id'])));
						$this->Sighting->id = $sighting['Sighting']['id'];
						$this->Sighting->read();
						$this->Sighting->set('correctiveAction', $this->request->data['correctiveAction']);
						$this->Sighting->save();
						//create action report
						$this->ActionLog->create();
						$this->ActionLog->saveMany(array(array(
								"followUpNumber" => 1,
								"correctiveAction" => $this->request->data['correctiveAction'],
								"nextVisitId" => $followUpVisitId,
								"service_report_id" => $this->ServiceReport->field('id'),
								"sighting_id" => $sighting['Sighting']['id'],
								"visit_id" => $this->request->data['visit_id'])));
					}
					if ($this->Visit->field("purpose") == "follow_up" || $this->Visit->field("purpose") == "action_log") {
						//get information about any previous followups
						$this->ActionLog->create();
						$lastAction = $this->ActionLog->find("first", array("conditions"=>array("nextVisitId"=>$this->request->data['visit_id'])));
						$count = $this->ActionLog->field('followUpNumber') + 1;
						$sightingId = $this->ActionLog->field('sighting_id');

						//create an action log entry linked to follow-up visit and sighting
						$this->ActionLog->create();
						$this->ActionLog->saveMany(array(array(
								"followUpNumber" => $count,
								"correctiveAction" => $this->request->data['correctiveAction'],
								"nextVisitId" => $followUpVisitId,
								"service_report_id" => $this->ServiceReport->field('id'),
								"sighting_id" => $sightingId,
								"visit_id" => $this->request->data['visit_id'])));
					}
					echo '{"result":"success"}';
				}
				else echo '{"result":"failure"}';
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}
	
	public function doesGPSMatch($visitId) {
		$this->loadModel('Client');
		$this->Visit->id = $visitId;
		$vLat = $this->Visit->field('latitude');
		$vLon = $this->Visit->field('longitude');
		$vLocm = $this->Visit->field('locationMeta');
		$this->Client->id = $this->Visit->field('client_id');
		$distance = $this->Client->distanceFromCurrent($vLat, $vLon);
		if (!($distance == -1)) {
			$locationMeta = json_decode($vLocm, true);
			if ($locationMeta['accuracy'] < 1000) {
				if ($distance > 2000) return $distance;
			}
		}
		return -1;
	}

	public function apiSaveIncompleteReport($db) {
		$this->loadModel('Users.User');
		$this->autoRender = false;
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'])) {
				$this->Visit->id = $this->request->data['visit_id'];
				$this->Visit->read();
				$this->Visit->set("status", "in progress");
				$date = date('Y-m-d H:i:s');
				$this->Visit->set("timeDue", date('Y-m-d H:i:s', strtotime($date . ' + 7 days')));
				if ($this->Visit->save()) echo '{"result":"success"}';
				else echo '{"result":"failure"}';
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}

	public function apiGetOpenVisitsAndLogs($db) {
		$this->loadModel('Users.User');
		$this->loadModel('Technician');
		$this->autoRender = false;
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'], $db)) {
				$this->Visit->unbindModel(array('hasMany'=>array('ActionLog', 'ServiceReport', 'Sighting', 'DeviceService'), 'hasAndBelongsToMany' => array('Chemical', 'Device', 'AreaSummary'), 'belongsTo' => array('Client')));
				$data = $this->Visit->find('all', array(
						'conditions' => array('Visit.client_id =' => $this->request->data['client_id'], 'Visit.status !='=>"complete")));
				echo gzencode(json_encode($data));
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}

	public function apiUpdateVisitData($db) {
		$this->loadModel('Users.User');
		$this->autoRender = false;
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'], $db)) {
				$this->Visit->id = $this->request->data['visit_id'];
				$this->Visit->read();
				if (isset($this->request->data['status'])) $this->Visit->set("status", $this->request->data['status']);
				if (isset($this->request->data['timeRequested'])) $this->Visit->set("timeRequested", $this->request->data['timeRequested']);
				if (isset($this->request->data['timeDue'])) $this->Visit->set("timeDue", $this->request->data['timeDue']);
				if (isset($this->request->data['timeStarted'])) $this->Visit->set("timeStarted", $this->request->data['timeStarted']);
				if (isset($this->request->data['technician_id'])) $this->Visit->set("technician_id", $this->request->data['technician_id']);
				if ($this->Visit->save()) echo '{"result":"success"}';
				else echo '{"result":"failure"}';
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}

	public function apiStartPremadeVisit($db) {
		$this->loadModel('Users.User');
		$this->autoRender = false;
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'], $db)) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'])) {
				$this->Visit->id = $this->request->data['visit_id'];
				$this->Visit->set("timeStarted", date("Y-m-d H:i:s"));
				$this->Visit->set("status", "in progress");
				if (isset($this->request->data['technician_id'])) $this->Visit->set("technician_id", $this->request->data['technician_id']);
				if ($this->Visit->save()) echo '{"result":"success"}';
				else echo '{"result":"failure"}';
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}
	
}
