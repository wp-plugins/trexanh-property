<?php
namespace TreXanhProperty\PluginSetup;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Property;
use TreXanhProperty\Core\Order;
use TreXanhProperty\Core\Config;


class PluginSetup {
    
    public static $plugin_pages = array(
        array(
            'name' => 'Submit Property',
            'slug' => 'submit-property',
            'wp_options' => array(
                'id' => 'submit_page_id',
            ),
        ),
        array(
            'name' => 'Submit Property > Payment',
            'slug' => 'submit-property-payment',
            'wp_options' => array(
                'id' => 'payment_page_id',
            ),
        ),
        array(
            'name' => 'My Properties',
            'slug' => 'my-properties',
            'wp_options' => array(
                'id' => 'my_properties_page_id',
            ),
        ),
        array(
            'name' => 'Properties',
            'slug' => 'properties',
            'wp_options' => array(
                'id' => 'properties_page_id',
            ),
        ),
        array(
            'name' => 'Submit Property > Payment status',
            'slug' => 'submit-property-payment-status',
            'wp_options' => array(
                'id' => 'payment_status_page_id',
            ),
        ),
    );
    
    public static function activation() {
        foreach (self::$plugin_pages as $page) {
            self::create_page(
                $page['name'], 
                $page['slug'], 
                TREXANHPROPERTY_PREFIX . $page['wp_options']['id']
            );
        }
        
        if ( ! Config::get_settings( 'general' ) ) {
            update_option(TREXANHPROPERTY_PREFIX . 'general_settings', array(
                'enable_property_submission' => true,
            ));
        }
        
    }

    public static function uninstall() {
        global $wpdb;
        
        // delete pages
        foreach (self::$plugin_pages as $page) {
            self::delete_page(
                TREXANHPROPERTY_PREFIX . $page['wp_options']['id']
            );
        }

        // delete options
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '" . TREXANHPROPERTY_PREFIX . "%';");
        
        // delete custom post type posts
        $custom_post_types = array(
            Property::get_post_type(),
            Order::POST_TYPE,
        );
        
        $query = "
          DELETE FROM `" . $wpdb->posts . "`
          WHERE `post_type` IN (" . implode( ',', $custom_post_types ) . ") 
        ";
        
        $wpdb->query($query);
        wp_trash_post();
    }  
    
    public static function create_page($the_page_title, $slug, $page_id_option_name) {

        $the_page = get_page_by_title( $the_page_title, $the_page );

        if ( ! $the_page ) {

            // Create post object
            $_p = array();
            $_p['post_title'] = $the_page_title;
            $_p['post_content'] = '';
            $_p['post_status'] = 'publish';
            $_p['post_type'] = 'page';
            $_p['comment_status'] = 'closed';
            $_p['ping_status'] = 'closed';
            $_p['post_category'] = array(1); // the default 'Uncatrgorised'

            // Insert the post into the database
            $the_page_id = wp_insert_post( $_p );

        }
        else {
            // the plugin may have been previously active and the page may just be trashed...

            $the_page_id = $the_page->ID;

            //make sure the page is not trashed...
            $the_page->post_status = 'publish';
            $the_page_id = wp_update_post( $the_page );

        }

        add_option( $page_id_option_name, $the_page_id );
    }

    public static function delete_page($page_id_option_name) {
        //  the id of our page...
        $the_page_id = get_option( $page_id_option_name );
        if( $the_page_id ) {

            wp_delete_post( $the_page_id, true );
        }
        delete_option($page_id_option_name);
    }    
}
