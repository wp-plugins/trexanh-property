<?php

namespace TreXanhProperty\Core\PaymentGateway;

use Omnipay\Omnipay;
use TreXanhProperty\Core\Config;

class Stripe extends AbstractPaymentGateway
{
    public function __construct()
    {
        $this->id = 'stripe';
        $this->title = 'Stripe';
        $this->description = '';
        $this->has_custom_html = true;
        $this->section_setting_description = __('Settings authentication with stripe.');
    }
    
    /**
     * 
     * @return boolean
     */
    protected function is_sanbox()
    {
        $sanbox = Config::get_setting('stripe_use_sanbox', 'payment', false);
        return ($sanbox == 'yes');
    }

    public function get_custom_html( $order )
    {
        if ($this->is_sanbox()) {
            $publicable_key = Config::get_setting('stripe_sanbox_publishable_key', 'payment');
        } else {
            $publicable_key = Config::get_setting('stripe_publishable_key', 'payment');
        }
        
        // Convert to cent
        $amount = $order->amount * 100;
        wp_enqueue_script('stripe-checkout', 'https://checkout.stripe.com/checkout.js', array('jquery'));
        wp_enqueue_script('txp-stripe-checkout', TREXANHPROPERTY__PLUGIN_URL . 'assets/js/stripe-checkout.js', array('stripe-checkout','jquery'));
        ?>
<script>
    var StripeParams = {
        key : '<?php echo $publicable_key; ?>',
        payment_method_id: '<?php echo $this->id; ?>',
        sitename: '<?php echo get_bloginfo() ?>',
        amount: <?php echo $amount; ?>
    };
</script>
<?php
    }

    public function get_setting_fields()
    {
        return array(
            'stripe_enable' => array(
                'title' => __( 'Enable', 'txp' ),
                'type' => 'checkbox',
                'checked_value' => 'yes',
            ),
            'stripe_secret_key' => array(
                'title' => __( 'Live Secret Key' , 'txp'),
                'type' => 'text'
            ),
            'stripe_publishable_key' => array(
                'title' => __( 'Live Publishable Key' , 'txp'),
                'type' => 'text'
            ),
            'stripe_use_sanbox' => array(
                'title' => __( 'Enable Test Mode' ),
                'type' => 'checkbox',
                'checked_value' => 'yes',
            ),
            'stripe_sanbox_secret_key' => array(
                'title' => __( 'Test Secret Key' , 'txp'),
                'type' => 'text'
            ),
            'stripe_sanbox_publishable_key' => array(
                'title' => __( 'Test Publishable Key' , 'txp'),
                'type' => 'text'
            ),
        );
    }

    /**
     * 
     * @return \Omnipay\Stripe\Gateway
     */
    protected function get_gateway()
    {
        $gateway = Omnipay::create('Stripe');
        
        if ( $this->is_sanbox() ) {
            $apiKey = Config::get_setting('stripe_sanbox_secret_key', 'payment');
        } else {
            $apiKey = Config::get_setting('stripe_secret_key', 'payment');
        }
        
        $gateway->initialize(array(
            'apiKey' => $apiKey
        ));
        
        return $gateway;
    }

    /**
     * 
     * @param int|string|\WP_Post|\TreXanhProperty\Core\Order $order_id
     * @return \TreXanhProperty\Core\PaymentGateway\PaymentResult
     */
    public function process_payment( $order_id )
    {
        $order = $this->get_order( $order_id );
        $gateway = $this->get_gateway();
        
        $card_token = isset($_POST['stripeToken']) ? sanitize_text_field($_POST['stripeToken']) : false;
        
        if ( ! $card_token ) {
            return new PaymentResult(PaymentResult::ERROR, __( 'Card invalid. Please check again.', 'txp' ));
        }
        
        /* @var $response \Omnipay\Stripe\Message\Response */
        
        $response = $gateway->purchase(array(
            // Number format is required by Stripe Payment Gateway.
            'amount' => number_format($order->amount, 2),
            'currency' => $order->order_currency,
            'token' => $card_token,
        ))->send();
        
        if ($response->isSuccessful()) {
            return new PaymentResult(PaymentResult::SUCCESS, '', $response->getTransactionReference());
        }
        
        return new PaymentResult(PaymentResult::ERROR, $response->getMessage());
    }
    
    public function is_enabled()
    {
        return Config::get_setting('stripe_enable', 'payment', 'no') == 'yes';
    }
}
