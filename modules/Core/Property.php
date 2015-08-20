<?php


/* 
 * Manage property custom postype
 */
namespace TreXanhProperty\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'TREXANHPROPERTY_STATUS_PENDING', 'pending' );
define( 'TREXANHPROPERTY_STATUS_PUBLISHED', 'publish' );

class Property {
    
     protected $order = null;
     
     const POST_TYPE = 'property';
     
     public static function get_post_type()
    {
         return self::POST_TYPE;
    }
    
    /**
     * To avoid conflict with other plugins, we namespace our input with prefix
     * @var string
     */
    public static $input_prefix = 'txp_property';

    /**
     * 
     * @return string
     */
    public static function get_prefix () {
        return TREXANHPROPERTY_PREFIX;
    }
    
    /**
     * 
     * @return string
     */
    public static function get_input_prefix () {
        return self::$input_prefix;
    }
    
    /**
     * Get property field value
     * 
     * @param int $id
     * @param string $field_name Property field name
     * @return mix
     */
    public static function get( $id, $field_name )
    {

        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */

        $value = get_post_meta( $id, self::$input_prefix . '_' . $field_name, true );

        return $value;
    }
    
    public $id = 0;

    /**
     *
     * @var \WP_Post 
     */

    public $post = null;

    public function __construct( $property )
    {
        if ( is_numeric( $property ) ) {
            $this->id = absint( $property );
            $this->post = get_post( $this->id );
        } elseif ( $property instanceof self ) {
            $this->id = absint( $property->id );
            $this->post = $property->post;
        } elseif ( isset( $property->ID ) ) {
            $this->id = absint( $property->ID );
            $this->post = $property;
        }
    }

    /**
     * __get function.
     *
     * @param string $key
     * @return mixed
     */
    public function __get( $key ) {
        
        $value = self::get( $this->id, $key );

        if ( ! empty( $value ) ) {
            $this->$key = $value;
        }

        return apply_filters( 'txp_get_' . $key, $value );
    }
    
    /**
     * 
     * @return Order | null;
     */
    public function get_order()
    {
        if ( !$this->order ) {
            $this->order = new Order( $this->order_id );
        }
        
        return $this->order;
    }
    
    /**
     * 
     * @return boolean
     */
    public function is_awaiting_approval()
    {
        // Need payment AND payment completed.
        if ( $this->order_id ) {
            $order = $this->get_order();
            if ( !$order->post) {
                return ( $this->post->post_status == TREXANHPROPERTY_STATUS_PENDING );
            }
            return ( $order->post->post_status == Order::STATUS_COMPLETED );
        }
        
        // Do not need payment.
        return ( $this->post->post_status == TREXANHPROPERTY_STATUS_PENDING );
    }
}

