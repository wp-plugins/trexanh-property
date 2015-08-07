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
use TreXanhProperty\Core\PropertyType;

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
        
        add_action( 'admin_menu', array( 'TreXanhProperty\Admin\Module', 'add_sub_menu' ), 1 );

        //Property - Property Type
        add_filter( 'manage_edit-' . Property::get_post_type() . '_columns', array( 'TreXanhProperty\Admin\PropertyList', 'set_custom_edit_property_columns' ) );
        add_action( 'manage_' . Property::get_post_type() . '_posts_custom_column' , array( 'TreXanhProperty\Admin\PropertyList', 'custom_property_column' ) , 10, 2 );
            //Admin : New form, edit form
        add_action( 'add_meta_boxes_' . Property::get_post_type(), array('TreXanhProperty\Admin\PropertyMetabox', 'add_meta_box') );
        add_action( 'save_post', array('TreXanhProperty\Admin\PropertyMetabox','save_meta_box_data'), 10, 3);                 

        //Order
            //Admin : list        
        add_filter( 'manage_edit-' . Order::POST_TYPE . '_columns', array( 'TreXanhProperty\Admin\OrderList', 'set_custom_edit_order_columns' ) );
        add_action( 'manage_' . Order::POST_TYPE .'_posts_custom_column' , array( 'TreXanhProperty\Admin\OrderList', 'custom_order_column' ) , 10, 2 );              
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
        wp_enqueue_style( 'dragula-style', plugins_url( $plugin_base_dir . '/assets/lib/dragula.js/dist/dragula.min.css') );
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style("wp-jquery-ui-dialog");
        wp_register_script( 'txl-dialog-script', plugins_url( $plugin_base_dir . '/modules/Admin/assets/js/dialog.js' ), array( 'jquery-ui-dialog' ) );
        wp_enqueue_script( 'txl-dialog-script' );
        //use google map without sensor input (your current location)
        wp_enqueue_script( 'google-script-geocode', 'http://maps.google.com/maps/api/js?sensor=false' );
        wp_enqueue_script( 'txp-script-geocode', plugins_url( $plugin_base_dir . '/assets/js/geocode.js'), array( 'jquery' ) );
        wp_enqueue_media();
        wp_register_script('my-upload', plugins_url( $plugin_base_dir . '/modules/Admin/assets/js/photo-gallery.js'), array('jquery','media-upload','thickbox'));
        wp_enqueue_script('my-upload');
        wp_register_script( 'dragula-script', plugins_url( $plugin_base_dir . '/assets/lib/dragula.js/dist/dragula.min.js' ), array( 'jquery' ) );
        wp_enqueue_script( 'dragula-script' );
        wp_register_script( 'angular-script', plugins_url( $plugin_base_dir . '/assets/lib/angular/angular.min.js' ) );
        wp_enqueue_script( 'angular-script' );
        wp_register_script( 'helper-functions-script', plugins_url( $plugin_base_dir . '/modules/Admin/assets/js/property-config/helper-functions.js' ) );
        wp_enqueue_script( 'helper-functions-script' );
        wp_register_script( 'directives-script', plugins_url( $plugin_base_dir . '/modules/Admin/assets/js/property-config/directives.js' ), array( 'angular-script' ) );
        wp_enqueue_script( 'directives-script' );
        wp_register_script(
            'property-type-script',
            plugins_url( $plugin_base_dir . '/modules/Admin/assets/js/property-config/property-type.js' ),
            array( 'angular-script', 'helper-functions-script', 'directives-script' )
        );
        wp_enqueue_script( 'property-type-script' );
        wp_register_script( 'custom-attribute-config-script', plugins_url( $plugin_base_dir . '/modules/Admin/assets/js/property-config/custom-attribute.js' ), array( 'angular-script' ) );
        wp_enqueue_script( 'custom-attribute-config-script' );
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
    
    public static function add_sub_menu()
    {
        //help menu
        add_submenu_page(
            'trexanh_property_homepage',
            'TreXanh Property Help',
            __( 'Help', 'txp' ),
            'manage_options',
            'trexanh_property_homepage',
            array( __CLASS__, 'create_homepage' )
        );
        
        if (!PropertyType::enable_property_type_feature()) {
            return;
        }
        //add new property menu (for all available property types)
        if ( !current_user_can( 'edit_posts')) {
            return ;
        }
        
        global $submenu;
        
        $property_menu_key = 'edit.php?post_type=' . Property::POST_TYPE;
        
        if (! isset($submenu[$property_menu_key]) ) {
            return ;
        }
        
        $types = PropertyType::get_types();

        $create_new_property_menu = 'post-new.php?post_type=' . Property::POST_TYPE;
            // Remove default add new on sub menu.
        foreach ($submenu[$property_menu_key] as $key => $property_menu) {
            if (  in_array($create_new_property_menu , $property_menu )) {
                unset($submenu[$property_menu_key][$key]);
            }
        }
        
        if (empty($types)) {
            return;
        }
            // Add one "add new" menu for each property type
        foreach ($types as $type) {
            if (empty($type['enabled'])) {
                continue;
            }
            $submenu_class = '';
            if (isset($_GET['property_type']) && $_GET['property_type'] == $type['id']) {
                $submenu_class = 'current';
            }
            
            $submenu[$property_menu_key][] = array(
                sprintf( __( 'Add New %s', 'txp' ), $type['name']),
                'edit_posts',
                sprintf('post-new.php?post_type=%s&property_type=%s', Property::POST_TYPE, $type['id']),
                '',
                $submenu_class,
            );
        }
        
        // Add separator
        $submenu[$property_menu_key][] = array(
            '',
            'edit_posts',
            'wp-menu-separator',
        );
        
        /**
         * Add Config Property menu
         */
        $tab_name = SettingPage::get_config_property_settings_key();
        $submenu[$property_menu_key][] = array(
            __( 'Config Property', 'txp'),
            'manage_options',
            'admin.php?page=trexanh_property_settings&tab=' . $tab_name,
        );
    }
    /**
     * Options page callback
     */
    public static function create_homepage()
    {
        ?>
        <div class="wrap">
            <h2>TreXanh Property Help</h2>
            To setup and config the plugin correctly, please check through help items:
            <ul>
                <li>
                    <a href="http://trexanhproperty.com/doc/set-up-properties/">How to create properties.</a>
                </li>
                <li>
                    <a href="http://trexanhproperty.com/doc/property-type-and-custom-attribute/">Add new property type like Landing, Commercial. Add new attribute to a property.</a>
                </li>
            </ul>
            
            Frontend listing:
            <ul>
                <li>
                    <a href="http://trexanhproperty.com/doc/properties-search-and-listing-on-frontend/">How to enable properties search and listing on frontend</a>
                </li>
                <li>
                    <a href="http://trexanhproperty.com/doc/shortcodes/">Shortcodes specification</a>
                </li>
                <li>
                    <a href="http://trexanhproperty.com/doc/plugin-template/">Override plugin's template. Build new template for new property type like Landing. Sample code for showing property attribute and property group</a>
                </li>
            </ul>
            
            Frondend property submit:
            <ul>
                <li>
                <a href="http://trexanhproperty.com/doc/submit-property/">How to enable Submit property on frontend, then customize that flow</a>
                </li>
            </ul>
        </div>
        <?php
    }
}