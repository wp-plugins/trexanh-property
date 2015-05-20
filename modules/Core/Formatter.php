<?php

namespace TreXanhProperty\Core;


use TreXanhProperty\Core\Config;
use TreXanhProperty\Core\Directory;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Formatter
{
    /**
     * 
     * @param string | int $number
     * @return string
     */
    public static function currency( $number, $currency_code = '', $symbol_pos = '' )
    {
        $thousands_sep = Config::get_setting( 'thousands_separator', 'general', ',' );
        
        $dec_point = Config::get_setting( 'decimal_separator', 'general', '.' );
        
        $decimals = Config::get_setting( 'currency_num_decimals', 'general', '2');
        
        if ( !$currency_code ) {
            $currency_code = Config::get_setting( 'currency', 'general', 'USD' );
        }
        
        $currency_symbol = Directory::get_currencies_symbol( $currency_code );
        
        if ( !$symbol_pos ) {
            $symbol_pos = Config::get_setting('symbol_pos', 'general', 'left');
        }
        
        $formatted_number = number_format( floatval($number), $decimals, $dec_point, $thousands_sep);
        
        if ($symbol_pos == 'right') {
            return $formatted_number . $currency_symbol;
        }
        
        return $currency_symbol . $formatted_number;
    }
}