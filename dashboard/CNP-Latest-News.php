<?php
include_once( ABSPATH . WPINC . '/feed.php' );

class CNP_Latest_News_Widget extends CNP_Dashboard_Widget {

	protected static $id = 'cnp-latest-news-widget';

	protected static $title = 'Latest News From Clark/Nikdel/Powell';

	protected static $position = 'side';

	public static function getNews() {
		$feed = fetch_feed('http://clarknikdelpowell.com/feed/');
		if (is_wp_error($feed)) return false;

		$max_items = $feed->get_item_quantity(5);
		$items = $feed->get_items(0, $max_items);

		if (count($items) === 0) return false;

		return $items;
	}

	public static function ajax() {
		$news = static::getNews();
		if (!$news) {
			?><p class="empty">There was an error retrieving the feed...</p><?php
		} elseif (count($news) === 0) {
			?><p class="empty">There were no articles to retrieve...</p><?php
		} else {
			?><ul id="cnp-latest-news-list"><?
				foreach($news as $item) {
					?>
						<li>
							<a href="<?php echo esc_url( $item->get_permalink() ); ?>" target="_blank"
              	title="<?php printf( 'Posted %s by %s', $item->get_date('j F Y'), $item->get_author()->get_name()); ?>">
                <h4><?php echo esc_html( $item->get_title() ); ?></h4>
              </a>
              <p><? echo esc_html( $item->get_description() ); ?></p>
						</li>
					<?
				}
			?></ul><?
		}
		die();
	}

	public static function display() {
		?>
			<div id="cnp-latest-news-container" style="display:none;"></div>
			<p id="cnp-latest-news-loading" class="empty">Loading...</p>
			<script>jQuery(function($){$('#cnp-latest-news-widget').cnp_latest_news();});</script>
		<?
	}

	protected static function show_widget() {
		$show = parent::show_widget();
		if (!$show) return false;
		return get_option('cnp-latest-news-enabled', true);
	}

	public static function initialize() {
			parent::initialize();
			add_action('wp_ajax_cnp_latest_news', array(get_called_class(), 'ajax'));
	}

}
