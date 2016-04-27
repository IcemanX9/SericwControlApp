<?php
App::uses('AppController', 'Controller');
/**
 * Devices Controller
 *
 * @property Device $Device
 * @property PaginatorComponent $Paginator
*/
class DevicesController extends AppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');

	public function isAuthorized($user) {
		$user = $this->Auth->user();
		// Allows only users role who are logged in to access resources by default
		if ($this->action=='apiGetClientDevices') {
			return true;
		}
		else return parent::isAuthorized($user);
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->Paginator->settings = array(
				'conditions' => array('Device.archived =' => '0'),
		);
		if (isset($this->request->params['pass'][0])){
			if ($this->request->params['pass'][0] == 'filterdamaged') $this->Paginator->settings = array(
					'conditions' => array('Device.archived' => '0', "OR"=>array(array('Device.damaged = 1'),array("Device.missing = 1"))),
					'order' => array('Device.modified' => 'desc')
			);
		}
		else {
			$this->Paginator->settings = array(
					'order' => array('Device.modified' => 'desc'));
		}
		$this->Device->recursive = 0;
		$this->set('devices', $this->Paginator->paginate());
	}


	/**
	 * gives a json list of clients orderd by location
	 *
	 * @return void
	 */
	public function apiGetClientDevices($db = 'default') {
		$this->loadModel("Client");
		$this->Client->setDataSource($db);
		$this->Device->setDataSource($db);
		$this->autoRender = false;
		$this->loadModel('Users.User');
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'])) {
				$data = $this->Device->find('all', array('contain' => array('Location'),
						'fields' => array('Device.id', 'Location.name', 'DeviceType.name', 'DeviceType.screen', 'Device.installed', 'Device.missing', 'Device.damaged'),
						'conditions' => array('Device.archived =' => '0', 'Device.active =' => '1', 'Client.id = 27' => $this->request->data['clientId'])));
				for ($i=0;$i!=sizeof($data); $i++) {
					$data[$i]['Device']['location'] = $data[$i]['Location']['name'];
					$data[$i]['Device']['screenType'] = $data[$i]['DeviceType']['screen'];
					$data[$i]['Device']['type'] = $data[$i]['DeviceType']['name'];
				}
				echo(json_encode($data));
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}


	public function getBarcode($id = null) {
		$this->autoRender = false;
		header('Content-type: image/png');
		header("Cache-Control: no-cache");
		header('Content-Disposition: attachment; filename="barcode-'.$id.'.png"');
		$this->Device->getBarcodeImage($id);
	}

	/**
	 * toggle active method ajax call
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function toggleActive($id, $state) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->Device->id = $id;
		if ($state == "true") $bool = 1;
		else $bool = 0;
		$this->Device->saveField('active', $bool);
		echo $bool;
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Device->exists($id)) {
			throw new NotFoundException(__('Invalid device'));
		}
		$options = array('conditions' => array('Device.' . $this->Device->primaryKey => $id));
		$this->set('device', $this->Device->find('first', $options));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Device->create();
			if ($this->request->data['Device']['device_type_id'] == 1) $this->request->data['Device']['daysBetweenSpecialService'] = 365; //for EFK fly trap
			if ($this->Device->save($this->request->data)) {
				$this->Session->setFlash(__('The device has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The device could not be saved. Please, try again.'));
			}
		}
		$clients = $this->Device->Client->find('list');
		$locations = $this->Device->Location->find('list', array("order"=>array("order"=>"asc")));
		$deviceTypes = $this->Device->DeviceType->find('list', array("order"=>array("order"=>"asc")));
		$this->set(compact('locations', 'clients', 'deviceTypes'));
	}

	/**
	 * add multiple method - for selecting client and number of devices to add
	 *
	 * @return void
	 */
	public function addMultiple() {
		$this->loadModel("DeviceType");
		if ($this->request->is('post')) {
			if ($this->request->data['Device']['automaticCreation']) {
				$this->DeviceType->create();
				$this->DeviceType->id = $this->request->data['Device']['device_type_id'];
				$deviceName = $this->DeviceType->field('name');
				for ($i=0;$i!=$this->request->data['Device']['number_devices'];$i++) {
					$this->Device->create();
					$this->Device->set('lastChecked', $this->request->data['Device']['lastChecked']);
					$this->Device->set('client_id', $this->request->data['Device']['Client']);
					$this->Device->set('device_type_id', $this->request->data['Device']['device_type_id']);
					$this->Device->set('label', $deviceName . ' - ' . ($i + $this->request->data['Device']['labelNumberingStart']));
					$this->Device->set('location_id', $this->request->data['Device']['location']);					
					$this->Device->save();
				}
				$this->Session->setFlash(__('The devices have been created.'));
				return $this->redirect(array('action' => 'index'));
			}
			else return $this->redirect(array('action' => 'addMultipleDetails', $this->request->data['Device']['Client'], $this->request->data['Device']['number_devices']));
		}
		$locations = $this->Device->Location->find('list', array("order"=>array("order"=>"asc")));
		$deviceTypes = $this->Device->DeviceType->find('list', array("order"=>array("order"=>"asc")));
		$this->set(compact('locations', 'deviceTypes'));
	}
	
	/**
	 * add multiple method - for selecting client and number of devices to add
	 *
	 * @return void
	 */
	public function addMultipleDetails($clientId, $numberDevices) {
		if ($this->request->is('post')) {
			for ($i=0;$i!=$this->request->data['Device']['numberDevices'];$i++) {
				$this->Device->create();
				$this->Device->set('lastChecked', $this->request->data['Device']['lastChecked']);
				$this->Device->set('client_id', $this->request->data['Device']['client_id']);
				$this->Device->set('device_type_id', $this->request->data['Device']['device_type_id'.$i]);
				$this->Device->set('label', $this->request->data['Device']['label'.$i]);
				$this->Device->set('location_id', $this->request->data['Device']['location'.$i]);
				$this->Device->save();
			}
			$this->Session->setFlash(__('The devices have been created.'));
			return $this->redirect(array('action' => 'index'));
		}
		$locations = $this->Device->Location->find('list', array("order"=>array("order"=>"asc")));
		$deviceTypes = $this->Device->DeviceType->find('list', array("order"=>array("order"=>"asc")));
		$this->set(compact('locations', 'deviceTypes'));
		$this->set('clientId', $clientId);
		$this->set('numberDevices', $numberDevices);	
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		if (!$this->Device->exists($id)) {
			throw new NotFoundException(__('Invalid device'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->request->data['Device']['device_type_id'] == 1) $this->request->data['Device']['daysBetweenSpecialService'] = 365; //for EFK Fly trap
			else $this->request->data['Device']['daysBetweenSpecialService'] = null;
			if ($this->Device->save($this->request->data)) {
				$this->Session->setFlash(__('The device has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The device could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Device.' . $this->Device->primaryKey => $id));
			$this->request->data = $this->Device->find('first', $options);
		}
		$clients = $this->Device->Client->find('list');
		$locations = $this->Device->Location->find('list', array("order"=>array("order"=>"asc")));
		$deviceTypes = $this->Device->DeviceType->find('list', array("order"=>array("order"=>"asc")));
		$this->set(compact('locations', 'clients', 'deviceTypes'));
	}

	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		/**
		 * no longer possible to delete / archive devices
		 */
		/*$this->Device->id = $id;
		if (!$this->Device->exists()) {
			throw new NotFoundException(__('Invalid device'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Device->saveField('archived', 1)) {
			$this->Device->saveField('active', 0);
			$this->Session->setFlash(__('The device has been deleted.'));
		} else {
			$this->Session->setFlash(__('The device could not be deleted. Please, try again.'));
		}*/
		$this->Session->setFlash(__('You are not able to delete devices.'));
		return $this->redirect(array('action' => 'index'));
	}
}
