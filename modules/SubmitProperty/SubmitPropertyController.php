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
use TreXanhProperty\Core\PaymentGateway\PaymentGatewayService;

/* 
 * Handling submit property flow.
 * Now support end user submit property : with/without payment, with/without review
 */

class SubmitPropertyController {
    
     protected static function check_enable_submission_status()
     {
        $user_can_submit = Config::get_setting('enable_property_submission', 'general', false);
        if ( ! $user_can_submit) {
            wp_die(
                __( 'You cannot submit new property in this time. The feature has been disabled by site administrator. Please go back and try later.', 'txp' ),
                    __( 'Feature not allowed', 'txp' )
            );
        }
     }


     public static function form() {
         
         self::check_enable_submission_status();
         
        ob_start();
        txp_get_template_part('submit-property.php');
        $html = ob_get_clean();
        return $html;
    }
    
    public static function save() {
        
        self::check_enable_submission_status();
        
        $data = $_POST;
        $post_status = self::get_status_for_new_property();
        $property = array(
            'post_title' => sanitize_text_field( $data['post_title'] ),
            'post_content' => sanitize_text_field( $data['post_content'] ),
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
        $property = txp_get_property($property_id);
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
                        'property'          => $property,
                        'order'         => $order,
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
    protected static function get_payment_gateway( $payment_method )
    {
        $gateways = PaymentGatewayService::getInstance();
        return $gateways->get( $payment_method );
    }

    public static function payment() {
        
        self::check_enable_submission_status();
        
        $app = \Slim\Slim::getInstance();
        $post_id = $app->request->post('post_id');
        $payment_method = $app->request->post('payment_method');
        
        $the_post = get_post($post_id);
        if (!$the_post) {
            return __('Not found property', 'txp');
        }
        
        $property = txp_get_property( $the_post );
        $order = $property->get_order();
        
        $gateways = PaymentGatewayService::getInstance();
        
        if ( ! $gateways->is_valid_payment_gateway( $payment_method ) ) {
            ob_start();
            txp_get_template_part(
                'submit-property/select-payment-method.php', 
                array( 
                    'property'  => $property,
                    'order'     => $order,
                    'message' => __( 'Invalid payment method.', 'txp' )
                )
            );
            return ob_get_clean();
        }
        
        $gateway = self::get_payment_gateway($payment_method);
        
        update_post_meta($order->id, '_payment_method', $gateway->id);
        
        ob_start();
        /* @var $result \TreXanhProperty\Core\PaymentGateway\PaymentResult */
        $result = $gateway->process_payment($order);
        if ($result->is_success()) {
            return self::payment_success_handler( $order, $result->get_transaction_id() );
        } else if ($result->is_error()) {
            txp_get_template_part( 
                'submit-property/select-payment-method.php', 
                array( 
                    'property'  => $property,
                    'order'     => $order,
                    'message' => $result->get_message()
                )
            );
            return ob_get_clean();
        }
        return ob_get_clean();
    }
    
    public static function payment_status() {
        
        self::check_enable_submission_status();
        
        $order_id = isset($_GET['txp_order']) ? $_GET['txp_order'] : 0;
        $the_post = get_post($order_id);
        if ( !$the_post ) {
            return __('No order found.', 'txp');
        }
        
        $order = txp_get_order( $the_post );
        $property = txp_get_property( $order->property_id );
        
        if (isset($_GET['action']) && $_GET['action'] == 'cancel') {
            txp_get_template_part( 
                'submit-property/select-payment-method.php', 
                array( 
                    'property'  => $property,
                    'order'     => $order,
                )
            );
            return ob_get_clean();
        }
        
        $gateway = self::get_payment_gateway($order->payment_method);
        
        /* @var $result \TreXanhProperty\Core\PaymentGateway\PaymentResult */
        $result = $gateway->payment_complete($order);
        ob_start();
        
        if ($result->is_success()) {
            return self::payment_success_handler($order, $result->get_transaction_id());
        } else if ($result->is_error()) {
            echo $result->get_message();
        } elseif ($result->is_redirect()) {
            // Redirect to offsite payment gateway
            $location = $result->get_redirect();
            header('Location: ' . $location);
            exit();
        }
        
        return ob_get_clean();
    }
    
    protected static function payment_success_handler( $order, $transaction_id = '' )
    {
        add_action( 'trexanhproperty_payment_completed', array( __CLASS__, 'send_email_payment_success' ) );
        $order->payment_complete( $transaction_id );
        $property_id = $order->property_id;
        $property = new Property( $property_id );
        // Need approve by administrator
        if ( self::is_approve_required() ) {
            self::send_email_payment_success($property->post, 'require_admin_to_approve');
            return __("Your property is submitted successfully. It now need to be approved by admin before it can be listed.", 'txp');
        }

        // Pulish property
        wp_update_post(array('ID' => $property_id, 'post_status' => 'publish'));
        do_action( 'trexanhproperty_payment_completed', $property->post );
        return sprintf(__('Your property is submitted and published successfully! <a href="%s">Click here</a> to view your property.'), get_permalink( $property_id ));
        
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

    /**
     * 
     * @param Property $post
     * @param string $status
     * @return boolean
     */
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
            'payment_method' => $order->get_payment_gateway()->title,
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