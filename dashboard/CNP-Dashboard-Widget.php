<?php

abstract class CNP_Dashboard_Widget {

	protected static $id = false;

	protected static $title = false;

	protected static $fields = null;

	protected static $show_widget = true;

	protected static $position = false;

	protected static $positions = array(
		'normal',
		'side'
	);

	protected static function nonce() {
		return static::$id . '-nonce';
	}

	public static function display() {
		if (isset($_POST[static::nonce()]) && wp_verify_nonce($_POST[static::nonce()], static::nonce())) static::save();
		if (static::$fields) {
			echo '<form class="cnp-dashboard-form" action="" method="POST">';
			wp_nonce_field(static::nonce(), static::nonce());
			CNP_Meta_Box_Field_Factory::display_dashboard_fields(static::$fields);
			echo '<div class="cnp-submit"><input name="save" type="submit" class="button button-primary button-large" value="Submit"/></div>';
			echo '</form>';
		}
	}

	protected static function save() {
		if (!isset($_POST[static::nonce()]) || !wp_verify_nonce($_POST[static::nonce()], static::nonce())) return $post_id;
		if (static::$fields) CNP_Meta_Box_Field_Factory::save_dashboard_fields(static::$fields);
	}

	public static function add_dashboard_widget() {
		if (!(static::$id && static::$title)) {
			trigger_error('$id, $title must be defined on CNP_Dashboard_Meta_Box subclasses.');
			return;
		}

		wp_add_dashboard_widget(
			static::$id,
			static::$title,
			array(get_called_class(), 'display')
		);

		static::move_dashboard_widget();
	}

	public static function move_dashboard_widget() {
		global $wp_meta_boxes;

		if (!static::$position) return;

		if (!in_array(static::$position, static::$positions)) static::$position = static::$positions[0];

		if (in_array(static::$id, array_keys($wp_meta_boxes['dashboard'][static::$position]['core']))) return;

		foreach(static::$positions as $position) {
			if ($position === static::$position) continue;
			if (in_array(static::$id, array_keys($wp_meta_boxes['dashboard'][$position]['core']))) {
				$current = $wp_meta_boxes['dashboard'][$position]['core'][static::$id];
				unset($wp_meta_boxes['dashboard'][$position]['core'][static::$id]);
				$wp_meta_boxes['dashboard'][static::$position]['core'][static::$id] = $current;
				break;
			}
		}
	}

	protected static function show_widget() {
		return apply_filters('cnp_show_dashboard_widget', static::$show_widget, static::$id);
	}

	public static function initialize() {
		if (static::show_widget()) {
			add_action('wp_dashboard_setup', array(get_called_class(), 'add_dashboard_widget'));
		}
	}

}
