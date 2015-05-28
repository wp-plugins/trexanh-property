<?php

/* 
 * Module Frontend setup.
 * Manage: search, shortcode, themes, query custom post type
 */

namespace TreXanhProperty\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Frontend\Frontend;

class Module
{
    private static $initiated = false;

    public static function init() {
        if ( ! self::$initiated ) {
            self::init_hooks();
        }
    }
    
    public static function init_hooks() {
        self::$initiated = true;

        add_action( 'template_include', array('TreXanhProperty\Frontend\TemplateLoader', 'template_loader'));
        add_action( 'wp_enqueue_scripts', array('TreXanhProperty\Frontend\Module', 'wp_enqueue_scripts') );
        add_action( 'wp_head', array('TreXanhProperty\Frontend\Module', 'include_map_info_box_template') );
        Frontend::init();
    }
    
    public static function wp_enqueue_scripts() {
        //get plugin folder name
        $plugin_base_dir = explode(DIRECTORY_SEPARATOR, rtrim(TREXANHPROPERTY__PLUGIN_DIR, DIRECTORY_SEPARATOR));
        if (empty($plugin_base_dir)) {
            return;
        }
        $plugin_base_dir = array_pop($plugin_base_dir);
        
        wp_enqueue_style('dashicons');
        wp_enqueue_style( 'txp-style-name', plugins_url( $plugin_base_dir . '/assets/css/front-end.css') );
        wp_enqueue_style( 'txp-bxslider-style', plugins_url( $plugin_base_dir . '/assets/lib/dw-bxslider-4/dist/jquery.bxslider.css') );
        wp_enqueue_style( 'txp-lightbox-style', plugins_url( $plugin_base_dir . '/assets/lib/prettyphoto/css/prettyPhoto.css') );

        //use google map without sensor input (your current location)
        wp_enqueue_script( 'google-script-geocode', 'http://maps.google.com/maps/api/js?sensor=false' );
        wp_enqueue_script( 'txp-script-geocode', plugins_url( $plugin_base_dir . '/assets/js/geocode.js' ), array( 'jquery' ) );
        wp_enqueue_script( 'txp-bxslider-script', plugins_url( $plugin_base_dir . '/assets/lib/dw-bxslider-4/dist/jquery.bxslider.min.js' ), array( 'jquery' ) );
        wp_enqueue_script( 'txp-lightbox-script', plugins_url( $plugin_base_dir . '/assets/lib/prettyphoto/js/jquery.prettyPhoto.js' ), array( 'jquery' ) );
        wp_register_script('my-upload', plugins_url( $plugin_base_dir . '/assets/js/photo-gallery.js'), array('jquery'));
        wp_enqueue_script('my-upload');
    }
    
    public static function include_map_info_box_template() {
        echo txp_get_template_part('js-templates/map-info-box.php');
    }
}