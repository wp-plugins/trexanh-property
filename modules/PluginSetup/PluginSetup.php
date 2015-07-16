<?php
namespace TreXanhProperty\PluginSetup;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Property;
use TreXanhProperty\Core\Order;
use TreXanhProperty\Core\Config;


class PluginSetup {
    
    public static $plugin_pages = array(
        array(
            'name' => 'Submit Property',
            'slug' => 'submit-property',
            'wp_options' => array(
                'id' => 'submit_page_id',
            ),
        ),
        array(
            'name' => 'Submit Property > Payment',
            'slug' => 'submit-property-payment',
            'wp_options' => array(
                'id' => 'payment_page_id',
            ),
        ),
        array(
            'name' => 'My Properties',
            'slug' => 'my-properties',
            'wp_options' => array(
                'id' => 'my_properties_page_id',
            ),
        ),
        array(
            'name' => 'Properties',
            'slug' => 'properties',
            'wp_options' => array(
                'id' => 'properties_page_id',
            ),
        ),
        array(
            'name' => 'Submit Property > Payment status',
            'slug' => 'submit-property-payment-status',
            'wp_options' => array(
                'id' => 'payment_status_page_id',
            ),
        ),
    );
    
    public static function activation() {
        foreach (self::$plugin_pages as $page) {
            self::create_page(
                $page['name'], 
                $page['slug'], 
                TREXANHPROPERTY_PREFIX . $page['wp_options']['id']
            );
        }
        
        if ( ! Config::get_settings( 'general' ) ) {
            update_option(TREXANHPROPERTY_PREFIX . 'general_settings', array(
                'enable_property_submission' => true,
                'currency' => 'USD'
            ));
        }
        
        $current_db_version = static::get_current_db_version();
        
        if ( ! $current_db_version || version_compare($current_db_version , TREXANHPROPERTY_DB_VERSION, '<' ) ) {
            static::update_db_version();
        }
        
        update_option(TREXANHPROPERTY_PREFIX . 'code_version', TREXANHPROPERTY_VERSION);
        
    }

    /**
     * 
     * @global \wpdb $wpdb
     */
    public static function uninstall() {
        global $wpdb;
        
        // delete pages
        foreach (self::$plugin_pages as $page) {
            self::delete_page(
                TREXANHPROPERTY_PREFIX . $page['wp_options']['id']
            );
        }

        // delete options
        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '" . TREXANHPROPERTY_PREFIX . "%';");
        
        // delete custom post type posts
        $custom_post_types = array(
            Property::get_post_type(),
            Order::POST_TYPE,
        );
        
        $query = "
          DELETE FROM `" . $wpdb->posts . "`
          WHERE `post_type` IN (" . implode( ',', $custom_post_types ) . ") 
        ";
        
        $wpdb->query($query);
        wp_trash_post();
    }  
    
    /**
     * 
     * @param string $the_page_title
     * @param string $slug
     * @param string $page_id_option_name
     */
    public static function create_page($the_page_title, $slug, $page_id_option_name) {

        $the_page = get_page_by_title( $the_page_title, $the_page );

        if ( ! $the_page ) {

            // Create post object
            $_p = array();
            $_p['post_title'] = $the_page_title;
            $_p['post_content'] = '';
            $_p['post_status'] = 'publish';
            $_p['post_type'] = 'page';
            $_p['comment_status'] = 'closed';
            $_p['ping_status'] = 'closed';
            $_p['post_category'] = array(1); // the default 'Uncatrgorised'

            // Insert the post into the database
            $the_page_id = wp_insert_post( $_p );

        }
        else {
            // the plugin may have been previously active and the page may just be trashed...

            $the_page_id = $the_page->ID;

            //make sure the page is not trashed...
            $the_page->post_status = 'publish';
            $the_page_id = wp_update_post( $the_page );

        }

        add_option( $page_id_option_name, $the_page_id );
    }
    
    /**
     * Check current db version, update db verions if need.
     */
    protected static function update_db_version()
    {
        $current_db_version = static::get_current_db_version();

        $updaters = array(
            '0.4' => 'updates/txp-update-0.4.php',
        );
        
        $manual_update = false;
        foreach ($updaters as $version => $updater) {
            if ( version_compare( $current_db_version, $version, '<' )) {
                if ( ! include ( $updater ) ) {
                    $manual_update = true;
                    break;
                }
                static::update_current_db_version($version);
            }
        }
        
        if ( $manual_update ) {
            add_action('admin_notices', array(__CLASS__, 'render_upate_notice'));
        }
    }
    
    /**
     * Compare current db version storaged in database with db version on code.
     * Run update if need.
     */
    public static function check_update()
    {
        $current_db_version = static::get_current_db_version();
        if ( ! $current_db_version || version_compare($current_db_version , TREXANHPROPERTY_DB_VERSION, '<' ) ) {
            static::update_db_version();
        }
    }
    
    public static function check_template_status()
    {
        // Should hide notify if current screen is overwrite templates.
        $current_screen = get_current_screen();
        $current_tab = isset($_GET['tab']) ? $_GET['tab'] : null;
        if ( $current_screen->id == 'trexanh-property_page_trexanh_property_system' && $current_tab == 'system_status' ) {
            return ;
        }
        
        $outdate = false;
        $overwrite_files = Status::get_overwrite_files();
        foreach ($overwrite_files as $file) {
            if (isset($file['is_outdate']) && $file['is_outdate'] == true) {
                $outdate = true;
                break;
            }
        }
        
        if ( $outdate ) {
            include 'views/notice-update-template.php';
        }
    }
    
    /**
     * Show notification to admin notice about should manual update plugin.
     */
    public static function render_upate_notice()
    {
        include TREXANHPROPERTY__PLUGIN_DIR . '/modules/Admin/views/notice-update.php';
    }

    /**
     * Return value of db version storage on db.
     * 
     * @return string|null
     */
    protected static function get_current_db_version()
    {
        $db_verion_key = TREXANHPROPERTY_PREFIX . 'db_version';
        return get_option($db_verion_key, null);
    }
    
    /**
     * Update options db_verion to new version.
     * 
     * @param string $version
     * @return boolean
     */
    protected static function update_current_db_version( $version )
    {
        $db_verion_key = TREXANHPROPERTY_PREFIX . 'db_version';
        return update_option($db_verion_key, $version);
    }

    public static function delete_page($page_id_option_name) {
        //  the id of our page...
        $the_page_id = get_option( $page_id_option_name );
        if( $the_page_id ) {

            wp_delete_post( $the_page_id, true );
        }
        delete_option($page_id_option_name);
    }    
}
