<?php

namespace TreXanhProperty\Admin;

use TreXanhProperty\Core\PaymentGateway\PaymentGatewayService;

class OrderList
{
    public static function custom_order_column( $column_name, $post_id ) {
        switch ($column_name) {
            case 'status':
                echo get_post_status($post_id);
                break;
            case 'payment_method':
                if ($payment_method = get_post_meta($post_id, '_payment_method', true)) {
                    $payment_service = PaymentGatewayService::getInstance();
                    echo $payment_service->get($payment_method)->title;
                }
                break;
            case 'completed_date':
                
                $completed_time = get_post_meta($post_id, '_completed_date', true);
                
                if ( !$completed_time ) {
                    echo __( 'Uncompleted', 'txp' );
                    break;
                }
                
                $time_diff = time() - strtotime($completed_time);
                
                if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
                    echo sprintf( __( '%s ago' ), human_time_diff( strtotime($completed_time) ) );
                } else {
                    echo $completed_time;
                }
                break;
            case 'order_title';
                echo sprintf(
                        __( '<a href="%s">#%s</a>', 'txp' ) ,
                        admin_url( 'post.php?post=' . $post_id . '&action=edit'),
                        $post_id
                    );
                
                $property_id = get_post_meta( $post_id, '_property_id', true );
                $property_title = get_the_title( $property_id );
                echo ' ';
                echo sprintf(
                        __( 'pay for <a href="%s">%s</a>', 'txp' ) ,
                        admin_url( 'post.php?post=' . $property_id . '&action=edit'),
                        $property_title
                        );
                break;
            default:
                break;
        }
    }
    
    public static function set_custom_edit_order_columns( $columns ) {
        $order_columns = array();
        
        $order_columns['cb'] = $columns['cb'];
        $order_columns['order_title'] = __( 'Order', 'txp' );
        $order_columns['status'] = __( 'Status', 'txp' );
        $order_columns['completed_date'] = __( 'Completed Date', 'txp' );
        $order_columns['payment_method'] = __( 'Payment method', 'txp' );
        
        return $order_columns;
    }
}
