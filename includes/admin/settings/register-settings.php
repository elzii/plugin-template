<?php
/**
 * Register Settings
 *
 * @package     PLUGIN_NAME
 * @subpackage  Functions
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0
 * @return mixed
 */
function plugin_name_get_option( $key = '', $default = false ) {
    global $plugin_name_settings;
    return isset( $plugin_name_settings[ $key ] ) ? $plugin_name_settings[ $key ] : $default;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0
 * @return array PLUGIN_NAME settings
 */
function plugin_name_get_settings() {

    $settings = get_option( 'plugin_name_settings' );
    if( empty( $settings ) ) {

        // Update old settings with new single option

        $general_settings = is_array( get_option( 'plugin_name_settings_general' ) )    ? get_option( 'plugin_name_settings_general' )      : array();


        $settings = array_merge( $general_settings );

        update_option( 'plugin_name_settings', $settings );
    }
    return apply_filters( 'plugin_name_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since 1.0
 * @return void
*/
function plugin_name_register_settings() {

    if ( false == get_option( 'plugin_name_settings' ) ) {
        add_option( 'plugin_name_settings' );
    }

    foreach( plugin_name_get_registered_settings() as $tab => $settings ) {

        add_settings_section(
            'plugin_name_settings_' . $tab,
            __return_null(),
            '__return_false',
            'plugin_name_settings_' . $tab
        );

        foreach ( $settings as $option ) {
            add_settings_field(
                'plugin_name_settings[' . $option['id'] . ']',
                $option['name'],
                function_exists( 'plugin_name_' . $option['type'] . '_callback' ) ? 'plugin_name_' . $option['type'] . '_callback' : 'plugin_name_missing_callback',
                'plugin_name_settings_' . $tab,
                'plugin_name_settings_' . $tab,
                array(
                    'id'      => $option['id'],
                    'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
                    'name'    => $option['name'],
                    'section' => $tab,
                    'size'    => isset( $option['size'] ) ? $option['size'] : null,
                    'options' => isset( $option['options'] ) ? $option['options'] : '',
                    'std'     => isset( $option['std'] ) ? $option['std'] : ''
                )
            );
        }

    }

    // Creates our settings in the options table
    register_setting( 'plugin_name_settings', 'plugin_name_settings', 'plugin_name_settings_sanitize' );

}
add_action('admin_init', 'plugin_name_register_settings');

/**
 * Retrieve the array of plugin settings
 *
 * @since 1.8
 * @return array
*/
function plugin_name_get_registered_settings() {

    $pages = get_pages();
    $pages_options = array( 0 => '' ); // Blank option
    if ( $pages ) {
        foreach ( $pages as $page ) {
            $pages_options[ $page->ID ] = $page->post_title;
        }
    }

    /**
     * 'Whitelisted' PLUGIN_NAME settings, filters are provided for each settings
     * section to allow extensions and other plugins to add their own settings
     */
    $plugin_name_settings = array(
        /** General Settings */
        'general' => apply_filters( 'plugin_name_settings_general',
            array(
                'basic_settings' => array(
                    'id' => 'basic_settings',
                    'name' => '<strong>' . __( 'Basic Settings', 'plugin_name' ) . '</strong>',
                    'desc' => '<hr>',
                    'type' => 'header'
                ),
                'network_slug' => array(
                    'id' => 'network_slug',
                    'name' => __( plugin_name_get_label_plural() . ' URL Slug', 'plugin_name' ),
                    'desc' => __( 'Enter the slug you would like to use for your ' . strtolower( plugin_name_get_label_plural() ) . '.'  , 'plugin_name' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => strtolower( plugin_name_get_label_plural() )
                ),
                'network_label_plural' => array(
                    'id' => 'network_label_plural',
                    'name' => __( plugin_name_get_label_plural() . ' Label Plural', 'plugin_name' ),
                    'desc' => __( 'Enter the label you would like to use for your ' . strtolower( plugin_name_get_label_plural() ) . '.', 'plugin_name' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => plugin_name_get_label_plural()
                ),
                'network_label_singular' => array(
                    'id' => 'network_label_singular',
                    'name' => __( plugin_name_get_label_singular() . ' Label Singular', 'plugin_name' ),
                    'desc' => __( 'Enter the label you would like to use for your ' . strtolower( plugin_name_get_label_singular() ) . '.', 'plugin_name' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => plugin_name_get_label_singular()
                ),
                'updates_posts_per_page' => array(
                    'id' => 'updates_posts_per_page',
                    'name' => __( 'Archive Posts Page', 'plugin_name' ),
                    'desc' => __( 'Enter the number of posts you would like to display on the archive template'  , 'plugin_name' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => '10'
                ),
            )
        ),
        
    );

    return $plugin_name_settings;
}

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function plugin_name_header_callback( $args ) {
    $html = '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
    echo $html;
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 */
function plugin_name_checkbox_callback( $args ) {
    global $plugin_name_settings;

    $checked = isset($plugin_name_settings[$args['id']]) ? checked(1, $plugin_name_settings[$args['id']], false) : '';
    $html = '<input type="checkbox" id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>';
    $html .= '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 */
function plugin_name_multicheck_callback( $args ) {
    global $plugin_name_settings;

    foreach( $args['options'] as $key => $option ):
        if( isset( $plugin_name_settings[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
        echo '<input name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
        echo '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;
    echo '<p class="description">' . $args['desc'] . '</p>';
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 1.3.3
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 */
function plugin_name_radio_callback( $args ) {
    global $plugin_name_settings;

    foreach ( $args['options'] as $key => $option ) :
        $checked = false;

        if ( isset( $plugin_name_settings[ $args['id'] ] ) && $plugin_name_settings[ $args['id'] ] == $key )
            $checked = true;
        elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $plugin_name_settings[ $args['id'] ] ) )
            $checked = true;

        echo '<input name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"" id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
        echo '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;

    echo '<p class="description">' . $args['desc'] . '</p>';
}



/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 */
function plugin_name_text_callback( $args ) {
    global $plugin_name_settings;

    if ( isset( $plugin_name_settings[ $args['id'] ] ) )
        $value = $plugin_name_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text" id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * PLUGIN_NAME Hidden Text Field Callback
 *
 * Renders text fields (Hidden, for necessary values in plugin_name_settings in the wp_options table)
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 * @todo refactor it is not needed entirely
 */
function plugin_name_hidden_callback( $args ) {
    global $plugin_name_settings;

    $hidden = isset($args['hidden']) ? $args['hidden'] : false;

    if ( isset( $plugin_name_settings[ $args['id'] ] ) )
        $value = $plugin_name_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="hidden" class="' . $size . '-text" id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['std'] . '</label>';

    echo $html;
}




/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 */
function plugin_name_textarea_callback( $args ) {
    global $plugin_name_settings;

    if ( isset( $plugin_name_settings[ $args['id'] ] ) )
        $value = $plugin_name_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<textarea class="large-text" cols="50" rows="5" id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    $html .= '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since 1.3
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 */
function plugin_name_password_callback( $args ) {
    global $plugin_name_settings;

    if ( isset( $plugin_name_settings[ $args['id'] ] ) )
        $value = $plugin_name_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="password" class="' . $size . '-text" id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
    $html .= '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since 1.3.1
 * @param array $args Arguments passed by the setting
 * @return void
 */
function plugin_name_missing_callback($args) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'plugin_name' ), $args['id'] );
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 */
function plugin_name_select_callback($args) {
    global $plugin_name_settings;

    if ( isset( $plugin_name_settings[ $args['id'] ] ) )
        $value = $plugin_name_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $name ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since 1.8
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 */
function plugin_name_color_select_callback( $args ) {
    global $plugin_name_settings;

    if ( isset( $plugin_name_settings[ $args['id'] ] ) )
        $value = $plugin_name_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $color ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @global $wp_version WordPress Version
 */
function plugin_name_rich_editor_callback( $args ) {
    global $plugin_name_settings, $wp_version;

    if ( isset( $plugin_name_settings[ $args['id'] ] ) )
        $value = $plugin_name_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
        $html = wp_editor( stripslashes( $value ), 'plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']', array( 'textarea_name' => 'plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']' ) );
    } else {
        $html = '<textarea class="large-text" rows="10" id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    }

    $html .= '<br/><label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 */
function plugin_name_upload_callback( $args ) {
    global $plugin_name_settings;

    if ( isset( $plugin_name_settings[ $args['id'] ] ) )
        $value = $plugin_name_settings[$args['id']];
    else
        $value = isset($args['std']) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text plugin_name_upload_field" id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<span>&nbsp;<input type="button" class="plugin_name_settings_upload_button button-secondary" value="' . __( 'Upload File', 'plugin_name' ) . '"/></span>';
    $html .= '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since 1.6
 * @param array $args Arguments passed by the setting
 * @global $plugin_name_settings Array of all the PLUGIN_NAME Options
 * @return void
 */
function plugin_name_color_callback( $args ) {
    global $plugin_name_settings;

    if ( isset( $plugin_name_settings[ $args['id'] ] ) )
        $value = $plugin_name_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $default = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="plugin_name-color-picker" id="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" name="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
    $html .= '<label for="plugin_name_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}



/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since 1.0.8.2
 * @param array $args Arguments passed by the setting
 * @return void
 */
function plugin_name_hook_callback( $args ) {
    do_action( 'plugin_name_' . $args['id'] );


    
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since 1.0.8.2
 * @param array $input The value inputted in the field
 * @return string $input Sanitizied value
 */
function plugin_name_settings_sanitize( $input = array() ) {

    global $plugin_name_settings;

    parse_str( $_POST['_wp_http_referer'], $referrer );

    $output    = array();
    $settings  = plugin_name_get_registered_settings();
    $tab       = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
    $post_data = isset( $_POST[ 'plugin_name_settings_' . $tab ] ) ? $_POST[ 'plugin_name_settings_' . $tab ] : array();

    $input = apply_filters( 'plugin_name_settings_' . $tab . '_sanitize', $post_data );

    // Loop through each setting being saved and pass it through a sanitization filter
    foreach( $input as $key => $value ) {

        // Get the setting type (checkbox, select, etc)
        $type = isset( $settings[ $key ][ 'type' ] ) ? $settings[ $key ][ 'type' ] : false;

        if( $type ) {
            // Field type specific filter
            $output[ $key ] = apply_filters( 'plugin_name_settings_sanitize_' . $type, $value, $key );
        }

        // General filter
        $output[ $key ] = apply_filters( 'plugin_name_settings_sanitize', $value, $key );
    }


    // Loop through the whitelist and unset any that are empty for the tab being saved
    if( ! empty( $settings[ $tab ] ) ) {
        foreach( $settings[ $tab ] as $key => $value ) {

            // settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
            if( is_numeric( $key ) ) {
                $key = $value['id'];
            }

            if( empty( $_POST[ 'plugin_name_settings_' . $tab ][ $key ] ) ) {
                unset( $plugin_name_settings[ $key ] );
            }

        }
    }

    // Merge our new settings with the existing
    $output = array_merge( $plugin_name_settings, $output );

    // @TODO: Get Notices Working in the backend.
    add_settings_error( 'plugin_name-notices', '', __( 'Settings Updated', 'plugin_name' ), 'updated' );

    return $output;

}

/**
 * Sanitize text fields
 *
 * @since 1.8
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function plugin_name_sanitize_text_field( $input ) {
    return trim( $input );
}
add_filter( 'plugin_name_settings_sanitize_text', 'plugin_name_sanitize_text_field' );

/**
 * Retrieve settings tabs
 *
 * @since 1.8
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function plugin_name_get_settings_tabs() {

    $settings = plugin_name_get_registered_settings();

    $tabs            = array();
    $tabs['general'] = __( 'General', 'plugin_name' );

    return apply_filters( 'plugin_name_settings_tabs', $tabs );
}
