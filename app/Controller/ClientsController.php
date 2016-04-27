<?php
App::uses('AppController', 'Controller');
/**
 * Clients Controller
 *
 */
class ClientsController extends AppController {

	
	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'RequestHandler');
	
	public function isAuthorized($user) {
		return parent::isAuthorized($user);
	}
	
	/**
	 * index method
	 *
	 * @return void
	*/
	public function index() {
		$this->Paginator->settings = array(
				'conditions' => array('Client.archived =' => '0'),
		);
		$this->Client->recursive = 0;
		$this->set('clients', $this->Paginator->paginate());
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
		$this->loadModel('User');
		$this->Client->id = $id;
		$data = $this->Client->find('first', array('conditions' => array('Client.id' => $id)));
		if ($state == "true") $bool = 1;
		else $bool = 0;
		$this->Client->saveField('active', $bool);
		$this->User->id = $data['Client']['user_id'];
		$this->User->saveField('active', $bool);
		echo $bool;
	}
	
	/**
	 * gives a json list of clients orderd by location
	 *
	 * @return void
	 */
	public function apiListingByLocation($db) {
		$this->loadModel('Users.User');
		$this->autoRender = false;
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'], $db)) {
				$this->Client->unbindModel(array('hasMany'=>array('Device', 'Visit'), 'hasAndBelongsToMany' => array('Chemical', 'Policy', 'Document'), 'belongsTo' => array('User', 'Company')));
				$data = $this->Client->find('all', array(
						'fields' => array('Client.id', 'Client.name', 'Client.latitude', 'Client.longitude'),
						'conditions' => array('Client.archived =' => '0', 'Client.active =' => '1')));
				$clientData;
				$count = 0;
				foreach ($data as $client) {
					$this->Client->id = $client['Client']['id'];
					$clientData[$count]['id'] = $client['Client']['id'];
					$clientData[$count]['name'] = $client['Client']['name'];
					$clientData[$count]['distance'] = round($this->Client->distanceFrom($this->request->data['latitude'],$this->request->data['longitude'],$client['Client']['latitude'],$client['Client']['longitude']) / 1000, 2);
					$count++;
				}
				//sort the results by distance (lowest to highest)
				usort($clientData, function($a, $b) {
    				return $a['distance'] - $b['distance'];});
				//finally we only want to return the first 25 results
				$clientData = array_slice($clientData, 0, 25);
				echo(json_encode($clientData));
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}
	
	/**
	 * gives a json list of clients orderd by location
	 *
	 * @return void
	 */
	public function apiGetClientByCode($db) {
		$this->loadModel('Users.User');
		$this->autoRender = false;
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'],$db)) {
				$this->Client->unbindModel(array('hasMany'=>array('Device', 'Visit'), 'hasAndBelongsToMany' => array('Chemical', 'Policy', 'Document'), 'belongsTo' => array('Document', 'User', 'Company')));
				$data = $this->Client->find('first', array(
						'fields' => array('Client.id', 'Client.name', 'Client.latitude', 'Client.longitude', 'Client.code'),
						'conditions' => array('Client.archived =' => '0', 'Client.active =' => '1', 'Client.code =' => $this->request->data['code'])));
				if (sizeof($data) == 0) echo "{'success':'false'}";
				else {
					$result['name'] = $data['Client']['name'];
					$result['id'] = $data['Client']['id'];
					$result['distance'] = round($this->Client->distanceFrom($this->request->data['latitude'],$this->request->data['longitude'],$data['Client']['latitude'],$data['Client']['longitude']) / 1000, 2);
					$result['success'] = 'true';
					echo(json_encode($result));
				}
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}
	
	/**
	 * gives a json list of clients orderd by location
	 *
	 * @return void
	 */
	public function apiGetClientData($db) {
		$this->loadModel('Users.User');
		$this->loadModel('Device');
		$this->loadModel('Visit');
		$this->loadModel('Chemical');
		$this->autoRender = false;
		if (isset($this->request->data['userid']) && isset($this->request->data['apiToken'])) {
			if ($this->User->apiIsAuthorised($this->request->data['userid'], $this->request->data['apiToken'], $db)) {
				//unbind data we don't need
				$this->Client->unbindModel(array('hasAndBelongsToMany' => array('Policy', 'Document'), 'belongsTo' => array('User', 'Company')));
				$this->Client->Behaviors->attach('Containable');
				$data = $this->Client->find('first', array('recursive'=>2,
						'contain'=>array('Visit'=>array('conditions' => array('Visit.status !=' => 'complete')),
										'Device'=>array('Location', 'DeviceType'),
										'Chemical'),
						'conditions' => array('Client.archived =' => '0', 'Client.active =' => '1', 'Client.id =' => $this->request->data['clientId'])));
				if (sizeof($data) == 0) echo "{'success':'false'}";
				else echo(gzencode(json_encode($data)));
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}
	
	/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->Client->id = $id;
		if (!$this->Client->exists()) {
			throw new NotFoundException(__('Invalid client'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Client->saveField('archived', 1)) {
			$this->Client->saveField('active', 0);
			$this->Session->setFlash(__('The client has been deleted. They will no longer be able to access their file and documents and will receive an error message if they attempt to log in.'));
		} else {
			$this->Session->setFlash(__('The client could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	
	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		$this->loadModel('User');
		$this->loadModel('Document');
		$this->loadModel('ClientsDocument');
		$numberClients = $this->Client->find('count', array("conditions"=>array("Client.archived"=>0)));
		$maxClientsMessage = 'You have reached the limit of clients you can add under your current package. Please contact your service provider to purchase additional client spaces or delete some clients to make room for more.';
		if ($this->globalSettings['max_clients'] <= $numberClients || ($this->request->is('post') && $this->globalSettings['max_clients'] <= $numberClients + 1))
			$this->Session->setFlash($maxClientsMessage, 'default', array('class' => 'warning'));
		if ($this->request->is('post')) {
			if ($this->globalSettings['max_clients'] <= $numberClients) return $this->redirect(array('action' => 'index'));
			else {
				if ($this->Document->saveMany(array(array(
							"name" => $this->request->data['Client']['name'] . ' Service Agreement',
							"filename" => $this->request->data['Client']['filename'],
							"file_type" => $this->request->data['Client']['file_type'],
							"document_category_id" => $this->request->data['Client']['document_category_id'],
							"mime" => $this->request->data['Client']['mime'],
							"order" => -1,
							"size" => $this->request->data['Client']['size'],
							"user_id" => $this->request->data['Client']['admin_user_id'],
							"policyDocument" => 1,
							"meta" => $this->request->data['Client']['meta'])))) {
						$docId = $this->Document->getId();
						$filename = Configure::read('file_upload_prefix') . $this->request->data['Client']['filename'];
						rename("upload_tmp/$filename", "files/$filename");
						//create the user
						$this->User->create();
						if ($this->User->saveField("username", $this->request->data['Client']['username']) &&
						$this->User->saveField("role", "client") &&
						$this->User->saveField("active", $this->request->data['Client']['active']) &&
						$this->User->saveField("email_verified", 1) &&
						$this->User->saveField("email", $this->request->data['Client']['email']) &&
						$this->User->saveField("password", Security::hash($this->request->data['Client']['password'], null, true))) {
							$this->request->data['Client']['user_id'] = $this->User->getId();
							$this->request->data['Client']['nextServiceScheduled'] = $this->request->data['Client']['nextService'];
							$this->Client->create();
							if ($this->Client->save($this->request->data)) {
								$clientId = $this->Client->getId();
								$this->ClientsDocument->create();
								$this->ClientsDocument->saveField("client_id", $clientId);
								$this->ClientsDocument->saveField("document_id", $docId);
								$this->Session->setFlash(__('The client has been created.'));
								return $this->redirect(array('action' => 'index'));
							} else {
								$this->Session->setFlash(__('The client could not be created. Please, try again.'));
							}
						}
				}
			}
		}
		$policies = $this->Client->Policy->find('list', array('conditions' => array('Policy.archived' => 0)));
		$commonChemicals = $this->Client->Chemical->find('list', array('fields'=>array('id'), 'conditions' => array('common' => 1)));
		$chemicals = $this->Client->Chemical->find('list');
		$companies = $this->Client->Company->find('list', array('conditions'=>array('Company.id >'=>'0')));
		$documents = $this->Client->Document->find('list', array('conditions' => array('Document.visibleToAll' => 0, 'Document.policyDocument' => 0)));
		$this->set(compact('companies', 'documents', 'chemicals', 'policies', 'commonChemicals'));
	}
	
	
	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->loadModel('User');
		$this->loadModel('Document');
		$newDoc = false;
		if (!$this->Client->exists($id)) {
			throw new NotFoundException(__('Invalid client'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if (!$this->User->exists($this->request->data['Client']['user_id'])) {
				throw new NotFoundException(__('Client has not been created properly and has no user account. Cannot edit.'));
			}
			$this->request->data['Client']['nextServiceScheduled'] = $this->request->data['Client']['nextService'];
			$this->User->id = $this->request->data['Client']['user_id'];
			$this->User->saveField("username", $this->request->data['Client']['username']);
			$this->User->saveField("active", $this->request->data['Client']['active']);
			$this->User->id = $this->request->data['Client']['user_id'];
			$this->User->saveField("username", $this->request->data['Client']['username']);
			$this->User->saveField("active", $this->request->data['Client']['active']);
			$this->User->saveField("email", $this->request->data['Client']['email']);
			if (strlen($this->request->data['Client']['password']) > 0) $this->User->saveField("password", Security::hash($this->request->data['Client']['password'], null, true));
			if ($this->Client->save($this->request->data)) {
				$this->Session->setFlash(__('The client has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The client could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Client.' . $this->Client->primaryKey => $id));
			$this->request->data = $this->Client->find('first', $options);
		}
		$policies = $this->Client->Policy->find('list', array('conditions' => array('Policy.archived' => 0)));
		$chemicals = $this->Client->Chemical->find('list');
		$companies = $this->Client->Company->find('list', array('conditions'=>array('Company.id >'=>'0')));
		$options['joins'] = array(array('table' => 'clients_documents', 'alias' => 'Client', 'type'=>'LEFT', 'conditions' => 'Client.document_id = Document.id'));
		$options['conditions'] = array('OR'=>array(
												'Document.name LIKE' => '%Service Agreement%', 
												'Document.document_category_id' => 9),
									  'Client.client_id' => $id,
									  'Document.archived' => 0);
		$importantDocuments = $this->Document->find('all', $options);																
		$documents = $this->Client->Document->find('list', array('conditions' => array('Document.visibleToAll' => 0, 'Document.policyDocument' => 0)));
		$this->set(compact('companies', 'documents', 'chemicals', 'policies', 'importantDocuments'));
	}
	
	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		if (!$this->Client->exists($id)) {
			throw new NotFoundException(__('Invalid client'));
		}
		$options = array('conditions' => array('Client.' . $this->Client->primaryKey => $id));
		$this->Client->recursive = 2;
		$this->set('client', $this->Client->find('first', $options));
	}
	
	/**
	 * linkMap method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function linkMap() {
		$this->loadModel('Document');
		$this->loadModel('ClientsDocument');
		if ($this->request->is('post')) {
			$filename = Configure::read('file_upload_prefix') . $this->request->data['Document']['filename'];
			rename("upload_tmp/$filename", "files/$filename");				
			$this->Document->create();
			if ($this->Document->save($this->request->data)) {
				$this->ClientsDocument->id = $this->Document->id;
				$this->ClientsDocument->saveField('specialType', 'map');
				$this->Session->setFlash(__('The document has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The document could not be saved. Please, try again.'));
			}
		}
		$this->set(compact('users'));
	}
	
	public function apiDownloadMap($clientid, $uid, $apit, $db) {
		$this->loadModel('Users.User');
		$this->loadModel('ClientsDocument');
		$this->loadModel('Document');
		$this->autoRender = false;
		if (isset($uid) && isset($apit)) {
			if ($this->User->apiIsAuthorised($uid, $apit)) {
				$data = $this->ClientsDocument->find('first', array('conditions'=>array('specialType'=>'map', 'Client.id'=>$clientid)));
				if (isset($data['ClientsDocument']['document_id'])) {
					$this->Document->id = $data['ClientsDocument']['document_id'];
					if (!$this->Document->exists()) {
						throw new NotFoundException(__('Invalid document'));
					}
					$path = 'files/' . $db . '/' . $this->Document->field('filename');
					header('Content-type: ' . $this->Document->field('mime'));
					header('Content-Disposition: attachment; filename="' . substr($this->Document->field('filename'), 1+ strpos($this->Document->field('filename'), "-")) . '"');
					readfile($path);
				}
			}
			else echo "Not authorised";
		}
		else echo "Not authorised";
	}
	
	/**
	 * Ajax call to get clients with names similar to string
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function getClientList($partialName) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->loadModel('Client');
		$this->Paginator->settings = array(
				'conditions' => array('Client.name LIKE' => '%' . $partialName . '%'),
				'limit' => '25'
		);
		$data = $this->paginate('Client');
		echo(json_encode($data));
	}
	
	public function checkUniqueness($field, $value) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$conditions = array(
				$field => $value
		);
		if ($this->Client->hasAny($conditions)){
			echo "true";
		}
		else echo "false";
	}
	
	
	/**
	 * Ajax call to get clients locations of first x clients
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function getClientLocations($limit = 50) {
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->loadModel('Client');
		$this->Client->unbindModel(array('hasMany'=>array('Device', 'Visit'), 'hasAndBelongsToMany' => array('Chemical', 'Policy', 'Document')));
		$this->Paginator->settings = array(
				'fields' => array('Client.latitude', 'Client.longitude', 'Client.id'),
				'conditions' => array('Client.latitude <>' => ''),
				'limit' => $limit
		);
		$data = $this->paginate('Client');
		echo(json_encode($data));
	}

	public function downloadBarcodes($clientId) {
		if ($this->request->is('post')) {
			$this->loadModel('Report');
			$ids = array();
			$keys = array_keys($this->request->data);
			foreach($keys as $key) {
				if ($this->request->data[$key] == 1) array_push($ids, substr($key, 6));
			}
			if ($this->request->data['Client']['OutputFormat'] == 0) $this->Report->generateBarcodeDocFromIds($clientId, $ids);
			else $this->Report->generate70x40BarcodeDocFromIds($clientId, $ids);
		}
		$this->loadModel("Device");
		$this->Device->unbindModel(array('belongsTo'=>array('Client'), 'hasMany'=>array('DevicesVisit', 'DeviceService')));
		$barcodes = $this->Device->find('all', array("conditions"=>array("client_id"=>$clientId, "Device.active"=>"1"), 'order'=>array('created'=>'asc')));
		$this->set("devices", $barcodes);
		$this->set("clientId", $clientId);
	}
	
	public function downloadAllBarcodes($clientId) {
		$this->autoRender = false;
		$this->loadModel('Report');
		$this->Report->generateBarcodeDoc($clientId);
	}
	
}
