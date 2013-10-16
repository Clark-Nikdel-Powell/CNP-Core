<?php

class CNP_Content_Freshness_Meta_Box extends CNP_Meta_Box {

	protected static $id = 'cnp-content-freshness-meta-box';

	protected static $title = 'Content Freshness';

	protected static $post_type = array(
		'page'
	);

	protected static $context = 'side';

	protected static $priority = 'low';

	protected static $fields = array(
		array(
			'type'    => 'checkbox',
			'id'      => 'cnp-content-freshness-active',
			'label'   => 'Active',
			'default' => true,
			'desc'    => 'Should this content stale?'
		),
		array(
			'type'    => 'slider',
			'id'      => 'cnp-content-freshness-length',
			'label'   => 'Length',
			'min'     => 1,
			'max'     => 31,
			'step'    => 1,
			'default' => 4
		),
		array(
			'type'    => 'select',
			'id'      => 'cnp-content-freshness-unit',
			'label'   => 'Units',
			'default' => 'months',
			'options' => array(
				'days'   => 'Days',
				'weeks'  => 'Weeks',
				'months' => 'Months',
				'years'  => 'Years'
			)
		)
	);

	protected static function getMeta($post_id) {
		$date = get_post_meta($post_id, 'cnp-content-freshness-date', true);

		$active = get_post_meta($post_id, 'cnp-content-freshness-active', true);
		if ('' === $date) $active = true;
		$active = (bool)$active;

		$unit = get_post_meta($post_id, 'cnp-content-freshness-unit', true);
		if (!in_array($unit, array_keys(static::$fields[2]['options']))) $unit = 'months';
		$unit = (string)$unit;

		$qty = get_post_meta($post_id, 'cnp-content-freshness-length', true);
		if ('' === $qty) $qty = 4;
		$qty = (int)$qty;

		$date = get_post_meta($post_id, 'cnp-content-freshness-date', true);
		if ('' === $date) {
			$date = new DateTime();
			$date->modify("+$qty $unit");
		} else {
			$date = DateTime::createFromFormat('Ymd', $date);
		}

		return array(
			'active' => $active,
			'unit'   => $unit,
			'qty'    => $qty,
			'date'   => $date
		);
	}

	public static function save($post_id, $post) {
		$meta = static::getMeta($post_id);
		extract($meta);

		if (!$active) return;

		$date = new DateTime($post->post_modified);
		$date->modify("+$qty $unit");

		update_post_meta($post_id, 'cnp-content-freshness-date', $date->format('Ymd'));
	}

	public static function display($post, $args = null) {
		$meta = static::getMeta($post->ID);
		extract($meta);

		$status = '<i>This content will never stale.</i>';
		if ($active) {
			if (new DateTime() > $date) {
				$status = '<b class="stale">This content is stale!</b><br/><i>Ensure the content is up-to-date, and click the update button.</i>';
			} else {
				$status = '<b>Fresh Until:</b> '.$date->format('m/d/Y');
			}
		}
		printf('<p class="cnp-content-freshness-status">%s</p>', $status);
		echo '<p>This content should be considered stale after:</p>';
		parent::display($post, $args);
	}



}
