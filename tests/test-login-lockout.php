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
		$user_obj = new StdClass();
		$user_obj->ID = 1;
		$L = new \gboone\LoginLockout();
		\WP_Mock::wpFunction('set_transient', array('times' => 1));
		\WP_Mock::wpPassthruFunction('wp_die');
		$user_id = \WP_Mock::wpFunction('get_user_by', array(
			'times' => 1, 
			'args' => array('slug', 'user'),
			'return' => $user_obj,
			)
		);
		\WP_Mock::wpFunction('get_transient', array(
			'times' => 1, 
			// 'args' => array('user_' . $user_id),
			'return' => false)
		);
		// Act
		$L->login_transient('user');
		// Test fails if set_transient isn't called
	}

}
?>