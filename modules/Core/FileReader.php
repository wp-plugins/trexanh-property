<?php

namespace TreXanhProperty\Core;

class FileReader
{
    /**
     * Retrieve metadata from a file. Based on WP Core's get_file_data function
     * 
     * @param string $file
     * @return string
     */
    public static function get_file_version( $file )
    {
        if (! file_exists( $file ) ) {
            return '';
        }
        // We don't need to write to the file, so just open for reading.
	$fp = fopen( $file, 'r' );

	// Pull only the first 8kiB of the file in.
	$file_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose( $fp );

	// Make sure we catch CR-only line endings.
	$file_data = str_replace( "\r", "\n", $file_data );
        
        $version = '';
        if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] ) {
            $version = _cleanup_header_comment( $match[1] );
        }

	return $version;
    }
    
    /**
     * 
     * @param string $dir
     * @return array
     */
    public static function list_files($dir)
    {
        $files = scandir( $dir );
        $result = array();

        if ( $files ) {
            foreach ( $files as $value ) {
                if ( !in_array( $value, array( ".", ".." ) ) ) {
                    if ( is_dir( $dir . DIRECTORY_SEPARATOR . $value ) ) {
                        $sub_files = self::list_files( $dir . DIRECTORY_SEPARATOR . $value );
                        foreach ( $sub_files as $sub_file ) {
                            $result[] = $value . DIRECTORY_SEPARATOR . $sub_file;
                        }
                    } else {
                        $result[] = $value;
                    }
                }
            }
        }
        return $result;
    }
}