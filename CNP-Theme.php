<?

/**
 * Describes action/filter hooks for theme related functions
 */
class CNP_Theme {

//-----------------------------------------------------------------------------
// THEME SUPPORT
//-----------------------------------------------------------------------------

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
	 * Size overrides for the reserved image intermediate sizes
	 * @var array
	 */
	private static $image_size_overrides = array(
		'thumbnail' => array(
			'width'  => 150,
			'height' => 150,
			'crop'   => true
		),
		'medium' => array(
			'width'  => 650,
			'height' => 0,
			'crop'   => false
		),
		'large' => array(
			'width'  => 1024,
			'height' => 0,
			'crop'   => false
		)
	);

	/**
	 * New image sizes to be available to the theme
	 * @var array
	 */
	private static $image_sizes = array(
		'small' => array(
			'width'  => 300,
			'height' => 0,
			'crop'   => false
		)
	);

	/**
	 * Default values for a sizing array
	 * @var array
	 */
	private static $image_size_default = array(
		'width'  => 0,
		'height' => 0,
		'crop'   => false
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

	/**
	 * Overrides the pre-existing image sizes for WordPress.
	 * THIS FUNCTION IS RUN IN THE ACTIVATE HOOK OF CNP-CORE!
	 * @access public
	 */
	public static function override_image_sizes() {
		$sizes = apply_filters('cnp_override_image_sizes', static::$image_size_overrides);
		foreach ($sizes as $size => $args) {
			$args = wp_parse_args($args, static::$image_size_default);
			update_option("{$size}_size_w", absint($args['width']));
			update_option("{$size}_size_h", absint($args['height']));
			update_option("{$size}_crop", (bool)$args["crop"]);
		}
	}

	/**
	 * Adds new image sizes to WordPress.
	 * @access public
	 */
	public static function add_image_sizes() {
		$sizes = apply_filters('cnp_image_sizes', static::$image_sizes);
		foreach ($sizes as $size => $args) {
			$args = wp_parse_args($args, static::$image_sizes);
			add_image_size($size, $args['width'], $args['height'], $args['crop']);
		}
	}



//-----------------------------------------------------------------------------
// THEME-RELATED HOOK OVERRIDES
//-----------------------------------------------------------------------------

	/**
	 * Creates a nicely formatted and more specific title element text
	 * for output in head of document, based on current view.
	 * @access public
	 * @param  string $title Default title text for current view
	 * @param  string $sep   Optional separator
	 * @return string        Filtered title
	 */
	public static function wp_title($title, $sep) {
		global $paged, $page;

		//ignore feed pages
		if (is_feed()) return $title;

		//add site name
		$title .= get_bloginfo('name');

		//add site description for the home/front page
		$desc = get_bloginfo('description', 'display');
		if ($desc && (is_home() || is_front_page()))
			$title = "$title $sep $desc";

		//add a page number if necessary
		if ($paged >= 2 || $page >= 2)
			$title = "$title $sep Page ".max($paged, $page);

		return $title;
	}

	/**
	 * Gets the description for the current page, either the excerpt of
	 * a singular item, or the site/blog description otherwise.
	 * @access public
	 */
	public static function description() {
		echo esc_attr(is_singular()
			? the_excerpt()
			: get_bloginfo('description')
		);
	}

//-----------------------------------------------------------------------------
// INITIALIZATION
//-----------------------------------------------------------------------------

	/**
	 * Adds all filter & action hooks to WP to handle when necessary
	 * @access public
	 */
	public static function initialize() {
		$cls = __CLASS__;

		add_action('after_setup_theme', array($cls, 'theme_support'));
		add_action('after_setup_theme', array($cls, 'add_image_sizes'));

		add_filter('wp_title', array($cls, 'wp_title'), 10, 2);
		add_action('cnp_description', array($cls, 'description'));
	}

}
