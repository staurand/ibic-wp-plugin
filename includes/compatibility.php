<?php
/**
 * IBIC Compatibility checks & information
 *
 * @package Ibic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Check if the server is compatible
 *
 * @return bool
 */
function ibic_compatibility_check() {
	// IBIC requires URL rewriting.
	if ( ! got_url_rewrite() ) {
		return false;
	}
	// SSL is required for the service worker to work.
	if ( ! is_ssl() ) {
		return false;
	}
	return true;
}

/**
 * Check if the server is compatible, if it's not display a notice.
 */
function ibic_compatibility_activation_check() {
	if ( ! ibic_compatibility_check() ) {
		set_transient( 'ibic_compatibility_admin_notice', true, 5 );
	}
}

/**
 * Show incompatibility notice
 */
function ibic_compatibility_show_notice() {
	if ( get_transient( 'ibic_compatibility_admin_notice' ) ) {
		?><div class="error notice is-dismissible"><p><?php esc_html_e( 'The IBIC plugin requires the Apache module "mod_rewrite" and https, please contact your administrator or hosting provider.', 'in-browser-image-compression' ); ?></p></div>
		<?php
		delete_transient( 'ibic_compatibility_admin_notice' );
	}
}

/**
 * Check that wasm file on the server has the right mime tyoe
 *
 * @return array
 */
function ibic_compatibility_wasm_mime_type_check() {
	$response     = wp_remote_get( IBIC_ASSETS_URL . 'sw/codecs/mozjpeg/mozjpeg_enc.wasm', array( 'sslverify' => false ) );
	$success      = false;
	$content_type = null;
	if ( ! is_wp_error( $response ) ) {
		$content_type = $response['headers']['content-type'];
		$success      = 'application/wasm' === $content_type;
	}

	return array(
		'success'      => $success,
		'content_type' => $content_type,
	);
}

/**
 * Check if we can retrieve information about "max_file_uploads" config.
 * If we can check if the value is greater or equal to 2 (for each media we have at least two files to send jpg/png and webp).
 *
 * @return array
 */
function ibic_compatibility_max_file_uploads_check() {
	$max_file_uploads = ini_get( 'max_file_uploads' );
	if ( is_numeric( $max_file_uploads ) ) {
		$max_file_uploads_int = (int) $max_file_uploads;
	} else {
		$max_file_uploads_int = 0;
	}
	return array(
		'success'          => $max_file_uploads_int >= 2,
		'max_file_uploads' => $max_file_uploads,
	);
}


/**
 * Add debug information to the "Site Health Info" admin page
 *
 * @param array $info Information passed by `debug_information` hook.
 * @see https://developer.wordpress.org/reference/hooks/debug_information/
 * @return mixed
 */
function ibic_compatibility_debug_information( $info ) {
	$wasm_mime_type_check                 = ibic_compatibility_wasm_mime_type_check();
	$max_file_uploads_check               = ibic_compatibility_max_file_uploads_check();
	$info['in-browser-image-compression'] = array(
		'label'  => __( 'IBIC plugin', 'in-browser-image-compression' ),
		'fields' => array(
			'ibic-mod-rewrite'      => array(
				'label' => __( 'The server supports URL rewriting', 'in-browser-image-compression' ),
				'value' => got_url_rewrite() ? __( 'Yes', 'in-browser-image-compression' ) : __( 'No', 'in-browser-image-compression' ),
			),
			'ibic-ssl'              => array(
				'label' => __( 'The site is secured (https enabled)', 'in-browser-image-compression' ),
				'value' => is_ssl() ? __( 'Yes', 'in-browser-image-compression' ) : __( 'No', 'in-browser-image-compression' ),
			),
			'ibic-wasm-mime-type'   => array(
				'label' => __( 'Wasm file mime type', 'in-browser-image-compression' ),
				'value' => $wasm_mime_type_check['success'] ? __( 'Correct', 'in-browser-image-compression' ) : __( 'Wrong', 'in-browser-image-compression' ),
				'debug' => $wasm_mime_type_check['content_type'],
			),
			'ibic-max-file-uploads' => array(
				'label' => __( '"max_file_uploads" PHP config', 'in-browser-image-compression' ),
				'value' => $max_file_uploads_check['success'] ? __( 'Correct', 'in-browser-image-compression' ) : __( 'Wrong', 'in-browser-image-compression' ),
				'debug' => $max_file_uploads_check['max_file_uploads'],
			),
		),
	);
	return $info;
}

/**
 * Enable webp image upload, required for WP < 5.8
 *
 * @param string[] $existing_mimes Mime types keyed by the file extension regex corresponding to those types.
 * @return mixed
 */
function ibic_enable_webp_image_upload( $existing_mimes ) {
	if ( ! isset( $existing_mimes['webp'] ) ) {
		$existing_mimes['webp'] = 'image/webp';
	}
	return $existing_mimes;
}
add_filter( 'mime_types', 'ibic_enable_webp_image_upload' );
