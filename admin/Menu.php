<?php

class CNP_Admin_Menu {

//-----------------------------------------------------------------------------
// MENU RELOCATION
//-----------------------------------------------------------------------------

	public static function relocate_media_menu() {
		global $menu;
		$media_key = -1;
		$media_item = null;
		$new_key = 58;
		foreach($menu as $key => $menu_item) {
			if (is_array($menu_item)
				&& array_key_exists(0, $menu_item)
				&& 'Media' === $menu_item[0]
			) {
				$media_key = $key;
				$media_item = $menu_item;
				break;
			}
		}

		if ($media_key === -1) return;

		unset($menu[$media_key]);
		$menu[$new_key] = $media_item;
	}

//-----------------------------------------------------------------------------
// MENU REMOVAL
//-----------------------------------------------------------------------------

	protected static $menu_pages_to_remove = array(
		'edit-comments.php',
		'link-manager.php',
	);

	protected static $admin_only_pages = array(
		'plugins.php',
		'tools.php',
		'options-general.php'
	);

	protected static $submenu_pages_to_remove = array(
		'themes.php' => array(
			'customize.php',
			'theme-editor.php'
		),
		'plugins.php' => array(
			'plugin-editor.php'
		)
	);

	protected static $admin_only_subpages = array(
		'index.php' => array(
			'update-core.php'
		),
		'themes.php' => array(
			'themes.php',
			'customize.php'
		)
	);

	public static function remove_menus() {
		$menu_pages_to_remove = apply_filters('cnp_menu_pages_to_remove', static::$menu_pages_to_remove);
		$submenu_pages_to_remove = apply_filters('cnp_submenu_pages_to_remove', static::$submenu_pages_to_remove);

		$admin_only_pages = apply_filters('cnp_admin_only_pages', static::$admin_only_pages);
		$admin_only_subpages = apply_filters('cnp_admin_only_subpages', static::$admin_only_subpages);

		$is_admin = in_array('administrator', wp_get_current_user()->roles);
		if (!$is_admin) {
			if (is_array($admin_only_pages))
				$menu_pages_to_remove = is_array($menu_pages_to_remove)
					? array_merge($menu_pages_to_remove, $admin_only_pages)
					: $admin_only_pages;

			if (is_array($admin_only_subpages))
				$submenu_pages_to_remove = is_array($submenu_pages_to_remove)
					? array_merge($submenu_pages_to_remove, $admin_only_subpages)
					: $admin_only_subpages;
		}

		if (is_array($menu_pages_to_remove))
			foreach($menu_pages_to_remove as $page)
				remove_menu_page($page);

		if (is_array($submenu_pages_to_remove))
			foreach($submenu_pages_to_remove as $parent => $pages)
				if (is_array($pages))
					foreach($pages as $page) {
						remove_submenu_page($parent, $page);
					}
				else
					remove_submenu_page($parent, $pages);
	}

	public static function initialize() {
		add_action('admin_menu', array(__CLASS__, 'relocate_media_menu'));
		add_action('admin_menu', array(__CLASS__, 'remove_menus'), 999);
	}

}
