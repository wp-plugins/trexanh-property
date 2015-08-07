<?php

namespace TreXanhProperty\PluginSetup;

use TreXanhProperty\Core\FileReader;

class Status
{
    public static function view_system_status()
    {
        ?>
        <table class="widefat">
            <thead>
                <tr>
                    <th colspan="2"><?php _e('Templates', 'txp'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Overrides:
                    </td>
                    <td>
                        <ul>
                        <?php
                        $overwrite_files = self::get_overwrite_files();
                        if ($overwrite_files) {
                            foreach ($overwrite_files as $file) {
                                if (isset($file['is_outdate']) && $file['is_outdate'] == true) {
                                    if ( ! $file['theme_version'] ) {
                                        echo sprintf('<li><code>%s</code> is out of date. The core version is %s</li>', $file['path'], $file['core_version'] );
                                    } else {
                                        echo sprintf('<li><code>%s</code> version <strong style="color: red">%s</strong> is out of date. The core version is %s</li>', $file['path'], $file['theme_version'], $file['core_version'] );
                                    }
                                } else {
                                    echo sprintf('<li><code>%s</code></li>', $file['path']);
                                }
                            }
                        } else {
                            echo '<li>-</li>';
                        }
                        ?>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    }
    
    public static function get_overwrite_files()
    {
        $template_path = TREXANHPROPERTY__PLUGIN_DIR . 'templates/';
        
        $scanned_files = FileReader::list_files($template_path);
        $overwrite_files = array();
        $outdated_templates = false;
        
        foreach ( $scanned_files as $file ) {
            if ( file_exists( get_stylesheet_directory() . '/trexanh-property/' . $file ) ) {
                $theme_file = get_stylesheet_directory() . '/trexanh-property/' . $file;
            } elseif ( file_exists( get_template_directory() . '/trexanh-property/' . $file ) ) {
                $theme_file = get_template_directory() . '/trexanh-property/' . $file;
            } else {
                $theme_file = false;
            }

            if ( $theme_file ) {
                $core_version = FileReader::get_file_version($template_path . $file );
                $theme_version = FileReader::get_file_version( $theme_file );
                
                if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
                    if ( !$outdated_templates ) {
                        $outdated_templates = true;
                    }
                    $overwrite_file_path = str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file );
                    $overwrite_files[] = array(
                        'path' => $overwrite_file_path,
                        'theme_version' => $theme_version ? $theme_version : null,
                        'core_version' => $core_version,
                        'is_outdate' => true
                    );
                } else {
                    $overwrite_files[] = array(
                        'path' => str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ),
                    );
                }
            }
        }
        
        return $overwrite_files;
    }
    
}
