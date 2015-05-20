<?php
/**
 * TreXanhProperty Uninstall
 *
 * Uninstalling TreXanhProperty -> deletes property, order, pages and options.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

define( 'TREXANHPROPERTY__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TREXANHPROPERTY_PREFIX', 'trexanhproperty_' );
require_once( TREXANHPROPERTY__PLUGIN_DIR . 'vendor/autoload.php' );

$system_settings = \TreXanhProperty\Core\Config::get_system();

if ( ! empty( $system_settings['delete_on_uninstall'] ) ) {
    \TreXanhProperty\PluginSetup\PluginSetup::uninstall();
}
