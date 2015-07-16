<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$num_orders = 0;
$all_post = array_merge( $published_posts, $not_approved_posts, $awaiting_payment_posts );
foreach ($all_post as $p) {
    $property = txp_get_property( $p );
    $order = $property->get_order();
    if ( $order->id ) {
        $num_orders++;
    }
}
?>
<div class="tabs">
    <ul class="tab-links">
        <li class="active">
            <a href="#published-posts">
                <?php echo sprintf( __( 'Published <sup>(%s)</sup>', 'txp' ), count($published_posts) ); ?>
            </a>
        </li>
        <li>
            <a href="#pending-posts">
                <?php echo sprintf( __( 'Pending <sup>(%s)</sup>', 'txp' ), count($not_approved_posts) ); ?>
            </a>
        </li>
        <li>
            <a href="#order">
                <?php echo __( 'Orders', 'txp' ); ?> <sup>(<?php echo $num_orders; ?>)</sup>
            </a>
        </li>
    </ul>
 
    <div class="tab-content">
        <div id="published-posts" class="tab active">
            <?php if (count($published_posts) === 0) { 
                echo "No published properties";
            } else { ?>
            <ul>
                <?php foreach ($published_posts as $p) : 
                    $property = txp_get_property( $p );
                    $order = $property->get_order();
                    ?>
                <li>
                    <a href="<?php echo $p->guid; ?>">
                        <?php echo esc_html( $p->post_title ); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php } ?>
        </div>
 
        <div id="pending-posts" class="tab">
            <?php if (count($not_approved_posts) === 0) { 
                echo "No awaiting approval properties";
            } else { ?>
            <ul>
                <?php foreach ($not_approved_posts as $p) : ?>
                <li>
                    <?php echo esc_html( $p->post_title ); ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php } ?>
        </div>
 
        <div id="order" class="tab">
            <table class="table">
            <?php foreach ($all_post as $p) : 
                $property = txp_get_property( $p );
                $order = $property->get_order();
                if ( $order->id ) { ?>
                <tr>
                    <td>
                        <a href="<?php echo site_url( '?p=' . $order->id ) ?>">
                            <?php echo __( "Order", "" ); ?> #<?php echo $order->id?>
                        </a>
                        <label class="text-muted">
                            <small>(<?php echo $order->get_status(); ?>)</small>
                        </label>
                        <?php if ( $order->get_status() === 'awaiting_payment' ) { ?>
                            <a class="pull-right" href="<?php echo site_url( '?p=' . $order->id ) ?>">
                                <?php echo __( "Pay now", "" ); ?>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
<?php
wp_register_script('tab_script', TREXANHPROPERTY__PLUGIN_URL . 'assets/js/tab.js');
wp_print_scripts('tab_script');