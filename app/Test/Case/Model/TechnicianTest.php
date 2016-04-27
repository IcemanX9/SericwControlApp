<?php
App::uses('Technician', 'Model');

/**
 * Technician Test Case
 *
 */
class TechnicianTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.technician',
		'app.document'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Technician = ClassRegistry::init('Technician');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Technician);

		parent::tearDown();
	}

}
