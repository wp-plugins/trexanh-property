    </h1>
    <div class="dashicons dashicons-location"></div>
    <?php
    global $property;
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
</div><!-- end entry-summary -->