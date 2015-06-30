<?php

namespace TreXanhProperty\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\PropertyForm;
use TreXanhProperty\Core\Property;

/* 
 * Wordpress show custom post attribute in metabox.
 * This class help to generate metabox and save data from metabox
 */

class PropertyMetabox {
    /**
     * Adds a box to the main column on the Property edit screens.
     */
    public static function add_meta_box($post) {
        
        $post_type = Property::get_post_type();        

        $options = get_option(SettingPage::get_config_property_settings_key());
        
        $default_property_type = 'property';
        
        if ( get_post_meta( $post->ID, Property::get_input_prefix() . '_property_type' )) {
            $property_type_id = get_post_meta( $post->ID, Property::get_input_prefix() . '_property_type', true );
        } else {
            $property_type_id = !empty( $_GET['property_type'] ) ? $_GET['property_type'] : $default_property_type;
        }

        $property_type = $options['property_types'][$property_type_id];
        
        if (empty($property_type['enabled'])) {
            wp_die( __( sprintf("The property type %s is not enabled or invalid.", $property_type['name']), "txp" ) );
        }
        
        // Add property & wp_nonce_field
        add_meta_box(
            'txp_metabox_property_type',
            __( 'Property Type', 'txp' ),
            array( __CLASS__, 'meta_box_property_type_callback'),
            $post_type,
            'normal',
            'high',
            array('type' => $property_type)
        );
        
        $ungrouped_attributes = $property_type['attributes'];
        foreach ($property_type['groups'] as $group) {
            add_meta_box(
                'txp_metabox_' . $group['id'],
                $group['name'],
                array('TreXanhProperty\Admin\PropertyMetabox', 'meta_box_callback'),
                $post_type,
                'normal',
                'default',
                $group['attributes']
            );
            $ungrouped_attributes = array_diff($ungrouped_attributes, $group['attributes']);
        }
        // render custom attributes
        if ( ! empty ( $ungrouped_attributes ) ) {
            add_meta_box(
                'txp_metabox_custom_attributes',
                __( 'Other attributes', 'txp' ),
                array('TreXanhProperty\Admin\PropertyMetabox', 'meta_box_callback'),
                $post_type,
                'normal',
                'default',
                $ungrouped_attributes
            );
        }
    }
    
    /**
     * 
     * Add property type value & wp_nonce_field
     * 
     * @param \WP_Post $post
     * @param array $callback_args
     */
    public static function meta_box_property_type_callback($post, $callback_args)
    {
        $type = isset( $callback_args['args']['type'] ) ? $callback_args['args']['type'] : array();
        
        $type_id = isset($type['id']) ? esc_attr($type['id']) : '';
        $type_name = isset( $type['name'] ) ? esc_html($type['name']) : '';
        
        wp_nonce_field( 'txp_meta_box', 'txp_meta_box_nonce' );
        echo sprintf(__('Property Type: <strong>%s</strong>', 'txp'), $type_name);
        echo '<input type="hidden" name="' . Property::get_input_prefix() . '_property_type" value="' . $type_id . '">';
    }
    
    /**
     * Prints the box content.
     * 
     * @param WP_Post $post The object for the current post/page.
     */
    public static function meta_box_callback( $post, $callback_args ) {
        $inputs = $callback_args['args'];
        echo PropertyForm::render_form_elements($inputs, $post);
    }
    
    /**
    * Save post metadata when a post is saved.
    *
    * @param int $post_id The post ID.
    * @param post $post The post object.
    * @param bool $update Whether this is an existing post being updated or not.
    */
    public static function save_meta_box_data( $post_id, $post, $update) {
        
       $post_type = Property::get_post_type();
       
        if ( $post_type != $post->post_type ) {
            return;
        }

        // Check if our nonce is set.
        if ( ! isset( $_POST['txp_meta_box_nonce'] ) ) {
            return;
        }
        
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['txp_meta_box_nonce'], 'txp_meta_box' ) ) {
            return;
        }
    
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }


        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
        // - Update the post's metadata.
        PropertyForm::save($post_id);
    }  
}
