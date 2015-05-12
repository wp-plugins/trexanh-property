<?php

namespace TreXanhProperty\SubmitProperty;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\PropertyForm;
use TreXanhProperty\Core\Property;
use TreXanhProperty\Core\Order;
use TreXanhProperty\Core\Config;
use TreXanhProperty\Core\EmailTemplate;
use Omnipay\Omnipay;

/* 
 * Handling submit property flow.
 * Now support end user submit property : with/without payment, with/without review
 */

class SubmitPropertyController {
    public static function form() {
        ob_start();
        txp_get_template_part('submit-property.php');
        $html = ob_get_clean();
        return $html;
    }
    
    public static function save() {
        $data = $_POST;
        $post_status = self::get_status_for_new_property();
        $property = array(
            'post_title' => sanitize_text_field( $data['post_title'] ),
            'post_status' => $post_status,
            'post_type' => Property::get_post_type(),
        );
        $is_user_logged_in = is_user_logged_in();
        /* handle case of property is submitted by guest */
        if ( !$is_user_logged_in ) {
            $result = self::pre_handle_guest_submit();
            if (!$result['success']) {
                return $result['view'];
            }
        }
        $property['post_author'] = get_current_user_id();
        $property_id = wp_insert_post($property);
        PropertyForm::save($property_id);
        if ( !$is_user_logged_in ) {
            self::post_handle_guest_submit($property_id);
        }
        // set order
        $general_settings = Config::get_settings('general');
        $payment_required = isset($general_settings['require_payment_to_submit']) ? $general_settings['require_payment_to_submit'] : null ;
        if ($payment_required) {
            // create order
            $order_data = array(
                'post_title' => sprintf(__('Order #%s', 'txp'), $property_id),
                'amount' => isset($general_settings['submit_fee']) ? $general_settings['submit_fee'] : 0,
                'created_date' => date("Y-m-d H:i:s"),
            );
            $order = Order::create_order($order_data);
            update_post_meta($order->id, '_property_id', $property_id);
            update_post_meta($order->id, '_customer_ip_address', txp_get_ip());
            update_post_meta($property_id, Property::$input_prefix . '_order_id', $order->id);
        }
        
        if ($post_status === TREXANHPROPERTY_STATUS_PENDING) {
            if ($payment_required) {
                ob_start();
                txp_get_template_part( 
                    'submit-property/select-payment-method.php', 
                    array( 
                        'post'          => $property,
                        'order'         => $order ,
                        'order_data'    => $order_data,
                        'post_id'       => $property_id
                    )
                );
                return ob_get_clean();
            } else {
                self::send_email_submit_property_success( get_post( $property_id ), 'require_admin_to_approve' );
                return "Your property is submitted successfully. It now need to be approved by admin before it can be listed.";
            }
        }
        if ($post_status === TREXANHPROPERTY_STATUS_PUBLISHED) {
            self::send_email_submit_property_success( get_post( $property_id ), '' );
            return "Your property is submitted and published successfully! <a href='" . post_permalink( $property_id ) . "'>Click here</a> to view your property.";
        }
        return "Successfully!";
    }
    
    /**
     * handle submission by a guest after the property is created successfully
     * 
     * @param int $property_id
     */
    protected static function post_handle_guest_submit($property_id)
    {
        $data = $_POST;
        if ( ! empty( $data['create_new_account'] ) ) {
            // if guest choose to create new account then do nothing
            return;
        }
        // otherwise, store guest email for sending her/him notification emails later
        add_post_meta( $property_id, 'guest_email', sanitize_email( $data['email'] ) );
    }
    
    /**
     * handle submission by a guest before the property is created
     * 
     * @return type
     */
    protected static function pre_handle_guest_submit()
    {
        $data = $_POST;
        /* $view will be returned on submit failed */
        $view = "<h4>" . __( "Submission failed!", "txp" ) . "</h4>";
        $backLink = "<a href='javascript:history.go(-1)'>"
                        . __( "Go back", "txp" )
                    . "</a>";
        $guest_email = sanitize_email( $data['email'], '' );
        if ( empty( $guest_email ) ) {
            $view .= "<p>" . __( "Please provide your email address.", "txp" ) . "</p>";
            $view .= $backLink;
            return array(
                'success' => false,
                'view' => $view,
            );
        }

        // guest choose to create new account
        if ( ! empty( $data['create_new_account'] ) ) {
            // create new account
            $splittedEmail = explode( "@", $guest_email );
            $user_id = register_new_user( $splittedEmail[0], $guest_email );
            if ( is_wp_error( $user_id ) ) {
                // register error then so error messages
                $errors = $user_id->errors;
                $view .= "<h5>" . __( "Registration failed!", "txp" ) . "</h5>";
                foreach ( $errors as $key => $messages ) {
                    foreach ( $messages as $message ) {
                        $view .= $message."<br>";
                    }
                }
                $view .= $backLink;
                return array(
                    'success' => false,
                    'view' => $view,
                );
            }
            // register success then auto log user in
            $user = get_user_by( 'id', $user_id ); 
            wp_set_current_user( $user_id, $user->user_login );
            wp_set_auth_cookie( $user_id );
            do_action( 'wp_login', $user->user_login );
        }
        return array(
            'success' => true,
            'view' => $view,
        );
    }
    
    /**
     * 
     * @return \Omnipay\Common\AbstractGateway
     */
    protected static function get_payment_gateway()
    {
        $payment_settings = Config::get_settings('payment');
        $enabled_gateway = 'paypal';
        $gateway_prefix = $enabled_gateway . '_';
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

    public static function payment() {
        $app = \Slim\Slim::getInstance();
        $post_id = $app->request->post('post_id');
        $the_post = get_post($post_id);
        if (!$the_post) {
            return __('Not found property', 'txp');
        }
        
        $property = new Property($the_post);
        $order = $property->get_order();
        
        update_post_meta($order->id, '_payment_method', 'Paypal');
        
        $gateway = self::get_payment_gateway();
        $response = $gateway->purchase( array(
            'amount' => floatval($order->amount),
            'returnUrl' => get_permalink(get_page_by_path('submit-property-payment-status')) . '?action=success&txp_property=' . $post_id,
            'cancelUrl' => get_permalink(get_page_by_path('submit-property-payment-status')) . '?action=cancel&txp_property=' . $post_id,
        ))->send();
        
        if ($response->isSuccessful()) {
            // Payment was successful
        } elseif ($response->isRedirect()) {
            // Redirect to offsite payment gateway
            $response->redirect();
        } else {
            // Payment failed
            echo $response->getMessage();
        }
        
        ob_start();
        return ob_get_clean();
    }
    
    public static function payment_success() {
        
        $property_id = isset($_GET['txp_property']) ? $_GET['txp_property'] : 0;
        $the_post = get_post($property_id);
        if (!$the_post) {
            return __('Not found property', 'txp');
        }
        $property = new Property($the_post);
        
        $gateway = self::get_payment_gateway();
        
        $order = $property->get_order();
        
        $response = $gateway->completePurchase(array(
            'amount' => floatval($order->amount),
        ))->send();
        
        ob_start();
        if ($response->isSuccessful()) {
            add_action( 'trexanhproperty_payment_completed', array( __CLASS__, 'send_email_payment_success' ) );
            $order->payment_complete( $response->getTransactionReference() );
            // Need approve by administrator
            if ( self::is_approve_required() ) {
                self::send_email_payment_success($property->post, 'require_admin_to_approve');
                return __("Your property is submitted successfully. It now need to be approved by admin before it can be listed.", 'txp');
            }
            
            // Pulish property
            wp_update_post(array('ID' => $property_id, 'post_status' => 'publish'));
            do_action( 'trexanhproperty_payment_completed', $property->post );
            return sprintf(__('Your property is submitted and published successfully! <a href="%s">Click here</a> to view your property.'), get_permalink( $property_id ));
            
        } elseif ($response->isRedirect()) {
            // Redirect to offsite payment gateway
            $response->redirect();
        } else {
            // Payment failed
            echo $response->getMessage();
        }
        return ob_get_clean();
    }
    
    /**
     * 
     * @return boolean | string
     */
    protected static function is_approve_required()
    {
        $settings = Config::get_settings('general');
        $approve_required = isset($settings['require_admin_to_approve']) ? $settings['require_admin_to_approve'] : false;
        if ( false === $approve_required ) {
            return false;
        }
        
        return true;
    }

    public static function send_email_payment_success( $post, $status = '' )
    {
        $key = 'payment_success';
        if ( $status == 'require_admin_to_approve' ) {
            $key = 'payment_success_require_admin_to_approve';
        }
        $email_template = EmailTemplate::load($key);
        $author = self::get_property_author($post);
        
        $property = new Property($post);
        /* @var $post Property */
        $order = $property->get_order();
        
        $subject_values = array(
            'site_name' => get_bloginfo(),
            'order_id' => $order->id,
        );
        
        $message_values = array(
            'recipient' => $author['name'],
            'payment_method' => 'Paypal',
            'transaction_id' => $order->transaction_id,
            'amount' => txp_currency($order->amount),
            'property_title' => $post->post_title,
            'property_link' => $post->guid,
            'site_name' => get_bloginfo(),
            'order_id' => $order->id,
        );
        
        return self::send_email(
                $author['email'],
                $email_template->get_subject( $subject_values ),
                $email_template->get_message( $message_values )
        );
    }
    
    protected static function send_email( $to, $subject, $message ) {
        $headers = array(
            'Content-Type: text/html; charset=UTF-8'
        );
        
        return wp_mail( $to, $subject, $message, $headers );
    }


    public static function send_email_submit_property_success( $property, $status = '' )
    {
        $key = 'submit_property_success';
        if ( $status == 'require_admin_to_approve' ) {
            $key = 'submit_property_success_require_admin_to_approve';
        }
        $email_template = EmailTemplate::load($key);
        $author = self::get_property_author($property);

        $subject_values = array(
            'site_name' => get_bloginfo(),
        );
        
        $message_values = array(
            'recipient' => $author['name'],
            'property_title' => $property->post_title,
            'property_link' => $property->guid,
            'site_name' => get_bloginfo(),
        );

        return self::send_email(
                $author['email'],
                $email_template->get_subject( $subject_values ),
                $email_template->get_message( $message_values )
        );
    }
    
    protected static function get_status_for_new_property() {
        $general_settings = Config::get_settings('general');
        $approveRequired = isset($general_settings['require_admin_to_approve']) ? $general_settings['require_admin_to_approve'] : false;
        $paymentRequired = isset($general_settings['require_payment_to_submit']) ? $general_settings['require_payment_to_submit'] : false;
        if (!$approveRequired && !$paymentRequired) {
            return TREXANHPROPERTY_STATUS_PUBLISHED;
        }
        return TREXANHPROPERTY_STATUS_PENDING;
    }
    
    /**
     * get author of a property
     * author can be a member or a guest
     * in case of guest, only guest's email is stored in a post meta field
     * 
     * @param object $property
     */
    protected static function get_property_author($property)
    {
        $author_id = (int) $property->post_author;
        if ( $author_id ) {
            // property submitted by a member
            $author = get_userdata($author_id);
            $author_name = $author->display_name;
            $author_email = $author->user_email;
        } else {
            // property submitted by a guest
            $property_id = $property->ID;
            $author_name = '';
            $author_email = get_post_meta( $property_id, 'guest_email', true );
        }
        return array(
            'name' => $author_name,
            'email' => $author_email,
        );
    }
}