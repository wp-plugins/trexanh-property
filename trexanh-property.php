<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
Plugin Name: TreXanh Property
Plugin URI: http://trexanhlab.com/wp/trexanh-property
Description: A clean, powerful and easy to use real estate solution on Wordpress.
Version: 0.6
Author: trexanhlab
Author URI: http://trexanhlab.com
License: GPLv2 or later
Text Domain: txp
*/

define( 'TREXANHPROPERTY_DB_VERSION', '0.4' );
define( 'TREXANHPROPERTY_VERSION', '0.6' );
define( 'TREXANHPROPERTY__MINIMUM_WP_VERSION', '4.1.0' );
define( 'TREXANHPROPERTY__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TREXANHPROPERTY__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TREXANHPROPERTY_PREFIX', 'trexanhproperty_' );
require_once( TREXANHPROPERTY__PLUGIN_DIR . 'vendor/autoload.php' );

\TreXanhProperty\Core\Module::init();

\TreXanhProperty\PluginSetup\Module::init();

if ( is_admin() ) {
    \TreXanhProperty\Admin\Module::init();
} else {
    \TreXanhProperty\Frontend\Module::init();

    \TreXanhProperty\SubmitProperty\Module::init();
}

// register_activation_hook() must be called from the main plugin file
// @link: https://wordpress.org/support/topic/register_activation_hook-does-not-work
/* Runs when plugin is activated */
register_activation_hook(__FILE__, array('\TreXanhProperty\PluginSetup\PluginSetup', 'activation')); 
