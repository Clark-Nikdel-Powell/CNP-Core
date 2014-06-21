<?php

class CNP_Users_General {

	protected static $contact_fields_to_remove = array(
		'aim',
		'yim',
		'jabber'
	);

	protected static $contact_fields_to_add = array(
		'facebook' => 'Facebook',
		'googleplus' => 'Google+',
		'linkedin' => 'LinkedIn',
		'twitter' => 'Twitter'
	);

	protected static $columns_to_remove = array(
		'posts'
	);

	public static function user_contactmethods($fields) {
		$remove = apply_filters('cnp_user_contact_fields_to_remove', static::$contact_fields_to_remove);
		$add    = apply_filters('cnp_user_contact_fields_to_add', static::$contact_fields_to_add);

		foreach ($remove as $field)
			unset($fields[$field]);

		$fields = array_merge($fields, $add);

		return $fields;
	}

	public static function manage_users_columns($cols) {
		$remove = apply_filters('cnp_user_columns_to_remove', static::$columns_to_remove);
		foreach($remove as $col) unset($cols[$col]);
		return $cols;
	}

	public static function initialize() {
		add_filter('user_contactmethods', array(get_called_class(), 'user_contactmethods'));
		add_filter('manage_users_columns', array(get_called_class(), 'manage_users_columns'));
	}

}
