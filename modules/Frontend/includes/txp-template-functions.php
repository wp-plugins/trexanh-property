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
    $template = locate_template( $template_names );
    
    
    //locate plugin's template
    if ( ! $template ) {
        $template = TREXANHPROPERTY__PLUGIN_DIR . 'templates/' . $template_name;
    }
    
    if ( ! empty( $params ) )  {
        extract($params);
    }
    include( $template );
}