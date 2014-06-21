<?php

class CNP_Users_Roles {

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

	protected static $roles_to_remove = array(
	/*
		'subscriber',
		'author',
		'contributor'
	*/
	);

	protected static $capabilities_to_add = array(
		'editor' => array(
			'edit_theme_options'
		)
	);

	protected static $capabilities_to_remove = array(
	/*
		'editor' => array(
			'capability'
		)
	*/
	);

	public static function manipulate_roles() {
		$roles_to_add = apply_filters('cnp_roles_to_add', static::$roles_to_add);
		$roles_to_remove = apply_filters('cnp_roles_to_remove', static::$roles_to_remove);
		$capabilities_to_add = apply_filters('cnp_capabilites_to_add', static::$capabilities_to_add);
		$capabilities_to_remove = apply_filters('cnp_capabilites_to_remove', static::$capabilities_to_remove);

		static::add_roles($roles_to_add);
		static::add_capabilities($capabilities_to_add);
		static::remove_capabilities($capabilities_to_remove);
		static::remove_roles($roles_to_remove);
	}

	protected static function add_roles($roles) {
		foreach($roles as $role => $data) {
			$the_role = get_role($role);
			if ($the_role) continue;

			if (!isset($data['display_name'])) {
				trigger_error('Display name is required for role: '.$role);
				continue;
			}

			if (!isset($data['model_role']) && !isset($data['capabilities'])) {
				trigger_error('A model role or capabilities list must be provided for role: '.$role);
				continue;
			}

			$capabilities = isset($data['capabilities']) ? $data['capabilities'] : array();
			if (isset($data['model_role'])) {
				$model = get_role($data['model_role']);
				if ($model) $capabilities = $capabilities + $model->capabilities;
			}

			add_role($role, $data['display_name'], $capabilities);
		}
	}

	protected static function remove_roles($roles) {
		foreach($roles as $role)
			remove_role($role);
	}

	protected static function add_capabilities($roles) {
		foreach($roles as $role => $capabilities) {
			$the_role = get_role($role);
			if (!$the_role) continue;
			foreach($capabilities as $cap) $the_role->add_cap($cap);
		}
	}

	protected static function remove_capabilities($roles) {
		foreach($roles as $role => $capabilities) {
			$the_role = get_role($role);
			if (!$the_role) continue;
			foreach($capabilities as $cap) $the_role->remove_cap($cap);
		}
	}

	public static function initialize() {
		/* noop */
	}

}
