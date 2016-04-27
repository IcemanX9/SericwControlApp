<?php
App::uses('AppModel', 'Model');
/**
 * Document Notification
 *
*/
class Notification extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';
	public $siteUrl = "http://www.servicecontrol.co.za/servicecontrol/";
	public $actsAs = array( 'AuditLog.Auditable' ); //makes this model auditable
	
	public function runAllNotifications($ignoreLastRun = false) {
		$notifications = $this->find('all', array('conditions'=>array('active'=>1, 'runFrequency > 0')));
		foreach ($notifications as $notification) $this->runNotification($notification['Notification']['id'], $ignoreLastRun);
	}

	public function runNotification($id, $ignoreLastRun) {
		$this->id = $id;
		$lastRun = strtotime($this->field('lastRun'));
		//only run if it hasn't been run recently
		if ($ignoreLastRun || $lastRun + $this->field('runFrequency') > time()) {
			$function = $this->field("function");
			$this->saveField("lastRun", date("Y-m-d H:i:s", time()));
			$this->$function($this->field("email"));
		}
	}
	
	//this function can be used to stick standard text and templates into the e-mail message
	public function formatMessage($message) {
		$header = "This is an automated message from Service Control Manager notification system. Please do not reply to this e-mail. <br><br>";
		$footer = "<br>Regards,<br>The Service Control Manager Robots";
		return $header . $message . $footer;
	}
	
	//this function can be used to stick standard text and templates into the e-mail message for clients
	public function formatClientMessage($message) {
		$header = "This is an automated message from Service Control Manager notification system. Please do not reply to this e-mail. <br><br>";
		$footer = "<br>Regards,<br>The Service Control Manager Robots";
		return $header . $message . $footer;
	}
	
	public function sendMail($to, $subject, $message) {
		App::import('Vendor', 'PHPMailer/PHPMailerAutoload');
		$mail = new PHPMailer;
		
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.servicecontrol.co.za';  		  // Specify main and backup server
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'jonathanh@servicecontrol.co.za';   // SMTP username
		$mail->Password = 'P@ss54321';                        // SMTP password
		
		$mail->From = 'no-reply@servicecontrol.co.za';
		$mail->FromName = 'Service Control Manager';
		
		$to = str_replace(" ", "", $to);
		$addresses = explode(',', $to);
		foreach ($addresses as $address) $mail->addAddress($address);	// Add recipients
		
		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->isHTML(true);                                  // Set email format to HTML
		
		$mail->Subject = $subject;
		$mail->Body    = $message;
		$mail->AltBody = 'This e-mail cannot be read by non-HTML mail clients';
		
		if(!$mail->send()) {
			//there is an error
			exit;
		}
	}
	
	public function dttod($datetime) {
		return date("Y-m-d", strtotime($datetime));
	}
	
	public function transcribe($oldText) {
		switch ($oldText) {
			case "routine_visit":
				return "Routine Visit";
				break;
			case "action_log":
				return "Follow up visit";
				break;
			case "follow_up":
				return "Follow up visit";
				break;
			case "sighting":
				return "Sighting";
				break;
			case "installation":
				return "Installation";
				break;
			default:
				return "Error Transcribing";
		}
	}
	
	public function checkOverdueServices($email) {
		App::import('Model', 'Visit');
		$visitModel = new Visit();
 		$overdueVisits = $visitModel->find('all', array("conditions"=>array("Visit.status != 'complete'", "Visit.timeDue < '" . date('Y-m-d H:i:s') . "'"), "order"=>array("Visit.client_id"=>"asc")));
 		$message = "Below is a list of all services which are currently overdue. Please deal with them by manually changing their status, changing the due date to a future date, or arranging a technician for a service.<br><br>";
 		$message = $message . "<a href='".$this->siteUrl."Visits/listOpenVisits/1'>Click here to manage the services in Service Control Manager</a><br><br>";
		foreach ($overdueVisits as $visit) {
			$message = $message . "<b>Client:</b> " . $visit['Client']['name'] . ", <b>Visit purpose: </b>".$this->transcribe($visit['Visit']['purpose'])." , <b>Visit requested:</b> "
					.$this->dttod($visit['Visit']['timeRequested']).", <b>Visit due:</b> ". $this->dttod($visit['Visit']['timeDue']) . "<br>"; 
		}
		if (sizeof($overdueVisits) > 0) {
			$message = $this->formatMessage($message);
			$this->sendMail($email, "**Service Control Notification** Overdue Services", $message);
		}
	}
	
	public function checkProblemDevices($email) {
		App::import('Model', 'Device');
		$deviceModel = new Device();
 		$problemDevices = $deviceModel->find('all', array("conditions"=>array("Device.active = 1",
 																				"Device.archived = 0",
 																				"OR"=>array(array('Device.damaged = 1'),array("Device.missing = 1")))));
 		$message = "Below is a list of all devices which are damaged, missing or obscured. Please deal with them by manually editing their details or by arranging replacements by a technician.<br><br>";
 		$message = $message . "<a href='".$this->siteUrl."Devices/index/filterdamaged'>Click here to manage the devices in Service Control Manager</a><br><br>";
		foreach ($problemDevices as $device) {
			$problemText="";
			if ($device['Device']['damaged'] == 1) $problemText = $problemText . "Damaged " . $device['Device']['damagedDate'] . "; ";
			if ($device['Device']['obscured'] == 1) $problemText = $problemText . "Obscured " . $device['Device']['obscuredDate'] . "; ";
			if ($device['Device']['missing'] == 1) $problemText = $problemText . "Missing " . $device['Device']['missingDate'];
			$message = $message . "<a href='".$this->siteUrl."Devices/edit/". $device['Device']['id'] . "'><b>Client:</b> " . $device['Client']['name'] . ", <b>Device location: </b>" 
					.$device['Device']['location'].", Device Type: " . $device['Device']['type'] . ", " . $problemText . "</a><br>";
		}
		if (sizeof($problemDevices) > 0) {
			$message = $this->formatMessage($message);
			$this->sendMail($email, "**Service Control Notification** Damaged or missing devices", $message);
		}
	}
	
	public function checkEFKGlobeReplacement($email) {
		//assumes 365 day globe replacement schedule
		App::import('Model', 'Device');
		$deviceModel = new Device();
		$problemDevices = $deviceModel->find('all', array("conditions"=>array("Device.active = 1",
				"Device.archived = 0",
				"Device.type"=>"EFK (Fly Trap)",
				"Device.lastSpecialServiceDate < '" . date('Y-m-d', time() - (365 * 24 * 60 * 60)) . "'")));
		$message = "Below is a list of all EFK devices which need to have their globes replaced because they have not been serviced in a year.<br><br>";
		$message = $message . "<a href='".$this->siteUrl."Devices/index/'>Click here to list all devices in Service Control Manager</a><br><br>";
		foreach ($problemDevices as $device) {
			$problemText="globe needs replacement";
			$message = $message . "<a href='".$this->siteUrl."Devices/edit/". $device['Device']['id'] . "'><b>Client:</b> " . $device['Client']['name'] . ", <b>Device location: </b>"
					.$device['Device']['location'].", Device Type: " . $device['Device']['type'] . ", " . $problemText . "</a><br>";
		}
		if (sizeof($problemDevices) > 0) {
			$message = $this->formatMessage($message);
			$this->sendMail($email, "**Service Control Notification** EFK Globes need replacement", $message);
		}
	}
	
	public function checkTechnicianCertificates($email){
		//assumes 365 day globe replacement schedule
		App::import('Model', 'Technician');
		$techModel = new Technician();
		$problemTechs = $techModel->find('all', array("conditions"=>array("Technician.active = 1",
																			"Technician.archived = 0",
																			"Technician.documentExpires < '" . date('Y-m-d', time()) . "'")));
		$message = "Below is a list of all technicians whose certification document has expired. Fix this problem by editing the technician and changing the Document Expires date and, if necessary, uploading a new certifcation document.<br><br>";
		$message = $message . "<a href='".$this->siteUrl."Technicians/index/'>Click here to list all technicians in Service Control Manager</a><br><br>";
		foreach ($problemTechs as $tech) {
			$problemText="Certification document expired on " . $tech['Technician']['documentExpires'];
			$message = $message . "<a href='".$this->siteUrl."Technicians/edit/". $tech['Technician']['id'] . "'><b>Name:</b> " . $tech['Technician']['name'] . ", " . $problemText . "</a><br>";
		}
		if (sizeof($problemTechs) > 0) {
			$message = $this->formatMessage($message);
			$this->sendMail($email, "**Service Control Notification** Technician Documents expired", $message);
		}
	}
	
	public function checkServiceGPSMatch($visitId, $distance) {
		$notification = $this->find('first', array("conditions"=>array("function"=>"checkServiceGPSMatch")));
		if ($notification['Notification']['active'] == 1) {
			App::import('Model', 'Visit');
			$visitModel = new Visit();
			$visit = $visitModel->find('first', array("conditions"=>array("Visit.id"=>$visitId)));
			$message = "A service was logged by " . $visit['Technician']['name'] . " during a service for " . $visit['Client']['name'] . " at " . date("Y-m-d H:i:s", strtotime($visit['Visit']['modified']))
						. ", but their device suggested that they were " . round($distance/1000,1) . "km distance from the client premises. <br>";
			$message = $this->formatMessage($message);
			$this->sendMail($notification['Notification']['email'], "**Service Control Notification** Technician Visit Location Mismatch!", $message);
		}
	}
	
	public function newServiceReportNotification($email, $serviceReportId) {
		$notification = $this->find('first', array("conditions"=>array("function"=>"newServiceReportNotification")));
		if ($notification['Notification']['active'] == 1) {
			App::import('Model', 'ServiceReport');
			$sr = new ServiceReport();
			$sr->id = $serviceReportId;
			$link = $this->siteUrl."Documents/download/" . $sr->field('document_id');
			$message = "A new Service Report has been generated and is ready for you to download.<br><br><a href='$link'>Click here to download.</a><br>";
			$message = $this->formatClientMessage($message);
			$this->sendMail($email, "**Service Control Notification** New Service Report Available", $message);
		}
	}
}