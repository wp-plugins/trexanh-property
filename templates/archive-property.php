<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The Template for displaying property archives
 *
 * Override this template by copying it to yourtheme/trexanh-property/archive-property.php
 */
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header( 'property' );
$display_mode = isset( $_GET['display'] ) ? $_GET['display'] : "grid";
if ( ! in_array( $display_mode, array( "grid", "map" ) ) ) {
    $display_mode = "grid";
}
$grid_view_url = esc_url(add_query_arg('display', 'grid'));
$map_view_url = esc_url(add_query_arg('display', 'map'));
?>
<section id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                if ( is_search() ) {
                    if ( get_query_var( 's' ) ) {
                        echo sprintf( __( 'Search Result for &ldquo;%s&rdquo;', 'txp' ), get_search_query() );
                    } else {
                        echo __( 'Search Result', 'txp' );
                    }
                }
                ?>
            </h1>
        </header>
        <div class="hentry properties-listing">
            <div class="display-mode-container">
                <div class="map-total-search-results pull-left">
                    <?php echo sprintf(__( "%s total found ", 'txp' ), $wp_query->found_posts); ?>
                </div>
                <a
                    href="<?php echo $grid_view_url; ?>"
                    <?php if ( "grid" == $display_mode ) { echo " class='selected'"; } ?>
                >
                    <span class="dashicons dashicons-grid-view"></span> <?php echo __( "Grid", "txp" ); ?>
                </a>&nbsp;
                <a
                    href="<?php echo $map_view_url; ?>"
                    <?php if ( "map" == $display_mode ) { echo " class='selected'"; } ?>
                >
                    <span class="dashicons dashicons-location"></span> <?php echo __( "Map", "txp" ); ?>
                </a>
            </div>
        <?php switch ( $display_mode ) {
            case "grid":
                if ( have_posts() ) : ?>
                        <div class="property-items-container">
                            <?php while ( have_posts() ) : the_post(); ?>

                                <?php txp_get_template_part( 'content-property.php' ); ?>

                            <?php endwhile; // end of the loop.  ?>
                        </div>

                    <?php
                        // Previous/next page navigation.
                        the_posts_pagination( array(
                            'prev_text'          => __( 'Previous page', 'txp' ),
                            'next_text'          => __( 'Next page', 'txp' ),
                            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'txp' ) . ' </span>',
                        ) );
                    ?>
                <?php else : ?>

                    <p><?php _e( 'No properties were found matching your selection.', 'txp' ); ?></p>

                <?php endif;
                break;
            case "map": ?>
                <?php
                    $data = array();
                    $message = '';
                    while ( have_posts() ) {
                        the_post();
                        global $property;
                        $attachments = get_posts( array(
                            'post_type' => 'attachment',
                            'post_parent' => $property->id,
                            'numberposts' => 1,
                        ));
                        $location_string = txp_get_property_location_string( $property );
                        $coordinates = array( null, null );
                        if ($property->address_coordinates) {
                            $coordinates = explode( ",", $property->address_coordinates );
                        }
                        
                        if ( $coordinates[0] && $coordinates[1] ) {
                            $data[] = array(
                                "title" => esc_html( $property->post->post_title ),
                                "url" => get_permalink(),
                                "image_url" => wp_get_attachment_thumb_url( $attachments[0]->ID ),
                                "type" => $property->listing_type,
                                "price" => $property->price,
                                "price_string" => txp_currency( $property->price ),
                                "rent_amount" => $property->rent,
                                "rent_amount_string" => txp_currency( $property->rent ). " " . __( "per", "txp" ) . " " . $property->rent_period,
                                "location_string" => $location_string,
                                "latitude" => trim( $coordinates[0] ),
                                "longitude" => trim( $coordinates[1] ),
                                "bedrooms" => $property->bedrooms,
                                "bathrooms" => $property->bathrooms,
                                'category' => strtolower( $property->category ),
                            );
                        } else {
                            if ( ! $message ) {
                                $message = __( 'There may have some properties with incorrect address information not be displayed on map', 'txp' );
                            }
                        }
                    }
                ?>
                <div id="map-container">
                    <div id="map" style="height:450px;"></div>
                    <?php if ( $message ) { ?>
                        <div class="map-notification">
                            <small>
                                <?php echo $message; ?>
                            </small>
                        </div>
                    <?php } ?>
                </div>                
                <?php wp_print_scripts( "underscore" ); ?>
                <script type="text/javascript" src="<?php echo TREXANHPROPERTY__PLUGIN_URL; ?>assets/lib/js-marker-clusterer/src/markerclusterer.js"></script>
                <script type="text/javascript" src="<?php echo TREXANHPROPERTY__PLUGIN_URL; ?>assets/js/helper-functions.js"></script>
                <script type="text/javascript" src="<?php echo TREXANHPROPERTY__PLUGIN_URL; ?>assets/js/properties-mapping.js"></script>
                <script>
                    ( function() {
                        var data = JSON.parse( '<?php echo json_encode( $data ); ?>' );
                        window.TrexanhProperty.properties_mapping( "map", data );
                    } )();
                </script>
                <?php break;
        } ?>
        </div>
    </main>
</section>

<?php get_sidebar(); ?>
<?php get_footer( 'property' ); ?>
