<?php

class CNP_Admin_Bar {

//-----------------------------------------------------------------------------
// UPDATE ADMIN BAR NODES
//-----------------------------------------------------------------------------

	/**
	 * Default value for whether or not the admin bar should show on front-end
	 * @var boolean
	 */
	private static $show_admin_bar = false;

	/**
	 * Whether or not the admin bar should show on the front-end.
	 * @access public
	 * @return boolean
	 */
	public static function show_admin_bar() {
		return apply_filters('cnp_show_admin_bar', static::$show_admin_bar);
	}

//-----------------------------------------------------------------------------
// UPDATE ADMIN BAR NODES
//-----------------------------------------------------------------------------

	/**
	 * Nodes to remove from the admin bar
	 * @var array
	 */
	private static $nodes_to_remove = array(
		'wp-logo',
		'site-name',
		'comments',
		'new-content',
		'my-account',
		'search',
		'edit',
		'about',
		'wp-logo-default',
		'wp-logo-external',
		'view'
	);

	/**
	 * The list of random greetings that will be displayed in the admin bar
	 * @var array
	 */
	private static $greetings = array(
		'Howdy, %s!',
		'Welcome back, %s!',
		'What\'s up, %s?',
		'Hey, %s!'
	);

	/**
	 * Run the updates on the default admin bar
	 * @access public
	 */
	public static function update_admin_bar($bar) {
		if (!is_admin() && !static::show_admin_bar()) return;
		static::remove_admin_bar_nodes($bar);
		static::add_admin_bar_nodes($bar);
	}

	/**
	 * Removes nodes from the admin bar
	 * @access public
	 */
	private static function remove_admin_bar_nodes($bar) {
		$nodes_to_remove = apply_filters('cnp_nodes_to_remove', static::$nodes_to_remove);
		foreach($nodes_to_remove as $node) $bar->remove_node($node);
	}

	/**
	 * Adds new nodes to the admin bar
	 * @access public
	 */
	private static function add_admin_bar_nodes($bar) {
		$nodes_to_add = array();
		$user = wp_get_current_user();
		$greetings = apply_filters('cnp_admin_bar_greetings', static::$greetings);

		//NEW LOGO & SITE TITLE BAR
		$nodes_to_add[] = array(
			'id'    => 'wp-logo',
			'title' => sprintf(
				'<span class="ab-icon"></span><span class="ab-label"> %s</span>',
				apply_filters('cnp_admin_bar_title', get_bloginfo('name'))
			),
			'href'  => apply_filters('cnp_admin_bar_url', is_admin() ? get_site_url() : get_admin_url()),
		);

		//LOGOUT LINK
		$nodes_to_add[] = array(
			'parent' => 'top-secondary',
			'id'     => 'cnp-logout',
			'title'  => 'Log Out',
			'href'   => wp_logout_url()
		);

		//PROFILE LINK
		$nodes_to_add[] = array(
			'parent' => 'top-secondary',
			'id'     => 'cnp-user',
			'title'  => sprintf(
				$greetings[array_rand($greetings)],
				$user->display_name
			),
			'href'   => admin_url('profile.php'),
			'meta'   => array('title' => 'Access Your Profile')
		);

		$nodes_to_add = apply_filters('cnp_nodes_to_add', $nodes_to_add);
		foreach($nodes_to_add as $node) $bar->add_node($node);
	}

//-----------------------------------------------------------------------------
// CHANGE ADMIN BAR LOGO
//-----------------------------------------------------------------------------

	/**
	 * Changes the admin bar logo (via CSS)
	 * @access public
	 */
	public static function change_admin_bar_logo() {
		$default_logo = 'resources/images/bar-logo.png';
		$logo_image = apply_filters('cnp_admin_bar_logo', CNP_URL.$default_logo);
		if (!$logo_image) return;
		?>
			<style type="text/css">
				#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon {
					background-image:url('<?= $logo_image; ?>') !important;
					background-position:0 0;
				}
				.mp6 #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon {
					margin: 5px 6px 5px 0px;
					padding: 0;
				}
				.mp6 #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
					display: none;
				}
				#wpadminbar.nojs #wp-admin-bar-wp-logo:hover > .ab-item .ab-icon,
				#wpadminbar #wp-admin-bar-wp-logo.hover > .ab-item .ab-icon {
					background-position:0 -21px;
				}
			</style>
		<?
	}

	/**
	 * Changes the admin bar logo (via CSS) if it's visible on the front end.
	 * @access public
	 * @return [type] [description]
	 */
	public static function change_admin_bar_logo_front_end() {
		if (static::show_admin_bar())
			return static::change_admin_bar_logo();
	}

//-----------------------------------------------------------------------------
// INITIALIZATION
//-----------------------------------------------------------------------------

	public static function initialize() {
		add_filter('show_admin_bar', array(__CLASS__, 'show_admin_bar'));
		add_action('admin_bar_menu', array(__CLASS__, 'update_admin_bar'), 999);
		add_action('admin_head', array(__CLASS__, 'change_admin_bar_logo'));
		add_action('wp_head', array(__CLASS__, 'change_admin_bar_logo_front_end'));
	}

}
