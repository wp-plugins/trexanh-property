<?php

namespace TreXanhProperty\Core;

use TreXanhProperty\Core\Config;
use TreXanhProperty\Core\PaymentGateway\PaymentGatewayService;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @property float $amount
 * @property string $completed_date
 * @property string $transaction_id
 * @property string $order_currency
 * @property int $customer_user
 * @property string $completed_date
 * @property string $payment_method
 */
class Order
{
    const STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    
    const POST_TYPE = 'property_order';
    
    public $id = 0;
    
    /**
     *
     * @var \WP_Post 
     */
    public $post = null;

    public function __construct( $order = 0 )
    {
        if ( is_numeric( $order ) ) {
            $this->id = absint( $order );
            $this->post = get_post( $this->id );
        } elseif ( $order instanceof self ) {
            $this->id = absint( $order->id );
            $this->post = $order->post;
        } elseif ( isset( $order->ID ) ) {
            $this->id = absint( $order->ID );
            $this->post = $order;
        }
    }
    
    public function get_payment_gateway()
    {
        $services = PaymentGatewayService::getInstance();
        if ( ! $this->payment_method ) {
            return null;
        }
        return $services->get($this->payment_method);
    }

    /**
     * __get function.
     *
     * @param string $key
     * @return mixed
     */
    public function __get( $key )
    {
        $value = get_post_meta( $this->id, '_' . $key, true );
        
        if ( !empty( $value ) ) {
            $this->$key = $value;
        }

        return $value;
    }
    
    /**
     * 
     * @return boolean|\Txp_Order
     */
    public function payment_complete($transaction_id = null)
    {
        if ( null !== $transaction_id) {
            add_post_meta($this->id, '_transaction_id', $transaction_id);
        }
        return $this->update_status(self::STATUS_COMPLETED);
    }
    
    /**
     * 
     * @return string
     */
    public function get_status()
    {
        return $this->post->post_status;
    }
    
    /**
     * 
     * @param string $new_status
     * @return boolean|\Txp_Order
     */
    public function update_status($new_status)
    {
        $old_status = $this->get_status();
        
        if ($old_status == $new_status) {
            return $this;
        }
        
        switch ($new_status) {
            case self::STATUS_COMPLETED:
                update_post_meta( $this->id, '_completed_date', current_time('mysql') );
            break;
            case self::STATUS_AWAITING_PAYMENT:
            case self::STATUS_CANCELLED:
                // Do something in feature.
            break;
            default:
                return false;
        }
        
        wp_update_post(array(
            'ID'          => $this->id,
            'post_status' => $new_status,
        ));
        
        return $this;
    }
    
    /**
     * 
     * @param type $args
     * @return Order
     */
    public static function create_order( $args = array() )
    {
        $default_args = array(
            'customer_id'   => null,
            'customer_note' => null,
            'amount'        => 0,
            'order_id'      => 0
        );

        $args = wp_parse_args( $args, $default_args );

        $order_data = array();
        $order_data['post_type'] = self::POST_TYPE;
        $order_data['post_status'] = apply_filters('txp_default_order_status', self::STATUS_AWAITING_PAYMENT);
        $order_data['post_author']   = get_current_user_id();
        $order_data['post_title']    = sprintf( __( 'Order &ndash; %s', 'txp' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'txp' ) ) );
        $order_id = wp_insert_post( apply_filters( 'txp_new_order_data', $order_data ), true );

        if (  is_wp_error( $order_id )) {
            return $order_id;
        }

        if ( is_numeric( $args['customer_id'] ) ) {
            update_post_meta( $order_id, '_customer_user', $args['customer_id'] );
        }

        update_post_meta( $order_id, '_order_key', 'txp_' . apply_filters( 'txp_generate_order_key', uniqid( 'order_' ) ) );
        update_post_meta( $order_id, '_order_currency', Config::get_setting( 'currency', 'general', 'USD') );
        update_post_meta( $order_id, '_amount', $args['amount']);

        return new Order($order_id);
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_completed()
    {
        return $this->get_status() == static::STATUS_COMPLETED;
    }
}
