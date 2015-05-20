<?php

/* 
 * Fetch setting which is define in various place including mainly wordpress's admin
 */

namespace TreXanhProperty\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Config {
    /**
     * Get config in Setting menu > $name
     * @param string $name
     */
    public static function get_settings($name) {
        switch ($name) {
            case 'general': 
                return get_option(TREXANHPROPERTY_PREFIX . 'general_settings');
            case 'payment':
                return get_option(TREXANHPROPERTY_PREFIX . 'payment_settings');
            default:
                return array();
        }
    }
    
    /**
     * 
     * @param string $key
     * @param string $name
     * @param mixed $empty
     * @return mixed
     */
    public static function get_setting( $key, $name, $empty = '')
    {
        $settings = self::get_settings($name);
        if ( isset($settings[$key]) ) {
            return $settings[$key];
        }
        return $empty;
    }
    
    /**
     * Get config in System menu
     */
    public static function get_system() {
        return get_option(TREXANHPROPERTY_PREFIX . 'setting_system');
    }
} 

