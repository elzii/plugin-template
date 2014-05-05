<?php
/**
 * Admin Notices
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
 * Admin Messages
 *
 * @since 1.0
 * @global $plugin_name_settings Array of all the options
 * @return void
 */
function plugin_name_admin_messages() {
    global $plugin_name_settings;

    settings_errors( 'plugin_name-notices' );
}
add_action( 'admin_notices', 'plugin_name_admin_messages' );


/**
 * Dismisses admin notices when Dismiss links are clicked
 *
 * @since 1.0
 * @return void
*/
function plugin_name_dismiss_notices() {

    $notice = isset( $_GET['plugin_name_notice'] ) ? $_GET['plugin_name_notice'] : false;

    if( ! $notice )
        return; // No notice, so get out of here

    update_user_meta( get_current_user_id(), '_plugin_name_' . $notice . '_dismissed', 1 );

    wp_redirect( remove_query_arg( array( 'plugin_name_action', 'plugin_name_notice' ) ) ); exit;

}
add_action( 'plugin_name_dismiss_notices', 'plugin_name_dismiss_notices' );
