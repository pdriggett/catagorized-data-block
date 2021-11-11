<?php
/**
 * Plugin Name:       Categorized Data Block
 * Description:       Example block written with ESNext standard and JSX support – build step required.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       categorized-data-block
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */
function create_block_categorized_data_block_block_init() {
	register_block_type( __DIR__ );
}
add_action( 'init', 'create_block_categorized_data_block_block_init' );

add_action( 'rest_api_init', function () {
	register_rest_route( 'categorized-data-block/v1', '/stone/(?P<stone_name>[a-zA-Z0-9]+)', array(
		'methods' => 'GET',
		'callback' => 'GetStoneDetails',
		'permission_callback' => function () {
			return current_user_can( 'edit_others_posts' );
		}
	) );

	register_rest_route( 'categorized-data-block/v1', '/stones', array(
		'methods' => 'GET',
		'callback' => 'GetStones',
		'permission_callback' => function () {
			return current_user_can( 'edit_others_posts' );
		},
	) );
} );

/**
 * Get all the entries for a stone
 * 
 * @param array $data Options for the function.
 * @return map with entries
 */
function GetStoneDetails( $data ) {
	global $wpdb;
	$tablenameCategories = $wpdb->prefix."categorizeddataCategories";
	$tablenameStones = $wpdb->prefix."categorizeddataStones";
	$tablenameEntries = $wpdb->prefix."categorizeddataEntries";

	$stone_name = ( isset( $data['stone_name'] ) ) ? sanitize_text_field($data['stone_name']) : '';

	$result = $wpdb->get_results("SELECT * FROM " .$tablenameStones. " WHERE name='" .$data['stone_name']."'");

	$stoneid = NULL;

	foreach($result as $stone) {
		if(isset($stone->idStones)) {
			$stoneid = $stone->idStones;
		}	
	}
	
	$entriesList = "";

	if( $stoneid != NULL) {
  		$entriesList = $wpdb->get_results("SELECT E.idEntries, E.entry, C.idCategories, C.category, S.idStones, S.name
										FROM ".$tablenameEntries." E
											INNER JOIN ".$tablenameStones." S On E.Stones_idStones=S.idStones
											INNER JOIN ".$tablenameCategories." C On E.Categories_idCategories=C.idCategories
										WHERE Stones_idStones = ".$stoneid."
										order by Categories_idCategories asc");
	}

	$categories = [];

	foreach($entriesList as $entry) {
		$category = $entry->category;
		
		if(!array_key_exists($category,$categories)) {
			$categories[$category] = [];
		}

		array_push($categories[$category],$entry->entry);
	}
	
	return $categories;
}

/**
 * Get all the entries for a stone
 * 
 * @param None
 * @return List of stone names
 */
function GetStones() {
	global $wpdb;
	$tablenameCategories = $wpdb->prefix."categorizeddataCategories";
	$tablenameStones = $wpdb->prefix."categorizeddataStones";
	$tablenameEntries = $wpdb->prefix."categorizeddataEntries";

	$result = $wpdb->get_results("SELECT * FROM " .$tablenameStones);

	$stones = [];
	foreach($result as $stone) {
		array_push($stones,$stone->name);
	}
	
	return $stones;
}