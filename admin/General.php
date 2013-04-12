<?

class CNP_Admin_General {

	public static function add_favicon() {
		$favicon_url = get_stylesheet_directory_uri() . '/favicon.png';
		echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
	}

	public static function admin_footer_text() {
		ob_start(); ?> 
			Created by <a href="http://clarknikdelpowell.com/">Clark Nikdel Powell</a>. Powered by <a href="http://wordpress.org">WordPress</a>.
		<? return ob_get_clean();
	}

	public static function hide_upgrade_notices() {
		if (!current_user_can('update_core'))
			add_filter('pre_site_transient_update_core', function($a) { return null; });
	}

	public static function enqueue_scripts() {
		wp_enqueue_style('cnp_admin_styles', CNP_URL.'resources/css/admin.css');		
	}

	public static function initialize() {
		add_action('admin_head', array(__CLASS__, 'add_favicon'));
		add_filter('admin_footer_text', array(__CLASS__, 'admin_footer_text'), 999);
		add_action('after_setup_theme', array(__CLASS__, 'hide_upgrade_notices'));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
	}
}
