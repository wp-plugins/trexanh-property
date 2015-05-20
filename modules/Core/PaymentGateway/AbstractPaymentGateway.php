<?php

namespace TreXanhProperty\Core\PaymentGateway;

use TreXanhProperty\Core\Order;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    public $id = '';
    public $description = '';
    public $section_setting_description;
    public $title = '';
    public $has_custom_html = false;
    
    public function print_description()
    {
        echo $this->section_setting_description;
    }
    
    protected function get_order( $order_id )
    {
        if ( ! $order_id instanceof Order) {
            return new Order($order_id);
            
        }
        
        return $order_id;
    }
    
    /**
     * 
     * @return string
     */
    public function get_description()
    {
        return apply_filters(TREXANHPROPERTY_PREFIX . 'get_' . $this->id . '_payment_method_description', $this->description);
    }
}
