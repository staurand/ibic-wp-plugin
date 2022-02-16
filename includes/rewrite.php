<?php
/**
 * IBIC Rewrite functions
 *
 * @package Ibic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Insert rewrite rules in the .htacces file
 *
 * @return bool
 */
function ibic_mod_rewrite_write_rules() {
	$htaccess = get_home_path() . '.htaccess';
	$result   = insert_with_markers( $htaccess, 'IBIC', ibic_mod_rewrite_rules() );
	return $result;
}

/**
 * Remove rewrite rules from the .htacces file
 */
function ibic_mod_rewrite_remove_rules() {
	$htaccess = get_home_path() . '.htaccess';
	insert_with_markers( $htaccess, 'IBIC', array() );
}

/**
 * Return rewrite rules as array (one entry per line)
 *
 * @return array
 */
function ibic_mod_rewrite_rules() {
	$rules = array();

	// Set Service-Worker-Allowed for the service worker to work on all admin pages.
	$rules [] = '<IfModule mod_headers.c>';
	$rules [] = 'Header set Service-Worker-Allowed "/wp-admin"';
	$rules [] = '</IfModule>';

	// Add wasm file type.
	$rules [] = '<IfModule mod_mime.c>';
	$rules [] = 'AddType application/wasm .wasm';
	$rules [] = '</IfModule>';

	// Rewrite original images path to optimized ones.
	$rules [] = '<IfModule mod_rewrite.c>';
	$rules [] = 'RewriteEngine On';

	// Check first if the browser supports WebP image and if we have one.
	$rules [] = 'RewriteCond %{REQUEST_FILENAME} -f';
	// Check if browser supports WebP images.
	$rules [] = 'RewriteCond %{HTTP_ACCEPT} image/webp';
	// Check if WebP replacement image exists.
	$rules [] = 'RewriteCond %{REQUEST_FILENAME}-ibic.webp -f';
	$rules [] = 'RewriteRule ^ %{REQUEST_FILENAME}-ibic.webp [L]';

	// Then check if we have an optimized jpg to serve.
	$rules [] = 'RewriteCond %{REQUEST_FILENAME} -f';
	$rules [] = 'RewriteCond %{REQUEST_FILENAME}-ibic.jpg -f';
	$rules [] = 'RewriteRule ^ %{REQUEST_FILENAME}-ibic.jpg [L]';

	// Then check if we have an optimized png to serve.
	$rules [] = 'RewriteCond %{REQUEST_FILENAME} -f';
	$rules [] = 'RewriteCond %{REQUEST_FILENAME}-ibic.png -f';
	$rules [] = 'RewriteRule ^ %{REQUEST_FILENAME}-ibic.png [L]';
	$rules [] = '</IfModule>';
	return $rules;
}
