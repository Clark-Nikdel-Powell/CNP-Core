<?php

class CNP_Admin_General {

	public static function add_favicon() {
		$favicon_url = get_stylesheet_directory_uri() . '/img/icons/admin-favicon.ico';
		echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
	}

	public static function admin_footer_text() {
		ob_start(); ?>
			Created by <a href="http://clarknikdelpowell.com/">Clark Nikdel Powell</a>. Powered by <a href="http://wordpress.org">WordPress</a>.
		<?php return ob_get_clean();
	}

	public static function hide_upgrade_notices() {
		if (!current_user_can('update_core'))
			add_filter('pre_site_transient_update_core', function($a) { return null; });
	}

	public static function enqueue_scripts() {
		global $wp_scripts;
		$ui = $wp_scripts->query('jquery-ui-core');

		wp_enqueue_media();

		wp_enqueue_style(
			'cnp_jquery-ui-smoothness',
			"//ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.css",
			false,
			null
		);
		wp_enqueue_style('cnp_admin_styles', CNP_URL.'resources/css/admin.css');

		wp_enqueue_script('cnp_admin_scripts', CNP_URL.'resources/js/admin.js', array(
			'jquery',
			'jquery-ui-datepicker',
			'jquery-ui-slider'
		));
	}

	public static function setup_additional_settings() {
		register_setting( 'general', 'company_name' );
		register_setting( 'general', 'phone_number' );
		register_setting( 'general', 'fax_number' );
		register_setting( 'general', 'street_address' );

		add_settings_field('company_name', 'Company Name', 'company_name_callback', 'general');
		add_settings_field('phone_number', 'Phone Number', 'phone_number_callback', 'general');
		add_settings_field('fax_number', 'Fax Number', 'fax_number_callback', 'general');
		add_settings_field('street_address', 'Street Address', 'street_address_callback', 'general');

		function company_name_callback() { ?>
			<input class="regular-text" type="text" name="company_name" value="<?php echo get_option('company_name') ?>" />
		<?php } // end phone_number_callback

		function phone_number_callback() { ?>
			<input class="regular-text" type="text" name="phone_number" value="<?php echo get_option('phone_number') ?>" />
		<?php } // end phone_number_callback

		function fax_number_callback() { ?>
			<input class="regular-text" type="text" name="fax_number" value="<?php echo get_option('fax_number') ?>" />
		<?php } // end fax_number_callback

		function street_address_callback() { ?>
			<input class="regular-text" type="text" name="street_address" value="<?php echo get_option('street_address') ?>" />
		<?php } // end phone_number_callback
	}

	public static function initialize() {
		add_action('login_head', array(__CLASS__, 'add_favicon'));
		add_action('admin_head', array(__CLASS__, 'add_favicon'));
		add_filter('admin_footer_text', array(__CLASS__, 'admin_footer_text'), 999);
		add_action('after_setup_theme', array(__CLASS__, 'hide_upgrade_notices'));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
		add_action('admin_init', array(__CLASS__, 'setup_additional_settings'));
	}
}