<?php

namespace TreXanhProperty\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Zend\Form\Factory as FormFactory;
use Zend\Form\View\Helper;
use TreXanhProperty\Core\Property;
use TreXanhProperty\Admin\SettingPage;

/* 
 * Manage property admin form: show fields, save fields
 * Modeling following Zend/Form concept
 */

class PropertyForm {
    
    /**
     *
     * @var array
     */
    protected static $property_fields_input;
    
    /**
     *
     * @var Zend/Form
     */
    protected static $form;
    
    /**
     * Return fields input configuration
     * Admin edit/add new form will need this information.
     * 
     * @return array
     */
    public static function get_fields_input( $name = null )
    {
        if ( !isset( self::$property_fields_input ) ) {
            //follow Zend/Form/Factory format
            //http://framework.zend.com/manual/current/en/modules/zend.form.quick-start.html#creation-via-factory
            $property_fields_input = array(
                'agent' => array(
                    'type' => 'text',
                    'attributes' => array(
                        'maxlength' => '60',
                    ),
                    'options' => array(
                        'label' => __( 'Agent', 'txp' ),
                    )
                ),
                'area' => array(
                    'type' => 'text',
                    'attributes' => array(
                        'maxlength' => '11',
                    ),
                    'options' => array(
                        'label' => __( 'Area', 'txp' ),
                    ),
                ),
                'area_unit' => array(
                    'type' => 'select',
                    'options' => array(
                        'label' => __( 'Area Unit', 'txp' ),
                        'value_options' => array(
                            'square_meter' => __( 'Square Meter', 'txp' ),
                            'sqft' => __( 'Square Feet', 'txp' ),
                        ),
                    ),
                ),
                'status' => array(
                    'type' => 'select',
                    'options' => array(
                        'label' => __( 'Property Status', 'txp' ),
                        'value_options' => array(
                            'current' => __( 'Current', 'txp' ),
                            'sold' => __( 'Sold', 'txp' ),
                        ),
                    )
                ),
                'category' => array(
                    'type' => 'select',
                    'options' => array(
                        'label' => __( 'Property Category', 'txp' ),
                        'value_options' => array(
                            'House' => __( 'House', 'txp' ),
                            'Unit' => __( 'Unit', 'txp' ),
                            'Studio' => __( 'Studio', 'txp' ),
                            'Apartment' => __( 'Apartment', 'txp' ),
                            'Flat' => __( 'Flat', 'txp' ),
                            'Other' => __( 'Other', 'txp' )
                        ),
                    )
                ),
                'listing_type' => array(
                    'type' => 'select',
                    'options' => array(
                        'label' => __( 'Listing Type', 'txp' ),
                        'value_options' => array(
                            'sale' => __( 'Sale', 'txp' ),
                            'lease' => __( 'Lease', 'txp' ),
                        ),
                    ),
                ),
                'featured' => array(
                    'type' => 'radio',
                    'options' => array(
                        'label' => __( 'Featured', 'txp' ),
                        'value_options' => array(
                            'yes' => __( 'Yes', 'txp' ),
                            'no' => __( 'No', 'txp' )
                        ),
                    ),
                ),
                //address
                'address_postcode' => array(
                    'type' => 'text',
                    'attributes' => array(
                        'maxlength' => '30',
                    ),
                    'options' => array(
                        'label' => __( 'Postcode', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                'address_street' => array(
                    'type' => 'text',
                    'attributes' => array(
                        'maxlength' => '50',
                    ),
                    'options' => array(
                        'label' => __( 'Street Name', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                'address_street_number' => array(
                    'type' => 'text',
                    'attributes' => array(
                        'maxlength' => '50',
                    ),
                    'options' => array(
                        'label' => __( 'Street Number', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                'address_coordinates' => array(
                    'type' => 'text',
                    'attributes' => array(
                        'maxlength' => '60',
                    ),
                    'options' => array(
                        'label' => __( 'Coordinates', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                'address_country' => array(
                    'type' => 'text',
                    'attributes' => array(
                        'maxlength' => '20',
                    ),
                    'options' => array(
                        'label' => __( 'Country', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                'address_state' => array(
                    'type' => 'text',
                    'attributes' => array(
                        'maxlength' => '30',
                    ),
                    'options' => array(
                        'label' => __( 'State', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                'address_city' => array(
                    'type' => 'text',
                    'attributes' => array(
                        'maxlength' => '30',
                    ),
                    'options' => array(
                        'label' => __( 'City', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                //sale price - rent price
                'price' => array(
                    'type' => 'number',
                    'attributes' => array(
                        'maxlength' => '20',
                    ),
                    'options' => array(
                        'label' => __( 'Price', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                'rent' => array(
                    'type' => 'number',
                    'attributes' => array(
                        'maxlength' => '20',
                    ),
                    'options' => array(
                        'label' => __( 'Rent Amount', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                'rent_period' => array(
                    'type' => 'select',
                    'options' => array(
                        'label' => __( 'Rent Period', 'txp' ),
                        'value_options' => array(
                            'day' => __( 'Day', 'txp' ),
                            'week' => __( 'Week', 'txp' ),
                            'month' => __( 'Month', 'txp' ),
                        ),
                    ),
                ),
                //attachment
                'video_url' => array(
                    'type' => 'url',
                    'options' => array(
                        'label' => __( 'Video URL', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                'floorplan' => array(
                    'type' => 'url',
                    'options' => array(
                        'label' => __( 'Floorplan', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
                'order' => array(
                    'type' => 'hidden',
                    'options' => array(
                        'label' => __( 'Order', 'txp' ),
                    ),
                    'attributes' => array(
                        'disabled' => true,
                    )
                ),
                'photo_gallery' => array(
                    'type' => 'hidden',
                    'options' => array(
                        'label' => __( 'Photo gallery', 'txp' ),
                    ),
                    'is_core_attribute' => true,
                ),
            );
            $prefix = array();
            foreach ( $property_fields_input as $key => $value ) {
                $prefix_name = Property::get_input_prefix() . '_' . $key;
                $value['name'] = $prefix_name;
                $prefix[$prefix_name] = $value;
            }
            self::$property_fields_input = $prefix;
        }
        if ( isset( $name ) ) {
            return self::$property_fields_input[Property::get_input_prefix() . '_' . $name];
        } else {
            return self::$property_fields_input;
        }
    }
    
    public $custom_attributes = array();
    
    public static function is_custom_attribute($attribute_id)
    {
        $attribute_id = Property::get_input_prefix() . "_" . $attribute_id;
        
        $options = get_option(SettingPage::get_config_property_settings_key());
        
        $custom_attributes = $options['custom_attributes'];
        
        foreach ($custom_attributes as $custom_attribute) {
            if ($attribute_id == $custom_attribute['id']) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Render with format of Wordpress admin > meta box
     * @param type $inputs
     * @param type $post
     * @return string
     */
    public static function render_form_elements( $inputs, $post = null)
    {
        $ignoreList = array("order");
        $html = '<table class="form-table">
            <tbody>';
                foreach ($inputs as $input_name) {
                    if (in_array($input_name, $ignoreList)) {
                        $html .= '</tbody></table>';
                        return $html;
                    }
                    $is_custom_attribute = self::is_custom_attribute($input_name);
                    if ($is_custom_attribute) {
                        $input = self::render_custom_attribute_form_element(Property::get_input_prefix() . "_" . $input_name, $post);
                    } else {
                        $input = self::render_form_element($input_name, $post);
                    }
                    if (  $input_name == 'address_coordinates' ) {
                        $html .= "<tr>
                            <th scope='row'><label for='{$input['name']}'>{$input['label']}</label></th>
                            <td>{$input['html']}</td>
                        </tr>                
                        <tr class='map' style='display:none'><td colspan='2'></td></tr>
                        ";
                    } elseif (  $input_name == 'photo_gallery' ) {
                        $html .= "<tr><td colspan='2'>" . $input['html'] . "</td></tr>";
                    } elseif (  $input_name == 'video_url' ) {
                        $html .= "<tr>
                                    <th scope='row'><label for='{$input['name']}'>{$input['label']}</label></th>
                                    <td>{$input['html']}</td>
                                </tr>";
                    } else {
                        $html .= 
                            "<tr>
                                <th scope='row'><label for='{$input['name']}'>{$input['label']}</label></th>
                                <td>{$input['html']}</td>
                            </tr>";
                    }
                }
        $html .= '</tbody>
        </table>';        
        return $html;
    }
    
    public static function render_custom_attribute_form_element($attribute_id, $post = null, $options = array())
    {
        $config = get_option( SettingPage::get_config_property_settings_key() );
        $custom_attributes_config = $config['custom_attributes'];
        $meta_values = get_post_meta( $post->ID );
        foreach ( $custom_attributes_config as $attribute )  {
            $element_name = $attribute['id'];
            if ( $element_name != $attribute_id ) {
                continue;
            }
            $value = ( isset( $meta_values[$element_name] ) ? $meta_values[$element_name][0] : "" );
            
            $result = array(
                'name' => $element_name,
                'label' => $attribute['title'],
                'html' => '',
                'type' => $attribute['type'],
            );
            switch ( $attribute['input'] ) {
                case "text":
                    $result['html'] = "<input type='text' data-custom-attribute-element name='$element_name' id='$element_name' value='$value' />";
                    break;
                case "textarea":
                    $result['html'] = "<textarea data-custom-attribute-element name='$element_name' id='$element_name' value='$value'></textarea>";
                    break;
                case "checkbox":
                    if ( "yes" == $value ) {
                        $checked = "checked";
                    } else {
                        $checked = "";
                    }
                    $result['html'] = "<input data-custom-attribute-element type='hidden' name='$element_name' value='no' />";
                    $result['html'] .= "<input data-custom-attribute-element type='checkbox' name='$element_name' id='$element_name' $checked value='yes' />";
                    break;
                case "currency":
                case "number":
                    $result['html'] = "<input data-custom-attribute-element type='number' name='$element_name' id='$element_name' value='$value' />";
                    break;
                case "select":
                    $select = "<select name='$element_name' id='$element_name'>";
                    $select .= "<option value=''" . (('' == $value) ? " selected" : '') . ">-</option>";
                    if (is_array($attribute['options'])) {
                        foreach ($attribute['options'] as $option) {
                            $select .= "<option value='$option'" . (($option == $value) ? " selected" : '') . ">$option</option>";
                        }                        
                    }
                    $select .= "</select>";
                    $result['html'] = $select;
                    break;
                case "multiselect":
                    $select = "<select name='{$element_name}[]' id='{$element_name}[]' multiple>";
                    if (is_array($attribute['options'])) {                        
                        $value_arr = (array) unserialize($value);
                        foreach ($attribute['options'] as $option) {
                            $select .= "<option value='$option'" . ((in_array($option, $value_arr)) ? " selected" : '') . ">$option</option>";
                        }                        
                    }
                    $select .= "</select>";
                    $result['html'] = $select;
                    break;    
            }
            return $result;
        }
    }
    
    public static function render_form_element($input_name, $post = null, $options = array())
    {
        $form = self::get_form();
        $input = self::get_fields_input($input_name);
        if ( ! $input ) {
            return array();
        }
        $element = $form->get($input['name']);
        if (!empty($options['class'])) {
            $element->setAttributes(array(
                'class' => $options['class'],
            ));
        }
        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */

        if ($post) {
            $value = get_post_meta($post->ID, $input['name'], true);
            $element->setValue(esc_attr($value));
        }

        $input_html = "";
        switch ($input['type']) {
            case 'text' :
                $viewHelper = new Helper\FormInput();
                $input_html = $viewHelper->render($element);
                break;
            case 'select' :
                $viewHelper = new Helper\FormSelect();
                $input_html = $viewHelper->render($element);
                break;
            case 'radio' :
                $viewHelper = new Helper\FormRadio();
                $input_html = $viewHelper->render($element);
                break;
            case 'number' :
                $viewHelper = new Helper\FormNumber();
                $input_html = $viewHelper->render($element);
                break;
            case 'url' :
                $viewHelper = new Helper\FormUrl();
                $input_html = $viewHelper->render($element);
                break;
        }
        if ($input['name'] == Property::get_input_prefix() . '_photo_gallery') {
            $input_html = "<div class='photo-gallery-container'>";
            $attachments = array();
            $current_attachment_ids = array();
            if ($post) {
                $attachments = PropertyGallery::get_attachment($post->ID);
                foreach ($attachments as $attachment) {
                    $attachment_id = $attachment->ID;
                    $current_attachment_ids[] = $attachment_id;
                    $thumbimg = wp_get_attachment_link($attachment_id, 'thumbnail', true);
                    $input_html.= '<div class="square150 photo-gallery-thumbnail">' . $thumbimg . '<a href="#" class="remove-image remove-current-image" data-id="' . $attachment_id . '">x</a></div>';
                }
            }
            $input_html.= '<div class="custom-upload-button square150">'
                        . '<input type="file" class="button" title="Add photo" />'
                    . '</div>';
            $input_html.= "<p class='gallery-message'" . (( $attachments ) ? " style='display:none;'" : "") . ">" . __("No photo in gallery.", "txp") . "</p>";
            $input_html.= "</div>";
            $input_html.= '<button id="open_uploader" class="button" type="button">Edit Gallery</button>';
            $input_html.= '<input type="hidden" name="gallery_photo_ids" value="' . implode(",", $current_attachment_ids) .'" />';
        }
        
        if ($input['name'] == Property::get_input_prefix() . '_video_url') {
            $input_html .= "<p>" . __('Please input an youtube or vimeo link', 'txp') . "</p>";
        }
        if ($input['name'] == Property::get_input_prefix() . '_address_coordinates') {
            $input_html .= "<button class='geocoder'>" . __('Map', 'txp') . "</button>";
        }
        
        $label = isset($input['options']['label']) ? $input['options']['label'] : '';
        return array(
            'name' => $input['name'],
            'label' => $label,
            'html' => $input_html,
            'type' => $input['type'],
        );
    }
    
    /**
     * 
     * @return Zend\Form\Form
     */
    public static function get_form() {
        if (isset(self::$form)) {
            return self::$form;
        }
        
        $inputs = self::get_fields_input();
        
        //prepare array so we can use Zend/Form/Factory to create
        //http://framework.zend.com/manual/current/en/modules/zend.form.quick-start.html#creation-via-factory
        $spec = array();
        foreach ($inputs as $input) {
            $input_spec = array();
            $input_spec['spec'] = $input;
            $spec[] = $input_spec;
        }
        $factory = new FormFactory();
        $form    = $factory->createForm(array(
            'hydrator' => 'Zend\Stdlib\Hydrator\ArraySerializable',
            'elements' => $spec
        ));
        
        self::$form = $form;
        return self::$form;
    }
    
    public static function save($post_id) {
        $fields = self::get_fields_input();

        foreach ( $fields as $field_name => $spec ) {
            if ( isset( $_REQUEST[$field_name] ) ) {
                update_post_meta( $post_id, $field_name, sanitize_text_field( $_REQUEST[$field_name] ) );
            }
        }
        // save custom attributes
        $options = get_option( SettingPage::get_config_property_settings_key() );
        $property_type_key = Property::get_input_prefix() . "_property_type";
        if ( isset( $_REQUEST[$property_type_key] ) && ! empty( $options['property_types'] ) ) {
            $property_type = $_REQUEST[$property_type_key];
            foreach ( $options['property_types'] as $types ) {
                if ( $types['id'] == $property_type ) {
                    update_post_meta( $post_id, $property_type_key, sanitize_text_field( $_REQUEST[$property_type_key] ) );
                    break;
                }
            }
        }
        
        if ( ! empty( $options['custom_attributes'] ) ) {
            foreach ( $options['custom_attributes'] as $attribute ) {
                $attribute_value = $_REQUEST[$attribute['id']];
                if ( isset( $attribute_value ) ) {
                    if (is_array($attribute_value)) {
                        update_post_meta( $post_id, $attribute['id'], $attribute_value );
                    } else {
                        update_post_meta( $post_id, $attribute['id'], sanitize_text_field( $attribute_value ) );
                    }                    
                } else {
                    //@note: should note delete meta, 
                    //we may delete other plugin post's data
                    //(find during enhance sweethome theme)
//                  delete_post_meta( $post_id, $attribute['id'] );
                    
                }
            }            
        }
    }

}