<?php

/* 
 * Module Common setup
 */

namespace TreXanhProperty\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use TreXanhProperty\Core\Property;

class Module
{
    private static $initiated = false;

    public static function init() {
     if ( ! self::$initiated ) {
            self::init_hooks();
        }
    }
    
    public static function register_widgets()
    {
        register_widget( 'TreXanhProperty\Core\Widget\PropertySearch' );
    }

    public static function init_hooks() {
        self::$initiated = true;
        add_action( 'init', array('\TreXanhProperty\Core\Module', 'register_post_type'), 0 );        
        add_action( 'widgets_init', array(__CLASS__, 'register_widgets') );
        
        add_action( 'save_post', array( '\TreXanhProperty\Core\PropertyGallery', 'update_gallery' ), 1, 2);
    }
    
    public static function register_post_type()
    {
        $post_type = Property::get_post_type();

        $labels = array(
            'name' => __('Properties', 'txp'),
            'singular_name' => __('Property', 'txp'),
            'menu_name' => __('Property', 'txp'),
            'add_new' => __('Add New', 'txp'),
            'add_new_item' => __('Add New Property', 'txp'),
            'new_item' => __('New Property', 'txp'),
            'edit_item' => __('Edit Property', 'txp'),
            'update_item' => __('Update Property', 'txp'),
            'all_items' => __('All Properties', 'txp'),
            'view_item' => __('View Property', 'txp'),
            'search_items' => __('Search Properties', 'txp'),
            'not_found' => __('Property Not Found', 'txp'),
            'not_found_in_trash' => __('Property Not Found in Trash', 'txp')
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'property', 'with_front' => false),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        );
        register_post_type($post_type, $args);
        
        $order_args = array(
            'labels' => array(
                'name' => __( 'Orders', 'txp' ),
                'singular_name' => __( 'Order', 'txp' ),
                'edit_item' => __('Edit Order', 'txp'),
                'not_found' => __('Order Not Found', 'txp'),
                'not_found_in_trash' => __('Order Not Found in Trash', 'txp')
            ),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => 'trexanh_property_homepage',
            'query_var' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => false, // Removes support for the "Add New" function
            ),
            'map_meta_cap' => true, // Allow users to edit existing orders.
            'has_archive' => false,
            'hierarchical' => false,
            'publicly_queryable' => true,
        );
        register_post_type( Order::POST_TYPE, $order_args);
        
        register_post_status( 'completed', array(
                'label'                     => _x( 'Completed', 'Order status', 'txp' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'txp' )
        ) );
        
        register_post_status( 'awaiting_payment', array(
                'label'                     => _x( 'Awaiting Payment', 'Order status', 'txp' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Awaiting Payment <span class="count">(%s)</span>', 'Awaiting Payment <span class="count">(%s)</span>', 'txp' )
        ) );
    }
}




