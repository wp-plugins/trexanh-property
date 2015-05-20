<?php

namespace TreXanhProperty\Core\PaymentGateway;

use TreXanhProperty\Core\PaymentGateway\PaymentGatewayInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ConfigInterface;

class PaymentGatewayService extends ServiceManager
{
    protected static $instance;
    
    /**
     * 
     * @return PaymentGatewayService
     */
    public static function getInstance()
    {
        if ( ! static::$instance ) {
           static::$instance = new static();
        }
        
        return static::$instance;
    }


    /**
     * 
     * @param ConfigInterface $configuration
     */
    public function __construct( ConfigInterface $configuration = null )
    {
        $this->init();
    }
    
    protected function init()
    {
        $methods = $this->get_payment_classes();
        foreach ( $methods as $method_id => $method_class ) {
            $this->setInvokableClass($method_id, $method_class);
        }
    }
    
    public function get_payment_classes()
    {
        $payment_classes = array(
            'paypal' => 'TreXanhProperty\Core\PaymentGateway\Paypal',
            'stripe' => 'TreXanhProperty\Core\PaymentGateway\Stripe',
        );
        
        return apply_filters( TREXANHPROPERTY_PREFIX . 'payment_methods', $payment_classes);
    }

    public function validatePlugin( $plugin )
    {
        if ( $plugin instanceof PaymentGatewayInterface) {
            return ;
        }
        
        throw new \Exception('Payment Gateway must implemented %s.', __NAMESPACE__ . '\\PaymentGatewayInterface');
    }
    
    /**
     * 
     * Get all available gateways.
     * 
     * @return array
     */
    public function get_available_gateway()
    {
        $gateways = $this->get_payment_classes();
        $available_gateways = array();
        foreach ($gateways as $gateway_id => $gateway_class) {
            $gateway = $this->get($gateway_id);
            if ( !$gateway->is_enabled()) {
                continue;
            }
            $available_gateways[$gateway_id] = $this->get($gateway_id);
        }
        
        return apply_filters( TREXANHPROPERTY_PREFIX . 'available_gateway', $available_gateways);
    }
    
    /**
     * Validate a payment gateway has supported and enabled.
     * @param string $gateway_id
     * @return boolean
     */
    public function is_valid_payment_gateway($gateway_id)
    {
        $availabel_gateways = $this->get_available_gateway();
        
        return isset( $availabel_gateways[$gateway_id]);
    }
}
