<?php
/*
	Plugin Name: CNP Core
	Plugin URI: http://clarknikdelpowell.com
	Version: 0.1.0
	Description: Provides base-level customization & security for all CNP sites. DO NOT DISABLE OR DELETE!!!
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
define('CNP_PATH', plugin_dir_path(__FILE__));
define('CNP_URL', plugin_dir_url(__FILE__));

////////////////////////////////////////////////////////////////////////////////
// PLUGIN DEPENDENCIES
////////////////////////////////////////////////////////////////////////////////

require_once CNP_PATH.'CNP-Theme.php';

require_once CNP_PATH.'functions/theme.php';

////////////////////////////////////////////////////////////////////////////////
// ROOT PLUGIN CLASS
////////////////////////////////////////////////////////////////////////////////

final class CNP_Core {

	public static function activation() {
		/* PLUGIN ACTIVATION LOGIC HERE */
	}

	public static function deactivation() {
		/* PLUGIN DEACTIVATION LOGIC HERE */
	}

	public static function uninstall() {
		/* PLUGIN DELETION LOGIC HERE */
	}

	public static function initialize() {
		CNP_Theme::initialize();
	}

}

////////////////////////////////////////////////////////////////////////////////
// PLUGIN INITIALIZATION
////////////////////////////////////////////////////////////////////////////////

register_activation_hook(__FILE__, array('CNP_Core', 'activation'));
register_deactivation_hook(__FILE__, array('CNP_Core', 'deactivation'));
register_uninstall_hook(__FILE__, array('CNP_Core', 'uninstall'));
CNP_Core::initialize();
