<?php 
/**
 * Plugin Name: PLUGIN_TEMPLATE
 * Plugin URI: PLUGIN_URI
 * Description: PLUGIN_DESC
 * Version: 1.0
 * Author: AUTHOR_NAME
 * Author URI: AUTHOR_URI
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'PLUGIN_NAME' ) ) :


/**
 * Main PLUGIN_NAME Class
 *
 * @since 1.0 */
final class PLUGIN_NAME {

  /**
   * @var PLUGIN_NAME Instance
   * @since 1.0
   */
  private static $instance;


  /**
   * PLUGIN_NAME Instance / Constructor
   *
   * Insures only one instance of PLUGIN_NAME exists in memory at any one
   * time & prevents needing to define globals all over the place. 
   * Inspired by and credit to PLUGIN_NAME.
   *
   * @since 1.0
   * @static
   * @uses PLUGIN_NAME::setup_globals() Setup the globals needed
   * @uses PLUGIN_NAME::includes() Include the required files
   * @uses PLUGIN_NAME::setup_actions() Setup the hooks and actions
   * @see PLUGIN_NAME()
   * @return void
   */
  public static function instance() {
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof PLUGIN_NAME ) ) {
      self::$instance = new PLUGIN_NAME;
      self::$instance->setup_constants();
      self::$instance->includes();
      // self::$instance->load_textdomain();
      // use @examples from public vars defined above upon implementation
    }
    return self::$instance;
  }



  /**
   * Setup plugin constants
   * @access private
   * @since 1.0 
   * @return void
   */
  private function setup_constants() {
    // Plugin version
    if ( ! defined( 'PLUGIN_NAME_VERSION' ) )
      define( 'PLUGIN_NAME_VERSION', '1.0' );

    // Plugin Folder Path
    if ( ! defined( 'PLUGIN_NAME_PLUGIN_DIR' ) )
      define( 'PLUGIN_NAME_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

    // Plugin Folder URL
    if ( ! defined( 'PLUGIN_NAME_PLUGIN_URL' ) )
      define( 'PLUGIN_NAME_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

    // Plugin Root File
    if ( ! defined( 'PLUGIN_NAME_PLUGIN_FILE' ) )
      define( 'PLUGIN_NAME_PLUGIN_FILE', __FILE__ );

    if ( ! defined( 'PLUGIN_NAME_DEBUG' ) )
      define ( 'PLUGIN_NAME_DEBUG', true );

    // if(!defined('PWUF_FOLLOW_DIR')) define('PWUF_FOLLOW_DIR', dirname( __FILE__ ) );
    // if(!defined('PWUF_FOLLOW_URL')) define('PWUF_FOLLOW_URL', plugin_dir_url( __FILE__ ) );
 
  }



  /**
   * Include required files
   * @access private
   * @since 1.0
   * @return void
   */
  private function includes() {
    global $plugin_name_settings, $wp_version;

    require_once PLUGIN_NAME_PLUGIN_DIR . '/includes/admin/settings/register-settings.php';
    $plugin_name_settings = plugin_name_get_settings();

    // Required Plugin Files
    require_once PLUGIN_NAME_PLUGIN_DIR . '/includes/functions.php';
    require_once PLUGIN_NAME_PLUGIN_DIR . '/includes/posttypes.php';
    require_once PLUGIN_NAME_PLUGIN_DIR . '/includes/scripts.php';
    require_once PLUGIN_NAME_PLUGIN_DIR . '/includes/shortcodes.php';

    if( is_admin() ){
        //Admin Required Plugin Files
        require_once PLUGIN_NAME_PLUGIN_DIR . '/includes/admin/admin-pages.php';
        require_once PLUGIN_NAME_PLUGIN_DIR . '/includes/admin/admin-notices.php';
        require_once PLUGIN_NAME_PLUGIN_DIR . '/includes/admin/settings/display-settings.php';

    }

    require_once PLUGIN_NAME_PLUGIN_DIR . '/includes/install.php';

  }

} /* end PLUGIN_NAME class */
endif; // End if class_exists check


/**
 * Main function for returning PLUGIN_NAME Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $sqcash = PLUGIN_NAME(); ?>
 *
 * @since 1.0
 * @return object The one true PLUGIN_NAME Instance
 */
function PLUGIN_NAME() {
  return PLUGIN_NAME::instance();
}


/**
 * Initiate
 * Run the PLUGIN_NAME() function, which runs the instance of the PLUGIN_NAME class.
 */
PLUGIN_NAME();



/**
 * Debugging
 * @since 1.0
 */
if ( PLUGIN_NAME_DEBUG ) {
  ini_set('display_errors','On');
  error_reporting(E_ALL);
}


