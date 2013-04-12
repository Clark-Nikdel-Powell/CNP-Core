<?

class CNP_Theme_Images {

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

	public static function initialize() {
		add_action('cnp_ready', array(__CLASS__, 'add_image_sizes'));
	}

}
