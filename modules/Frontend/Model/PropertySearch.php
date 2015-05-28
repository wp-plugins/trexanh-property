<?php

namespace TreXanhProperty\Frontend\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\Core\Property;
use WP_Query;
use WP_Meta_Query;

class PropertySearch
{
    public static function on_search_property( WP_Query $query )
    {
        // no paging if search results is shown in map mode
        if (array_key_exists( "display", $_GET ) && "map" == $_GET['display'] ) {
            $query->set('posts_per_page', -1);
        }
        add_filter( 'posts_clauses', array( __CLASS__, 'posts_clauses' ) );
        return $query;
    }

    protected static function get_query_param( $key, $empty = null )
    {
        if ( !empty( $_GET[$key] ) ) {
            return $_GET[$key];
        }

        return $empty;
    }
    
    public static function get_search_query($value = '')
    {
        $description = array();
        
        $for = '';
        if ( get_query_var('s') ) {
            $description[] = sprintf( __( 'keyword &ldquo;%s&rdquo; ', 'txp' ), esc_html( get_query_var('s') ) );
            $for = 'for';
        } else {
            $description[] = __( 'Any ', 'txp' );
        }
        
        $bedrooms = self::get_query_param( 'bedrooms' );
        $bathrooms = self::get_query_param( 'bathrooms' );
        $category = self::get_query_param( 'category' );
        
        $min_area = self::get_query_param('min_area');
        $max_area = self::get_query_param('max_area');
        $area_unit = self::get_query_param('area_unit');
        
        $price_from = self::get_query_param('price_from');
        $price_to = self::get_query_param('price_to');
        
        $listing_type = self::get_query_param('listing_type');
        
        $garages = self::get_query_param('garages');
        
        if ($category && $category != 'all') {
            $description[] = sprintf( __( '%s <b>%s</b> ', 'txp' ),$for, esc_html( $category ) );
            $for = 'for';
        }
        
        if ( $listing_type && $listing_type != 'all' ) {
            $description[] = sprintf( __( '%s <b>%s</b> ', 'txp' ), $for, esc_html( $listing_type ) );
        }
        
        $with = 'with';
        if ($bedrooms && $bedrooms !== 'all') {
            $description[] = sprintf( __( '%s %s bedroom(s)' ),$with, esc_html( $bedrooms) ) ;
            $with = ',';
        }
        
        if ($bathrooms && $bathrooms !== 'all') {
            $description[] = sprintf( __( '%s %s bathroom(s)' ), $with , esc_html( $bathrooms) ) ;
            $with = ',';
        }
        
        if ($garages && $garages !== 'all') {
            $description[] = sprintf( __( '%s %s garage(s)' ),$with, esc_html( $garages) ) ;
            $with = ',';
        }
        
        if ( $min_area > 0 && $max_area > 0 && $max_area >= $min_area ) {
            $area_unit_description = '';
            if ( $area_unit == 'square_meter' ) {
                $area_unit_description = 'm<sup>2</sup>';
            } else if ( $area_unit == 'sqft' ) {
                $area_unit_description = 'ft<sup>2</sup>';
            }
            $description[] = sprintf( __( '%s area from %s %s to %s %s' , 'txp'),$with, esc_html( $min_area ), $area_unit_description, esc_html( $max_area ), $area_unit_description );
            $with = ',';
        }
        
        // Search by price.
        if ( $price_from > 0 && $price_to > 0 && $price_to > $price_from ) {
            
            $description[] = sprintf( __( '%s price from %s to %s', 'txp' ), $with, txp_currency( $price_from ), txp_currency( $price_to ) );
            $with = ', ';
        } else if ( $price_from > 0 ) {
            $description[] = sprintf( __( '%s price >= %s', 'txp' ), $with, txp_currency( $price_from ) );
            $with = ', ';
        } else if ( $price_to > 0 ) {
            $description[] = sprintf( __( '%s price <= %s', 'txp' ), $with, txp_currency( $price_to ) );
            $with = ', ';
        }
        
        return implode('', $description);
    }


    protected static function add_prefix_meta_key($key)
    {
        return Property::$input_prefix . '_' . $key;
    }

    public static function posts_clauses( $args )
    {
        global $wpdb;
        $bedrooms = self::get_query_param( 'bedrooms' );
        $bathrooms = self::get_query_param( 'bathrooms' );
        $category = self::get_query_param( 'category' );
        
        $min_area = self::get_query_param('min_area');
        $max_area = self::get_query_param('max_area');
        $area_unit = self::get_query_param('area_unit');
        
        $price_from = self::get_query_param('price_from');
        $price_to = self::get_query_param('price_to');
        
        $listing_type = self::get_query_param('listing_type');
        
        $garages = self::get_query_param('garages');
        
        $meta_queries = array();

        if ( $bedrooms && $bedrooms !== 'all' ) {
            $compare = '=';
            if ( strpos( $bedrooms, '+' ) !== false ) {
                $compare = '>=';
                $bathrooms = substr( $bedrooms, 0, strpos( $bedrooms, '+' ) );
            }

            $meta_queries = array(
                array(
                    'key' => self::add_prefix_meta_key('bedrooms'),
                    'value' => $bedrooms,
                    'type' => 'numeric',
                    'compare' => $compare,
                )
            );
        }

        if ( $bathrooms && $bathrooms !== 'all' ) {
            $compare = '=';

            if ( strpos( $bathrooms, '+' ) !== false ) {
                $compare = '>=';
                $bathrooms = substr( $bathrooms, 0, strpos( $bathrooms, '+' ) );
            }

            $meta_queries[] = array(
                'key' => self::add_prefix_meta_key('bathrooms'),
                'value' => $bathrooms,
                'type' => 'numeric',
                'compare' => $compare,
            );
        }
        
        if ($listing_type && $listing_type !== 'all') {
            $meta_queries[] = array(
                'key' => self::add_prefix_meta_key( 'listing_type' ),
                'value' => $listing_type,
                'compare' => '=',
            );
        }
        
        // Search by area
        if ($min_area > 0 && $max_area > 0 && $max_area >= $min_area ) {
            $meta_queries[] = array(
                'key' => self::add_prefix_meta_key( 'area' ),
                'value' => array($min_area, $max_area),
                'type' => 'numeric',
                'compare' => 'between',
            );
            
            $meta_queries[] = array(
                'key' => self::add_prefix_meta_key( 'area_unit' ),
                'value' => $area_unit,
                'compare' => '=',
            );
        }
        
        // Search by price.
        if ( $price_from > 0 && $price_to > 0 && $price_to > $price_from ) {
            $meta_queries[] = array(
                'key' => self::add_prefix_meta_key( 'price' ),
                'value' => array($price_from, $price_to),
                'type' => 'numeric',
                'compare' => 'between'
            );
        } else if ( $price_from > 0 ) {
            $meta_queries[] = array(
                'key' => self::add_prefix_meta_key( 'price' ),
                'value' => $price_from,
                'type' => 'numeric',
                'compare' => '>='
            );
        } else if ( $price_to > 0 ) {
            $meta_queries[] = array(
                'key' => self::add_prefix_meta_key( 'price' ),
                'value' => $price_to,
                'type' => 'numeric',
                'compare' => '<='
            );
        }
        
        if ( $category && $category !== 'all' ) {
            $meta_queries[] = array(
                'key' => self::add_prefix_meta_key('category'),
                'value' => $category,
                'compare' => '=',
            );
        }
        
        if ( $garages && $garages !== 'all' ) {
            $compare = '=';

            if ( strpos( $garages, '+' ) !== false ) {
                $compare = '>=';
                $garages = substr( $garages, 0, strpos( $garages, '+' ) );
            }

            $meta_queries[] = array(
                'key' => self::add_prefix_meta_key('garage'),
                'value' => $garages,
                'type' => 'numeric',
                'compare' => $compare,
            );
        }
        if (array_key_exists( "display", $_GET ) && "map" == $_GET['display'] ) {
            $meta_queries[] = array(
                'key' => self::add_prefix_meta_key('address_coordinates'),
                'value' => '""',
                'compare' => "!=",
            );
        }
        $query = new WP_Meta_Query( $meta_queries );
        $sub_queries = $query->get_sql( 'post', $wpdb->posts, 'ID' );

        $args['join'] .= $sub_queries['join'];
        $args['where'] .= $sub_queries['where'];

        return $args;
    }
}
