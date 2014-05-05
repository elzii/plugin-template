<?php
/**
 * Scripts
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
 * Load scripts for the admin area.
 *
 * @since 1.0
 * @author Bryan Monzon
 */
function plugin_name_load_admin_scripts( $hook ) 
{
    global $post,
    $plugin_name_settings,
    $plugin_name_settings_page,
    $wp_version;

    $js_dir  = PLUGIN_NAME_PLUGIN_URL . 'assets/js/';
    $css_dir = PLUGIN_NAME_PLUGIN_URL . 'assets/css/';

    wp_register_script( 'plugin-template-admin-scripts', $js_dir . 'admin-scripts.js', array('jquery'), PLUGIN_NAME_VERSION, true );

    wp_enqueue_script( 'plugin-template-admin-scripts' );
    wp_localize_script( 'plugin-template-admin-scripts', 'plugin_name_vars', array(
        'new_media_ui' => apply_filters( 'plugin_name_use_35_media_ui', 1 ),
        ) 
    );

    if ( $hook == $plugin_name_settings_page ) {
        
        if( function_exists( 'wp_enqueue_media' ) && version_compare( $wp_version, '3.5', '>=' ) ) {
            //call for new media manager
            wp_enqueue_media();
        }
    }

    
}
add_action( 'admin_enqueue_scripts', 'plugin_name_load_admin_scripts', 100 );


/**
 * Load frontend scripts and styles
 *
 * @author Bryan Monzon
 * @since 1.0
 * @return [type] [description]
 */
function plugin_name_load_scripts()
{
    global $post, $wp;

    $js_dir  = PLUGIN_NAME_PLUGIN_URL . 'assets/js/';
    $css_dir = PLUGIN_NAME_PLUGIN_URL . 'assets/css/';

    wp_enqueue_script( 'nam-follow', $js_dir . 'follow.js', array( 'jquery' ), PLUGIN_NAME_VERSION, true );
        wp_localize_script( 'nam-follow', 'nam_vars', array(
            'processing_error' => __( 'There was a problem processing your request.', 'nam' ),
            'login_required'   => __( 'Oops, you must be logged-in to follow users.', 'nam' ),
            'logged_in'        => is_user_logged_in() ? 'true' : 'false',
            'ajaxurl'          => admin_url( 'admin-ajax.php' ),
            'nonce'            => wp_create_nonce( 'follow_nonce' )
        ) );


    wp_register_script( 'nam-updates-scripts', $js_dir . 'updates.js', array('jquery'), PLUGIN_NAME_VERSION, true );
    wp_enqueue_script( 'nam-updates-scripts' );
    wp_localize_script( 'nam-updates-scripts', 'nam_updates_form_vars', array( 
           'ajaxurl'        => admin_url( 'admin-ajax.php' ),
           'redirecturl'    => home_url( $wp->request ), //current url
           'loadingmessage' => __('Posting update...'),
           'security_nonce' => wp_create_nonce( 'form-update-nonce' )
       ));


    wp_register_style( 'form-styles', $css_dir . 'form-styles.css', false, PLUGIN_NAME_VERSION, false );
    wp_enqueue_style( 'form-styles' );

    


}
add_action( 'wp_enqueue_scripts', 'plugin_name_load_scripts' );