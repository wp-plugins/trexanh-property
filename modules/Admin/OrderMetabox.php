<?php

namespace TreXanhProperty\Admin;

use TreXanhProperty\Core\Order;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OrderMetabox
{
    public static function add_meta_box()
    {
        add_meta_box(
            'txp_metabox_order',
            __( 'Order', 'txp' ),
            array(__CLASS__ , 'callback'),
            Order::POST_TYPE,
            'normal',
            'default'
        );
        
        add_meta_box(
            'txp_metabox_order_action',
            __( 'Order Action', 'txp' ),
            array(__CLASS__ , 'order_action_callback'),
            Order::POST_TYPE,
            'side',
            'default'
        );
    }
    
    public static function order_action_callback($post)
    {
        ?>
        <div class="submitbox">
            <div class="wide">
                <div class="left" id="delete-action"><?php

                        if ( current_user_can( 'delete_post', $post->ID ) ) {

                                if ( ! EMPTY_TRASH_DAYS ) {
                                        $delete_text = __( 'Delete Permanently', 'txp' );
                                } else {
                                        $delete_text = __( 'Move to Trash', 'txp' );
                                }
                                ?><a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo $delete_text; ?></a><?php
                        }
                ?></div>
                <input  type="submit" class="button-primary right" name="save" value="<?php echo __( 'Save Order', 'txp' ); ?>" />
                <div class="clear"></div>
            </div>
        </div>
        <?php
    }
    
    /**
     * 
     * @param string $post_id
     * @param string $post
     * @return void
     */
    public static function save_order($post_id, $post)
    {
        if ( $post->post_type != Order::POST_TYPE ) {
            return;
        }

        // Check if our nonce is set.
        if ( ! isset( $_POST['txp_metabox_order_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['txp_metabox_order_nonce'], 'txp_metabox_order' ) ) {
            return;
        }
    
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
        }


        if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
        }
        
        $order = new Order( $post );
        $order->update_status( sanitize_text_field($_POST['order_status']) );
        update_post_meta($post_id, '_payment_method', sanitize_text_field($_POST['payment_method']));
        update_post_meta($post_id, '_transaction_id', sanitize_text_field($_POST['transaction_id']));
    }
    
    public static function callback($order, $metabox )
    {
        $order = new Order( $order );
        wp_nonce_field( 'txp_metabox_order', 'txp_metabox_order_nonce' );
        
        ?>
        <style type="text/css">
                #post-body-content, #titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none }
                #txp_metabox_order .handlediv, #txp_metabox_order h3.hndle { display: none}
        </style>
        <h2><?php echo sprintf( __( 'Order #%s Details', 'txp' ), $order->id ); ?></h2>
        <i class="order-description">
            <?php
                echo sprintf(__('Payment via %s', 'txp'), $order->get_payment_gateway()->title);
                
                if ( $order->transaction_id ) {
                    echo ' (' . esc_html(  $order->transaction_id  ). ')';
                }
                
                if ( $order->customer_ip_address ) {
                    echo '. ';
                    echo sprintf( __( 'Client IP: %s', 'txp' ), $order->customer_ip_address );
                }
            ?>
        </i>
        <table class="form-table">
            <tr>
                <th>
                    <label><?php echo __('Submitted by:', 'txp'); ?></label>
                </th>
                <td>
                    <?php
                    $property = txp_get_property($order->property_id);
                    if ( $property->post->post_author ) {
                        $user = get_userdata( $property->post->post_author );
                        echo '<a href="' .admin_url('user-edit.php?user_id=' . $user->ID) . '">';
                        echo esc_html( $user->display_name );
                        echo '</a>';
                    } else {
                        echo get_post_meta( $order->property_id, 'guest_email', true);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>
                    <label><?php echo __('Amount: ', 'txp'); ?></label>
                </th>
                <td>
                    <?php echo txp_currency($order->amount, $order->order_currency ); ?>
                </td>
            </tr>
            <tr>
                <th>
                    <label><?php echo __('Item: ', 'txp'); ?></label>
                </th>
                <td>
                    <a href="<?php echo esc_attr( admin_url( 'post.php?post=' . $order->property_id . '&action=edit'  ) ); ?>">
                        <?php echo esc_html( get_the_title( $order->property_id )); ?>
                    </a>&nbsp; 
                    <a class="button button-small" href="<?php echo esc_attr( get_permalink( $order->property_id ) ); ?>">
                        <span class="dashicons dashicons-visibility"></span>&nbsp;<?php echo __( 'View Property', 'txp' ); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th>
                    <label><?php echo __( 'Order Status:', 'txp' ); ?></label>
                </th>
                <td>
                    <select name="order_status">
                        <option value="<?php echo Order::STATUS_AWAITING_PAYMENT ?>" <?php selected($order->get_status(), Order::STATUS_AWAITING_PAYMENT ); ?>>
                            <?php echo __( 'Awaiting Payment', 'txp' ); ?>
                        </option>
                        <option value="<?php echo Order::STATUS_COMPLETED ?>" <?php selected($order->get_status(), Order::STATUS_COMPLETED ); ?>>
                            <?php echo __( 'Completed', 'txp' ); ?>
                        </option>
                        <option value="<?php echo Order::STATUS_CANCELLED ?>" <?php selected($order->get_status(), Order::STATUS_CANCELLED ); ?>>
                            <?php echo __( 'Cancelled', 'txp' ); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label><?php echo __('Payment method: ', 'txp'); ?></label>
                </th>
                <td>
                    <select name="payment_method">
                    <?php
                    $gateways = txp_get_available_payment_gateways();
                    foreach ($gateways as $gateway) {
                        ?>
                        <option value="<?php echo esc_attr($gateway->id); ?>" <?php selected( $order->payment_method, $gateway->id ); ?>><?php echo esc_html($gateway->title); ?></option>
                        <?php
                    }
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label><?php echo __('Transaction ID: ', 'txp'); ?></label>
                </th>
                <td>
                    <input name="transaction_id" value="<?php echo esc_attr( $order->transaction_id ); ?>" />
                </td>
            </tr>
            <?php
                if ( Order::STATUS_COMPLETED == $order->get_status() ) {
            ?>
            <tr>
                <th>
                    <label><?php echo __('Completed Date:', 'txp'); ?></label>
                </th>
                <td>
                    <?php echo esc_html( $order->completed_date ); ?>
                </td>
            </tr>
                <?php }?>
        </table>
        <?php
    }

}