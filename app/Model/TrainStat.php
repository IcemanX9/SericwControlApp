<?php
App::uses('AppModel', 'Model');
/**
 * Bug Model
 *
 * @property User $User
*/
class TrainStat extends AppModel {

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public function newTrain($number, $wagons) {
		$this->create();
		$this->set('number', $number);
		$this->set('wagons', $wagons);
		$this->set('timeArrivalLogged', date('Y-m-d H:i:s', time()));
		$this->save();
	}
	
	public function updateTrain($number, $field, $value) {
		$this->create();
		$data = $this->find('first', array('conditions'=>array('number'=>$number)));
		$this->id = $data['TrainStat']['id'];
		$this->read();
		$this->set($field, $value);
		$this->save();
	}

}