<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header( 'order' );
?>
<?php while ( have_posts() ) : the_post(); ?>
    <?php
    $order = new TreXanhProperty\Core\Order( $post );
    ?>
    <div id="property-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="summary entry-summary">
            <h3 itemprop="name" class="product_title entry-title"><?php the_title(); ?></h3>
            <p>
                <?php echo sprintf( __( '<strong>Order Status:</strong> %s', 'txp' ), esc_html( $order->get_status() ) ); ?>
                <?php
                if ( TreXanhProperty\Core\Order::STATUS_COMPLETED == $order->get_status() ) {
                    ?>
            <p>
                <?php echo sprintf( __( '<strong>Completed Date:</strong> %s', 'txp' ), esc_html( $order->completed_date ) ); ?>
            </p>
            <p>
                <?php echo sprintf( __( '<strong>Transaction ID:</strong> #%s', 'txp' ), esc_html( $order->transaction_id ) ); ?>
            </p>
                <?php
                }
                ?>
            </p>
            <p>
                <?php echo sprintf( __( '<strong>Amount: </strong>%s %s', 'txp' ), txp_currency($order->amount, $order->order_currency ), $order->order_currency ); ?>
            </p>
            <p>
                <?php echo sprintf( __( '<strong>Payment method:</strong> %s', 'txp' ), esc_html( $order->payment_method ) ); ?>
            </p>
            <p>
                <?php echo sprintf( __( '<strong>Item: </strong> %s', 'txp' ),
                        '<a href="' . esc_attr( get_permalink( $order->property_id ) ) . '">' . 
         esc_html( get_the_title( $order->property_id ))
                        .'</a>');
                ?>
            </p>
        </div>
    </div>
<?php endwhile; // end of the loop.  ?>
<?php get_footer( 'order' ); ?>
