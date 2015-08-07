<?php

namespace TreXanhProperty\Core\Widget;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

use WP_Widget;
use TreXanhProperty\Frontend\Shortcode\Properties;

class PropertyMapListing extends WP_Widget
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->widget_id = 'trexanhproperty_widget_property_map_listing';
        $this->widget_cssclass = 'trexanhproperty widget_property_map_listing';
        $this->widget_description = __( 'Display properties in a map.', 'txp' );
        $this->settings = array(
            'height' => array(
                'type' => 'text',
                'default' => '',
                'label' => __( 'Map height (px), leave blank to use default', 'txp' )
            ),
            'ids' => array(
                'type' => 'text',
                'default' => '',
                'label' => __( 'Only display property with these ids', 'txp' )
            ),
            'featured' => array(
                'type' => 'checkbox',
                'default' => 'no',
                'label' => __( 'Show featured properties', 'txp' ),
            )
        );

        $widget_ops = array(
            'classname' => $this->widget_cssclass,
            'description' => $this->widget_description
        );

        $this->WP_Widget( $this->widget_id, __( 'TXP Property Map Listing', 'txp' ), $widget_ops );

        add_action( 'save_post', array( $this, 'clean_widget_cache' ) );
        add_action( 'deleted_post', array( $this, 'clean_widget_cache' ) );
        add_action( 'switch_theme', array( $this, 'clean_widget_cache' ) );
    }

    public function clean_widget_cache()
    {
        wp_cache_delete( apply_filters( 'txp_cached_widget_id', $this->widget_id ), 'widget' );
    }

    /**
     * update function.
     *
     * @see WP_Widget->update
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update( $new_instance, $old_instance )
    {

        $instance = $old_instance;

        if ( !$this->settings ) {
            return $instance;
        }

        foreach ( $this->settings as $key => $setting ) {

            if ( isset( $new_instance[$key] ) ) {
                $instance[$key] = sanitize_text_field( $new_instance[$key] );
            } elseif ( 'checkbox' === $setting['type'] ) {
                $instance[$key] = 'no';
            }
        }

        $this->clean_widget_cache();

        return $instance;
    }

    public function widget( $args, $instance )
    {
        echo $args['before_widget'];
        
        $title = empty( $instance['title'] ) ? '' : $instance['title'];
        $widget_title = apply_filters( 'widget_title', $title , $instance, $this->id_base );
        
        if ( $widget_title ) {
            echo $args['before_title'] . $widget_title . $args['after_title'];
        }

        echo Properties::map_properties($instance);

        echo $args['after_widget'];
    }

    /**
     * form function.
     *
     * @see WP_Widget->form
     * @param array $instance
     */
    public function form( $instance )
    {

        if ( !$this->settings ) {
            return;
        }

        foreach ( $this->settings as $key => $setting ) {
            
            $value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['default'];
            switch ( $setting['type'] ) {

                case 'text' :
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
                        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
                    </p>
                    <?php
                    break;
                case 'checkbox' :
                    ?>
                    <p>
                        <input id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="yes" <?php checked( $value, 'yes' ); ?> />
                        <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
                    </p>
                    <?php
                    break;
            }
        }
    }

}
