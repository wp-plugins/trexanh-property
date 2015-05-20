<?php

namespace TreXanhProperty\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Directory;
use TreXanhProperty\Core\Formatter;
use TreXanhProperty\Core\PaymentGateway\PaymentGatewayService;

class SettingPage
{
    public function __construct() {
        $this->general_settings_key = TREXANHPROPERTY_PREFIX . 'general_settings';
        $this->payment_settings_key = TREXANHPROPERTY_PREFIX . 'payment_settings';
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
        add_action( 'init', array( &$this, 'load_settings' ) );
    }
    
    
    /*
     * For easier overriding we declared the keys
     * here as well as our tabs array which is populated
     * when registering settings
     */
    private $general_settings_key;
    private $payment_settings_key;
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
    }

    public function delete_settings() {
        delete_option( $this->general_settings_key );
        delete_option( $this->payment_settings_key );
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
                    <form method="post" action="options.php">
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
        
        // setting page
        add_submenu_page(
            'trexanh_property_homepage',
            'TreXanh Property Settings',
            __( 'Settings', 'txp' ),
            'manage_options',
            'trexanh_property_settings',
            array( $this, 'plugin_options_page' ),
            '',
            '25.1'
        );
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
}
