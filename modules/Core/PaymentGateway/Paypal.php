<?php

namespace TreXanhProperty\Core\PaymentGateway;

use TreXanhProperty\Core\Config;
use TreXanhProperty\Core\Order;
use Omnipay\Omnipay;
use TreXanhProperty\Core\PaymentGateway\PaymentResult;

class Paypal extends AbstractPaymentGateway
{
    public function __construct()
    {
        $this->id = 'paypal';
        $this->section_setting_description = __( 'Settings authentication with paypal.', 'txp' );
        $this->title = 'Paypal';
    }
    
    /**
     * 
     * @return string
     */
    public function get_setting_fields()
    {
        return array(
            'paypal_enable' => array(
                'title' => __( 'Enable', 'txp' ),
                'type' => 'checkbox',
                'checked_value' => 'yes'
            ),
            'paypal_username' => array(
               'title' => __( 'Username', 'txp' ),
                'type' => 'text'
            ),
            'paypal_password' => array(
                'title' => __( 'Password', 'txp' ),
                'type' => 'text'
            ),
            'paypal_signature' => array(
                'title' => __( 'Signature', 'txp' ),
                'type' => 'textarea',
                'attributes' => array(
                    'cols' => 40
                ),
            ),
            'paypal_use_sanbox' => array(
                'title' => __( 'Sanbox', 'txp' ),
                'type' => 'checkbox',
                'checked_value' => 'on',
            ),
            'paypal_sanbox_username' => array(
                'title' => __( 'Sanbox Username', 'txp' ),
                'type' => 'text'
            ),
            'paypal_sanbox_password' => array(
                'title' => __( 'Sanbox Password', 'txp' ),
                'type' => 'text',
            ),
            'paypal_sanbox_signature' => array(
                'title' => __( 'Signature', 'txp' ),
                'type' => 'textarea',
                'attributes' => array(
                    'cols' => 40
                ),
            ),
        );
    }
    
    /**
     * 
     * @param int|string|Order|\WP_Post $order_id
     * @return PaymentResult
     */
    public function payment_complete( $order_id )
    {
        $order = $this->get_order( $order_id );
        
        $gateway = $this->get_gateway();
        
        $response = $gateway->completePurchase(array(
            'amount' => floatval($order->amount),
        ))->send();
        
        return $this->build_result( $response );
    }
    
    /**
     * 
     * @param \Omnipay\Common\Message\ResponseInterface $response
     * @return PaymentResult
     */
    protected function build_result( $response )
    {
        if ($response->isSuccessful()) {
            return new PaymentResult(PaymentResult::SUCCESS, '', $response->getTransactionReference());
        } elseif ($response->isRedirect()) {
            // Redirect to offsite payment gateway
            return $response->redirect();
        } else {
            // Payment failed
            return new PaymentResult(PaymentResult::ERROR, $response->getMessage());
        }
    }
    
    /**
     * 
     * @param int|string|Order|\WP_Post $order_id
     * @return PaymentResult
     */
    public function process_payment( $order_id )
    {
        $order = $this->get_order( $order_id );
        
        $gateway = $this->get_gateway();
        
        $response = $gateway->purchase( array(
            'amount' => floatval($order->amount),
            'returnUrl' => get_permalink(get_page_by_path('submit-property-payment-status')) . '?action=success&txp_order=' . $order->id,
            'cancelUrl' => get_permalink(get_page_by_path('submit-property-payment-status')) . '?action=cancel&txp_order=' . $order->id,
        ))->send();
        
        return $this->build_result( $response );
    }
    
    /**
     * 
     * @return \Omnipay\PayPal\ExpressGateway
     */
    protected function get_gateway()
    {
        $payment_settings = Config::get_settings('payment');
        $gateway_prefix = $this->id . '_';
        $testmode = false;
        if ($payment_settings[$gateway_prefix . 'use_sanbox']) {
            $gateway_prefix .= 'sanbox_';
            $testmode = true;
        }
        
        $parameters = array(
            'username' => $payment_settings[$gateway_prefix . 'username'],
            'password' => $payment_settings[$gateway_prefix . 'password'],
            'signature' => $payment_settings[$gateway_prefix . 'signature'],
            'currency' => Config::get_setting('currency', 'general'),
            'testMode' => $testmode,
        );
        
        $gateway = Omnipay::create('PayPal\Express');
        $gateway->initialize($parameters);
        
        return $gateway;
    }
    
    public function is_enabled()
    {
        return Config::get_setting('paypal_enable', 'payment', 'no') == 'yes';
    }
}
