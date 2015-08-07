<?php

namespace TreXanhProperty\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Frontend\Model\PropertySearch;

/**
 * Manage themes, template, shortcodes,.. thing relate to frontend
 */
class Frontend
{

    private static $initiated = false;
    protected static $_instace = null;
    
    public static function init()
    {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    public static function instance()
    {
        if (self::$_instace === null) {
            self::$_instace = new self();
        }

        return self::$_instace;
    }

    public static function init_hooks()
    {
        self::$initiated = true;
        
        static::includes();
        add_action('after_setup_theme', array(__CLASS__, 'include_template_functions'));
        add_action('pre_get_posts', array(__CLASS__, 'pre_get_posts'));
        // register shortcodes
        add_action('init', array( 'TreXanhProperty\Frontend\Shortcode\Properties', 'init'));
    }

    public static function pre_get_posts( \WP_Query $query )
    {
        $post_type = \get_query_var( 'post_type', null );
        if ( $post_type === 'property' && is_search() ) {
            PropertySearch::on_search_property( $query );
        }
    }

    public static function is_frontend()
    {
        return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
    }
    
    public static function includes() {
        include_once( 'includes/txp-template-hooks.php' );
    }

    public static function include_template_functions()
    {
        include_once( 'includes/txp-template-functions.php' );
    }

    public static function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(__FILE__));
    }
}



