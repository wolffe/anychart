<?php
/*
Plugin Name: AnyChart
Version: 1.0
Plugin URI: http://getbutterfly.com/wordpress-plugins/anychart/
Description: Simple bar chart custom post generator.
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/

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
define('ANYCHART_VERSION', '1.0');

// plugin localization
$plugin_dir = basename(dirname(__FILE__)); 
load_plugin_textdomain('anychart', false, $plugin_dir . '/languages'); 

include_once('includes/updater.php');
if(is_admin()) {
	$config = array(
		'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
		'proper_folder_name' => 'plugin-name', // this is the name of the folder your plugin lives in
		'api_url' => 'https://api.github.com/repos/username/repository-name', // the github API url of your github repo
		'raw_url' => 'https://raw.github.com/username/repository-name/master', // the github raw url of your github repo
		'github_url' => 'https://github.com/username/repository-name', // the github url of your github repo
		'zip_url' => 'https://github.com/username/repository-name/zipball/master', // the zip url of the github repo
		'sslverify' => true // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
		'requires' => '3.0', // which version of WordPress does your plugin require?
		'tested' => '3.3', // which version of WordPress is your plugin tested up to?
		'readme' => 'README.MD' // which file to use as the readme for the version number
	);
	new WPGitHubUpdater($config);
}

// settings menu
function anychart_wp_menu() {
	add_menu_page('AnyChart', 'AnyChart', 'manage_options', __FILE__, 'anychart_dashboard_page', ANYCHART_PLUGIN_URL . '/images/icon-16.png');
}

include(ANYCHART_PLUGIN_PATH . '/includes/registration.php');

function anychart_styles() {
	wp_enqueue_style('anychart-styles', ANYCHART_PLUGIN_URL . '/css/style.css');	
}

function anychart_scripts() {
	wp_enqueue_script('jquery');

	wp_enqueue_script( 'functions', ANYCHART_PLUGIN_URL . '/js/functions.js' );
}
add_action('wp_enqueue_scripts', 'anychart_scripts');

// Roo activation
register_activation_hook(__FILE__, 'anychart_init');

// Roo actions
add_action('init', 'create_anychart_type'); // registration
add_action('init', 'create_anychart_taxonomies', 0); // registration

add_action('admin_menu', 'anychart_wp_menu'); // settings menu

add_action('wp_print_styles', 'anychart_styles');

// Roo shortcodes
add_shortcode('anychart', 'anychart_main');

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
        <link rel="stylesheet" type="text/css" href="' . ANYCHART_PLUGIN_URL . '/css/graph.css" />
		<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700,300" rel="stylesheet" type="text/css">';

		$display .= '
		<section class="main">
			<dl>
				<dt>' . ucwords($type) . ' (%) - ' . $caption . '</dt>';

				query_posts(array(
					'post_status' => 'publish',
					'post_type' => array('anychart'),
					'posts_per_page' => -1,
					'anychart_category' => $type,
				));
				if(have_posts()) : while(have_posts()) : the_post();
					$value = get_the_content() * 10;
					$color = sprintf("#%06x", rand(0, 16777215));
					$display .= '
					<dd class="p' . $value . '"><b>' . get_the_title() . ' (' . get_the_content() . '%)&nbsp;</b></dd>';
					$display .= '<style type="text/css">dd.p' . $value . ' b { width: ' . (100 - get_the_content()) . '%; } dd.p' . $value . ' { background-color: ' . $color . '; }</style>';
				endwhile;
				endif;
		$display .= '</dl>
		</section>';

		wp_reset_query();

		return $display;
	}
	else {
		$display = '';
		return $display;
	}
}
?>