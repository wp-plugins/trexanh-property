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
        <?php echo $property->category; ?>
    </div>
    
    <div class="property-meta">
    <?php
        $listing_type = $property->listing_type;

        if ($listing_type == 'sale') {
            echo __( 'Sale', 'txp' );
            echo $property->price ? " " . txp_currency( $property->price ) : "";
        } elseif ($listing_type == 'lease') {
            echo __( 'Rent', 'txp' );
            if ( $property->rent ) {
                echo " " . txp_currency( $property->rent );
                switch ($property->rent_period) {
                    case "month":
                        echo __( " per month", "txp" );
                        break;
                    case "week":
                        echo __( " per week", "txp" );
                        break;
                    case "day":
                        echo __( " per day", "txp" );
                        break;
                }
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
            <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
        </h1>
        <div class="dashicons dashicons-location"></div>
        <?php
            echo
                $property->address_postcode  . ', '  .
                $property->address_street_number . ' ' . $property->address_street . ', '  .
                $property->address_city . ', '  .
                $property->address_state . ', '  .
                $property->address_country;
        ?>
        <hr>
        <?php echo $property->bedrooms; ?> <?php echo __( 'beds', 'txp' ); ?>&nbsp;
        <?php echo $property->bathrooms; ?> <?php echo __( 'baths', 'txp' ); ?>
    </div>
</article>
