<?php

class CNP_Settings_Social {

	protected static $settings_page = 'general';

	protected static $settings_group = 'cnp_social';

	protected static $group_title = 'Social Media Info';

	protected static $callback_prefix = 'display_';

	protected static $social_settings = array(
		'facebook_url' => 'Facebook URL',
		'twitter_handle' => 'Twitter Handle',
		'youtube_url' => 'YouTube URL',
		'flickr_url' => 'Flickr URL',
		'rss_url' => 'RSS Feed URL'
	);

	public static function add_social_fields() {
		add_settings_section(static::$settings_group, static::$group_title, array(get_called_class(), 'display_settings'), static::$settings_page);

		$social_settings = apply_filters('cnp_social_settings', static::$social_settings);

		foreach($social_settings as $setting => $label) {
			register_setting(static::$settings_page, $setting);
			add_settings_field($setting, $label, array(get_called_class(), static::$callback_prefix.$setting), static::$settings_page, static::$settings_group);
		}
	}

	public static function display_settings() { /* noop */ }

	public static function display_facebook_url($args) {
		printf(
			'<input class="regular-text" id="facebook_url" name="facebook_url" value="%s" />',
			esc_attr(get_option('facebook_url'))
		);
	}

	public static function display_twitter_handle($args) {
		printf(
			'@<input class="regular-text" id="twitter_handle" name="twitter_handle" value="%s" />',
			esc_attr(get_option('twitter_handle'))
		);
	}

	public static function display_youtube_url($args) {
		printf(
			'<input class="regular-text" id="youtube_url" name="youtube_url" value="%s" />',
			esc_attr(get_option('youtube_url'))
		);
	}

	public static function display_flickr_url($args) {
		printf(
			'<input class="regular-text" id="flickr_url" name="flickr_url" value="%s" />',
			esc_attr(get_option('flickr_url'))
		);
	}

	public static function display_rss_url($args) {
		printf(
			'<input class="regular-text" id="rss_url" name="rss_url" value="%s" /><p class="description">Default is <code>%s</code></p>',
			esc_attr(get_option('rss_url', get_bloginfo('rss2_url'))),
			get_bloginfo('rss2_url')
		);
	}

	public static function initialize() {
		add_action('admin_init', array(get_called_class(), 'add_social_fields'));
	}

}
