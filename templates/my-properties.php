<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// dislay in tabs format - @link: http://inspirationalpixels.com/tutorials/creating-tabs-with-html-css-and-jquery
?>
<div class="tabs">
    <ul class="tab-links">
        <li class="active">
            <a href="#tab1">
                <?php
                echo sprintf( __( 'Published <sup>(%s)</sup>', 'txp' ), count($published_posts) );
                ?>
            </a>
        </li>
        <li>
            <a href="#tab2">
                <?php
                echo sprintf( __( 'Awaiting approval <sup>(%s)</sup>', 'txp' ), count($not_approved_posts) );
                ?>
            </a>
        </li>
        <li>
            <a href="#tab3">
                <?php
                echo sprintf( __( ' Awaiting payment <sup>(%s)</sup>', 'txp' ), count($awaiting_payment_posts) );
                ?>
               
            </a>
        </li>
    </ul>
 
    <div class="tab-content">
        <div id="tab1" class="tab active">
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
                    <?php if ( $order->id ) { ?>
                    (<a href="<?php echo site_url( '?p=' . $order->id ) ?>">#<?php echo $order->id?></a>)
                    <?php } ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php } ?>
        </div>
 
        <div id="tab2" class="tab">
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
 
        <div id="tab3" class="tab">
            <?php if (count($awaiting_payment_posts) === 0) { 
                echo "No awaiting payment properties";
            } else { ?>
            <ul>
                <?php foreach ($awaiting_payment_posts as $p) : ?>
                <li>
                    <span><?php echo esc_html( $p->post_title ); ?></span>
                    <form method="post" action="<?php echo site_url( '/submit-property-payment/' ); ?>" style="display:inline-block;">
                        <input type="hidden" name="post_id" value="<?php echo $p->ID; ?>" />
                        <input type="submit" value="Do Payment" style="padding: 8px;font-size: 15px;background: transparent;color: black;text-transform: none;text-decoration: underline;" />
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php } ?>
        </div>
    </div>
</div>
<style>
/*----- Tabs -----*/
.tabs {
    width:100%;
    display:inline-block;
}
 
    /*----- Tab Links -----*/
    .tab-links {
        margin-bottom:0;
    }
    /* Clearfix */
    .tab-links:after {
        display:block;
        clear:both;
        content:'';
    }
 
    .tab-links li {
        margin:0px 5px;
        float:left;
        list-style:none;
    }
 
    .tab-links a {
        padding:9px 15px;
        display:inline-block;
        font-size:16px;
        transition:all linear 0.15s;
        outline: none;
        border-bottom:0;
        color:#ccc;
    }

    li.active a, li.active a:hover {
        color:#4c4c4c;
        border-bottom: 2px solid !important;
    }
 
    /*----- Content of Tabs -----*/
    .tab-content {
        padding:25px;
    }
 
    .tab {
        display:none;
    }

    .tab.active {
        display:block;
    }
</style>
<script>
jQuery(document).ready(function() {
    jQuery('.tabs .tab-links a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
        // Show/Hide Tabs
        var effect = 'fade';        
        switch (effect) {
            case 'no-effect':
                jQuery('.tabs ' + currentAttrValue).show().siblings().hide();
                break;
            case 'slide-1':
                jQuery('.tabs ' + currentAttrValue).siblings().slideUp(400);
                jQuery('.tabs ' + currentAttrValue).delay(400).slideDown(400);
                break;
            case 'slide-2':
                jQuery('.tabs ' + currentAttrValue).slideDown(400).siblings().slideUp(400);
                break;
            case 'fade':
                jQuery('.tabs ' + currentAttrValue).fadeIn(400).siblings().hide();
                break;
        }
 
        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
 
        e.preventDefault();
    });
});
</script>