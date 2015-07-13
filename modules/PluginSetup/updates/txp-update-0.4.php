<?php

/**
 * Update db to 0.4
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Property;
use TreXanhProperty\Admin\SettingPage;

// setup default property types.
$options = txp_get_default_property_type_config();
update_option(SettingPage::get_config_property_settings_key(), $options);

$args = array(
    'post_type' => Property::POST_TYPE,
    'posts_per_page' => -1 // Select all properties
);

$query = new WP_Query($args);
// Assign exists properties to property type default
while ( $query->have_posts() ) {
    $query->the_post();
    update_post_meta( get_the_ID(), Property::$input_prefix . '_property_type', 'house');
}

// Update automatically success
return true;