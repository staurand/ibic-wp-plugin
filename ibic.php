<?php
/**
 * Plugin Name:     IBIC (In Browser Image Compression)
 * Plugin URI:      https://github.com/staurand/ibic-wp-plugin
 * Description:     Compress your images in your browser!
 * Author:          Stéphane Taurand
 * Text Domain:     ibic
 * Domain Path:     /languages
 * Version:         1.1.1
 * License:         GPLv2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package         Ibic
 */

$ibic_build_number = require __DIR__ . '/build-number.php';
define( 'IBIC_VERSION', '1.1.1~' . $ibic_build_number );
define( 'IBIC_ASSETS_PATH', plugin_dir_url( __FILE__ ) . 'assets/dist/' );

require_once __DIR__ . '/includes/helper.php';
require_once __DIR__ . '/includes/compatibility.php';
require_once __DIR__ . '/includes/lifecycle.php';
require_once __DIR__ . '/includes/assets.php';
require_once __DIR__ . '/includes/media.php';
require_once __DIR__ . '/includes/rewrite.php';
require_once __DIR__ . '/includes/admin-page.php';

// Add action on plugin activation / deactivation.
register_activation_hook( __FILE__, 'ibic_activation' );
register_deactivation_hook( __FILE__, 'ibic_deactivation' );

// Enqueue required assets on admin pages.
add_action( 'admin_enqueue_scripts', 'ibic_enqueue_admin_script' );
add_filter( 'script_loader_tag', 'ibic_admin_script_as_module', 10, 3 );

// Add media related hooks.
// # Ajax functions.
add_action( 'wp_ajax_ibic_get_media', 'ibic_ajax_get_media' );
add_action( 'wp_ajax_ibic_upload_compressed_media', 'ibic_upload_compressed_media' );
add_action( 'wp_ajax_ibic_reset_media', 'ibic_ajax_reset_media' );
add_action( 'wp_ajax_ibic_get_media_completion_status', 'ibic_ajax_media_completion_status' );
// # WP Hooks.
add_action( 'delete_attachment', 'ibic_media_on_delete_attachment', 10, 1 );
add_action( 'wp_update_attachment_metadata', 'ibic_media_reset_media_state_on_change', 10, 2 );

// Add compatibility related hooks.
add_action( 'admin_notices', 'ibic_compatibility_show_notice' );
add_filter( 'debug_information', 'ibic_compatibility_debug_information', 10, 1 );

// Add admin page
add_action('admin_menu', 'ibic_admin_page_register' );
