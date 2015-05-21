<?php
/**
 * Manage property's gallery
 */
namespace TreXanhProperty\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PropertyGallery
{
    /**
     * Get gallery's images of property
     * @param type $post_id property's post id
     * @return array List of posts.
     */
    public static function get_attachment($post_id)
    {
        return get_posts( array(
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_parent' => $post_id,
        ));
    }

    /**
     * Link gallery to property
     * 
     * @param type $post_ids gallery's images, each image will have a post id
     * @param type $parent_id property's post id
     */
    public static function set_parent($post_ids, $parent_id)
    {
        $post_ids = (!is_array($post_ids)) ? array($post_ids) : $post_ids;
        foreach ( $post_ids as $post_id ) {
            wp_update_post( array(
                'ID' => $post_id,
                'post_parent' => $parent_id,
            ));
        }
    }

    /**
     * Update gallery's images for property. This is called when post is updated.
     * 
     * @param type $post_id property's id
     * @param type $post property's post which include gallery's images
     */
    public static function update_gallery($post_id, $post)
    {
        // Post will be updated when payment complete
        // In those cases, just need to update post status and should do nothing with the gallery
        // If payment with paypal payment gateway, a get request will be sent to update post status
        // If payment with stripe payment gateway, a post request will be sent with payment method data to update post status
        // in both cases, we do nothing here
        $request_method = strtolower( $_SERVER['REQUEST_METHOD'] );
        if ( $request_method === 'get' ) {
            return;
        }
        if ( $request_method === 'post' && isset( $_POST['payment_method'] ) ) {
            return;
        }
        // otherwise, update the gallery
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        
        $post_type = $post->post_type;

        if(!$post_id || $post_type !== Property::get_post_type()) {
            return;
        }
        // update current attachments
        $attachments = self::get_attachment($post_id);
        $current_attachment_ids = array();
        if ( $attachments ) {
            foreach ( $attachments as $attachment ) {
                $current_attachment_ids[] = $attachment->ID;
            }
        }
        if ( !empty( $_POST['gallery_photo_ids'] ) ) {
            $new_attachment_ids = explode(",", $_POST['gallery_photo_ids']);
            foreach($new_attachment_ids as $p_id) {
                // add link between the new attachment and the property
                $existing_key = array_search($p_id, $current_attachment_ids);
                if ($existing_key !== false) {
                    unset($current_attachment_ids[$existing_key]);
                } else {
                    self::set_parent($p_id, $post_id);
                }
            }
        }
        // remove link between attachments which are no longer linked to the property
        if (count($current_attachment_ids)) {
            self::set_parent($current_attachment_ids, 0);
        }
        // upload images and link new attachments
        if (isset($_FILES['gallery_images'])) {
            // rearrange the $_FILES
            $files = array();
            foreach( $_FILES['gallery_images'] as $key => $all ){
                foreach( $all as $i => $val ){
                    $files[$i][$key] = $val;    
                }    
            }
            foreach ($files as $file) {
                // Get the type of the uploaded file. This is returned as "type/extension"
                $arr_file_type = wp_check_filetype(basename($file['name']));
                $uploaded_file_type = $arr_file_type['type'];

                // Set an array containing a list of acceptable formats
                $allowed_file_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png');

                // If the uploaded file is the right format
                if (in_array($uploaded_file_type, $allowed_file_types)) {

                    // Options array for the wp_handle_upload function. 'test_upload' => false
                    $upload_overrides = array('test_form' => false);

                    // Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
                    $uploaded_file = wp_handle_upload($file, $upload_overrides);
                    // If the wp_handle_upload call returned a local path for the image
                    if (isset($uploaded_file['file'])) {
                        // The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
                        $file_name_and_location = $uploaded_file['file'];

                        // Generate a title for the image that'll be used in the media library
                        $file_title_for_media_library = 'your title here';

                        // Set up options array to add this file as an attachment
                        $attachment = array(
                            'post_mime_type' => $uploaded_file_type,
                            'post_title' => 'Uploaded image ' . addslashes($file_title_for_media_library),
                            'post_content' => '',
                            'post_status' => 'inherit',
                            'post_parent' => $post_id,
                        );

                        // Run the wp_insert_attachment function. This adds the file to the media library and generates the thumbnails. If you wanted to attch this image to a post, you could pass the post id as a third param and it'd magically happen.
                        $attach_id = wp_insert_attachment($attachment, $file_name_and_location);
                        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attach_id, $file_name_and_location);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                    } else {
                        // wp_handle_upload returned some kind of error.
                    }
                } else {
                    // wrong file type
                }
            }
        }
    }
}