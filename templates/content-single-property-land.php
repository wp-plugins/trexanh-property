<?php
/**
 * @version 0.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/* @var $property Txp_Property */
global $property;
$config = txp_get_property_type_config( $property->property_type );
if (!$config) {
    die("Invalid property type.");
}
$has_gallery = false;
if (in_array('photo_gallery', $config['attributes'])) {
    $has_gallery = true;
}
$ungrouped_attributes = $config['attributes'];
foreach ($config['groups'] as $group) {
    $ungrouped_attributes = array_diff($ungrouped_attributes, $group['attributes']);
}
if (!empty($ungrouped_attributes)) {
    $config['groups']['ungrouped_attributes'] = array(
        'id' => 'ungrouped_attributes',
        'name' => 'Other attributes',
        'attributes' => $ungrouped_attributes,
    );
}
$manually_rendered_attributes = array(
    "price",
    "bedrooms",
    "bathrooms",
    "garage",
    "ensuite",
    "toilet",
    "new_construction",
    "air_conditioning",
    "security_system",
    "pool",
    "area",
    "address_postcode",
    "address_coordinates",
    "address_street_number",
    "address_street",
    "address_city",
    "address_state",
    "address_country",
    "floorplan",
    "video_url",
    "photo_gallery",
);
foreach ($config['groups'] as $g_index => $group) {
    foreach ($group['attributes'] as $a_index => $attribute) {
        if (in_array($attribute, $manually_rendered_attributes)) {
            unset($config['groups'][$g_index]['attributes'][$a_index]);
            if (empty($config['groups'][$g_index]['attributes'])) {
                unset($config['groups'][$g_index]);
            }
        }
    }
}
?>
<div id="property-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="summary entry-summary">
        <h1 itemprop="name" class="product_title entry-title"><?php echo esc_html( get_the_title() ); ?></h1>
        <?php if ($has_gallery) {
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
            }
        ?>
        <p>
            <strong><?php echo __( "OVERVIEW", "txp" ); ?></strong>
        </p>
        <table>
            <?php if ( $property->price ) { ?>
            <tr>
                <th><?php echo __( "Price", "txp" ); ?></th>
                <td><?php echo txp_currency( esc_html( $property->price ) ); ?></td>
            </tr>
            <?php } ?>
            <?php if ( $property->area ) { ?>
            <tr>
                <th><?php echo __( "Area", "txp" ); ?></th>
                <td><?php echo esc_html( $property->area ); ?> m<sup>2</sup></td>
            </tr>
            <?php } ?>
            <?php if ( $property->bedrooms ) { ?>
            <tr>
                <th><?php echo __( "Beds", "txp" ); ?></th>
                <td><?php echo esc_html( $property->bedrooms ); ?></td>
            </tr>
            <?php } ?>
            <?php if ( $property->bathrooms ) { ?>
            <tr>
                <th><?php echo __( "Baths", "txp" ); ?></th>
                <td><?php echo esc_html( $property->bathrooms ); ?></td>
            </tr>
            <?php } ?>
            <?php if ( $property->garage ) { ?>
            <tr>
                <th><?php echo __( "Garages", "txp" ); ?></th>
                <td><?php echo esc_html( $property->garage ); ?></td>
            </tr>
                <?php } ?>
            <?php if ( $property->toilet ) { ?>
            <tr>
                <th><?php echo __( "Toilets", "txp" ); ?></th>
                <td><?php echo esc_html( $property->toilet ); ?></td>
            </tr>
            <?php } ?>
            <?php if ( $property->ensuite ) { ?>
            <tr>
                <th><?php echo __( "Ensuite", "txp" ); ?></th>
                <td><?php echo ucwords( $property->ensuite ); ?></td>
            </tr>
            <?php } ?>
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
                    <span class="amentity<?php echo ($property->new_construction == "on") ? " dashicons-before dashicons-yes" : " dashicons-before dashicons-no-alt unavailable" ?>">
                        <?php echo __( "New construction", "txp" ); ?>
                    </span>
                </td>
                <td>
                    <span class="amentity<?php echo ($property->pool == "on") ? " dashicons-before dashicons-yes" : " dashicons-before dashicons-no-alt unavailable" ?>">
                        <?php echo __( "Pool", "txp" ); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="amentity<?php echo ($property->air_conditioning == "on") ? " dashicons-before dashicons-yes" : " dashicons-before dashicons-no-alt unavailable" ?>">
                        <?php echo __( "Air conditioning", "txp" ); ?>
                    </span>
                </td>
                <td>
                    <span class="amentity<?php echo ($property->security_system == "on") ? " dashicons-before dashicons-yes" : " dashicons-before dashicons-no-alt unavailable" ?>">
                        <?php echo __( "Security system", "txp" ); ?>
                    </span>
                </td>
            </tr>
        </table>
        <?php foreach ($config['groups'] as $group) { ?>
            <p>
                <strong><?php echo strtoupper( $group['name'] ) ?></strong>
            </p>
            <table>
                <?php foreach ($group['attributes'] as $index => $attribute_id) {
                    $attribute = $config['attributes_data'][$attribute_id];
                ?>
                <tr>
                    <th><?php echo $attribute['label']; ?></th>
                    <?php if ($attribute['type'] == 'checkbox' || $attribute['type'] == 'radio') { ?>
                        <td><?php echo ( $property->{$attribute['id']} ? __("Yes", "txp") : __("No", "txp") ); ?></td>
                    <?php } elseif ($attribute['type'] == 'currency') { ?>
                        <td><?php echo ( $property->{$attribute['id']} ? txp_currency($property->{$attribute['id']}) : "-" ); ?></td>
                    <?php } else { ?>
                        <td><?php echo ( $property->{$attribute['id']} ? ucwords( $property->{$attribute['id']} ) : "-" ); ?></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </table>
        <?php }?>
        
        <p>
            <strong><?php echo __( "MAP", "txp" ); ?></strong>
        </p>
        <span class="dashicons-before dashicons-location">
        <?php
            $location_string = txp_get_property_location_string( $property );
            echo $location_string ? $location_string : "-";
        ?>
        </span>
        <div id="map-canvas"></div>
        <script>
            (function() {
                
                <?php if ($has_gallery) { ?>
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
                <?php } ?>
                <?php if ( $property->address_coordinates ) {
                    $coordinates = explode( ",", $property->address_coordinates ); ?>
                    window.txl_map.show_map_at_coordinates(
                        'map-canvas',
                        '<?php echo trim( $coordinates[0] ); ?>',
                        '<?php echo trim( $coordinates[1] ); ?>'
                    );
                <?php } else { ?>
                    var location = {
                        street_number : '<?php echo $property->address_street_number; ?>',
                        street : '<?php echo $property->address_street; ?>',
                        city : '<?php echo $property->address_city; ?>',
                        state : '<?php echo $property->address_state; ?>',
                        country : '<?php echo $property->address_country; ?>'
                    };
                    window.txl_map.ajax_gen_map( 'map-canvas', location);
                <?php } ?>
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