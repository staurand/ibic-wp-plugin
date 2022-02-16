<?php
/**
 * Class MediaTest
 *
 * @package Ibic
 */

/**
 * Test Media
 */
class MediaTest extends WP_UnitTestCase {

	/**
	 * Test function `ibic_get_media_to_process`
	 */
	public function test_media_to_process() {
		// Check that we don't have media to process.
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 0 );

		// We upload a new image to the library and then check that we have one media to process.
		ibic_test_helper_upload_media_to_library( __DIR__ . '/assets/test.png' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 1 );

		// We upload a file type not managed by IBIC plugin then we check that we still have one media to process.
		ibic_test_helper_upload_media_to_library( __DIR__ . '/assets/Lorem ipsum.pdf' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 1 );

		// We upload a new image to the library and then check that we have two medias to process.
		ibic_test_helper_upload_media_to_library( __DIR__ . '/assets/test.png' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 2 );

		// Mark the uploaded media as processed and check that we don't have media to process anymore.
		update_post_meta( $medias[0]['id'], '_ibic_processed', '1' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 1 );
	}

	/**
	 * Test hooks on medias
	 */
	public function test_media_to_hooks() {
		// Check that we don't have media to process.
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 0 );

		// We upload a new image to the library and then check that we have one media to process.
		ibic_test_helper_upload_media_to_library( __DIR__ . '/assets/test.png' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 1 );

		$media_id = $medias[0]['id'];

		// Test action `delete_attachment`.
		$optimised_media_path = $this->fake_processed_media( $media_id );
		do_action( 'delete_attachment', $media_id );
		// Check that the optimised file has been deleted.
		$this->assertTrue( ! is_file( $optimised_media_path ) );
		// Check that we don't have post meta anymore.
		$this->assertTrue( get_post_meta( $media_id, '_ibic_processed', true ) === '' );
		$this->assertTrue( get_post_meta( $media_id, '_ibic_error', true ) === '' );

		// Test action `wp_update_attachment_metadata`.
		$optimised_media_path = $this->fake_processed_media( $media_id );
		do_action( 'wp_update_attachment_metadata', array(), $media_id );
		// Check that we don't have post meta anymore.
		$this->assertTrue( get_post_meta( $media_id, '_ibic_processed', true ) === '' );
		$this->assertTrue( get_post_meta( $media_id, '_ibic_error', true ) === '' );
	}

	/**
	 * Fake processed media
	 *
	 * @param int $media_id The post ID.
	 *
	 * @return string|string[]
	 */
	public function fake_processed_media( $media_id ) {
		$media_path           = get_attached_file( $media_id );
		$optimised_media_path = str_replace( '.png', '.png-ibic.webp', $media_path );
		file_put_contents( $optimised_media_path, file_get_contents( __DIR__ . '/assets/test.png-ibic.webp' ) );
		$this->assertTrue( is_file( $optimised_media_path ) );
		update_post_meta( $media_id, '_ibic_processed', '1' );
		update_post_meta( $media_id, '_ibic_error', 'error' );

		return $optimised_media_path;
	}

}
