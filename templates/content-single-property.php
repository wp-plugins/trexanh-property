<?php
/* @var $property Txp_Property */
global $property;
?>
<div id="property-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="summary entry-summary">
        <h1 itemprop="name" class="product_title entry-title"><?php esc_html(the_title()); ?></h1>
        <?php
            $attachments = get_posts(array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_parent' => $property->id,
            ));
            if ( count( $attachments ) > 1 ) {
                $items = "";
                $pager = "";
                foreach ($attachments as $index => $attachment) {
                    $attachment_id = $attachment->ID;
                    $class = "post-attachment mime-" . sanitize_title($attachment->post_mime_type);
                    $items .= "<li>"
                                . "<a href='" . wp_get_attachment_url( $attachment_id ) . "' rel=\"view_gallery[property_gallery]\">"
                                    . wp_get_attachment_image( $attachment_id, 'full' )
                                . "</a>"
                            . "</li>";
                    $pager .= "<a data-slide-index='$index' href=''>"
                                . wp_get_attachment_image( $attachment_id, 'thumbnail' )
                            . "</a>";
                }
                ?>
                <div class="slider-wrapper transparent">
                    <ul class="bxslider">
                        <?php echo $items; ?>
                    </ul>
                    <div id="bx-pager">
                        <?php echo $pager; ?>
                    </div>
                </div>
                <?php
            } elseif ( count( $attachments ) === 1 ) { ?>
                <p>
                    <a href="<?php echo wp_get_attachment_url( $attachments[0]->ID ); ?>" rel="view_gallery[property_gallery]">
                        <?php echo wp_get_attachment_image( $attachments[0]->ID, 'large' ) ?>
                    </a>
                </p>
            <?php } else { ?>
                <p>
                    <img src="<?php echo TREXANHPROPERTY__PLUGIN_URL; ?>assets/images/property-placeholder.gif" />
                </p>
            <?php }
        ?>
        <p>
            <strong><?php echo __( "OVERVIEW", "txp" ); ?></strong>
        </p>
        <table>
            <tr>
                <th><?php echo __( "Price", "txp" ); ?></th>
                <td>
                    <?php if ( ! $property->price ) { ?>
                        -
                    <?php } else { ?>
                        <span class="price-tag">
                            <?php echo txp_currency( $property->price ); ?>
                        </span>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th><?php echo __( "Property type", "txp" ); ?></th>
                <td><?php echo ucwords( $property->category ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( "Contract type", "txp" ); ?></th>
                <td>
                    <?php switch( $property->listing_type ) {
                        case "sale":
                            echo __( "For Sale", "txp" );
                            break;
                        case "lease":
                            $for_rent = true;
                            echo __( "For Rent", "txp" );
                            break;
                        case "both":
                            echo __( "Sale / Rent", "txp" );
                            break;
                    } ?>
                </td>
            </tr>
            <?php if ( ! empty( $for_rent ) ) { ?>
            <tr>
                <th><?php echo __( "Rent Period", "txp" ); ?></th>
                <td><?php echo ucwords( $property->rent_period ); ?></td>
            </tr>
            <?php } ?>
            <tr>
                <th><?php echo __( "Status", "txp" ); ?></th>
                <td><?php echo ucwords( $property->status ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( "Beds", "txp" ); ?></th>
                <td><?php echo esc_html( $property->bedrooms ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( "Baths", "txp" ); ?></th>
                <td><?php echo esc_html( $property->bathrooms ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( "Garages", "txp" ); ?></th>
                <td><?php echo esc_html( $property->garage ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( "Toilets", "txp" ); ?></th>
                <td><?php echo esc_html( $property->toilet ); ?></td>
            </tr>
            <tr>
                <th><?php echo __( "Featured", "txp" ); ?></th>
                <td><?php echo ucwords( $property->featured ); ?></td>
            </tr>
        </table>
        <p>
            <strong><?php echo __( "DESCRIPTION", "txp" ); ?></strong>
            <?php echo the_content(); ?>
        </p>
        <p>
            <strong><?php echo __( "AMENITIES", "txp" ); ?></strong>
        </p>
        <table>
            <tr>
                <td>
                    <span class="amentity<?php echo ($property->new_construction == "no") ? " dashicons-before dashicons-no-alt unavailable" : " dashicons-before dashicons-yes" ?>">
                        <?php echo __( "New construction", "txp" ); ?>
                    </span>
                </td>
                <td>
                    <span class="amentity<?php echo ($property->pool == "no") ? " dashicons-before dashicons-no-alt unavailable" : " dashicons-before dashicons-yes" ?>">
                        <?php echo __( "Pool", "txp" ); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="amentity<?php echo ($property->air_conditioning == "no") ? " dashicons-before dashicons-no-alt unavailable" : " dashicons-before dashicons-yes" ?>">
                        <?php echo __( "Air conditioning", "txp" ); ?>
                    </span>
                </td>
                <td>
                    <span class="amentity<?php echo ($property->security_system == "no") ? " dashicons-before dashicons-no-alt unavailable" : " dashicons-before dashicons-yes" ?>">
                        <?php echo __( "Security system", "txp" ); ?>
                    </span>
                </td>
            </tr>
        </table>
        <p>
            <strong><?php echo __( "LOCATION", "txp" ); ?></strong>
        </p>
        <span class="dashicons-before dashicons-location">
        <?php
            echo
            esc_html( $property->address_postcode ) . ', '  .
                esc_html( $property->address_street_number ) . ' ' . esc_html( $property->address_street ) . ', '  .
                esc_html( $property->address_city ) . ', '  .
                esc_html( $property->address_state ) . ', '  .
                esc_html( $property->address_country );
        ?>
        </span>
        <div id="map-canvas"></div>
        <script>
            (function() {
                <?php // show slider ?>
                (function ($) {
                    $( document ).ready( function () {
                        if ($( ".slider-wrapper" ).length) {
                            $( ".slider-wrapper" ).removeClass( "transparent" );
                            $( '.bxslider' ).bxSlider( {
                                pagerCustom: '#bx-pager',
                                infiniteLoop: false,
                                adaptiveHeight: true
                            } );
                        }
                    } );
                    $( "a[rel^='view_gallery']" ).prettyPhoto( {
                        social_tools : false,
                        deeplinking : false,
                        autoplay_slideshow : true,
                        slideshow : 5000
                    } );
                } )( jQuery );
                <?php // show map ?>
                var location = {
                    street_number : '<?php echo $property->address_street_number; ?>',
                    street : '<?php echo $property->address_street; ?>',
                    city : '<?php echo $property->address_city; ?>',
                    state : '<?php echo $property->address_state; ?>',
                    country : '<?php echo $property->address_country; ?>'
                };
                window.txl_map.ajax_gen_map( 'map-canvas', location );
            })();
        </script>
        <?php if ( $property->floorplan ) { ?>
            <br>
            <p>
                <strong><?php echo __( "FLOORPLAN", "txp" ); ?></strong>
            </p>
            <img src="<?php echo esc_attr( $property->floorplan ); ?>" class="floorplan-image" />
        <?php } ?>
        <?php if ( $property->video_url ) {
            $video_url = $property->video_url;
            // get youtube video ID from URL and generate youtube embed link
            $youtube_pattern = '%^(?:https?://)?(?:www\.)?(?:youtu\.be/| youtube\.com(?:/embed/| /v/| /watch\?v=))([\w-]{10,12}).*$%x';
            preg_match($youtube_pattern, $video_url, $matches);
            if (!empty($matches)) { ?>
                <p>
                    <strong><?php echo __( "VIDEO", "txp" ); ?></strong>
                </p>
                <iframe class="property-video" frameborder="0" allowfullscreen="1" src="//www.youtube.com/embed/<?php echo $matches[1]; ?>?wmode=transparent&amp;enablejsapi=1"></iframe>
            <?php } else {
                $vimeoPattern = '~(?:<iframe [^>]*src=")?(?:https?:\/\/(?:[\w]+\.)*vimeo\.com(?:[\/\w]*\/videos?)?\/([0-9]+)[^\s]*)"?(?:[^>]*></iframe>)?(?:<p>.*</p>)?~ix';
                preg_match($vimeoPattern, $video_url, $matches);
                if (!empty($matches)) {
            ?>
                <p>
                    <strong><?php echo __( "VIDEO", "txp" ); ?></strong>
                </p>
                <iframe class="property-video" src="//player.vimeo.com/video/<?php echo $matches[1]; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            <?php }
            }
        } ?>
    </div>
</div>