<?php
/**
 * IBIC Assets management
 *
 * @package Ibic
 */

/**
 * Function called when the plugin is activated.
 * Write rewrite rules to .htaccess file and check plugin compatibility
 */
function ibic_activation() {
	ibic_mod_rewrite_write_rules();
	ibic_compatibility_activation_check();
}

/**
 * Function called when the plugin is deactivated.
 * Remove rewrite rules from the .htaccess file.
 */
function ibic_deactivation() {
	ibic_mod_rewrite_remove_rules();
}
