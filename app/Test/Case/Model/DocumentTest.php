<?php
App::uses('Document', 'Model');

/**
 * Document Test Case
 *
 */
class DocumentTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.document'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Document = ClassRegistry::init('Document');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Document);

		parent::tearDown();
	}

}
