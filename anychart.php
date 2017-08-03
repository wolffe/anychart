<?php
/*
Plugin Name: AnyChart
Version: 1.1
Plugin URI: http://getbutterfly.com/wordpress-plugins/anychart/
Description: Simple bar chart custom post generator.
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/
Update URL: https://github.com/wolffe/anychart/

Copyright 2013  Ciprian Popescu  (email : getbutterfly@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define('ANYCHART_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('ANYCHART_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)));
define('ANYCHART_VERSION', '1.1');

// plugin localization
$plugin_dir = basename(dirname(__FILE__)); 
load_plugin_textdomain('anychart', false, $plugin_dir . '/languages'); 

require_once('includes/updater.php');
if(is_admin()) {
	$config = array(
		'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
		'proper_folder_name' => 'anychart', // this is the name of the folder your plugin lives in
		'api_url' => 'https://api.github.com/repos/wolffe/anychart', // the github API url of your github repo
		'raw_url' => 'https://raw.github.com/wolffe/anychart/master', // the github raw url of your github repo
		'github_url' => 'https://github.com/wolffe/anychart', // the github url of your github repo
		'zip_url' => 'https://github.com/wolffe/anychart/zipball/master', // the zip url of the github repo
		'sslverify' => true, // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
		'requires' => '4.0', // which version of WordPress does your plugin require?
		'tested' => '4.1.1', // which version of WordPress is your plugin tested up to?
		'readme' => 'README.MD' // which file to use as the readme for the version number
	);
	new WP_GitHub_Updater($config);
}

// settings menu
function anychart_wp_menu() {
	add_submenu_page('edit.php?post_type=anychart', 'AnyChart Settings', 'AnyChart Settings', 'manage_options', 'anychart', 'anychart_dashboard_page');
}

include(ANYCHART_PLUGIN_PATH . '/includes/registration.php');

function anychart_styles() {
	wp_enqueue_style('anychart-styles', ANYCHART_PLUGIN_URL . '/css/graph.css');	
}

// Roo activation
register_activation_hook(__FILE__, 'anychart_init');

// Roo actions
add_action('init', 'create_anychart_type'); // registration
add_action('init', 'create_anychart_taxonomies', 0); // registration

add_action('admin_menu', 'anychart_wp_menu'); // settings menu

add_action('wp_print_styles', 'anychart_styles');

// Roo shortcodes
add_shortcode('anychart', 'anychart_main');

function anychart_dashboard_page() {
	?>
	<div class="wrap">
		<h2>AnyCharts</h2>
		<div id="poststuff" class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h3>At a Glance</h3>
				<div class="inside">
					<p>This is just a placeholder page. In order to add/edit/remove chart items, find the <b>Chart Items</b> menu item to the left.</p>
				</div>
			</div>
		</div>
	</div>
	<?php
}

// Main directory function
// Displays link submission form and category table
function anychart_main($atts) {
	extract(shortcode_atts(array(
		'type' => '',
		'caption' => '',
		'show' => 'yes'
	), $atts));

	// SHOW SELECTED CATEGORY
	if($type != '' && $show == 'yes') {
		$display = '';

		$display .= '
		<h2>' . ucwords($type) . ' (%)<br><small>' . $caption . '</small></h2>
		<section class="main">
			<dl>';
				query_posts(array(
					'orderby' => 'date',
					'order' => 'ASC',
					'post_status' => 'publish',
					'post_type' => array('anychart'),
					'posts_per_page' => -1,
					'anychart_category' => $type,
				));
				if(have_posts()) : while(have_posts()) : the_post();
					$value = get_the_content() * 10;
					$color = sprintf("#%06x", rand(0, 16777215));
					$display .= '
					<dd class="p' . $value . '"><b>' . get_the_title() . '<!-- <small>(' . get_the_content() . '%)</small>-->&nbsp;</b></dd>';
					$display .= '<style type="text/css">dd.p' . $value . ' b { width: ' . (100 - get_the_content()) . '%; } dd.p' . $value . ' { background-color: ' . $color . '; }</style>';
				endwhile;
				endif;
		$display .= '</dl>
		</section><br><br>';

		wp_reset_query();

		return $display;
	}
	else {
		$display = '';
		return $display;
	}
}
?>
