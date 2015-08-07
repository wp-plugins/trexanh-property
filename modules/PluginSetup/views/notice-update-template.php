<?php
/**
 * Admin View: Notice - Template Check
 */
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<div id="message" class="error">
    <p><?php _e( '<strong>Your theme has bundled outdated copies of Trexanh Property template files.</strong> If you notice an issue on your site, this could be the reason. Please contact your theme developer for further assistance. You can review the System Status for full details.', 'txp' ); ?></p>
    <p class="submit">
        <a href="<?php echo esc_url(  admin_url( 'admin.php?page=trexanh_property_system&tab=system_status' )); ?>" class="button-primary">
            <?php _e('View outdate files') ?>
        </a>
    </p>
</div>