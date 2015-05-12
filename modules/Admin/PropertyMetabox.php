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
    public static function add_meta_box() {

        $post_type = Property::get_post_type();        

        $fieldsets = PropertyForm::get_fieldsets();
        
        $ignoreList = array('payment');
        
        foreach ($fieldsets as $key => $fieldset) {
            if (in_array($key, $ignoreList)) {
                continue;
            }
            add_meta_box(
                'txp_metabox_' . $key,
                __( $fieldset['label'], 'txp' ),
                array('TreXanhProperty\Admin\PropertyMetabox', 'meta_box_callback'),
                $post_type,
                'normal',
                'default',
                $fieldset['fields']
            );            
        }
    }
        
    /**
     * Prints the box content.
     * 
     * @param WP_Post $post The object for the current post/page.
     */
    function meta_box_callback( $post, $callback_args ) {
        wp_nonce_field( 'txp_meta_box', 'txp_meta_box_nonce' );
        
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
