<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header( 'order' );
?>
<?php while ( have_posts() ) : the_post(); ?>
    <?php $order = txp_get_order( $post); ?>
    <div id="property-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="summary entry-summary">
            <h3 itemprop="name" class="product_title entry-title"><?php echo esc_html( get_the_title() ); ?></h3>
            <p>
                <?php echo sprintf( __( '<strong>Order Status:</strong> %s', 'txp' ), esc_html( $order->get_status() ) ); ?>
            </p>
            <?php if ( $order->payment_method ) { ?>
                <p>
                    <?php echo sprintf( __( '<strong>Payment method:</strong> %s', 'txp' ), ucfirst( esc_html( $order->payment_method ) ) ); ?>
                </p>
            <?php } ?>
            <?php if ( $order->is_completed() ) { ?>
                <p>
                    <?php echo sprintf( __( '<strong>Completed Date:</strong> %s', 'txp' ), esc_html( $order->completed_date ) ); ?>
                </p>
                <p>
                    <?php echo sprintf( __( '<strong>Transaction ID:</strong> %s', 'txp' ), esc_html( $order->transaction_id ) ); ?>
                </p>
            <?php } ?>
            </p>
            <p>
                <?php echo sprintf( __( '<strong>Amount: </strong>%s %s', 'txp' ), txp_currency($order->amount, $order->order_currency ), $order->order_currency ); ?>
            </p>
            <p>
                <?php echo sprintf( __( '<strong>Item: </strong> %s', 'txp' ),
                        '<a href="' . esc_attr( get_permalink( $order->property_id ) ) . '">' . 
         esc_html( get_the_title( $order->property_id ))
                        .'</a>');
                ?>
            </p>
            <?php if ( "awaiting_payment" == $order->get_status() ) { ?>
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
                    } elseif ( $order->payment_method ) {
                        $selected_method = $order->payment_method;
                    } else {
                        $method_ids = array_keys($available_gateways);
                        $selected_method = array_shift( $method_ids );
                    }

                    foreach ($available_gateways as $id => $gateway) {
                        ?>
                    <span class="payment_method">
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
                    </span>&nbsp;
                    <?php } ?>
                    <input type='hidden' name='post_id' value='<?php echo $order->property_id;?>' />
                </div>
                <input type='submit' value='<?php echo __( 'Do payment', 'txp' ); ?>' />
            </form>
            <?php } ?>
        </div>
    </div>
<?php endwhile; // end of the loop.  ?>
<?php get_footer( 'order' ); ?>
