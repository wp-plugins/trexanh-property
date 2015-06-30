<?php

namespace TreXanhProperty\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Directory;
use TreXanhProperty\Core\Formatter;
use TreXanhProperty\Core\PaymentGateway\PaymentGatewayService;
use TreXanhProperty\Core\Property;

class SettingPage
{
    public function __construct() {
        $this->general_settings_key = TREXANHPROPERTY_PREFIX . 'general_settings';
        $this->payment_settings_key = TREXANHPROPERTY_PREFIX . 'payment_settings';
        $this->config_property_settings_key = self::get_config_property_settings_key();
    }
    
    public static function get_config_property_settings_key()
    {
        return TREXANHPROPERTY_PREFIX . 'property_type_setting';
    }
    
    /**
     * Start up
     */
    public function init_hooks()
    {
        //Admin: setting page + menu
        add_action( 'admin_menu', array( $this, 'add_plugin_pages' ) );
        add_action( 'admin_init', array( &$this, 'register_payment_settings' ) );
        add_action( 'admin_init', array( &$this, 'register_general_settings' ) );
        add_action( 'admin_init', array( &$this, 'register_config_property_settings' ) );
        add_action( 'init', array( &$this, 'load_settings' ) );
    }
    
    
    /*
     * For easier overriding we declared the keys
     * here as well as our tabs array which is populated
     * when registering settings
     */
    private $general_settings_key;
    private $payment_settings_key;
    private $config_property_settings_key;
    private $plugin_options_key = 'trexanh_property_settings';
    private $plugin_settings_tabs = array();

    private $general_settings;
    private $payment_settings;


    /*
     * Loads both the general and advanced settings from
     * the database into their respective arrays. Uses
     * array_merge to merge with default values if they're
     * missing.
     */
    public function load_settings() {
        $this->general_settings = (array) get_option( $this->general_settings_key );
        $this->payment_settings = (array) get_option( $this->payment_settings_key );
        $this->config_property_settings = (array) get_option( $this->config_property_settings_key );
    }

    public function delete_settings() {
        delete_option( $this->general_settings_key );
        delete_option( $this->payment_settings_key );
        delete_option( $this->config_property_settings_key );
    }
    
    /*
     * Registers the general settings via the Settings API,
     * appends the setting to the tabs array of the object.
     */
    public function register_general_settings() {
        register_setting(
            $this->general_settings_key, // Option group
            $this->general_settings_key, // Option group
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_submitting', // ID
            __('Submit Property Settings', 'txp'), // Title
            array( $this, 'print_section_info' ), // Callback
            $this->general_settings_key // Page
        );
        
        add_settings_field(
            'enable_property_submission', // ID
            __( 'Enable Property Submission', 'txp' ), // Title 
            array( $this, 'render_field' ), // Callback
            $this->general_settings_key, // Page
            'setting_section_submitting', // Section
            array(
                'name' => sprintf('%s[%s]', $this->general_settings_key, 'enable_property_submission'),
                'type' => 'checkbox',
                'attributes' => array(
                    'id' => 'enable_property_submission',
                ),
                'value' => isset( $this->general_settings['enable_property_submission'] ) ? $this->general_settings['enable_property_submission'] : '',
            )  
        );
        
        add_settings_field(
            'require_admin_to_approve', // ID
            __( 'Require admin to approve', 'txp' ), // Title 
            array( $this, 'render_field' ), // Callback
            $this->general_settings_key, // Page
            'setting_section_submitting', // Section
            array(
                'name' => sprintf('%s[%s]', $this->general_settings_key, 'require_admin_to_approve'),
                'type' => 'checkbox',
                'value' => isset( $this->general_settings['require_admin_to_approve'] ) ? $this->general_settings['require_admin_to_approve'] : '',
            )  
        );
        add_settings_field(
            'require_payment_to_submit', // ID
            __( 'Need payment to submit property', 'txp' ), // Title 
            array( $this, 'render_field' ), // Callback
            $this->general_settings_key, // Page
            'setting_section_submitting', // Section
            array(
                'name' => sprintf('%s[%s]', $this->general_settings_key, 'require_payment_to_submit'),
                'type' => 'checkbox',
                'attributes' => array(
                    'id' => 'require_payment_to_submit'
                ),
                'value' => isset( $this->general_settings['require_payment_to_submit'] ) ? $this->general_settings['require_payment_to_submit'] : '',
            )
        );
        
        $currency = isset( $this->general_settings['currency'] ) ? $this->general_settings['currency'] : 'USD';
        $currency_symbol = Directory::get_currencies_symbol( $currency );
        
        add_settings_field(
            'submit_fee', // ID
            sprintf( __( 'Submit fee (%s)', 'txp' ),  $currency_symbol), // Title 
            array( $this, 'render_field' ), // Callback
            $this->general_settings_key,
            'setting_section_submitting', // Section
            array(
                'name' => sprintf('%s[%s]', $this->general_settings_key, 'submit_fee'),
                'type' => 'text',
                'value' => isset( $this->general_settings['submit_fee'] ) ? $this->general_settings['submit_fee'] : '',
                'attributes' => array(
                    'id' => 'submit_fee'
                ),
            )
        );
        
        add_settings_section(
            'setting_section_currency', // ID
            __( 'Currency Settings', 'txp'), // Title
            array( $this, 'print_section_info' ), // Callback
            $this->general_settings_key // Page
        );
        
        
        $currencies = Directory::get_supported_currencies();
        
        foreach ($currencies as $key => $name) {
            $symbol = Directory::get_currencies_symbol($key);
            if ($symbol) {
                $currencies[$key] = sprintf('%s (%s)', $name, $symbol);
            }
        }
        
        add_settings_field(
            'currency', // ID
            __( 'Currency', 'txp' ), // Title 
            array( $this, 'render_field' ), // Callback
            $this->general_settings_key,
            'setting_section_currency', // Section
            array(
                'name' => sprintf('%s[%s]', $this->general_settings_key, 'currency'),
                'type' => 'select',
                'options' => $currencies,
                'value' => $currency,
            )
        );
        
        add_settings_field(
            'thousands_separator', // ID
            __( 'Thousands Separator', 'txp' ), // Title 
            array( $this, 'render_field' ), // Callback
            $this->general_settings_key,
            'setting_section_currency', // Section
            array(
                'name' => sprintf('%s[%s]', $this->general_settings_key, 'thousands_separator'),
                'type' => 'text',
                'value' => isset( $this->general_settings['thousands_separator'] ) ? $this->general_settings['thousands_separator'] : ',',
            )
        );
        
        add_settings_field(
            'decimal_separator', // ID
            __( 'Decimal Separator', 'txp' ), // Title 
            array( $this, 'render_field' ), // Callback
            $this->general_settings_key,
            'setting_section_currency', // Section
            array(
                'name' => sprintf('%s[%s]', $this->general_settings_key, 'decimal_separator'),
                'type' => 'text',
                'value' => isset( $this->general_settings['decimal_separator'] ) ? $this->general_settings['decimal_separator'] : '.',
            )
        );
        
        add_settings_field(
            'currency_num_decimals', // ID
            __( 'Number of Decimals', 'txp' ), // Title 
            array( $this, 'render_field' ), // Callback
            $this->general_settings_key,
            'setting_section_currency', // Section
            array(
                'name' => sprintf('%s[%s]', $this->general_settings_key, 'currency_num_decimals'),
                'type' => 'text',
                'value' => isset( $this->general_settings['currency_num_decimals'] ) ? $this->general_settings['currency_num_decimals'] : '2',
            )
        );
        
        add_settings_field(
            'symbol_pos', // ID
            __( 'Symbol Position', 'txp' ), // Title 
            array( $this, 'render_field' ), // Callback
            $this->general_settings_key,
            'setting_section_currency', // Section
            array(
                'name' => sprintf('%s[%s]', $this->general_settings_key, 'symbol_pos'),
                'type' => 'select',
                'value' => isset( $this->general_settings['symbol_pos'] ) ? $this->general_settings['symbol_pos'] : 'left',
                'options' => array(
                    'left' => sprintf( __( 'Left (%s)', 'txp' ), Formatter::currency(3.99, '', 'left') ),
                    'right' => sprintf( __( 'Right (%s)', 'txp' ), Formatter::currency(3.99, '', 'right')),
                ),
            )
        );
        
    }
    
    public function render_field($args = array())
    {
        $name = $args['name'];
        unset($args['name']);
        $helper_class = '\Zend\Form\View\Helper\Form' . ucfirst( $args['type'] );
        
        if ( ! class_exists( $helper_class ) ) {
            return ;
        }
        
        $helper = new $helper_class();
        $input_class = '\Zend\Form\Element\\' . ucfirst( $args['type']);
        
        /* @var $input \Zend\Form\Element\Checkbox */
        $value = '';
        if (isset($args['value'])) {
            $value = $args['value'];
            unset($args['value']);
        }
        $input = new $input_class($name, $args);
        $input->setValue($value);
        if ( isset( $args['attributes'] ) ) {
            $input->setAttributes($args['attributes']);
        }
        echo $helper->render($input);
    }
    
    public function register_payment_settings()
    {
        register_setting($this->payment_settings_key, $this->payment_settings_key);
        $gateways = PaymentGatewayService::getInstance();
        $payment_methods = $gateways->get_payment_classes();
        
        foreach ($payment_methods as $method_id => $method_class) {
            $method = $gateways->get($method_id);
            $fields = $method->get_setting_fields();
            add_settings_section(
                $method->id,
                sprintf( __( '%s settings', 'txp' ), $method->title ),
                array($method, 'print_description'),
                $this->payment_settings_key
            );
            
            $this->register_payment_fields($fields, $method->id);
        }
    }
    
    public function register_config_property_settings()
    {
        register_setting($this->config_property_settings_key, $this->config_property_settings_key);
        add_settings_section(
            'property_config',
            sprintf( __( '', 'txp' ) ),
            array( $this, 'print_config_property_section_info'),
            $this->config_property_settings_key
        );
        add_settings_field(
            'configurations',
            '',
            array( $this, 'property_config_callback_function'),
            $this->config_property_settings_key,
            'property_config'
        );
    }

    public function property_config_callback_function()
    {
        $options = get_option($this->config_property_settings_key);
        if (empty($options)) {
            $options = txp_get_default_property_type_config();
            update_option($this->config_property_settings_key, $options);
        }
        if ( empty ( $options['custom_attributes'] ) ) {
            $options['custom_attributes'] = array(
                array(
                    'id' => '',
                    'title' => '',
                    'type' => '',
                    'input' => '',
                )
            );
        }

        $property_types = array();
        foreach ($options['property_types'] as $type => $value) {
            $property_types[] = array(
                'id' => $value['id'],
                'name' => $value['name'],
            );
        }
        
        $all_available_attributes = array();

        foreach ($options['custom_attributes'] as $attribute) {
            $attr_id = str_replace("txp_property_", "", $attribute['id']);
            $all_available_attributes[$attr_id] = array(
                'id' => $attr_id,
                'name' => $attribute['title'],
            );
        }
        $form_attributes = \TreXanhProperty\Core\PropertyForm::get_fields_input();
        $core_attributes = array();
        foreach ($form_attributes as $id => $attribute) {
            if (array_key_exists('is_core_attribute', $attribute) && $attribute['is_core_attribute']) {
                $attr_id = str_replace("txp_property_", "", $id);
                $attribute = array(
                    'id' => $attr_id,
                    'name' => $attribute['options']['label'],
                );
                $all_available_attributes[$attr_id] = $attribute;
                $core_attributes[] = $attribute;
            }
        }

        $action = 'edit_property_type';
        $property_type = (isset($_GET['property_type']) ? $_GET['property_type'] : "");
        if (array_key_exists('action', $_GET)) {
            $action = $_GET['action'];
            switch( $action) {
                case "clone_property_type":
                    $cloned_property_type = $options['property_types'][$property_type];
                    $cloned_property_type['name'] = "Clone of " . $cloned_property_type['name'];
                    $cloned_property_type['id'] = "clone_of_" . $cloned_property_type['id'];
                    $options['property_types'] = array(
                        $cloned_property_type['id'] => $cloned_property_type,
                    );
                    $property_type = $cloned_property_type['id'];
                    break;
                case "new_property_type":
                    $new_property_type_id = 'new_property_type';
                    $options['property_types'] = array(
                        'new_property_type' => array(
                            'id' => $new_property_type_id,
                            'name' => 'New Property Type',
                            'attributes' => array(),
                            'enabled' => true,
                            'groups' => array()
                        )
                    );
                    $property_type = $new_property_type_id;
                    break;
            }
        }
        $new_url = add_query_arg( array( 'action' => 'new_property_type', 'section' => 'property_types' ) );
        if (isset($_GET['property_type'])) {
            $new_url = remove_query_arg( array('property_type'), $new_url );
        }
        $clone_url = add_query_arg( array( 'action' => 'clone_property_type', 'section' => 'property_types' ) );
        if (!isset($_GET['property_type'])) {
            $property_type = "";
            foreach ($options['property_types'] as $type => $value) {
                $clone_url = add_query_arg( array('property_type' => $type), $clone_url );
                break;
            }
        }
        $attribute_input_types = array(
            array(
                'id' => 'text',
                'name' => __( "Text", "txp" ),
            ),
            array(
                'id' => 'textarea',
                'name' => __( "Textarea", "txp" ),
            ),
            array(
                'id' => 'checkbox',
                'name' => __( "Checkbox", "txp" ),
            ),
            array(
                'id' => 'number',
                'name' => __( "Number", "txp" ),
            ),
            array(
                'id' => 'select',
                'name' => __( "Select", "txp" ),
            ),
            array(
                'id' => 'currency',
                'name' => __( "Currency", "txp" ),
            ),
        );
        $selected_tab = (isset($_GET['section']) ? $_GET['section'] : "property_types");
        ?>
            <div class="tabs property-types-config-container">
                <ul class="tab-links">
                    <li ng-class="{'active' : selectedTab == 'property_types' }">
                        <a href="" ng-click="selectedTab = 'property_types'">
                            <?php echo __( "Property Types", "txp" ); ?>
                        </a>
                    </li>
                    <li ng-class="{'active' : selectedTab == 'config_attributes' }">
                        <a href="" ng-click="selectedTab = 'config_attributes'">
                            <?php echo __( "Config Attributes", "txp" ); ?>
                        </a>
                    </li>
                </ul>
                <input type="hidden" name="selected_tab" ng-value="selectedTab"/>
                <div class="tab-content">
                    <div id="property-types" class="tab" ng-class="{'active' : selectedTab == 'property_types' }">
                        <?php if ('edit_property_type' == $action) { ?>
                            <h3 style="margin-top:0;">
                                <?php echo __( "Update property type", "txp" ); ?>
                            </h3>
                            <?php echo __( "Property type", "txp" ); ?>
                            <select class="property-type-picker" ng-model="selectedPropertyTypeId" ng-options="type.id as type.name for type in propertyTypes" ng-change="changePropertyType()">
                            </select>
                            <div class="property-type-actions">
                                <a class="add-new-h2" href="<?php echo $clone_url; ?>">
                                    <?php echo __( "Clone this type", "txp" ); ?>
                                </a>
                                <a class="add-new-h2" href="<?php echo $new_url; ?>">
                                    <?php echo __( "Add new type", "txp" ); ?>
                                </a>
                            </div>
                        <?php } ?>
                        <?php if ('clone_property_type' == $action) { ?>
                            <h3>
                                <?php echo __( "Clone property type", "txp" ); ?>
                            </h3>
                        <?php } ?>
                        <?php if ('new_property_type' == $action) { ?>
                            <h3>
                                <?php echo __( "New property type", "txp" ); ?>
                            </h3>
                        <?php } ?>
                        <div class="property-types-container">
                            <div class="property-type-container">
                                <div class="inline-edit-container">
                                    <span class="edit-label"><?php echo __( "Name", "txp" ); ?>:</span>
                                    <span class="edit-view" ng-hide="showUpdateTypeNameEditor">
                                        <strong>{{selectedPropertyType.name}}</strong>&nbsp;
                                        <a href="" ng-click="showUpdatePropertyTypeNameEditor()"><span class="dashicons-before dashicons-edit"></span></a>
                                    </span>
                                    <span class="edit-editor" ng-show="showUpdateTypeNameEditor">
                                        <input type="text" ng-model="tempPropertyTypeName" focus="showUpdateTypeNameEditor" ng-keydown="propertyTypeNameInputChanged($event)">&nbsp;
                                        <a href="" ng-click="updatePropertyTypeName()"><span class="dashicons-before dashicons-yes"></span></a>
                                        <a href="" ng-click="hideUpdatePropertyTypeNameEditor()"><span class="dashicons-before dashicons-no"></span></a>
                                    </span>
                                </div><br>
                                <div>
                                    <span><?php echo __( "Enable", "txp" ); ?>:</span>&nbsp;
                                    <input type="checkbox" ng-model="selectedPropertyType.enabled" /> <i ng-show="!selectedPropertyType.enabled">(If you do not enable then you will not able to create new property of this type)</i>
                                </div>
                                <h4>
                                    <?php echo __( "Config attributes", "txp" ); ?>
                                </h4>
                                <div class="property-type-groups-container">
                                    <div class="property-type-group-container" ng-repeat="groupConfig in selectedPropertyType.groups track by groupConfig.id" id="{{groupConfig.id}}">
                                        <div class="inline-edit-container">
                                            <span class="edit-view" ng-hide="updateGroupNameEditorShown[groupConfig.id]">
                                                <strong>{{groupConfig.name}}</strong>&nbsp;
                                                <a href="" ng-click="showUpdateGroupNameEditor(groupConfig.id)" ng-if="groupConfig.id != ungroupedAttributesGroupId"><span class="dashicons-before dashicons-edit"></span></a>
                                            </span>
                                            <span class="edit-editor" ng-show="updateGroupNameEditorShown[groupConfig.id]">
                                                <input
                                                    type="text"
                                                    value="{{groupConfig.name}}"
                                                    ng-model="tempGroupName[groupConfig.id]"
                                                    focus="updateGroupNameEditorShown[groupConfig.id]"
                                                    ng-keydown="groupNameInputChanged($event, groupConfig.id)"
                                                />&nbsp;
                                                <a href="" ng-click="updateGroupName(groupConfig.id)"><span class="dashicons-before dashicons-yes"></span></a>
                                                <a href="" ng-click="hideUpdateGroupNameEditor(groupConfig.id)"><span class="dashicons-before dashicons-no"></span></a>
                                            </span>
                                        </div>
                                        <div class="property-type-group-attributes-container attributes-list" data-group-id="{{groupConfig.id}}">
                                            <div
                                                class="attribute-item"
                                                ng-repeat="attributeId in groupConfig.attributes"
                                                data-attribute-id="{{attributeId}}"
                                                title="<?php echo __( "Drag and drop into other group to change group", "txp" ); ?>"
                                            >
                                                <span class="draggable-icon"></span> {{attributeName(attributeId)}} <a href="" class="remove-attribute" ng-click="removeAttribute(attributeId, groupConfig.id)"><span class="dashicons-before dashicons-no"></span></a>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="" ng-click="showAddAttributesForm(groupConfig.id)" ng-hide="addFormsShown[groupConfig.id]">
                                                <?php echo __( "Add attributes", "txp" ); ?>
                                            </a>
                                        </div>
                                        <div class="add-attributes-form" ng-show="addFormsShown[groupConfig.id]">
                                            <h4>
                                                <?php echo __( "Click on attributes to add", "txp" ); ?>
                                            </h4>
                                            <ul class="attributes-list">
                                                <li class="attribute-item" ng-repeat="attr in availableAttributes track by attr.id">
                                                    <label ng-click="addAttributeToGroup(attr.id, groupConfig.id)">{{attr.name}}</label>
                                                </li>
                                            </ul>
                                            <div class="buttons">
                                                <button type="button" class="button button-primary" ng-click="hideAddAttributesForm(groupConfig.id)">
                                                    <?php echo __( "OK", "txp" ); ?>
                                                </button>
                                            </div>
                                        </div>
                                        <div
                                            class="dashicons-before dashicons-no actions-menu-trigger"
                                            ng-if="groupConfig.id != ungroupedAttributesGroupId"
                                            ng-class="{'open' : actions_menu_shown[groupConfig.id]}"
                                            ng-click="confirmRemoveGroup(groupConfig.id)"
                                        ></div>
                                    </div>
                                </div>
                                <div class="actions">
                                    <button type="button" class="button button-default" ng-click="addNewGroup()"><?php echo __( "New group", "txp" ); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="property-attributes" class="tab custom-attributes-container" ng-controller="CustomAttributeConfigCtrl" ng-class="{'active' : selectedTab == 'config_attributes' }">
                        <h3 style="margin-top:0;">
                            <?php echo __( "Core Attributes", "txp" ); ?>
                            <small><small>(You can not edit or delete these attributes)</small></small>
                        </h3>
                        <ul class="attributes-list">
                            <li ng-repeat="attr in coreAttributes" class="attribute-item">{{attr.name}}&nbsp;&nbsp;&nbsp;&nbsp;</li>
                        </ul>
                        <hr>
                        <h3>
                            <?php echo __( "Custom Attributes", "txp" ); ?>
                        </h3>
                        <table>
                            <tr>
                                <th>
                                    <?php echo __( "Attribute name", "txp" ); ?>
                                </th>
                                <th>
                                    <?php echo __( "Input type", "txp" ); ?>
                                </th>
                                <th>
                                    <?php echo __( "Attribute options", "txp" ); ?>
                                </th>
                                <th>
                                    <?php echo __( "Action", "txp" ); ?>
                                </th>
                            </tr>
                            <tr ng-repeat="customAttributeConfig in customAttributesConfig track by customAttributeConfig.id">
                                <td style="border-bottom:1px solid #ddd;width:1%;" data-th="<?php echo __( "Attribute name", "txp" ); ?>">
                                    <input
                                        type="text"
                                        ng-init="customAttributeConfig.new_title = customAttributeConfig.title"
                                        ng-model="customAttributeConfig.new_title"
                                        ng-blur="customAttributeTitleChanged(customAttributeConfig)"
                                        ng-keydown="attributeNameInputKepressed($event, customAttributeConfig)"
                                    />
                                </td>
                                <td style="border-bottom:1px solid #ddd;width:1%;" data-th="<?php echo __( "Input type", "txp" ); ?>">
                                    <select ng-model="customAttributeConfig.input" ng-change="customAttributeInputTypeChanged(customAttributeConfig)">
                                        <option value="" ng-selected="!customAttributeConfig.input"> - </option>
                                        <option
                                            ng-repeat="inputType in attributeInputTypes track by inputType.id"
                                            ng-value="inputType.id"
                                            ng-selected="inputType.id == customAttributeConfig.input"
                                        >
                                            {{inputType.name}}
                                        </option>
                                    </select>
                                </td>
                                <td style="border-bottom:1px solid #ddd;" data-th="<?php echo __( "Attribute options", "txp" ); ?>">
                                    <div ng-show="customAttributeConfig.input == 'select'" class="attribute-option-values-container">
                                        <span class="value-tag" ng-repeat="option in customAttributeConfig.options" >
                                            {{option}}
                                            <span
                                                class="dashicons-before dashicons-no remove-icon"
                                                ng-click="removeAttributeOption(customAttributeConfig, $index)"
                                            ></span>
                                        </span>
                                        <input
                                            type="text"
                                            placeholder="Enter or comma to add"
                                            ng-model="customAttributeConfig.new_option"
                                            ng-keydown="attributeOptionInputKepressed($event, customAttributeConfig)"
                                            focus="customAttributeConfig.input == 'select'"
                                        />
                                    </div>
                                </td>
                                <td style="border-bottom:1px solid #ddd;width:1%;text-align:center;" data-th="<?php echo __( "Action", "txp" ); ?>">
                                    <a href="" ng-click="removeAttributeConfig(customAttributeConfig)" class="delete-icon">
                                        <span class="dashicons-before dashicons-no"></span>
                                    </a>
                                    <a href="" ng-click="removeAttributeConfig(customAttributeConfig)" class="delete-link">
                                        <?php echo __( "Delete", "txp" ); ?>
                                    </a>
                                    <hr class="mobile-divider">
                                </td>
                            </tr>
                        </table>
                        <br>
                        <a href="" class="button button-default" ng-click="addCustomAttribute()">
                            <?php echo __( "Add attribute", "txp" ); ?>
                        </a>
                    </div>
                    <script>
                        if (window.TrexanhProperty === undefined) {
                            window.TrexanhProperty = {};
                        }
                        window.TrexanhProperty.propertyConfigPageUrl = '<?php echo remove_query_arg( array( 'action', 'property_type', 'section') ) ?>';
                        window.TrexanhProperty.propertyConfigAction = '<?php echo $action; ?>';
                        window.TrexanhProperty.propertyTypesConfig = JSON.parse('<?php echo json_encode($options['property_types']); ?>');
                        window.TrexanhProperty.propertyTypes = JSON.parse('<?php echo json_encode($property_types); ?>');
                        window.TrexanhProperty.selectedTab = '<?php echo $selected_tab; ?>';
                        window.TrexanhProperty.customAttributesConfig = JSON.parse('<?php echo json_encode($options['custom_attributes']); ?>');
                        window.TrexanhProperty.selectedPropertyType = '<?php echo $property_type; ?>';
                        window.TrexanhProperty.allAvailableAttributes = JSON.parse('<?php echo json_encode($all_available_attributes); ?>');
                        window.TrexanhProperty.coreAttributes = JSON.parse('<?php echo json_encode($core_attributes); ?>');
                        window.TrexanhProperty.attributeInputTypes = JSON.parse('<?php echo json_encode($attribute_input_types); ?>');
                        window.TrexanhProperty.TxlDialog = new window.Txl.Dialog();
                        window.TrexanhProperty.showAlertDialog = function(message, title) {
                            var dialog = window.TrexanhProperty.TxlDialog;
                            dialog.setMode("alert");
                            if (!title) {
                                title = "Warning";
                            }
                            dialog.setTitle(title);
                            dialog.setMessage(message);
                            dialog.open();
                        };
                        window.TrexanhProperty.showConfirmDialog = function(message, title, okCallback, cancelCallback) {
                            var dialog = window.TrexanhProperty.TxlDialog;
                            dialog.setMode("confirm");
                            if (!title) {
                                title = "Confirm";
                            }
                            dialog.setTitle(title);
                            dialog.setMessage(message);
                            if (okCallback) {
                                dialog.setOkCallback(okCallback);
                            }
                            if (cancelCallback) {
                                dialog.setCancelCallback(cancelCallback);
                            }
                            dialog.open();
                        };
                        jQuery(document).ready(function($) {
                            // remove empty left column
                            $(".form-table th[scope='row']").remove();
                            var action = window.TrexanhProperty.propertyConfigAction;
                            var util = window.TrexanhProperty.helperFunctions;
                            if ('new_property_type' == action || 'clone_property_type' == action) {
                                // add button to allow to cancel add new or clone property type action
                                var backUrl = window.TrexanhProperty.propertyConfigPageUrl;
                                var propertyType = util.getUrlParameter('property_type');
                                if (propertyType) {
                                    backUrl += "&property_type=" + propertyType;
                                }
                                var cancelButtonLink = "<a href='" + backUrl + "' class='button button-default' style='margin-left:10px;'>Cancel</a>";
                                $(".submit").append(cancelButtonLink);
                            }
                        });
                    </script>
                </div>
            </div>
        <?php
    }

    public function plugin_settings_save()
    {
        # Our new data
        $data = $_POST;
        if ( empty( $data ) ) {
            return;
        }
        $key = $data['option_page'];
        if ( $this->config_property_settings_key == $key ) {
            // attributes config
            $config = array(
                'property_types_config' => json_decode(stripslashes($data['property_types_config']), true),
                'custom_attributes_config' => json_decode(stripslashes($data['custom_attributes_config']), true),
            );
            
            $custom_attributes = $config['custom_attributes_config'];

            // save property types config
            $property_types_config = $config['property_types_config'];
            $action = "edit_property_type";
            if (array_key_exists('action', $_GET) && in_array($_GET['action'], array('new_property_type', 'clone_property_type'))) {
                $action = $_GET['action'];
            }
            // redirect user to page with manipulating property type selected
            $redirect = remove_query_arg( array( 'action', 'property_type' ) );
            $redirect = add_query_arg( array( 'property_type' => $data['working_property_type_id'] ), $redirect );
            if (in_array($action, array('new_property_type', 'clone_property_type'))) {
                $option = get_option( $key );
                $property_types_config = array_merge($property_types_config, $option['property_types']);
            }
            update_option( $key, array(
                'custom_attributes' => $custom_attributes,
                'property_types' => $property_types_config,
            ) );
            // delete in-use attributes - remove all post meta with that key
            if (array_key_exists('deleted_in_use_attributes', $data) && $data['deleted_in_use_attributes']) {
                $deleted_in_use_attributes = explode(",", $data['deleted_in_use_attributes']);
                foreach ($deleted_in_use_attributes as $attribute_id) {
                    delete_post_meta_by_key(Property::get_input_prefix() . "_" . $attribute_id);
                }
            }
            
            // update in-use attributes - remove all post meta with that key
            if (array_key_exists('updated_in_use_attributes', $data) && $data['updated_in_use_attributes']) {
                $updated_in_use_attributes = json_decode(stripslashes($data['updated_in_use_attributes']), true);
                global $wpdb;
                foreach ($updated_in_use_attributes as $attribute) {
                    $old_key = Property::get_input_prefix() . "_" . $attribute['old_id'];
                    $new_key = Property::get_input_prefix() . "_" . $attribute['new_id'];
                    $wpdb->query("UPDATE $wpdb->postmeta SET `meta_key` = '$new_key' WHERE `meta_key` LIKE '$old_key'");
                }
            }
            if (array_key_exists('selected_tab', $data)) {
                $redirect = add_query_arg( array( 'section' => $data['selected_tab'] ), $redirect );
            }
            wp_redirect( $redirect );
        } else {
            update_option( $key, $data[$key] );
            wp_redirect( $_SERVER['REQUEST_URI'] );
        }
    }

    protected function register_payment_fields($fields, $section = '')
    {
        foreach ($fields as $id => $field) {
            if ( ! isset($field['name'] ) ) {
                $field['name'] = sprintf('%s[%s]', $this->payment_settings_key, $id);
            }
            
            $field['value'] = isset( $this->payment_settings[$id] ) ? $this->payment_settings[$id] : '';
            
            add_settings_field(
                    $id,
                    $field['title'],
                    array($this, 'render_field'),
                    $this->payment_settings_key, $section,
                    $field
            );
        }
    }
	
    /*
     * Plugin Options page rendering goes here, checks
     * for active tab and replaces key with the related
     * settings key. Uses the plugin_options_tabs method
     * to render the tabs.
     */

    public function plugin_options_page()
    {
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
        $setting_updated = false;
        if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == 'true' ) {
            $setting_updated = true;
        }
        ?>
            <div class="wrap">
                <?php
                if ($setting_updated) {
                    ?>
                    <div id="message" class="updated">
                        <p><?php echo __('Your settings has been updated.', 'txp'); ?></p>
                    </div>
                    <?php
                }
                ?>
        <?php $this->plugin_options_tabs(); ?>
                    <form method="post" action="" ng-app="App" ng-controller="PropertyConfigCtrl" ng-submit="submit($event)" ng-cloak>
        <?php wp_nonce_field( 'update-options' ); ?>
        <?php settings_fields( $tab ); ?>
        <?php do_settings_sections( $tab ); ?>  
        <?php submit_button(); ?>
                    </form>
                </div>
        <?php
        wp_enqueue_script( 'settings-page', TREXANHPROPERTY__PLUGIN_URL . 'modules/Admin/assets/js/settings-page.js', array( 'jquery' ) );
    }

    /*
     * Renders our tabs in the plugin options page,
     * walks through the object's tabs array and prints
     * them one by one. Provides the heading for the
     * plugin_options_page method.
     */

    public function plugin_options_tabs()
    {
        $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
        
        screen_icon();
        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</h2>';
    }

    /**
     * Add options page
     */
    public function add_plugin_pages()
    {
        $this->plugin_settings_tabs[$this->general_settings_key] = __( 'General', 'txp' );
        $this->plugin_settings_tabs[$this->payment_settings_key] = __( 'Payment', 'txp' );
        $this->plugin_settings_tabs[$this->config_property_settings_key] = __( 'Config property', 'txp' );
        
        // setting page
        $hook = add_submenu_page(
            'trexanh_property_homepage',
            'TreXanh Property Settings',
            __( 'Settings', 'txp' ),
            'manage_options',
            'trexanh_property_settings',
            array( $this, 'plugin_options_page' ),
            '',
            '25.1'
        );

        add_action('load-'.$hook, array( $this, 'plugin_settings_save' ));
    }
    
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        
        if ( ! empty( $input['enable_property_submission'] )) {
            $new_input['enable_property_submission'] = true;
        }
        
        if( !empty( $input['require_admin_to_approve'] ) ) {
            $new_input['require_admin_to_approve'] = true;
        }
        
        if( !empty( $input['require_payment_to_submit'] ) ) {
            $new_input['require_payment_to_submit'] = true;
        }
        
        if( isset( $input['submit_fee'] ) ) {
            $new_input['submit_fee'] = sanitize_text_field( $input['submit_fee'] );
        }
        
        if( !empty( $input['currency'] ) ) {
            $new_input['currency'] = sanitize_text_field( $input['currency'] );
        }
        
        if( !empty( $input['thousands_separator'] ) ) {
            $new_input['thousands_separator'] = sanitize_text_field( $input['thousands_separator'] );
        }
        
        if( !empty( $input['decimal_separator'] ) ) {
            $new_input['decimal_separator'] = sanitize_text_field( $input['decimal_separator'] );
        }
        
        if( isset( $input['currency_num_decimals'] ) ) {
            $new_input['currency_num_decimals'] = sanitize_text_field( $input['currency_num_decimals'] );
        }
        
        if( !empty( $input['symbol_pos'] ) ) {
            $new_input['symbol_pos'] = sanitize_text_field( $input['symbol_pos'] );
        }
        
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print '';
    }
    
    public function print_config_property_section_info()
    {
        echo "<br><br>" . __( "Setup custom attributes for properties and different property types. Specify which custom attributes will be associated with which property type.", "txp" );
    }
}
