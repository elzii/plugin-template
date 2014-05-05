<?php
/**
 * Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Example function description
 * 
 * @param  [type] $query [description]
 * @return [type]        [description]
 */
function plugin_name_example_function() {
    
    global $plugin_name_settings;

    if ( is_admin() || ! $query->is_main_query() )
        return;

    // Do stuff here
}
add_action( 'XYZ_example_function', 'plugin_name_example_function', 1 );



