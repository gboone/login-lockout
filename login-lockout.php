<?php
/*
Plugin Name: WordPress Login Lockout
Plugin URI: http://github.com/gboone
Description: This plugin contains classes that help developers turn WordPress into a full CMS. 

The plugin includes the useful `build_cfpb_post_type()` and `build_cfpb_taxonomy()` 
functions. The first allows you to create a new custom post type by passing the 
following parameters: $name (the singular name), $plural (the, uh, plural version of
$name) and $slug. The second allows you to build a new custom taxonomy with a few 
simple parameters.

Version: 1.3.2
Author: Greg Boone, Aman Kaur, Matthew Duran
Author URI: http://github.cfpb.gov/gboone
License: Public Domain work of the Federal Government

Code complexity: 5, OK. (pdepend --summary-xml=QA/scotlandphp-summary.xml scotland.php)
Passes phpcs --standard=WordPress
*/
namespace gboone;
class LoginLockout {
	public function __construct() {

	}

	public function build() {
		add_action('wp_authenticate', array($this, 'transient_check'));
		add_action('password_reset', array( $this, 'flush_transient'), 10, 2);
	}

	public function transient_check($key) {
		global $interim_login;
		$user = get_user_by( 'slug', 'gboone' );
		$t = get_transient( $key );
		if ( $interim_login == false ) {
			$this->login_transient($_POST['log']);
		}
	}

	public function flush_transient( $user, $new_pass ) {
		$key = 'user_' . $user->ID;
		delete_transient( $key );
	}

	public function login_transient($user) {
		$user_id = get_user_by( 'slug', $user )->ID;
		$key = 'user_' . $user_id;
		$t = get_transient( 'user_' . $user_id );
		if ( intval($t) < 3 ) {
			$t = intval($t);
			$t++;
			set_transient($key, strval($t), 900);
		} elseif ( intval($t) >=3 ) {
			wp_die("{$key} have attempted to login {$t} times and are now locked out. Please use the reset password feature to unlock your account.");
		} else {
			set_transient( 'user_'. $user_id, '0', $expiration = 900 );
		}
	}
}

$L = new \gboone\LoginLockout();

add_action('plugins_loaded', array($L, 'build'));