<?php

class CNP_Admin_Login {

	/**
	 * Changes the login logo url to the home page url.
	 * @access public
	 * @return string The site url set in Settings
	 */
	public static function update_login_url() { 
		return 'http://clarknikdelpowell.com/'; 
	}

	/**
	 * Changes the login logo title attribute to the site title.
	 * @access public
	 * @return string The title of the site set in Settings
	 */
	public static function update_login_title() {
		return get_bloginfo('name');
	}

	/**
	 * Changes the login logo to the CNP logo, and can be
	 * overwritten with a client logo as desired.
	 * 
	 * @access public
	 */
	public static function update_login_logo() {
		$default_image = 'resources/images/login-logo.png';
		$default_size  = array(548, 126);
		$desired_width = 274;
		
		$image = apply_filters('cnp_login_logo', CNP_URL.$default_image);
		$size  = apply_filters('cnp_login_logo_size', $default_size);

		$new_width = min($desired_width, $size[0]);

		$resize = array(
			$new_width,
			$size[1] * $new_width / $size[0]
		);

		?><style>
			.login h1 a {
				background-image: <?= "url('$image')"; ?>;
				background-size: <?= "{$resize[0]}px {$resize[1]}px" ?>;
				height: <?= ($resize[1] + 4).'px'; ?>;
			}
		</style><?php
	}

//-----------------------------------------------------------------------------
// INITIALIZATION
//-----------------------------------------------------------------------------

	public static function initialize() {
		add_filter('login_headerurl', array(__CLASS__, 'update_login_url'));
		add_filter('login_headertitle', array(__CLASS__, 'update_login_title'));
		add_action('login_head', array(__CLASS__, 'update_login_logo'));
	}

}
