<?php

/* 
 * Manage property list
 */


namespace TreXanhProperty\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Property as Txp;
use TreXanhProperty\Core\Order;
use TreXanhProperty\Core\Formatter;
use TreXanhProperty\Core\PropertyType;

class PropertyList {
    public static function set_custom_edit_property_columns($columns) {
        if (!PropertyType::enable_property_type_feature()) {
            return $columns;
        }
        
        $columns['price'] = __( 'Price', 'txp' );
        $columns['property_type'] = __( 'Type', 'txp' );
        $columns['address'] = __( 'Address', 'txp' );
        $columns['order'] = __( 'Order', 'txp' );
        $columns['photo'] = __( 'Photo', 'txp' );

        return $columns;
    }

    public static function custom_property_column( $column, $post_id ) {
        if (!PropertyType::enable_property_type_feature()) {
            return ;
        }
        
        switch ( $column ) {
            case 'property_type':
                $type_id = Txp::get( $post_id, 'property_type' );
                $property_type = PropertyType::get_type( $type_id );
                if ($property_type) {
                    echo $property_type['name'];
                }
                break;
            case 'price' :
                $listing_type = Txp::get($post_id, 'listing_type');

                if ($listing_type == 'sale') {
                    echo sprintf( __( 'Sale price: %s', 'txp' ), Formatter::currency( Txp::get($post_id, 'price') ) );
                } else if ($listing_type == 'lease') {
                    echo sprintf( 
                            __( 'Rent price: %s per %s', 'txp' ),
                            Formatter::currency( Txp::get($post_id, 'rent') ),
                            Txp::get($post_id, 'rent_period')
                        );
                } else {
                    _e( 'Unable to get listing type', 'txp' );
                }
                break;

            case 'address' :
                $property = new \stdClass();
                $property->address_postcode = Txp::get($post_id, 'address_postcode');
                $property->address_street_number = Txp::get($post_id, 'address_street_number');
                $property->address_street = Txp::get($post_id, 'address_street');
                $property->address_city = Txp::get($post_id, 'address_city');
                $property->address_state = Txp::get($post_id, 'address_state');
                $property->address_country = Txp::get($post_id, 'address_country');
                echo txp_get_property_location_string($property);
                break;
            
            case 'order' :
                $order_id = Txp::get($post_id, 'order_id');
                if ($order_id == "") {
                    echo "-";
                } else {
                    $order = new Order( $order_id );
                    echo "Order: #<a href=\"" . admin_url( 'post.php?post=' . $order_id) . "&action=edit\">" . $order->id . '</a><br>';
                    echo sprintf( __( 'Amount: %s ', 'txp' ), Formatter::currency( $order->amount, $order->order_currency ) ) . '<br />';
                    echo sprintf( __( 'Status: %s ', 'txp' ), $order->get_status() ) . '<br />';
                    if  ($order->get_status() === Order::STATUS_COMPLETED) {
                        echo sprintf( __( 'Completed at: %s', 'txp' ), $order->completed_date );
                    }
                }
                break;
            case 'photo' :
                $attachments = get_posts( array(
                    'post_type' => 'attachment',
                    'posts_per_page' => -1,
                    'post_parent' => $post_id,
                ));
                if ($attachments) {
                    echo wp_get_attachment_image( $attachments[0]->ID, 'thumbnail' );
                } else {
                    echo __( "No photo", 'txp' );
                }
                break;
        }
    }    
}

