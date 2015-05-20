<?php
wp_enqueue_script( 'credit-card-form', TREXANHPROPERTY__PLUGIN_URL . 'assets/js/select-payment-method.js', array('jquery') );
?>
<p><?php echo __( 'You need to do payment to complete your submission.', 'txp' ); ?></p>
<div><strong><?php echo __( 'Review Order', 'txp' ); ?></strong></div>
<table>
<tr>
<td style='width:30%;'><?php echo __( 'Property title', 'txp' ); ?></td>
<td><?php echo esc_html( $property->post->post_title ); ?></td>
</tr>
<tr>
<td><?php echo __( 'Amount', 'txp' ); ?></td>
<td><?php echo txp_currency($order->amount); ?></td>
</tr>
<tr>
<td><?php echo __( 'Status', 'txp' ); ?></td>
<td><?php echo esc_html( $order->post->post_status );?></td>
</tr>
</table>
<p>
<?php
if ( isset($message) ) {
    echo '<p>' . $message . '</p>';
}
?>
<form method='post' id="payment_property_form" action='<?php echo site_url('/submit-property-payment/'); ?>'>
    <div id="payment_method_box">
        <div><strong><?php echo __( 'Payment method', 'txp' ); ?></strong></div>
        <?php
        $available_gateways = txp_get_available_payment_gateways();
        if (! $available_gateways) {
            echo __('No payment method available found.', 'txp');
        }
        $selected_method = '';
        if (isset($_POST['payment_method'])) {
            $selected_method = $_POST['payment_method'];
        } else {
            $method_ids = array_keys($available_gateways);
            $selected_method = array_shift( $method_ids );
        }
        
        foreach ($available_gateways as $id => $gateway) {
            ?>
        <div class="payment_method">
            <input type="radio" id="payment_method_<?php echo $id; ?>" <?php checked( $selected_method, $id); ?> name="payment_method" value="<?php echo $id; ?>">
            <label for="payment_method_<?php echo $id; ?>">
                <?php echo $gateway->title; ?>
                <div id="<?php echo $gateway->id; ?>_payment_description_box" style="display: none;">
                    <?php echo $gateway->get_description(); ?>
                    <?php
                    if ( $gateway->has_custom_html ) {
                        echo $gateway->get_custom_html( $order );
                    }
                    ?>
                </div>
            </label>
        </div>
        <?php
        }
        ?>
        <input type='hidden' name='post_id' value='<?php echo $property->id;?>' />
    </div>
    <input type='submit' value='<?php echo __( 'Do payment', 'txp' ); ?>' />
</form>