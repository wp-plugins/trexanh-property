<?php

/**
 * @version 0.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$bathrooms = esc_attr( isset( $_GET['bathrooms'] ) ? $_GET['bathrooms'] : 'all' );
$bedrooms = esc_attr( isset( $_GET['bedrooms'] ) ? $_GET['bedrooms'] : 'all' );
$category = esc_attr( isset( $_GET['category'] ) ? $_GET['category'] : 'all' );
$listing_type = esc_attr( isset( $_GET['listing_type'] ) ? $_GET['listing_type'] : 'all' );
$area_unit = esc_attr( isset( $_GET['area_unit'] ) ? $_GET['area_unit'] : 'square' );

$price_from = esc_attr( isset( $_GET['price_from'] ) ? $_GET['price_from'] : '' );
$price_to = esc_attr( isset( $_GET['price_to'] ) ? $_GET['price_to'] : '' );

$garages = esc_attr( isset( $_GET['garages'] ) ? $_GET['garages'] : 'all' );

$min_area = esc_attr( isset( $_GET['min_area'] ) ? $_GET['min_area'] : '' );
$max_area = esc_attr( isset( $_GET['max_area'] ) ? $_GET['max_area'] : '' );

$price_arr = array(
    '' => __('Any', 'txp'),
    50000   => txp_currency(50000),
    100000  =>	txp_currency(100000),
    150000  =>	txp_currency(150000),
    200000  =>	txp_currency(200000),
    250000  =>	txp_currency(250000),
    300000  =>	txp_currency(300000),
    350000  =>	txp_currency(350000),
    400000  =>	txp_currency(400000),
    450000  =>	txp_currency(450000),
    500000  =>	txp_currency(500000),
    550000  =>	txp_currency(550000),
    600000  =>	txp_currency(600000),
    650000  =>	txp_currency(650000),
    700000  =>	txp_currency(700000),
    750000  =>	txp_currency(750000),
    800000  =>	txp_currency(800000),
    850000  =>	txp_currency(850000),
    900000  =>	txp_currency(900000),
    950000  =>	txp_currency(950000),
    1000000 =>	txp_currency(1000000),
    1250000 =>	txp_currency(1250000),
    1500000 =>	txp_currency(1500000),
    1750000 =>	txp_currency(1750000),
    2000000 =>	txp_currency(2000000),
    2500000 =>	txp_currency(2500000),
    3000000 =>	txp_currency(3000000),
    4000000 =>	txp_currency(4000000),
    5000000 =>	txp_currency(5000000),
    10000000=>	txp_currency(10000000),
);

$display_mode = esc_attr( isset( $_GET['display'] ) ? $_GET['display'] : "grid" );

function select_options($input_name, $options, $selected_value = '', $attrs = array())
{
    $element = new Zend\Form\Element\Select($input_name, $options);
    $element->setValueOptions($options);
    $element->setValue($selected_value);
    $element->setAttributes($attrs);
    
    $select_helper = new \Zend\Form\View\Helper\FormSelect(); 
    echo $select_helper->render($element);
}
?>
<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="txp-property-search-form">
    <label for="s"><?php _e( 'Search for:', 'txp' ); ?></label>
    <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search Properties&hellip;', 'placeholder', 'txp' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'txp' ); ?>" />
    
    <?php if ( isset( $search_by_area ) && $search_by_area == 'yes' ) { ?>
    <div class="txp-row">
        <div class="txp-half-row">
            <label for="min_area"><?php echo __('Min Area', 'txp'); ?></label>
            <input type="number" name="min_area" id="min_area" value="<?php echo $min_area; ?>" />
        </div>
        <div class="txp-half-row">
            <label for="max_area"><?php echo __('Max Area', 'txp'); ?></label>
            <input type="number" name="max_area" id="max_area" value="<?php echo $max_area; ?>" />
        </div>
        <label for="area_unit"><?php echo __( 'Area unit', 'txp' ); ?></label>
            <?php
            select_options( 'area_unit', array(
                'square_meter' => __('Square Meter', 'txp'),
                'sqft' => __('Square Feet', 'txp'),
            ), $area_unit);
            ?>
    </div>
    <?php } ?>
    
    <?php if ( isset( $search_by_price ) && $search_by_price == 'yes' ) { ?>
    <div class="txp-row">
        <div class="txp-half-row">
            <label for="price_from"><?php echo __( 'Price from', 'txp' ); ?></label>
            <?php
                select_options( 'price_from', $price_arr, $price_from, array('id' => 'price_from') );
            ?>
        </div>
        <div class="txp-half-row">
            <label for="price_to"><?php echo __( 'Price to', 'txp' ); ?></label>
            <?php
                select_options( 'price_to', $price_arr, $price_to, array('id' => 'price_to') );
            ?>
        </div>
    </div>
    <?php } ?>
    
    <?php if ( isset( $search_by_listing_type ) && $search_by_listing_type == 'yes' ) { ?>
    <div class="txp-row">
        <label for="listing_type"><?php _e( 'Listing type', 'txp' ); ?></label>
        <?php
            select_options( 'listing_type', array(
                'all' => __('Any', 'txp'),
                'sale' => __('Sale', 'txp'),
                'lease' => __('Lease', 'txp'),
            ), $listing_type, array('id' => 'listing_type') );
        ?>
    </div>
    <?php } ?>
    
    <?php if ( isset( $search_by_category ) && $search_by_category == 'yes' ) { ?>
    <div class="txp-row">
        <label for="txp-property-search-category"><?php _e( 'Category', 'txp' ); ?></label>
        <?php
                select_options(
                    'category', 
                    array(
                        'all' => __( 'Any', 'txp' ),
                        'House' => __( 'House', 'txp' ),
                        'Unit' => __( 'Unit', 'txp' ),
                        'Studio' => __( 'Studio', 'txp' ),
                        'Apartment' => __( 'Apartment', 'txp' ),
                        'Flat' => __( 'Flat', 'txp' ),
                        'Other' => __( 'Other', 'txp' ),
                    ),
                    $category,
                        array('id' => 'txp-property-search-category')
                    );
        ?>
    </div>
    <?php } ?>
    
    <?php if ( isset( $search_by_bedroom ) && $search_by_bedroom == 'yes' ) { ?>
    <div class="txp-row">
        <label for="bedrooms"><?php _e( 'Bedrooms', 'txp' ); ?></label>
        <?php
                select_options( 'bedrooms', array(
                    'all' => __('Any', 'txp'),
                    '1' => '1',
                    '2' => '2',
                    '3+' => '3+',
                ), $bedrooms, array('id' => 'bedrooms') );
        ?>
    </div>
    <?php } ?>
    
    <?php if ( isset( $search_by_bathroom ) && $search_by_bathroom == 'yes' ) { ?>
    <div class="txp-row">
        <label for="bathrooms"><?php _e( 'Bathrooms', 'txp' ); ?></label>
        <?php
                select_options( 'bathrooms', array(
                    'all' => __('Any', 'txp'),
                    '1' => '1',
                    '2' => '2',
                    '3+' => '3+',
                ), $bathrooms, array('id' => 'bathrooms') );
        ?>
    </div>
    <?php } ?>
    
    <?php if ( isset( $search_by_garage ) && $search_by_garage == 'yes' ) { ?>
    <div class="txp-row">
        <label for="garages"><?php _e( 'Garages', 'txp' ); ?></label>
        <?php
                select_options( 'garages', array(
                    'all' => __('Any', 'txp'),
                    '1' => '1',
                    '2' => '2',
                    '3+' => '3+',
                ), $garages, array('id' => 'garages') );
        ?>
    </div>
    <?php } ?>
    
    <?php
    if ( empty( $submit_label ) ) {
        $submit_label = __( 'Search', 'txp' );
    }
    ?>
    <br>
    <input type="submit" value="<?php echo esc_attr( $submit_label ); ?>" />
    <input type="hidden" name="post_type" value="property" />
    <input type="hidden" value="<?php echo $display_mode ?>" name="display" />
</form>
