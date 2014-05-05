<?php
/**
 * Admin Pages
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
 * Creates the admin menu pages and assigns them their global variables
 *
 * @since  1.0
 * @global  $plugin_name_settings_page
  * @return void
 */
function plugin_name_add_menu_page() {
    global $plugin_name_settings_page;

    $plugin_name_settings_page = add_submenu_page( 'edit.php?post_type=plugin_name', __( 'Settings', 'plugin_name' ), __( 'Settings', 'plugin_name'), 'edit_pages', 'plugin-template-settings', 'plugin_name_settings_page' );
    
}
add_action( 'admin_menu', 'plugin_name_add_menu_page', 11 );
