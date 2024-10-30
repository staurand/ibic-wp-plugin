<?php
/**
 * IBIC Media management (list, upload, reset, delete)
 *
 * @package Ibic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once __DIR__ . '/helper.php';

/**
 * Get media to process as WP_Query.
 *
 * @param string $filter Filter the query with a prebuilt filter: "TO_PROCESS" OR "WITH_ERROR".
 *
 * @return WP_Query
 */
function ibic_get_media_to_process_as_wp_query( $filter = 'TO_PROCESS' ) {
	$query_params = array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'post_mime_type' => array( 'image/jpeg', 'image/png' ),
		'author'         => get_current_user_id(),
		'posts_per_page' => 10,
		// @TODO: create new table in DB and save media processed? (maybe a good way to store compression rate...).
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'meta_query'     => array(
			'relation' => 'OR',
			array(
				'key'   => '_ibic_processed',
				'value' => '0',
			),
			array(
				'key'     => '_ibic_processed',
				'compare' => 'NOT EXISTS',
			),
		),
	);
	if ( 'WITH_ERROR' === $filter ) {
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		$query_params['meta_query'] = array(
			array(
				'key'   => '_ibic_processed',
				'value' => '1',
			),
			array(
				'key'     => '_ibic_error',
				'compare' => 'EXISTS',
			),
		);
	}

	return new WP_Query( $query_params );
}
/**
 * Return a list of media to process filtered by type (jpg/png) and only for the current logged-in user (for security reason we don't want to process media uploaded by other users)
 *
 * @return array[]
 */
function ibic_get_media_to_process() {
	$media = ibic_get_media_to_process_as_wp_query();
	return array_map(
		function ( $medium ) {
			return array(
				'id'   => $medium->ID,
				'name' => $medium->post_title,
				'urls' => ibic_get_media_urls( $medium->ID ),
			);
		},
		$media->posts
	);
}
/**
 * Return a list of media with errors.
 *
 * @return array[]
 */
function ibic_get_media_with_error() {
	$media = ibic_get_media_to_process_as_wp_query( 'WITH_ERROR' );
	return array_map(
		function ( $medium ) {
			return array(
				'id'     => $medium->ID,
				'name'   => $medium->post_title,
				'urls'   => ibic_get_media_urls( $medium->ID ),
				'error'  => true,
				'errors' => get_post_meta( $medium->ID, '_ibic_error', true ),
			);
		},
		$media->posts
	);
}

/**
 * Return all URLs for a media
 *
 * @param int $post_id The post ID.
 *
 * @return array
 */
function ibic_get_media_urls( $post_id ) {
	static $image_sizes;
	if ( ! isset( $image_sizes ) ) {
		$image_sizes = get_intermediate_image_sizes();
	}

	$urls       = array();
	$medium_url = wp_get_attachment_url( $post_id );
	$urls[]     = $medium_url;
	foreach ( $image_sizes as $image_size ) {
		$image_url  = wp_get_attachment_image_url( $post_id, $image_size );
		$image_path = ibic_media_url_to_path( $image_url );
		if ( is_file( $image_path ) ) {
			$urls[] = $image_url;
		}
	}
	$urls = array_unique( $urls );
	$urls = array_values( $urls ); // reset keys.

	return $urls;
}

/**
 * Ajax entry point to retrieve the media list to process
 */
function ibic_ajax_get_media() {
	if ( ! ibic_current_user_can_compress_media() ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$filter = isset( $_GET['filter'] ) ? sanitize_text_field( wp_unslash( $_GET['filter'] ) ) : '';
	if ( ! in_array( $filter, array( 'TO_PROCESS', 'WITH_ERROR' ), true ) ) {
		$filter = 'TO_PROCESS';
	}
	switch ( $filter ) {
		case 'WITH_ERROR':
			$media = ibic_get_media_with_error();
			break;
		default:
			$media = ibic_get_media_to_process();
			break;
	}
	wp_send_json( $media, 200 );
}

/**
 * Set the media "_ibic_processed" meta to 1 so it will be excluded from the list and store the error.
 * Send json error response.
 *
 * @param string $error The upload error.
 * @param int    $media_id The post ID.
 * @param int    $http_code HTTP code.
 */
function ibic_upload_compressed_media_failed( $error, $media_id = 0, $http_code = 200 ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'IBIC: ' . $error );
	}
	if ( $media_id > 0 ) {
		update_post_meta( $media_id, '_ibic_processed', '1' );
		update_post_meta( $media_id, '_ibic_error', $error );
	}
	wp_send_json_error( null, $http_code );
}
/**
 * Upload compressed media
 * Expected request:
 * $_POST['id'] int => the attachment id
 * $_POST['urls'] array => a list of files linked to this attachment
 * $_POST['error'] string => error message if any
 * $_FILES['media'][...][0...n] where 0...n is the index of the compressed media in the urls list
 * $_FILES['media'][name][0...n][~file_format~] string => where ~file_format~ is the optimised file format name (e.g "png", "webp")
 * $_FILES['media'][tmp_name][0...n][~file_format~] string => where the value is the optimised file temp path (e.g "/tmp/php8ImL0k")
 * $_FILES['media'][size][0...n][~file_format~] string => where the value is the optimised file size in bytes
 */
function ibic_upload_compressed_media() {
	if ( ! ibic_current_user_can_compress_media() || ! check_ajax_referer( 'ibic_upload_compressed_media', false, false ) ) {
		wp_die();
	}

	$post_id    = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
	$medium_url = wp_get_attachment_url( $post_id );
	if ( ! $medium_url ) {
		ibic_upload_compressed_media_failed( __( 'Trying to update non existing media', 'ibic' ) );
	}

	if ( isset( $_POST['error'] ) ) {
		$error = sanitize_text_field( wp_unslash( $_POST['error'] ) );
		ibic_upload_compressed_media_failed( $error, $post_id );
	}

	if ( empty( $_POST ) && empty( $_FILES ) && ! empty( $_SERVER['CONTENT_LENGTH'] ) && (int) $_SERVER['CONTENT_LENGTH'] > wp_max_upload_size() ) {
		ibic_upload_compressed_media_failed( __( 'The uploaded file exceeds the server max upload size.', 'ibic' ), 0, 413 );
	}

	if ( ! isset( $_POST['id'] ) || ! isset( $_POST['urls'] ) || ! is_array( $_POST['urls'] ) ) {
		ibic_upload_compressed_media_failed( __( 'Parameters are missing to update the media', 'ibic' ), 0, 400 );
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	$files = $_FILES;

	$original_urls = ibic_get_media_urls( $post_id );

	$urls = array_map( 'esc_url_raw', wp_unslash( $_POST['urls'] ) );
	foreach ( $urls  as $index => $url ) {
		if ( ! in_array( $url, $original_urls, true ) ) {
			continue;
		}
		$original_file_path = ibic_media_url_to_path( $url );
		$medium_dir         = trailingslashit( dirname( $original_file_path ) );
		$medium_filename    = basename( $url );

		foreach ( array_keys( $files['media']['name'][ $index ] ) as $file_format ) {
			$new_file_name = sanitize_file_name( $medium_filename . '-ibic.' . $file_format );
			$filepath      = $medium_dir . $new_file_name;
			$wp_filetype   = wp_check_filetype( $medium_filename );
			if ( ! $wp_filetype['ext'] && ! current_user_can( 'unfiltered_upload' ) ) {
				continue;
			}

			$file_size = filesize( $original_file_path );
			$new_size  = $files['media']['size'][ $index ][ $file_format ];
			if ( false === $file_size || $file_size <= $new_size ) {
				continue;
			}

			$tmp_file_path = $files['media']['tmp_name'][ $index ][ $file_format ];
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$bits = file_get_contents( $tmp_file_path );

			if ( is_file( $filepath ) ) {
				wp_delete_file( $filepath );
			}

			$upload_result = wp_upload_bits( basename( $filepath ), null, $bits, get_the_date( 'Y/m', $post_id ) );
			if ( $upload_result['error'] ) {
				/* translators: %s: file path */
				ibic_upload_compressed_media_failed( sprintf( __( 'Could not write file %s', 'ibic' ), $filepath ), $post_id );
			}
		}
	}

	// If there is no partial param, assume the service worker is not up-to-date and it's a request will all optimised medias.
	if ( ! isset( $_POST['partial'] ) || '1' === $_POST['partial'] ) {
		update_post_meta( $post_id, '_ibic_processed', '1' );
	}
	delete_post_meta( $post_id, '_ibic_error' );

	wp_send_json_success();
}

/**
 * Reset media state
 *
 * @param int $post_id The post ID.
 */
function ibic_media_reset_media_state( $post_id ) {
	delete_post_meta( $post_id, '_ibic_error' );
	delete_post_meta( $post_id, '_ibic_processed' );
}

/**
 * Remove optimised files and post meta when the media is removed
 *
 * @param int $post_id The post ID.
 */
function ibic_media_on_delete_attachment( $post_id ) {
	$urls = ibic_get_media_urls( $post_id );
	// add media guid just in case the image has been edited in wp.
	$post              = get_post( $post_id );
	$urls []           = $post->guid;
	$optimised_formats = array( 'png', 'jpg', 'webp' );
	foreach ( $urls as $url ) {
		$filepath = ibic_media_url_to_path( $url );
		foreach ( $optimised_formats as $optimised_format ) {
			if ( is_file( $filepath . '-ibic.' . $optimised_format ) ) {
				wp_delete_file( $filepath . '-ibic.' . $optimised_format );
			}
		}
	}
	ibic_media_reset_media_state( $post_id );
}

/**
 * If the media metadata has changed assume the image could have changed
 *
 * @param array $data          Array of updated attachment meta data.
 * @param int   $post_id The post ID.
 * @return array $data
 */
function ibic_media_reset_media_state_on_change( $data, $post_id ) {
	ibic_media_reset_media_state( $post_id );
	return $data;
}

/**
 * Ajax entry point to reset media state
 */
function ibic_ajax_reset_media() {
	if ( ! ibic_current_user_can_compress_media() || ! check_ajax_referer( 'ibic_reset_media', false, false ) ) {
		wp_die();
	}

	if ( ! isset( $_POST['id'] ) ) {
		wp_send_json_error();
	}

	$post_id    = intval( $_POST['id'] );
	$medium_url = wp_get_attachment_url( $post_id );
	if ( ! $medium_url ) {
		wp_send_json_error();
	}

	ibic_media_reset_media_state( $post_id );

	wp_send_json_success();
}

/**
 * Ajax function to return the current media completion status.
 *
 * @return void
 */
function ibic_ajax_media_completion_status() {
	$to_be_processed = ibic_get_media_to_process_as_wp_query();
	$count           = $to_be_processed->found_posts;
	if ( $count > 0 ) {
		echo esc_html(
			sprintf(
				/* Translators: %1$d number of images */
				_n( '%1$d image to be processed', '%1$d images to be processed', $count, 'ibic' ),
				$count
			)
		);
	}
	wp_die();
}
