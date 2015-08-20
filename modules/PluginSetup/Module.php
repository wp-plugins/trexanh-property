<?php

/* 
 * Module Plugin setup
 */

namespace TreXanhProperty\PluginSetup;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Module
{
    private static $initiated = false;

    public static function init()
    {
        if ( !self::$initiated ) {
            self::init_hooks();
        }
    }

    public static function init_hooks()
    {
        add_action( 'admin_init', array(__NAMESPACE__ . '\PluginSetup', 'check_update'));
        add_action('admin_notices', array(__NAMESPACE__ . '\PluginSetup', 'check_template_status'));
        self::$initiated = true;
    }
}