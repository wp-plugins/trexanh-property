<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function txp_setup_property_data($post) {
    unset( $GLOBALS['property'] );

    if ( is_int( $post ) )
        $post = get_post( $post );

    if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'property' ) ) )
        return;

    $GLOBALS['property'] = new \TreXanhProperty\Core\Property($post);

    return $GLOBALS['property'];
}
add_action('the_post', 'txp_setup_property_data');

/**
 * Use wordpress default template locate mechanism.
 * If not found, then try plugin's default template
 * 
 * @param string $template_name
 * @param type $params
 */
function txp_get_template_part( $template_name, $params = array() ) {
    //locate theme's template
    $template_names[] = $template_name;
    $template = txp_locate_template( $template_names );
    
    //locate plugin's template
    if ( ! $template ) {
        if (file_exists(TREXANHPROPERTY__PLUGIN_DIR . 'templates/' . $template_name)) {
            $template =  TREXANHPROPERTY__PLUGIN_DIR . 'templates/' . $template_name;
        } 
        //find template in other extensions
        else {
            global $txp_extension_template_folders;
            if (!empty($txp_extension_template_folders)) {
                foreach ($txp_extension_template_folders as $folder) {
                    if (file_exists($folder . $template_name)) {
                        $template =  $folder . $template_name;
                    }
                }
            }
            
        }
        
    }
    
    if ( ! empty( $params ) )  {
        extract($params);
    }
    include( $template );
}

/**
 * load property template for property detail page
 * if template file for a certain property type exists, it will be loaded
 * otherwise, load the default template
 * 
 * @param string $property_type
 * @param array $params
 */
function txp_get_property_detail_template_by_type( $property_type, $params = array() ) {
    //locate theme's template
    $template_name = "content-single-property-{$property_type}.php";
    $template_names[] = $template_name;
    $template_names[] = 'content-single-property.php';
    $template = txp_locate_template( $template_names );
    
    if ( ! $template ) {
        if ( file_exists( TREXANHPROPERTY__PLUGIN_DIR . "templates/" . $template_name ) ) {
            $template = TREXANHPROPERTY__PLUGIN_DIR . "templates/" . $template_name ;
        }
        
    }
    
    if (!$template) {
         if ( file_exists( TREXANHPROPERTY__PLUGIN_DIR . "templates/content-single-property.php" ) ) {
            $template = TREXANHPROPERTY__PLUGIN_DIR . "templates/content-single-property.php" ;
        }
    }
    
    if ( ! empty( $params ) )  {
        extract($params);
    }
    include( $template );
}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
 * inherit from a parent theme can just overload one file.
 * 
 * Files will be searched in trexanh-property folder in the theme folder first
 * 
 * @since 2.7.0
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool $load If true the template file will be loaded if it is found.
 * @param bool $require_once Whether to require_once or require. Default true. Has no effect if $load is false.
 * @return string The template filename if one is located.
 */
function txp_locate_template($template_names, $load = false, $require_once = true)
{
    $located = '';
    $templates_folder = "trexanh-property";
    foreach ((array) $template_names as $template_name) {
        if (!$template_name)
            continue;
        if (file_exists(STYLESHEETPATH . "/$templates_folder/" . $template_name)) {
            $located = STYLESHEETPATH . "/$templates_folder/" . $template_name;
            break;
        } else if (file_exists(TEMPLATEPATH . "/$templates_folder/" . $template_name)) {
            $located = TEMPLATEPATH . "/$templates_folder/" . $template_name;
            break;
        }
    }

    if ($load && '' != $located)
        load_template($located, $require_once);

    return $located;
}

function txp_get_search_query()
{
    return \TreXanhProperty\Frontend\Model\PropertySearch::get_search_query();
}
