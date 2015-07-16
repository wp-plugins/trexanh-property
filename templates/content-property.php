<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The default template for displaying content
 *
 */
global $property;
?>

<article id="post-<?php the_ID(); ?>" class="property-item">
    <div class="property-meta right">
        <?php echo esc_html( $property->category ); ?>
    </div>
    
    <div class="property-meta">
    <?php
        $listing_type = $property->listing_type;

        if ($listing_type == 'sale') {
            if ($property->price) {
                echo sprintf( __('Sale %s', 'txp'), txp_currency( $property->price ) );
            } else {
                echo __( 'Sale', 'txp' );
            }
        } elseif ($listing_type == 'lease') {
            if ( $property->rent ) {
                echo sprintf( __("Rent %s per %s", 'txp'), txp_currency( $property->rent ), esc_html($property->rent_period) ) ;
            } else {
                echo __( 'Rent', 'txp' );
            }
        }
    ?>
    </div>
    <?php
        $attachments = get_posts( array(
            'post_type' => 'attachment',
            'post_parent' => $property->id,
            'numberposts' => 1,
        ));
    ?>
    <a href="<?php echo the_permalink(); ?>">
        <?php
            if ( count( $attachments ) ) {
                echo wp_get_attachment_image( $attachments[0]->ID, 'large' );
            } else { ?>
                <img src="<?php echo TREXANHPROPERTY__PLUGIN_URL; ?>assets/images/property-placeholder.gif" />
            <?php }
        ?>
    </a>
    <div class="entry-summary">
        <h1 class="entry-title">
            <a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo esc_html( the_title('','', false) ); ?></a>
        </h1>
        <div class="dashicons dashicons-location"></div>
        <?php
            $location_string = txp_get_property_location_string( $property );
            echo $location_string ? esc_html($location_string) : "-";
        ?>
        <hr>
        <?php if ($property->bedrooms) { ?>
            <?php echo esc_html( $property->bedrooms ); ?> <?php echo __( 'beds', 'txp' ); ?>&nbsp;
        <?php } ?>
        <?php if ($property->bathrooms) { ?>
            <?php echo esc_html( $property->bathrooms ); ?> <?php echo __( 'baths', 'txp' ); ?>
        <?php } ?>
    </div>
</article>
