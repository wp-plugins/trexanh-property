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
}
