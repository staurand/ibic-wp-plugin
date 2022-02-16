<?php
/**
 * IBIC Helpers for tests
 *
 * @package Ibic
 */

/**
 * Upload a media to the library
 *
 * @param string $file_path The media file path.
 */
function ibic_test_helper_upload_media_to_library( $file_path ) {
	$filename = basename( $file_path );
	$ext_type = wp_check_filetype( $file_path );
	$temp     = tempnam( sys_get_temp_dir(), 'ibic' );
	file_put_contents( $temp, file_get_contents( $file_path ) );

	$controller = new WP_REST_Attachments_Controller( 'attachment' );
	$request    = new WP_REST_Request( '', '' );
	$request->set_file_params(
		array(
			'file' => array(
				'tmp_name' => $temp,
				'name'     => $filename,
				'type'     => $ext_type['type'],
				'size'     => filesize( $file_path ),
			),
		)
	);
	$controller->create_item( $request );
}
