<?php
/**
 * Class RewriteTest
 *
 * @package Ibic
 */

/**
 * Test Rewrite
 */
class RewriteTest extends WP_UnitTestCase {

	/**
	 * Check that htaccess rewrite rules worked
	 */
	public function test_rewrite_rules() {
		// Remove existing htaccess file.
		$htaccess = get_home_path() . '.htaccess';
		if ( file_exists( $htaccess ) ) {
			unlink( $htaccess );
		}
		// Check that we don't find markers.
		list($htaccess_content, $pos_start, $pos_end) = $this->get_htaccess_and_markers();
		$this->assertFalse( $pos_start );
		$this->assertFalse( $pos_end );

		// Test if markers exist and the rewrite rules are added.
		ibic_mod_rewrite_write_rules();
		list($htaccess_content, $pos_start, $pos_end) = $this->get_htaccess_and_markers();
		$markers_found                                = false !== $pos_start && false !== $pos_end;
		$this->assertNotFalse( $markers_found );
		if ( $markers_found ) {
			$rewrite_cond_pos = strpos( $htaccess_content, 'RewriteCond', $pos_start );
			$this->assertTrue( false !== $rewrite_cond_pos && $rewrite_cond_pos > $pos_start && $pos_start < $pos_end );
		}

		// Test if markers exist and the rewrite rules have been removed.
		ibic_mod_rewrite_remove_rules();
		list($htaccess_content, $pos_start, $pos_end) = $this->get_htaccess_and_markers();
		$markers_found                                = false !== $pos_start && false !== $pos_end;
		$this->assertNotFalse( $markers_found );
		if ( $markers_found ) {
			$rewrite_cond_pos = strpos( $htaccess_content, 'RewriteCond', $pos_start );
			$this->assertFalse( $rewrite_cond_pos );
		}
	}

	/**
	 * Return htaccess content and position of IBIC markers
	 *
	 * @return array
	 */
	public function get_htaccess_and_markers() {
		$htaccess = get_home_path() . '.htaccess';
		if ( ! file_exists( $htaccess ) ) {
			return array( '', false, false );
		}
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$htaccess_content = file_get_contents( $htaccess );
		if ( ! $htaccess_content ) {
			return array( '', false, false );
		}
		$pos_start = strpos( $htaccess_content, '# BEGIN IBIC' );
		$pos_end   = strpos( $htaccess_content, '# END IBIC' );
		return array( $htaccess_content, $pos_start, $pos_end );
	}

}
