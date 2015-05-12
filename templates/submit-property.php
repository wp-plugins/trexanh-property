<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$is_user_logged_in = is_user_logged_in();
if ( !$is_user_logged_in ) { ?>
    <p>
    <?php
    echo __( "Already registered?", "txp" ); ?>
    <a href="<?php echo wp_login_url( get_permalink( get_page_by_path( 'submit-property' ) ) ); ?>">
        <?php echo __( "Click here", "txp" ); ?>
    </a>
    <?php echo __( "to log in", "txp" ) . "."; ?>
    </p>
<?php } ?>

<form method="POST" enctype="multipart/form-data" encoding="multipart/form-data" class="submit-property-form">
    <fieldset>
        <legend><?php _e('Title', 'framework') ?></legend>
        <input type="text" name="post_title" id="post_title" class="required" required="required" />
    </fieldset>
    <br>
<?php 
$form = \TreXanhProperty\Core\PropertyForm::render_submit_property_form();
echo $form; ?>
<?php if ( !$is_user_logged_in ) { ?>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="email">
                        <?php echo __( "Your email", "txp" ); ?> <sup>*</sup>
                    </label>
                </th>
                <td>
                    <input type="email" name="email" value="" required="required" />
                    <div>
                        <?php echo __( "We will send notification email about your submission and your account information (if you check the Create new account? checkbox) to this address", "txp" ); ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <label>
        <input type="checkbox" name="create_new_account" />
        <?php echo __( "Create new account?", "txp" ); ?>
    </label>
    <div>
        <sup>(<?php echo __( "Your account information will be sent you via email.", "txp" ); ?>)</sup>
    </div>
<?php } ?>
    <button type="submit">
        <?php echo __( "Submit", "txp" ); ?>
    </button>
</form>