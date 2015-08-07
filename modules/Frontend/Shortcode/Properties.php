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
            'txp_properties_map_listing' => __CLASS__ . '::map_properties',
        );

        foreach ( $shortcodes as $shortcode => $callback ) {
            add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $callback );
        }
    }

    public static function properties( $atts = array() )
    {
        ob_start();

        $paging_supported = 'yes';
        $properties = self::build_query( $atts, $paging_supported );

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
            if ( is_array( $atts ) && array_key_exists('paging', $atts) && 'yes' == $atts['paging'] ) {
                $args = array(
                    'total' => $properties->max_num_pages,
                );
                txp_get_template_part( 'shortcode-paginator.php', array( 'paginate_links' => paginate_links( $args ) ) );
            }
        endif;

        return ob_get_clean();
    }

    public static function map_properties( $atts = array() )
    {        
        // One page can have multiple shortcodes so we need generate unique id for each dom which will be converted into map
        $dom_id = mt_rand(1, 1000);
        
        do_action("trexanhproperty_property_map_listing_before", $dom_id);
        
        // default height of the map
        $height = 350;
        if ( is_array( $atts ) && array_key_exists( "height", $atts ) && !empty($atts['height']) ) {
            if (intval( $atts['height'] ) > 0) {
                $height = intval( $atts['height'] );
            }            
        }
        ob_start();
        echo "<style>#map-container-$dom_id #map-$dom_id { height: $height" . "px; }</style>";
        
        $properties = self::build_query( $atts );
        if ( $properties->have_posts() ) {
            $data = array();
        ?>
            <div id="map-container-<?php echo $dom_id; ?>" class="txp_map_container">
                <div id="map-<?php echo $dom_id; ?>"></div>
            </div>
            <?php
                while ( $properties->have_posts() ) {
                    $properties->the_post();
                    global $property;
                    
                    $attachment = get_post_thumbnail_id();
                    $location_string = txp_get_property_location_string( $property );
                    $coordinates = array( null, null );
                    if ($property->address_coordinates) {
                        $coordinates = explode( ",", $property->address_coordinates );
                    }
                    $data[] = array(
                        "title" => esc_html( $property->post->post_title ),
                        "url" => get_permalink(),
                        "image_url" => wp_get_attachment_thumb_url( $attachment ),
                        "type" => $property->listing_type,
                        "price" => $property->price,
                        "price_string" => txp_currency( $property->price ),
                        "rent_amount" => $property->rent,
                        "rent_amount_string" => txp_currency( $property->rent ). " " . __( "per", "txp" ) . " " . $property->rent_period,
                        "location_string" => $location_string,
                        "latitude" => $coordinates[0],
                        "longitude" => $coordinates[1],
                        "bedrooms" => $property->bedrooms,
                        "bathrooms" => $property->bathrooms,
                        'category' => strtolower( $property->category ),
                    );
                }
                wp_print_scripts("underscore");
                wp_register_script( "google-script-geocode", "https://maps.googleapis.com/maps/api/js" );
                wp_register_script( "markerclusterer_script", TREXANHPROPERTY__PLUGIN_URL . "assets/lib/js-marker-clusterer/src/markerclusterer.js" );
                wp_register_script( "helper_functions_script", TREXANHPROPERTY__PLUGIN_URL . "assets/js/helper-functions.js" );
                wp_register_script( "properties_mapping_script", TREXANHPROPERTY__PLUGIN_URL . "assets/js/properties-mapping.js" );
                wp_print_scripts("google-script-geocode");
                wp_print_scripts("markerclusterer_script");
                wp_print_scripts("helper_functions_script");
                wp_print_scripts("properties_mapping_script");
            ?>
            <script>
                ( function() {
                    var data = JSON.parse( '<?php echo json_encode( $data ); ?>' );
                    window.TrexanhProperty.properties_mapping( "map-<?php echo $dom_id; ?>", data );
                } )();
            </script>
        <?php }
        
        do_action("trexanhproperty_property_map_listing_after", $dom_id);
        
        return ob_get_clean();
    }
    
    protected static function build_query( $atts, $paging_supported = "no" )
    {
        $atts = shortcode_atts( array(
            // newest properties first by default
            'orderby' => 'post_date',
            'order' => 'desc',
            'ids' => '',
            // unlimited by default
            'items_per_page' => -1,
            'featured' => 0,
            'sorting' => '',
            'paged' => '',
            'paging' => '',
        ), $atts );
        
        $meta_query = array();
        
        // featured listing - example of usage: [txp_properties featured=1 items_per_page=4]
        if ( $atts['featured'] === 'yes' ) {
            $meta_query[] = array(
                'key' => Property::get_input_prefix() . '_featured',
                'value' => 'yes'
            );
        }
        
        // latest first sorting - example of usage: [txp_properties sorting="latest first" items_per_page=6]
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
            'posts_per_page' => intval( $atts['items_per_page'] ),
            'meta_query' => $meta_query,
        );
        if ( 'yes' == $paging_supported ) {
            $args['paged'] = intval( $atts['paged'] );
        }
        if ( !empty( $atts['ids'] ) ) {
            $ids = explode( ',', $atts['ids'] );
            $ids = array_map( 'trim', $ids );
            $ids = array_map( 'intval', $ids );
            $args['post__in'] = $ids;
        }

        return new WP_Query( apply_filters( 'txp_shortcode_properties_query', $args, $atts ) );
    }
}
