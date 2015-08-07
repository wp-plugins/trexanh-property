<?php

namespace TreXanhProperty\Core;
use TreXanhProperty\Admin\SettingPage;

class PropertyType
{
    public static function get_types()
    {
        $options = get_option(SettingPage::get_config_property_settings_key());
        return !empty($options['property_types']) ? $options['property_types'] : array();
    }
    
    public static function get_type( $type_id )
    {
        $types = static::get_types();
        if (isset($types[$type_id])) {
            return $types[$type_id];
        }
        
        return null;
    }
    
    /**
     * Whether or not we enable custom property type feature.
     * Property type feature include
     * - Menu for create property of custom property type
     * - Form to enter a property of custom property type
     * - Setting form to customize property type attribute
     * 
     * @return boolean
     */
    public static function enable_property_type_feature()
    {
        $enable = apply_filters('trexanhproperty_enable_custom_property_type', true);
        if ($enable == null) {
            return false;
        }        
        return $enable;
    }
}
