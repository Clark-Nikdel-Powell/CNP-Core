<?php
/*
	Plugin Name: CNP Core
	Plugin URI: http://clarknikdelpowell.com
	Version: 0.1.0
	Description: Provides base-level customization & security for all CNP sites. DO NOT DEACTIVATE OR DELETE!!!
	Author: Chris Roche
	Author URI: http://clarknikdelpowell.com

	Copyright 2013+ Clark/Nikdel/Powell (email : croche@clarknikdelpowell.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2 (or later), 
	as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

////////////////////////////////////////////////////////////////////////////////
// PLUGIN CONSTANT DEFINITIONS
////////////////////////////////////////////////////////////////////////////////

//FILESYSTEM CONSTANTS
define('CNP_PATH',         plugin_dir_path(__FILE__));
define('CNP_URL',          plugin_dir_url(__FILE__));
define('CNP_CORE_VERSION', '0.1.0');

////////////////////////////////////////////////////////////////////////////////
// PLUGIN DEPENDENCIES
////////////////////////////////////////////////////////////////////////////////

//ADMIN
require_once CNP_PATH.'admin/General.php';
require_once CNP_PATH.'admin/Login.php';
require_once CNP_PATH.'admin/Bar.php';

//THEME
require_once CNP_PATH.'theme/General.php';
require_once CNP_PATH.'theme/Support.php';
require_once CNP_PATH.'theme/Images.php';
require_once CNP_PATH.'theme/Widgets.php';

//ABSTRACTS
require_once CNP_PATH.'CNP-Post-Type.php';

//FUNCTIONS
require_once CNP_PATH.'functions/theme.php';

////////////////////////////////////////////////////////////////////////////////
// ROOT PLUGIN CLASS
////////////////////////////////////////////////////////////////////////////////

final class CNP_Core {

	/**
	 * Enforce that this plugin is loaded before all other plugins. This ensures 
	 * that the classes added here are immediately available to other plugins.
	 * 
	 * @access public
	 */
	public static function load_first() {
		$plugin_url = plugin_basename(__FILE__);
		$active_plugins = get_option('active_plugins', array());
		$key = array_search($plugin_url, $active_plugins);
		if (!$key) return;
		array_splice($active_plugins, $key, 1);
		array_unshift($active_plugins, $plugin_url);
		update_option('active_plugins', $active_plugins);
	}

//-----------------------------------------------------------------------------
// PLUGIN RELATED HOOKS
//-----------------------------------------------------------------------------

	public static function activation() {
		add_action('shutdown', array('CNP_Theme_Images', 'override_image_sizes'));
	}

	public static function deactivation() {
		/* PLUGIN DEACTIVATION LOGIC HERE */
	}

	public static function uninstall() {
		/* PLUGIN DELETION LOGIC HERE */
	}

	public static function initialize() {
		//ADMIN
		CNP_Admin_General::initialize();
		CNP_Admin_Login::initialize();
		CNP_Admin_Bar::initialize();
		//THEME
		CNP_Theme_General::initialize();
		CNP_Theme_Support::initialize();
		CNP_Theme_Images::initialize();
		CNP_Theme_Widgets::initialize();


		add_action('activated_plugin', array(__CLASS__, 'load_first'));
	}

}

////////////////////////////////////////////////////////////////////////////////
// PLUGIN INITIALIZATION
////////////////////////////////////////////////////////////////////////////////

register_activation_hook(__FILE__, array('CNP_Core', 'activation'));
register_deactivation_hook(__FILE__, array('CNP_Core', 'deactivation'));
register_uninstall_hook(__FILE__, array('CNP_Core', 'uninstall'));
CNP_Core::initialize();
