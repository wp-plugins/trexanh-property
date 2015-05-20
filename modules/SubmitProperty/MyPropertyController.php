<?php

namespace TreXanhProperty\SubmitProperty;

use TreXanhProperty\Core\Property;
use TreXanhProperty\Core\Order;

class MyPropertyController
{
    public static function output()
    {
        if (!is_user_logged_in()) {
            wp_die( sprintf(
                __("You do not have sufficient permissions to access this page. Please <a href='%s'>log in.</a>", 'txp'),
                wp_login_url('/my-properties')
            ) );
        }
        
        $current_user = wp_get_current_user();
        
        $args = array(
            'posts_per_page'    => -1,
            'offset'            => 0,
            'orderby'           => 'post_date',
            'order'             => 'ASC',
            'post_type'         => 'property',
            'post_status'       => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
            'author'            => $current_user->ID,
        );
        // get his posts 'ASC'
        $current_user_posts = get_posts( $args );

        $published_posts = array();
        $not_approved_posts = array();
        $awaiting_payment_posts = array();

        foreach ( $current_user_posts as $post ) {
            if ( $post->post_status == 'publish' ) {
                $published_posts[] = $post;
            } elseif ( $post->post_status == 'pending' ) {
                $property = new Property($post);
                if ( $property->is_awaiting_approval() ) {
                    $not_approved_posts[] = $post;
                } else {
                    $awaiting_payment_posts[] = $post;
                }
            }
        }

        ob_start();
        txp_get_template_part(
            'my-properties.php',
            array(
                'published_posts' => $published_posts,
                'not_approved_posts' => $not_approved_posts,
                'awaiting_payment_posts' => $awaiting_payment_posts,
        ) );
        
        return ob_get_clean();
    }
}