<?

class CNP_Theme_Support {

	/**
	 * The theme features to be enabled for the current theme.
	 * @see http://codex.wordpress.org/add_theme_support
	 * @var array
	 */
	private static $theme_features = array(
		//'post_formats'         => array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat'),
			'post-thumbnails'      => true, //or array(...) to specify it for only certain post types
		//'custom_background'    => true, //or array(...) to specify default values
		//'custom_header'        => true, //or array(...) to specify default values
		//'automatic-feed-links' => true,
			'menus'                => true
	);

	/**
	 * Adds theme support to the current site.
	 * @access public
	 */
	public static function theme_support() {
		$features = apply_filters('cnp_theme_features', static::$theme_features);
		foreach($features as $feature => $args) {
			if (is_array($args)) add_theme_support($feature, $args);
			elseif($args) add_theme_support($feature);
		}
	}

	public static function initialize() {
		add_action('cnp_ready', array(__CLASS__, 'theme_support'));
	}

}
