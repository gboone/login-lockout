<?php
/*
Plugin Name: WordPress Login Lockout
Plugin URI: http://github.com/gboone
Description: A plugin to limit login attempts at the user level

Other plugins have attempted to lock out users based on a combination of user 
name and IP address. This plugin attempts to lock out users based solely on how 
many times a user attempts to log in to the system.

Version: 1.0
Author: Greg Boone
Author URI: http://github.cfpb.gov/gboone
License: Public Domain work of the Federal Government

*/
namespace gboone;
class LoginLockout {

	public function build() {
		add_action('wp_authenticate', array($this, 'login_check'));
		add_action('password_reset', array( $this, 'flush_transient'), 10, 2);
	}

	public function login_check() {
		global $interim_login;
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
		$t = get_transient( $key );
		if ( intval($t) < 3 ) {
			$t = intval($t);
			$t++;
			set_transient($key, strval($t), 900);
		} elseif ( intval($t) >=3 ) {
			wp_die("You have attempted to login {$t} times and are now locked out. 
				Please use the reset password feature to unlock your account.");
		} else {
			set_transient( 'user_'. $user_id, '0', $expiration = 900 );
		}
		return $t;
	}
}

$L = new \gboone\LoginLockout();

add_action('plugins_loaded', array($L, 'build'));