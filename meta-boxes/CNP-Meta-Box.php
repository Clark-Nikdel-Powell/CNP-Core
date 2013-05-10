<?php

abstract class CNP_Meta_Box {

	protected static $id = false;

	protected static $title = false;

	protected static $post_type = false;

	protected static $context = false;

	protected static $show_meta_box = true;

	protected static $allowed_contexts = array(
		'normal',
		'advanced',
		'side'
	);

	protected static $priority = false;

	protected static $allowed_priorities = array(
		'default',
		'high',
		'low',
		'core'
	);

	protected static $callback_args = null;

	protected static $fields = null;

	protected static function nonce() {
		return static::$id . '-nonce';
	}

	protected static function post_types() {
		$post_types = static::$post_type;
		if (!is_array($post_types)) $post_types = array($post_types);
		return apply_filters('cnp_meta_box_post_types', $post_types, static::$id);
	}

	protected static function callback_args() {
		return static::$callback_args;
	}

	public static function display($post, $args = null) {
		if (static::$fields) {
			wp_nonce_field(static::nonce(), static::nonce());
			CNP_Meta_Box_Field_Factory::display_post_fields(static::$fields, $post->ID);
		}
	}

	public static function save($post_id, $post) { }

	public static function save_post($post_id, $post) {
		if (!in_array($post->post_type, static::post_types())) return $post_id;
		if (!isset($_POST[static::nonce()]) || !wp_verify_nonce($_POST[static::nonce()], static::nonce())) return $post_id;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
		if (static::$fields) CNP_Meta_Box_Field_Factory::save_post_fields(static::$fields, $post_id);
		static::save($post_id, $post);
		return $post_id;
	}

	public static function add_meta_box() {
		if (!(static::$id && static::$title && static::$post_type)) {
			trigger_error('$id, $title, & $post_type must be defined on CNP_Meta_Box subclasses.');
			return;
		}

		if (!static::$context || !in_array(static::$context, static::$allowed_contexts)) 
			static::$context = static::$allowed_contexts[0];

		if (!static::$priority || !in_array(static::$priority, static::$allowed_priorities)) 
			static::$priority = static::$allowed_priorities[0];

		foreach(static::post_types() as $type) {
			add_meta_box(
				static::$id,
				static::$title,
				array(get_called_class(), 'display'),
				$type,
				static::$context,
				static::$priority,
				static::callback_args()
			);			
		}
	}

	protected static function show_meta_box() {
		return apply_filters('cnp_show_meta_box', static::$show_meta_box, static::$id);
	}

	public static function initialize() {
		if (static::show_meta_box()) {
			add_action('add_meta_boxes', array(get_called_class(), 'add_meta_box'));
			add_action('save_post', array(get_called_class(), 'save_post'), 10, 2);
		}
	}

}
