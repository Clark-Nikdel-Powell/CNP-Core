<?php

class CNP_Content_Freshness_Widget extends CNP_Dashboard_Widget {

	protected static $id = 'cnp-content-freshness-widget';

	protected static $title = 'Stale Content';

	protected static $position = 'side';

	public static function getStale() {
		$args = array(
			'post_type' => 'any',
			'post_status' => array('publish'),
			'order' => 'ASC',
			'orderby' => 'modified',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'cnp-content-freshness-active',
					'value' => '0',
					'compare' => '!='
				),
				array(
					'key' => 'cnp-content-freshness-date',
					'type' => 'DATE',
					'value' => date('Ymd'),
					'compare' => '<='
				)
			)
		);

		return new WP_Query($args);
	}

	public static function ajax() {
		$qry = static::getStale();

		if (0 === $qry->post_count) {
			?><p class="empty"><i>No stale content found.</i></p><?php
		} else {
			ob_start(); ?>
				<table>
					<thead>
						<tr>
							<th>Type</th>
							<th>Title</th>
							<th>Last Modified</th>
						</tr>
					</thead>
					<tbody>
						<?php while ($qry->have_posts()) { $qry->the_post(); ?>
							<tr>
								<td><?php echo get_post_type(); ?></td>
								<td><?php edit_post_link(get_the_title()); ?></td>
								<td><?php the_modified_time('D, M j \a\t g:i a'); ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php echo ob_get_clean();
			wp_reset_query();
			wp_reset_postdata();
		}

		die();
	}

	public static function display() {
		?>
			<div id="cnp-content-freshness-container" style="display:none;"></div>
			<p id="cnp-content-freshness-loading" class="empty">Loading...</p>
			<script>jQuery(function($){$('#cnp-content-freshness-widget').cnp_freshness_widget();});</script>
		<?php
	}

	protected static function show_widget() {
		$show = parent::show_widget();
		if (!$show) return false;
		return get_option('cnp-content-freshness-enabled', true);
	}

	public static function initialize() {
			parent::initialize();
			add_action('wp_ajax_cnp_content_freshness', array(get_called_class(), 'ajax'));
	}

}
