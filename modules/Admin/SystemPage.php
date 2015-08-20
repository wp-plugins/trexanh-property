<?php

namespace TreXanhProperty\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\PluginSetup\Status;

class SystemPage
{
    private $settings_key;
    
    public function __construct() {
        $this->settings_key = TREXANHPROPERTY_PREFIX . 'setting_system';
    }
    
    /**
     * Start up
     */
    public function init_hooks()
    {
        //Admin: setting page + menu
        add_action( 'admin_menu', array( $this, 'add_plugin_pages' ) );
        add_action( 'admin_init', array( &$this, 'register_settings' ) );
        add_action( 'init', array( &$this, 'load_settings' ) );
    }
    
    
    /**
     * Add options page
     */
    public function add_plugin_pages()
    {
        $this->plugin_settings_tabs[$this->settings_key] = 'General';
        $this->plugin_settings_tabs['system_status'] = 'Status';
        
        // setting page
        add_submenu_page(
            'trexanh_property_homepage',
            'TreXanh Property System',
            'System',
            'manage_options',
            'trexanh_property_system',
            array( $this, 'plugin_options_page' ),
            plugins_url( 'lib/images/icon.png' ),
            '25.2'
        );
    }
    
    /*
     * Renders our tabs in the plugin options page,
     * walks through the object's tabs array and prints
     * them one by one. Provides the heading for the
     * plugin_options_page method.
     */

    function plugin_options_tabs()
    {
        $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->settings_key;
        
        screen_icon();
        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=trexanh_property_system&tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</h2>';
    }
    
    /*
     * Loads both the general and advanced settings from
     * the database into their respective arrays. Uses
     * array_merge to merge with default values if they're
     * missing.
     */
    function load_settings() {
        $this->settings = (array) get_option( $this->settings_key );
    }
    
    /*
     * Registers the advanced settings and appends the
     * key to the plugin settings tabs array.
     */
    function register_settings() {
        
        register_setting(
            $this->settings_key, // Option group
            $this->settings_key, // Option group
            array( $this, 'sanitize' ) // Sanitize
        );
        
        // General settings
        add_settings_section(
            'general',
            __('General', 'txp'),
            null,
            $this->settings_key
        );
        
        add_settings_field(
            'delete_on_uninstall', // ID
            __('Delete on uninstall', 'txp'), // Title 
            array( $this, 'delete_on_uninstall_callback' ), // Callback
            $this->settings_key,
            'general' // Section           
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
        
        if( isset( $input['delete_on_uninstall'] ) ) {
            $new_input['delete_on_uninstall'] = true;
        }

        return $new_input;
    }
    
    function plugin_options_page() {
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->settings_key;
    
        ?>
    <div class="wrap">
    <?php $this->plugin_options_tabs(); ?>
        <?php 
        if ($tab == 'system_status') {
            Status::view_system_status();
        } else {
        ?>
            <form method="post" action="options.php">
        <?php wp_nonce_field( 'update-options' ); ?>
        <?php settings_fields( $tab ); ?>
        <?php do_settings_sections( $tab ); ?>  
        <?php submit_button(); ?>
            </form>
        <?php } ?>
        </div>
    <?php
    }
    
    public function delete_on_uninstall_callback()
    {
        $des = __('The plugin will delete all TreXanhProperty, Property, Order data when uninstalling via Plugins > Delete.', 'txp');
        $value = (isset( $this->settings['delete_on_uninstall'] ) && $this->settings['delete_on_uninstall']) ? 'checked' : '';
        ?>
        <p>
            <label>
                <input type='checkbox' name='<?php echo $this->settings_key?>[delete_on_uninstall]' <?php echo $value ;?>  />
                <?php _e('Enabled', 'txp');?>
            </label>
            
        </p>
        <p class='description'><?php echo $des;?></p>
        <?php
    }    
    
    
}