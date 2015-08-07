<?php

namespace TreXanhProperty\SubmitProperty;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TreXanhProperty\SubmitProperty\SubmitPropertyController;
/* 
 * Routing
 */

class Module {
    
    private static $initiated = false;

    public static function init()
    {
        if ( !self::$initiated ) {
            self::init_hooks();
        }
    }

    public static function init_hooks()
    {
        self::$initiated = true;
        add_action('init', array('\TreXanhProperty\SubmitProperty\Module', 'route'));
//        add_action('trexanhproperty_payment_completed', array(''));
        add_filter( 'the_content', array('\TreXanhProperty\SubmitProperty\Module', 'render') ); 
    }
    
    public static function is_registered_route() {
        
        //Note: wordpress can be in root directory or in subfolder
        //wordpress base path
        $base_path = parse_url(get_site_url());
        $base_path = isset($base_path['path']) ? $base_path['path'] : '';
        $routes = array(
            '/submit-property/',
            '/submit-property-payment/',
            '/submit-property-payment-status/',
            '/my-properties/',
        );
        
        foreach ($routes as &$route) {
            $route = $base_path . $route;
        }
        
        $slug = $_SERVER['REQUEST_URI'];
        if ( strpos( $slug, '?') !== false) {
            $slug = substr($slug, 0, strpos( $slug, '?'));
        }
        
        return in_array($slug, $routes);
    }
    
    public static function route (  ) {
        if (self::is_registered_route()) {
            $app = new \Slim\Slim(array(
                'templates.path' => __DIR__  . '/templates'
            ));
            
            $app->get('/submit-property/', function () {                
                global $slim_response;
                $slim_response = SubmitPropertyController::form();
            });
            $app->post('/submit-property/', function () {                
                global $slim_response;
                $slim_response = SubmitPropertyController::save();
            });
            $app->get('/my-properties/', function () {
                global $slim_response;
                $slim_response = MyPropertyController::output();
            });
            
            $app->post('/submit-property-payment/', function () {
                global $slim_response;
                $slim_response = SubmitPropertyController::payment();
            });
            
            $app->get('/submit-property-payment-status/', function () {
                global $slim_response;
                $slim_response = SubmitPropertyController::payment_status();
            });
            $app->run();
        }
    }
    
    public static function render($content ) {
        if (self::is_registered_route()) {
            global $slim_response;
            return $slim_response;
        }
        return $content;
    }
}