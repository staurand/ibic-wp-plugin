<?php
/**
 * Uninstall the plugin. This file is included when the user delete completely the plugin.
 *
 * @package Ibic
 */

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}
require_once __DIR__ . '/includes/media.php';

/**
 * Remove plugin's content from the database
 */
function ibic_uninstall_clean_up() {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	$posts_id = $wpdb->get_results( "SELECT p.ID FROM {$wpdb->posts} p JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = '_ibic_processed';" );
	foreach ( $posts_id as $post ) {
		ibic_media_on_delete_attachment( $post->ID );
	}
}

ibic_uninstall_clean_up();

