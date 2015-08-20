    <?php global $property; ?>        
    <?php if (isset($property->category) && !empty($property->category)) { ?>
    <div class="property-meta right">
        <?php echo esc_html( $property->category ); ?>
    </div>
    <?php } ?>


    <?php if (isset($property->listing_type) && !empty($property->listing_type)) { ?>
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
    <?php } ?>

    <a href="<?php echo the_permalink(); ?>">
        <?php
            if ( has_post_thumbnail() ) {
                echo get_the_post_thumbnail( $attachments[0]->ID, 'large' );
            } else { ?>
                <img src="<?php echo TREXANHPROPERTY__PLUGIN_URL; ?>assets/images/property-placeholder.gif" />
            <?php }
        ?>
    </a>
    <div class="entry-summary">
        <h1 class="entry-title">