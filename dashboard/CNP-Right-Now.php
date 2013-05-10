<?php

class CNP_Right_Now_Widget extends CNP_Dashboard_Widget {

	protected static $id = 'cnp-right-now';

	protected static $title = 'Right Now';

	public static function sortPostTypes($a, $b) {
		$al = $a->labels->name;
		$bl = $b->labels->name;
		if ($al == $bl) return 0;
		return $al > $bl ? 1 : -1;
	}

	public static function ajax() {
		global $wp_version;

		$post_types = get_post_types(array('public' => true), 'objects');
		usort($post_types, array(get_called_class(), 'sortPostTypes'));

		$active_plugins = count(get_option('active_plugins', array()));
		$total_plugins = count(get_plugins());
		$public = (bool)get_option('blog_public', true);

		ob_start(); ?>
			<div class="right-now-block">
				<h4>Content</h4>
				<table>
					<tbody>
						<? foreach($post_types as $pt) { 
							$counts = (array)wp_count_posts($pt->name, 'readable'); 
							$edit_link = $pt->name == 'attachment' ? 'upload.php' : 'edit.php?post_type='.$pt->name;
							$draft_link = $edit_link.'&post_status=draft';
						?>
							<tr>
								<td><a href="<?= $edit_link; ?>"><?= array_sum($counts) - $counts["auto-draft"]; ?></a></td>
								<td>
									<a href="<?= $edit_link; ?>"><?= $pt->labels->name;?></a>
									<? if ($counts["draft"]) { ?> <a href="<?= $draft_link; ?>"><small><?= $counts["draft"]; ?> Drafts</small></a><? } ?></td>
							</tr>
						<? } ?>
					</tbody>
				</table>
			</div>
			<div class="right-now-block">
				<h4>Details</h4>
				<p id="right-now-details">
					<b class="blog-name"><?= bloginfo('name'); ?></b> is running on 
					<b class="wordpress-version">WordPress <?= $wp_version ?></b> with the 
					<b class="theme-name"><?= wp_get_theme(); ?> Theme</b>. There are
					<b class="active-plugins"><?= $active_plugins ?> Active Plugins</b> of the <?= $total_plugins; ?> plugins installed.
					This site is currently <b class="blog-public"><?= $public ? '' : 'NOT'; ?> indexable</b> by search engines and archivers.
				</p>
			</div>
		<? echo ob_get_clean();

		die();
	}

	public static function display() {
		?>
			<div id="cnp-right-now-container" style="display:none;"></div>
			<p id="cnp-right-now-loading" class="empty">Loading...</p>
			<script>jQuery(function($){$('#cnp-right-now').cnp_right_now();});</script>
		<?
	}

	protected static function show_widget() {
		$show = parent::show_widget();
		if (!$show) return false;
		return get_option('cnp-right-now-enabled', true);
	}

	public static function initialize() {
		parent::initialize();
		add_action('wp_ajax_cnp_right_now', array(get_called_class(), 'ajax'));
	}
}
