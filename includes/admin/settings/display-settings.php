<?php
/**
 * Admin Options Page
 *
 * @package     Fifty Framework Updates
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0
 * @global $plugin_name_settings Array of all the SQCASH Options
 * @return void
 */
function plugin_name_settings_page() {
    global $plugin_name_settings;

    $active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], plugin_name_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';

    ob_start();
    ?>
    <div class="wrap">
        <h2>Network <?php echo plugin_name_get_label_plural(); ?> Settings <span style="font-size:12px;">-v. <?php echo PLUGIN_NAME_VERSION; ?></span></h2>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach( plugin_name_get_settings_tabs() as $tab_id => $tab_name ) {

                $tab_url = add_query_arg( array(
                    'settings-updated' => false,
                    'tab' => $tab_id
                ) );

                $active = $active_tab == $tab_id ? ' nav-tab-active' : '';

                echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
                    echo esc_html( $tab_name );
                echo '</a>';
            }
            ?>
        </h2>
        <div id="tab_container">
            <form method="post" action="options.php">
                <table class="form-table">
                <?php
                settings_fields( 'plugin_name_settings' );
                do_settings_fields( 'plugin_name_settings_' . $active_tab, 'plugin_name_settings_' . $active_tab );
                ?>
                </table>
                <style>
                p.submit{
                    border-top:1px solid #DFDFDF;
                    margin-top:30px;
                }
                </style>
                <?php submit_button(); ?>
            </form>
        </div><!-- #tab_container-->
    </div><!-- .wrap -->
    <?php
    echo ob_get_clean();
}
