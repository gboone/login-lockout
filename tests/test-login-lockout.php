<?php
class LoginLockoutTest extends PHPUnit_Framework_TestCase {
	function setUp() {
		\WP_Mock::setUp();
		parent::setUp();
	}

	function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}

	function testLoginTransientExpectsTransientSet() {
		$L = new \gboone\LoginLockout();
		\WP_Mock::wpFunction('set_transient', array('times' => 1));

		// Act
		$L->login_transient(1, 0);
		// Test fails if set_transient isn't called
	}

}
?>