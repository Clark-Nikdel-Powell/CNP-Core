<?php

class CNP_Users_Roles {

	protected static $roles_to_remove = array(
	/*
		'subscriber',
		'author',
		'contributor'
	*/
	);

	protected static $roles_to_add = array(
	/*
		'role' => array(
			'display_name' => 'Display Name',
			'model_role' => 'role_to_model_after',
			// OR //
			'capabilities' => array(
				// LIST OUT CAPABILTIES...
			)
		)
	*/
	);

	protected static $capabilities_to_add = array(
		'editor' => array(
			'edit_theme_options'
		)
	);

	protected static $capabilites_to_remove = array(
	/*
		'editor' => array(
			'capability'
		)
	*/
	);

	public static function manipulate_roles() {
		
	}

	public static function initialize() {

	}

}
