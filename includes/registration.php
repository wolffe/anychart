<?php
// registration module
// version: 1.0.1
// anychart | Chart | Chart Type

// create a new custom post
function create_anychart_type() {
	register_post_type('anychart',
		array(
			'labels' 		=> array(
				'name' 					=> 'Chart Items',
				'singular_name' 		=> 'Chart Item',
				'add_new' 				=> 'Add New',
				'add_new_item' 			=> 'Add New Chart Item',
				'edit' 					=> 'Edit',
				'edit_item' 			=> 'Edit Chart Item',
				'new_item' 				=> 'New Chart Item',
				'view' 					=> 'View',
				'view_item' 			=> 'View Chart Item',
				'search_items' 			=> 'Search Chart Items',
				'not_found' 			=> 'No Chart Item Found',
				'not_found_in_trash' 	=> 'No Chart Items found in Trash',
				'parent' 				=> 'Parent Chart Item'
			),
			'public' 		=> true,
			'show_ui' 		=> true,
			'menu_position' => 18,
			'supports' 		=> array('title', 'editor'),
			'taxonomies' 	=> array(''),
			'menu_icon' 	=> ANYCHART_PLUGIN_URL . '/images/icon-16.png',
			'has_archive' 	=> true,
			'rewrite' 		=> array('slug' => 'anychart'),
		)
	);
}

// create new taxonomies
function create_anychart_taxonomies() {
	$labels = array(
		'name' 				=> _x('Chart Types', 'taxonomy general name'),
		'singular_name' 	=> _x('Chart Type', 'taxonomy singular name'),
		'search_items' 		=> __('Search Chart Types'),
		'all_items' 		=> __('All Chart Types'),
		'parent_item' 		=> __('Parent Chart Types'),
		'parent_item_colon' => __('Parent Chart Types:'),
		'edit_item' 		=> __('Edit Chart Type'), 
		'update_item' 		=> __('Update Chart Type'),
		'add_new_item' 		=> __('Add New Chart Type'),
		'new_item_name' 	=> __('New Chart Type Name'),
		'menu_name' 		=> __('Chart Types'),
	);

	// create a new hierarchical taxonomy (like categories)
	register_taxonomy('anychart_type', array('anychart'), array(
		'hierarchical' 	=> true,
		'labels' 		=> $labels,
		'show_ui' 		=> true,
		'query_var' 	=> true,
		'rewrite' 		=> array('slug' => 'type'),
	));
}
?>
