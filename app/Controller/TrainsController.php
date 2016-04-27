<?php
App::uses('AppController', 'Controller');
/**
 * Visits Controller
 *
 * @property Visit $Visit
 * @property PaginatorComponent $Paginator
*/
class TrainsController extends AppController {


	public function apiAddConversation() {
		$this->loadModel('Conversation');
		$this->autoRender = false;
		$this->Conversation->create();
		$this->Conversation->set('sender', $this->data['sender']);
		$this->Conversation->set('message', $this->data['message']);
		$this->Conversation->set('category', "General");
		$this->Conversation->set('time', date("Y-m-d H:i:s", time() + 5));
		$this->Conversation->save();
	}
	
	public function apiGetNewConversations() {
		$this->loadModel('Conversation');
		$this->autoRender = false;
		$data = $this->Conversation->find('all', array(
										'conditions'=>array('time >' => $this->data['time']),
										'order'=>array('time'=>'ASC')));
		echo(json_encode($data));
	}
	
	public function apiSendTrainData() {
		$this->loadModel('Conversation');
		$this->loadModel('TrainStat');
		$this->autoRender = false;
		$this->Train->create();
		switch ($this->data['type']){
			case "incoming":
				$this->Train->create();
				$this->Train->set('number', $this->data['number']);
				$this->Train->set('arrival', $this->data['etaTime']);
				$this->Train->set('stage', 1);
				$this->Train->set('stageETA', $this->data['eta']);
				$this->Train->set('wagons', $this->data['wagons']);
				$this->Train->set('grade', '???');
				$this->Train->set('origin', 'Mines');
				$this->Train->set('headlineText', "- ". $this->data['number']." from mines");
				$this->Train->set('statusText', "ETA " . date("H\hi", time() + ($this->data['eta'] * 60)) . ", " . $this->data['wagons'] . " wagons");
				$this->Train->set('notificationText', "" . $this->data['number'] . " will be arriving " . $this->data['eta'] . " minutes, " . $this->data['wagons'] . " wagons");
				$this->Train->set('active', 1);
				$this->Train->save();
				$this->TrainStat->newTrain($this->data['number'], $this->data['wagons']);
				break;
			case "arrived":
				$this->Train->create();
				$data = $this->Train->find('first', array('conditions'=>array('number'=>$this->data['number'])));
				$this->Train->id = $data['Train']['id'];
				$this->Train->read();
				$this->Train->set('stage', 2);
				$this->Train->set('headlineText', "- ". $this->data['number']." in D-yard");
				$this->Train->set('statusText', "Arrived at " . date("H\hi", time()) . ", " . $this->Train->field('wagons') . " wagons");
				$this->Train->set('notificationText', "" . $this->data['number'] . " ARRIVED at " . date("H\hi", time()) . ", " . $this->Train->field('wagons') . " wagons");
				$this->Train->set('modified', date('Y-m-d H:i:s', time() + 5));
				$this->Train->save();
				$this->TrainStat->updateTrain($this->data['number'], 'timeArrived', date('Y-m-d H:i:s', time()));
				break;
			case "serviceStarted":
				$this->Train->create();
				$data = $this->Train->find('first', array('conditions'=>array('number'=>$this->data['number'])));
				$this->Train->id = $data['Train']['id'];
				$this->Train->read();
				if ($this->Train->field('stage')==3) $this->Train->set('reminderSent', 1);
				else $this->Train->set('reminderSent', 0);
				$this->Train->set('stage', 3);
				$this->Train->set('headlineText', "- ". $this->data['number']." in wagon service");
				$this->Train->set('stageETA', $this->data['eta']);
				$this->Train->set('statusText', "Updated " . date("H\hi", time()) . ", ETC at " . date("H\hi", time() + ($this->data['eta'] * 60)));
				$this->Train->set('notificationText', "" . $this->data['number'] . " will finish service in ~" . $this->data['eta'] . " minutes");
				$this->Train->set('modified', date('Y-m-d H:i:s', time() + 5));
				$this->Train->save();
				$this->TrainStat->updateTrain($this->data['number'], 'timeWagonServiceStarts', date('Y-m-d H:i:s', time()));
				break;
			case "serviceFinished":
				$this->Train->create();
				$data = $this->Train->find('first', array('conditions'=>array('number'=>$this->data['number'])));
				$this->Train->id = $data['Train']['id'];
				$this->Train->read();
				$this->Train->set('stage', 5);
				$this->Train->set('headlineText', "- ". $this->data['number']." waiting for shunt loco");
				$this->Train->set('statusText', "Service complete, Waiting in arrivals yard for shunt loco");
				$this->Train->set('notificationText', "" . $this->data['number'] . " serviced finished. Waiting for loco.");
				$this->Train->set('modified', date('Y-m-d H:i:s', time() + 5));
				$this->Train->save();
				$this->TrainStat->updateTrain($this->data['number'], 'timeWagonServiceFinished', date('Y-m-d H:i:s', time()));
				break;
			case "shuntCoupled":
				$this->Train->create();
				$data = $this->Train->find('first', array('conditions'=>array('number'=>$this->data['number'])));
				$this->Train->id = $data['Train']['id'];
				$this->Train->read();
				$this->Train->set('stage', 6);
				$this->Train->set('active', 0);
				$this->Train->save();
				$this->TrainStat->updateTrain($this->data['number'], 'timeWagonsCoupledToShunt', date('Y-m-d H:i:s', time()));
				break;
		}
	}
	
	public function apiGetTrainUpdates() {
		$this->loadModel('Conversation');
		$this->autoRender = false;
		$data = $this->Train->find('all', array('conditions'=>array('active'=>1), 'order'=>array('modified'=>'ASC')));
		foreach ($data as $train) {
			if ($train['Train']['reminderSent'] == 0 && (strtotime($train['Train']['modified']) + ($train['Train']['stageETA'] * 60) - (30*60) - time() < 0)) {
				$this->Train->create();
				$this->Train->id = $train['Train']['id'];
				$this->Train->read();
				$this->Train->set('notificationText', "WAGON SERVICE - UPDATE ETA FOR ".$this->Train->field('number'));
				$this->Train->set('modified', date('Y-m-d H:i:s', time() + 5));
				$this->Train->set('reminderSent', 1);
				$this->Train->save();
			}
		}
		echo(gzencode(json_encode($data)));
	}
	
	public function apiGetTrainNotifications() {
		$this->loadModel('Conversation');
		$this->autoRender = false;
		$data = $this->Train->find('all', array(
										'conditions'=>array('modified >' => $this->data['time']),
										'order'=>array('modified'=>'ASC')));
		echo(gzencode(json_encode($data)));
	}
	
}
