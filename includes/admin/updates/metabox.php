<?php
/**
 * Metabox Functions
 *
 * @package     Updates
 * @subpackage  Admin/Classes
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** All Downloads *****************************************************************/

/**
 * Register all the meta boxes for the Download custom post type
 *
 * @since 1.0
 * @return void
 */
function plugin_name_add_meta_box() {

    $post_types = apply_filters( 'plugin_name_metabox_post_types' , array( 'simple_docs' ) );

    foreach ( $post_types as $post_type ) {

        /** Class Configuration */
        //add_meta_box( 'updateinfo', sprintf( __( '%1$s Disable', 'plugin_name' ), plugin_name_get_label_singular(), plugin_name_get_label_plural() ),  'plugin_name_render_meta_box', $post_type, 'side', 'core' );

        
    }
}
add_action( 'add_meta_boxes', 'plugin_name_add_meta_box' );


/**
 * Sabe post meta when the save_post action is called
 *
 * @since 1.0
 * @param int $post_id Download (Post) ID
 * @global array $post All the data of the the current post
 * @return void
 */
function plugin_name_meta_box_save( $post_id) {
    global $post, $plugin_name_settings;

    if ( ! isset( $_POST['plugin_name_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['plugin_name_meta_box_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) )
        return $post_id;

    if ( isset( $post->post_type ) && $post->post_type == 'revision' )
        return $post_id;




    // The default fields that get saved
    $fields = apply_filters( 'plugin_name_metabox_fields_save', array(
            'plugin_name_disable_link_to',


        )
    );


    foreach ( $fields as $field ) {
        if ( ! empty( $_POST[ $field ] ) ) {
            $new = apply_filters( 'updates_metabox_save_' . $field, $_POST[ $field ] );
            update_post_meta( $post_id, $field, $new );
        } else {
            delete_post_meta( $post_id, $field );
        }
    }
}
add_action( 'save_post', 'plugin_name_meta_box_save' );





/** Class Configuration *****************************************************************/

/**
 * Class Metabox
 *
 * Extensions (as well as the core plugin) can add items to the main download
 * configuration metabox via the `plugin_name_meta_box_fields` action.
 *
 * @since 1.0
 * @return void
 */
function plugin_name_render_meta_box() {
    global $post, $plugin_name_settings;

    do_action( 'plugin_name_meta_box_fields', $post->ID );
    wp_nonce_field( basename( __FILE__ ), 'plugin_name_meta_box_nonce' );
}




function plugin_name_render_fields( $post )
{
    global $post, $plugin_name_settings; 

    /*$postmeta_check = get_post_meta($post->ID);
    echo '<pre>';
    var_dump($postmeta_check);
    echo '</pre>';*/
    $diable_link_to = get_post_meta( $post->ID, 'plugin_name_disable_link_to', true);
    ?>
    
    <div id="plugin_name_disable_link_to">
        <p>
            <label for="plugin_name_disable_link_to">
                <input type="checkbox" name="plugin_name_disable_link_to" value="1"<?php checked(1, $diable_link_to ); ?> >
                Disable link to update single
            </label>
        </p>
    </div>
    


    <?php

}
add_action( 'plugin_name_meta_box_fields', 'plugin_name_render_fields', 10 );

