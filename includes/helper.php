<?php
/**
 * IBIC Helpers
 *
 * @package Ibic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Return true if the current logged-in user can compress media
 *
 * @return bool
 */
function ibic_current_user_can_compress_media() {
	return current_user_can( 'publish_posts' );
}

/**
 * Replace base url by base dir in the url
 *
 * @param string $url The URL to transform.
 * @return string
 */
function ibic_media_url_to_path( $url ) {
	$upload_dir = wp_get_upload_dir();
	return str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $url );
}
