<?php
/**
 * @todo : remove later
 * Load templates for custom post type: property, order
 */
namespace TreXanhProperty\Frontend;

use TreXanhProperty\Core\Order;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TemplateLoader
{
    public static function template_loader( $template )
    {
        $file = '';

        if ( is_single() && get_post_type() == 'property' ) {

            $file = 'single-property.php';
            $find[] = $file;
            $find[] = 'trexanh-property/' . $file;
        } else if (is_single() && get_post_type() == Order::POST_TYPE ) {
            $file = 'single-order.php';
            $find[] = $file;
            $find[] = 'trexanh-property/' . $file;
        } elseif ( get_post_type() == 'property' ) {
            $file = 'archive-property.php';
            $find[] = $file;
            $find[] = 'trexanh-property/' . $file;
        }

        if ( $file ) {
            $template = locate_template( array_unique( $find ) );
            if ( !$template ) {
                // get template from plugin template;
                $template = TREXANHPROPERTY__PLUGIN_DIR . 'templates/' . $file;
            }
        }

        return $template;
    }

}