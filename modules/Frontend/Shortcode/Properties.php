<?php

namespace TreXanhProperty\Frontend\Shortcode;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use WP_Query;
use TreXanhProperty\Core\Property;

class Properties
{
    public static function init()
    {

        $shortcodes = array(
            'txp_properties_listing' => __CLASS__ . '::properties',
        );

        foreach ( $shortcodes as $shortcode => $callback ) {
            add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $callback );
        }
    }

    public static function properties( $atts = array() )
    {
        $atts = shortcode_atts( array(
            // newest properties first by default
            'orderby' => 'post_date',
            'order' => 'desc',
            'ids' => '',
            // unlimited by default
            'limit' => -1,
            'featured' => 0,
            'sorting' => '',
        ), $atts );
        $meta_query = array();
        
        // featured listing - example of usage: [txp_properties featured=1 limit=4]
        if ( $atts['featured'] === 'yes' ) {
            $meta_query[] = array(
                'key' => Property::get_input_prefix() . '_featured',
                'value' => 'yes'
            );
        }
        
        // latest first sorting - example of usage: [txp_properties sorting="latest first" limit=6]
        if ( $atts['orderby'] === 'time' ) {
            $atts['orderby'] = 'post_date';
            if ($atts['order'] === 'descending') {
                $atts['order'] = 'desc';
            } else {
                $atts['order'] = 'asc';
            }
        }

        $args = array(
            'post_type' => 'property',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'posts_per_page' => intval( $atts['limit'] ),
            'meta_query' => $meta_query
        );

        if ( !empty( $atts['ids'] ) ) {
            $ids = explode( ',', $atts['ids'] );
            $ids = array_map( 'trim', $ids );
            $ids = array_map( 'intval', $ids );
            $args['post__in'] = $ids;
        }

        ob_start();

        $properties = new WP_Query( apply_filters( 'txp_shortcode_properties_query', $args, $atts ) );

        if ( $properties->have_posts() ) :
            ?>
            <div class="property-items-container">
            <?php while ( $properties->have_posts() ) : $properties->the_post(); ?>

                <?php 
                txp_get_template_part( 'content-property.php' ); 
                ?>

            <?php endwhile; // end of the loop. ?>
            </div>
        <?php

        endif;

        return ob_get_clean();
    }

}
