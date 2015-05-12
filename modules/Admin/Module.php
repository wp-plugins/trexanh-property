<?php

/**
 * Bootstrap file for Admin Module
 */
namespace TreXanhProperty\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Admin\SettingPage;
use TreXanhProperty\Admin\SystemPage;

use TreXanhProperty\Core\Property;
use TreXanhProperty\Core\Order;

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

        add_action( 'admin_menu', array( 'TreXanhProperty\Admin\Module', 'admin_menu' ) );

        //Admin : list
        add_filter( 'manage_edit-property_columns', array( 'TreXanhProperty\Admin\PropertyList', 'set_custom_edit_property_columns' ) );
        add_action( 'manage_property_posts_custom_column' , array( 'TreXanhProperty\Admin\PropertyList', 'custom_property_column' ) , 10, 2 );
        
        add_filter( 'manage_edit-' . Order::POST_TYPE . '_columns', array( 'TreXanhProperty\Admin\OrderList', 'set_custom_edit_order_columns' ) );
        add_action( 'manage_' . Order::POST_TYPE .'_posts_custom_column' , array( 'TreXanhProperty\Admin\OrderList', 'custom_order_column' ) , 10, 2 );      

        //Admin : New form, edit form
        add_action( 'add_meta_boxes_' . Property::get_post_type(), array('TreXanhProperty\Admin\PropertyMetabox', 'add_meta_box') );
        add_action( 'save_post', array('TreXanhProperty\Admin\PropertyMetabox','save_meta_box_data'), 10, 3);
        add_action( 'add_meta_boxes_' . Order::POST_TYPE, array( 'TreXanhProperty\Admin\OrderMetabox', 'add_meta_box' ) );
        add_action( 'save_post', array('TreXanhProperty\Admin\OrderMetabox','save_order'), 10, 3);
        
        //Admin : css, js
        add_action( 'admin_enqueue_scripts', array('TreXanhProperty\Admin\Module', 'admin_enqueue_scripts') );
        
        $settingPage = new SettingPage();
        $settingPage->init_hooks();
        
        $systemPage = new SystemPage();
        $systemPage->init_hooks();
    }
    
    public static function get_plugin_folder_name() {
        $plugin_base_dir = explode(DIRECTORY_SEPARATOR, rtrim(TREXANHPROPERTY__PLUGIN_DIR, DIRECTORY_SEPARATOR));
        if (empty($plugin_base_dir)) {
            return;
        }
        return array_pop($plugin_base_dir);
    }
        
    public static function admin_enqueue_scripts() {
        //get plugin folder name
        $plugin_base_dir = self::get_plugin_folder_name();
        if (empty($plugin_base_dir)) {
            return;
        }
        wp_enqueue_style( 'txp-style-name', plugins_url( $plugin_base_dir . '/modules/Admin/assets/css/style.css') );
        //use google map without sensor input (your current location)
        wp_enqueue_script( 'google-script-geocode', 'http://maps.google.com/maps/api/js?sensor=false' );
        wp_enqueue_script( 'txp-script-geocode', plugins_url( $plugin_base_dir . '/assets/js/geocode.js'), array( 'jquery' ) );
        wp_enqueue_media();
        wp_register_script('my-upload', plugins_url( $plugin_base_dir . '/modules/Admin/assets/js/photo-gallery.js'), array('jquery','media-upload','thickbox'));
        wp_enqueue_script('my-upload');
    }
    
    public static function admin_init() {
        
    }
    
    public static function admin_menu() {
        $plugin_base_dir = self::get_plugin_folder_name();
        if (empty($plugin_base_dir)) {
            return;
        }
        // plug in homepage
        add_menu_page(
            'TreXanh Property Homepage',
            'Trexanh Property',
            'manage_options',
            'trexanh_property_homepage',
            array( 'TreXanhProperty\Admin\Module', 'create_homepage' ),
            'dashicons-admin-home',
            '25.1'
        );        
    }
    
    /**
     * Options page callback
     */
    public static function create_homepage()
    {
        ?>
        <div class="wrap">
            <h2>TreXanh Property Setting page</h2>
        </div>
        <?php
    }
}