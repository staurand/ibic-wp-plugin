<?php
/**
 * IBIC Assets management
 *
 * @package Ibic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Enqueue admin scripts and css
 */
function ibic_enqueue_admin_script() {
	if ( ibic_current_user_can_compress_media() ) {
		$ibic_admin_config = wp_json_encode(
			array(
				'assets_path'     => IBIC_ASSETS_PATH,
				'sw_config'       => array(
					'codecs_path'      => IBIC_ASSETS_PATH . 'sw/codecs/',
					'image_list_url'   => add_query_arg( '_wpnonce', wp_create_nonce( 'ibic_get_media' ), admin_url( 'admin-ajax.php?action=ibic_get_media' ) ),
					'image_upload_url' => add_query_arg( '_wpnonce', wp_create_nonce( 'ibic_upload_compressed_media' ), admin_url( 'admin-ajax.php?action=ibic_upload_compressed_media' ) ),
				),
				'image_reset_url' => add_query_arg( '_wpnonce', wp_create_nonce( 'ibic_reset_media' ), admin_url( 'admin-ajax.php?action=ibic_reset_media' ) ),
			)
		);
		wp_register_script( 'ibic-admin-config-js', '', array(), IBIC_VERSION, true );
		wp_enqueue_script( 'ibic-admin-config-js' );
		wp_add_inline_script(
			'ibic-admin-config-js',
			<<<SCRIPT
window.IBIC_ADMIN_CONFIG = $ibic_admin_config;
SCRIPT
			,
			'before'
		);

		$ibic_admin_deps = array( 'jquery', 'ibic-admin-config-js', 'wp-i18n' );
		$screen = get_current_screen();
		if ($screen->id === 'media_page_ibic-image-compression') {
			wp_enqueue_style( 'ibic-admin-ui-css', IBIC_ASSETS_PATH . 'ui/ui.css', null, IBIC_VERSION );
			wp_enqueue_script( 'ibic-admin-ui-js', IBIC_ASSETS_PATH . 'ui/ui.js', array(), IBIC_VERSION, true );
			$ibic_admin_deps[]='ibic-admin-ui-js';
		}
		wp_enqueue_script( 'ibic-admin-js', IBIC_ASSETS_PATH . 'ibic-admin.js', $ibic_admin_deps, IBIC_VERSION, true );
	}
}

/**
 * Add 'type="module"' to the ibic-admin.js script tag
 *
 * @param string $tag    The `<script>` tag for the enqueued script.
 * @param string $handle The script's registered handle.
 * @param string $src    The script's source URL.
 *
 * @return string
 */
function ibic_admin_script_as_module( $tag, $handle, $src ) {
	if ( 'ibic-admin-js' !== $handle ) {
		return $tag;
	}
	// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
	$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
	return $tag;
}
