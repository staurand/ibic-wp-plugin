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
		?><div class="error notice is-dismissible"><p><?php esc_html_e( 'The IBIC plugin requires the Apache module "mod_rewrite" and https, please contact your administrator or hosting provider.', 'ibic' ); ?></p></div>
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
	$response     = wp_remote_get( IBIC_ASSETS_PATH . 'sw/codecs/mozjpeg/mozjpeg_enc.wasm', array( 'sslverify' => false ) );
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
 * Add debug information to the "Site Health Info" admin page
 *
 * @param array $info Information passed by `debug_information` hook.
 * @see https://developer.wordpress.org/reference/hooks/debug_information/
 * @return mixed
 */
function ibic_compatibility_debug_information( $info ) {
	$wasm_mime_type_check = ibic_compatibility_wasm_mime_type_check();
	$info['ibic']         = array(
		'label'  => __( 'IBIC plugin', 'ibic' ),
		'fields' => array(
			'ibic-mod-rewrite'    => array(
				'label' => __( 'The server supports URL rewriting', 'ibic' ),
				'value' => got_url_rewrite() ? __( 'Yes', 'ibic' ) : __( 'No', 'ibic' ),
			),
			'ibic-ssl'            => array(
				'label' => __( 'The site is secured (https enabled)', 'ibic' ),
				'value' => is_ssl() ? __( 'Yes', 'ibic' ) : __( 'No', 'ibic' ),
			),
			'ibic-wasm-mime-type' => array(
				'label' => __( 'Wasm file mime type', 'ibic' ),
				'value' => $wasm_mime_type_check['success'] ? __( 'Correct', 'ibic' ) : __( 'Wrong', 'ibic' ),
				'debug' => $wasm_mime_type_check['content_type'],
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
