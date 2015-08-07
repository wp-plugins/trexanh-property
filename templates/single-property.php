<?php

/**
 * 
 * @version 0.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('property');
?>

<?php
while (have_posts()) {
    the_post();
    global $property;
    txp_get_property_detail_template_by_type( $property->property_type );
}
?>

<?php get_footer('property'); ?>
