<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Ibic
 */

$ibic_tests_dir = getenv( 'WP_TESTS_DIR' );

require_once __DIR__ . '/../vendor/autoload.php';

if ( ! $ibic_tests_dir ) {
	$ibic_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $ibic_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $ibic_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $ibic_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function ibic_test_manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/ibic.php';
}
tests_add_filter( 'muplugins_loaded', 'ibic_test_manually_load_plugin' );

require_once __DIR__ . '/helper.php';

// Start up the WP testing environment.
require $ibic_tests_dir . '/includes/bootstrap.php';
