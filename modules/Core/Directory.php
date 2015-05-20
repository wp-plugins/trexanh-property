<?php

namespace TreXanhProperty\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Directory
{

    public static function get_supported_currencies()
    {
        return array(
            'AUD' => __( 'Australian Dollars', 'txp' ),
            'CAD' => __( 'Canadian Dollars', 'txp' ),
            'CNY' => __( 'Chinese Yuan', 'txp' ),
            'EUR' => __( 'Euros', 'txp' ),
            'HKD' => __( 'Hong Kong Dollar', 'txp' ),
            'JPY' => __( 'Japanese Yen', 'txp' ),
            'KRW' => __( 'South Korean Won', 'txp' ),
            'MXN' => __( 'Mexican Peso', 'txp' ),
            'NZD' => __( 'New Zealand Dollar', 'txp' ),
            'GBP' => __( 'Pounds Sterling', 'txp' ),
            'RUB' => __( 'Russian Ruble', 'txp' ),
            'SGD' => __( 'Singapore Dollar', 'txp' ),
            'THB' => __( 'Thai Baht', 'txp' ),
            'TWD' => __( 'Taiwan New Dollars', 'txp' ),
            'UAH' => __( 'Ukrainian Hryvnia', 'txp' ),
            'USD' => __( 'US Dollars', 'txp' ),
            'VND' => __( 'Vietnamese Dong', 'txp' ),
        );
    }

    public static function get_currencies_symbol( $currency = '' )
    {
        switch ( $currency ) {
            case 'AUD' :
            case 'CAD' :
            case 'HKD' :
            case 'MXN' :
            case 'NZD' :
            case 'SGD' :
            case 'USD' :
                $currency_symbol = '$';
                break;
            case 'CNY' :
            case 'JPY' :
                $currency_symbol = '¥';
                break;
            case 'EUR' :
                $currency_symbol = '€';
                break;
            case 'GBP' :
                $currency_symbol = '£';
                break;
            case 'KRW' :
                $currency_symbol = '₩';
                break;
            case 'RUB' :
                $currency_symbol = 'руб';
                break;
            case 'THB' :
                $currency_symbol = '฿';
                break;
            case 'TWD' :
                $currency_symbol = 'NT$';
                break;
            case 'UAH' :
                $currency_symbol = '₴';
                break;
            case 'VND' :
                $currency_symbol = '₫';
                break;
            default :
                $currency_symbol = '';
                break;
        }
        
        return $currency_symbol;
    }

}
