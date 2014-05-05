<?php
/**
 * Post Type Functions
 *
 * @package     PLUGIN_NAME
 * @subpackage  Functions
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the Downloads custom post type
 *
 * @since 1.0
 * @return void
 */
function setup_plugin_name_post_types() {
	global $plugin_name_settings;
	
	$archives = defined( 'PLUGIN_NAME_DISABLE_ARCHIVE' ) && PLUGIN_NAME_DISABLE_ARCHIVE ? false : true;

	//Check to see if anything is set in the settings area.
	if( !empty( $plugin_name_settings['plugin_slug'] ) ) {
	    $slug = defined( 'PLUGIN_NAME_SLUG' ) ? PLUGIN_NAME_SLUG : $plugin_name_settings['plugin_slug'];
	} else {
	    $slug = defined( 'PLUGIN_NAME_SLUG' ) ? PLUGIN_NAME_SLUG : 'plugin_slug';
	}
	
	$rewrite  = defined( 'PLUGIN_NAME_DISABLE_REWRITE' ) && PLUGIN_NAME_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$resource_labels =  apply_filters( 'resource_labels', array(
		'name'               => '%2$s',
		'singular_name'      => '%1$s',
		'add_new'            => __( 'Add New', 'plugin_name' ),
		'add_new_item'       => __( 'Add New %1$s', 'plugin_name' ),
		'edit_item'          => __( 'Edit %1$s', 'plugin_name' ),
		'new_item'           => __( 'New %1$s', 'plugin_name' ),
		'all_items'          => __( 'All %2$s', 'plugin_name' ),
		'view_item'          => __( 'View %1$s', 'plugin_name' ),
		'search_items'       => __( 'Search %2$s', 'plugin_name' ),
		'not_found'          => __( 'No %2$s found', 'plugin_name' ),
		'not_found_in_trash' => __( 'No %2$s found in Trash', 'plugin_name' ),
		'parent_item_colon'  => '',
		'menu_name'          => __( '%2$s', 'plugin_name' )
	) );

	foreach ( $resource_labels as $key => $value ) {
	   $resource_labels[ $key ] = sprintf( $value, plugin_name_get_label_singular(), plugin_name_get_label_plural() );
	}

	$resource_args = array(
		'labels'             => $resource_labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-welcome-widgets-menus',
		'query_var'          => true,
		'rewrite'            => $rewrite,
		'map_meta_cap'       => true,
		'has_archive'        => $archives,
		'show_in_nav_menus'  => true,
		'hierarchical'       => false,
		'supports'           => apply_filters( 'plugin_name_supports', array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments', 'author' ) ),
	);
	register_post_type( 'plugin_post_type', apply_filters( 'plugin_name_post_type_args', $resource_args ) );
	
}
add_action( 'init', 'setup_plugin_name_post_types', 1 );

/**
 * Get Default Labels
 *
 * @since 1.0.8.3
 * @return array $defaults Default labels
 */
function plugin_name_get_default_labels() {
	global $plugin_name_settings;

	if( !empty( $plugin_name_settings['network_label_plural'] ) || !empty( $plugin_name_settings['network_label_singular'] ) ) {
	    $defaults = array(
			'singular' => $plugin_name_settings['network_label_singular'],
			'plural'   => $plugin_name_settings['network_label_plural']
	    );
	 } else {
		$defaults = array(
			'singular' => __( 'PostType', 'plugin_name' ),
			'plural'   => __( 'PostTypes', 'plugin_name')
		);
	}
	
	return apply_filters( 'plugin_name_default_name', $defaults );

}

/**
 * Get Singular Label
 *
 * @since 1.0.8.3
 * @return string $defaults['singular'] Singular label
 */
function plugin_name_get_label_singular( $lowercase = false ) {
	$defaults = plugin_name_get_default_labels();
	return ($lowercase) ? strtolower( $defaults['singular'] ) : $defaults['singular'];
}

/**
 * Get Plural Label
 *
 * @since 1.0.8.3
 * @return string $defaults['plural'] Plural label
 */
function plugin_name_get_label_plural( $lowercase = false ) {
	$defaults = plugin_name_get_default_labels();
	return ( $lowercase ) ? strtolower( $defaults['plural'] ) : $defaults['plural'];
}

/**
 * Change default "Enter title here" input
 *
 * @since 1.4.0.2
 * @param string $title Default title placeholder text
 * @return string $title New placeholder text
 */
function plugin_name_change_default_title( $title ) {
     $screen = get_current_screen();

     if  ( 'plugin_name' == $screen->post_type ) {
     	$label = plugin_name_get_label_singular();
        $title = sprintf( __( 'Enter %s title here', 'plugin_name' ), $label );
     }

     return $title;
}
add_filter( 'enter_title_here', 'plugin_name_change_default_title' );
