<?php

namespace TreXanhProperty\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EmailTemplate
{
    protected $message;
    
    protected $subject;
    
    public function __construct( $subject = '', $message = '' )
    {
        $this->subject = $subject;
        $this->message = $message;
    }


    /**
     * 
     * @param array $values
     * @return string
     */
    public function get_message( $values = array() )
    {
        if ( ! $values ) {
            return $this->message;
        }
        
        $message = $this->message;
        foreach ( $values as $key => $value ) {
            $message = str_replace( '{' . $key .'}', $value, $message );
        }
        
        return $message;
    }
    
    /**
     * 
     * @param array $values
     * @return string
     */
    public function get_subject( $values = array() )
    {
        if ( ! $values ) {
            return $this->subject;
        }
        
        $subject = $this->subject;
        foreach ( $values as $key => $value ) {
            $subject = str_replace( '{' . $key .'}', $value, $subject );
        }
        
        return $subject;
    }

    public static function load( $key )
    {
        switch ( $key ) {
            case 'payment_success':
                $subject = '[{site_name}] - Your order #{order_id} is complete';
                $message = 'Hi {recipient},<br>'
                        . '<p>Your order #{order_id} for the property <strong>{property_title}</strong> is complete. The property is now published.</p>'
                        . '<p>Below is order details for your reference.</p>'
                        . '<p><strong>Order #{order_id}</strong</p>'
                        . '<table cellspacing="0" cellpadding="6" style="width:100%;border:1px solid #eee" border="1">'
                            . '<thead><tr>'
                                . '<th scope="col" style="text-align:left;border:1px solid #eee;padding:12px">Property title</th>'
                                . '<th scope="col" style="text-align:left;border:1px solid #eee;padding:12px">Payment method</th>'
                                . '<th scope="col" style="text-align:left;border:1px solid #eee;padding:12px">Transaction ID</th>'
                                . '<th scope="col" style="text-align:left;border:1px solid #eee;padding:12px">Price</th>'
                            . '</tr></thead>'
                            . '<tbody><tr>'
                                . '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px">{property_title}</td>'
                                . '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;padding:12px">{payment_method}</td>'
                                . '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;padding:12px">{transaction_id}</td>'
                                . '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;padding:12px"><span>{amount}</span></td>'
                            . '</tr></tbody>'
                            . '<tfoot>'
                                . '<tr>'
                                    . '<th scope="row" colspan="3" style="text-align:right;border:1px solid #eee;padding:12px">Total</th>'
                                    . '<td style="text-align:left;border:1px solid #eee;padding:12px"><span>{amount}</span></td>'
                                . '</tr>'
                            . '</tfoot>'
                        . '</table>'
                        . '<p>You can view your property <a href="{property_link}">here</a>.</p>'
                        . '<div>Best regards,</div>'
                        . '<div>{site_name}</div>';
                return new self( $subject, $message );
            case 'payment_success_require_admin_to_approve':
                $subject = '[{site_name}] - Your order #{order_id} is complete';
                $message = 'Hi {recipient},<br>'
                        . '<p>Your order #{order_id} for the property <strong>{property_title}</strong> is complete. The property now need admin to approve to be published.</p>'
                        . '<p>Below is the order details for your reference.</p>'
                        . '<p><strong>Order #{order_id}</strong</p>'
                        . '<table cellspacing="0" cellpadding="6" style="width:100%;border:1px solid #eee" border="1">'
                            . '<thead><tr>'
                                . '<th scope="col" style="text-align:left;border:1px solid #eee;padding:12px">Property title</th>'
                                . '<th scope="col" style="text-align:left;border:1px solid #eee;padding:12px">Payment method</th>'
                                . '<th scope="col" style="text-align:left;border:1px solid #eee;padding:12px">Transaction ID</th>'
                                . '<th scope="col" style="text-align:left;border:1px solid #eee;padding:12px">Price</th>'
                            . '</tr></thead>'
                            . '<tbody><tr>'
                                . '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;word-wrap:break-word;padding:12px">{property_title}</td>'
                                . '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;padding:12px">{payment_method}</td>'
                                . '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;padding:12px">{transaction_id}</td>'
                                . '<td style="text-align:left;vertical-align:middle;border:1px solid #eee;padding:12px"><span>{amount}</span></td>'
                            . '</tr></tbody>'
                            . '<tfoot>'
                                . '<tr>'
                                    . '<th scope="row" colspan="3" style="text-align:right;border:1px solid #eee;padding:12px">Total</th>'
                                    . '<td style="text-align:left;border:1px solid #eee;padding:12px"><span>{amount}</span></td>'
                                . '</tr>'
                            . '</tfoot>'
                        . '</table><br>'
                        . '<div>Best regards,</div>'
                        . '<div>{site_name}</div>';
                return new self( $subject, $message );
            case 'submit_property_success_require_admin_to_approve':
                $subject = '[{site_name}] - Your property has been submitted';
                $message = 'Hi {recipient},<br>'
                        . '<p>Your property <strong>{property_title}</strong> has been submitted successfully! '
                        . 'It need admin\'s approval to be published. This may take a while.</p>'
                        . '<br><div>Best regards,</div>'
                        . '<div>{site_name}</div>';
                return new self( $subject, $message );
            case 'submit_property_success':
                $subject = '[{site_name}] - Your property has been published';
                $message = 'Hi {recipient},<br>'
                        . '<p>The property <strong>{property_title}</strong> you have submitted has been published!</p>'
                        . '<p>You can view your property <strong><a href="{property_link}">here</a></strong>.</p>'
                        . '<br><div>Best regards,</div>'
                        . '<div>{site_name}</div>';
                return new self( $subject, $message );
        }
    }
}
