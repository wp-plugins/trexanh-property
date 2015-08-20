<?php
/**
 * 
 * @version 0.4
 */

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
    <input type="hidden" name="txp_property_property_type" value="house" />
    <fieldset>
        <legend><?php _e('PROPERTY TYPE', 'txp') ?></legend>
        <input type="text" value="<?php _e('House', 'txp') ?>" readonly="readonly" disabled="disabled" />
    </fieldset>
    <br>
    <fieldset>
        <legend><?php _e('Title', 'txp') ?></legend>
        <input type="text" name="post_title" id="post_title" class="required" required="required" />
    </fieldset>
    <br>
    <fieldset>
        <legend><?php _e('Description', 'txp') ?></legend>
        <textarea name="post_content" id="post_content"></textarea>
    </fieldset>
    <br>
    <fieldset>
        <legend>
            <?php echo __( "Property Info", "txp" ); ?>
        </legend>
    </fieldset>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Agent", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'agent' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Area", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'area' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Area Unit", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'area_unit' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Property Status", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'status' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Featured", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'featured' );?></td>
            </tr>
        </tbody>
    </table>
    <fieldset>
        <legend>
            <?php echo __( "Price", "txp" ); ?>
        </legend>
    </fieldset>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Listing Type", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'listing_type' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Price", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'price' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Rent Amount", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'rent' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Rent Period", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'rent_period' );?></td>
            </tr>
        </tbody>
    </table>
    <fieldset>
        <legend>
            <?php echo __( "Features", "txp" ); ?>
        </legend>
    </fieldset>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Bedrooms", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'bedrooms' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Bathrooms", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'bathrooms' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Ensuite", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'ensuite' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Toilet", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'toilet' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Garage", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'garage' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "New Construction", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'new_construction' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Air Conditioning", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'air_conditioning' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Pool", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'pool' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Security System", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'security_system' );?></td>
            </tr>
        </tbody>
    </table>
    <fieldset>
        <legend>
            <?php echo __( "Address", "txp" ); ?>
        </legend>
    </fieldset>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Postcode", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'address_postcode' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Street Number", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'address_street_number' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Street Name", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'address_street' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "City", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'address_city' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "State", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'address_state' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Country", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'address_country' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Coordinates", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'address_coordinates' );?></td>
            </tr>                
            <tr class="map" style="display:none"><td colspan="2"></td></tr>
        </tbody>
    </table>
    <fieldset>
        <legend>
            <?php echo __( "Media", "txp" ); ?>
        </legend>
    </fieldset>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Video URL", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'video_url' );?></td>
            </tr>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __( "Floorplan", "txp" ); ?>
                    </label>
                </th>
                <td><?php txp_render_property_form_element( 'floorplan' );?></td>
            </tr>
        </tbody>
    </table>
    <fieldset>
        <legend>
            <?php echo __( "Photo Gallery", "txp" ); ?>
        </legend>
    </fieldset>
    <table class="form-table">
        <tbody>
            <tr>
                <td><?php txp_render_property_form_element( 'photo_gallery' );?></td>
            </tr>
        </tbody>
    </table>
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