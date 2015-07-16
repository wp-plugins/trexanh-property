<?php
/**
 * Admin View: Notice - Update
 */
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<div id="message" class="error">
    <p>
        <?php _e( '<strong>Trexanh Property Data Update Required</strong> &#8211; We just need to update your install to the latest version', 'txp' ); ?>
    </p>
    <p class="submit">
        <a href="<?php echo esc_url( add_query_arg( 'do_update_trexanh-property', 'true', admin_url( 'admin.php?page=trexanh_property_settings' ) ) ); ?>" class="txp-update-now button-primary">
            <?php _e( 'Update now', 'txp' ); ?>
        </a>
    </p>
</div>

<script type="text/javascript">
    jQuery('.txp-update-now').click('click', function () {
        var answer = confirm('<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'txp' ); ?>');
        return answer;
    });
</script>
