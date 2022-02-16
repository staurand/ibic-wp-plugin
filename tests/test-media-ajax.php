<?php
/**
 * Class MediaAjaxTest
 *
 * @package Ibic
 */

/**
 * Test Media Ajax
 */
class MediaAjaxTest extends WP_Ajax_UnitTestCase {

	/**
	 * Test `ibic_upload_compressed_media` ajax function with a subscriber user (does not have the right permissions)
	 */
	public function test_media_ajax_upload_compressed_media_with_subscriber() {
		$this->_setRole( 'subscriber' );

		// Check that we don't have media to process.
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 0 );

		// Upload an image to the media library.
		ibic_test_helper_upload_media_to_library( __DIR__ . '/assets/test.png' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 1 );

		// Test `ibic_upload_compressed_media` ajax.
		$media_id    = $medias[0]['id'];
		$media_path  = get_attached_file( $media_id );
		$_POST['id'] = $media_id;
		// limit urls to one.
		$_POST['urls'] = array( wp_get_attachment_url( $media_id ) );

		$temp         = tempnam( sys_get_temp_dir(), 'ibic' );
		$temp_content = file_get_contents( __DIR__ . '/assets/test.png-ibic.webp' );
		file_put_contents( $temp, $temp_content );
		$_FILES = $this->buildFILES(
			array(
				'webp' => __DIR__ . '/assets/test.png-ibic.webp',
			)
		);

		// Try to process the media with user that does not have the right permissions.
		$_REQUEST['_wpnonce'] = wp_create_nonce( 'ibic_upload_compressed_media' );
		try {
			$this->_handleAjax( 'ibic_upload_compressed_media' );
		} catch ( WPAjaxDieStopException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// We check below if an exception has been thrown.
		}

		$this->assertTrue( isset( $e ) );
		$this->assertTrue( '' === $this->_last_response );

		$medias = ibic_get_media_to_process();
		$this->assertTrue( 1 === count( $medias ) );
	}

	/**
	 * Test `ibic_upload_compressed_media` ajax function with an editor user (does have the right permissions)
	 */
	public function test_media_ajax_upload_compressed_media_with_editor() {
		$this->_setRole( 'editor' );

		// Check that we don't have media to process.
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 0 );

		// Upload an image to the media library.
		ibic_test_helper_upload_media_to_library( __DIR__ . '/assets/test.png' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( count( $medias ) === 1 );

		// Test `ibic_upload_compressed_media` ajax.
		$media_id    = $medias[0]['id'];
		$media_path  = get_attached_file( $media_id );
		$_POST['id'] = $media_id;
		// limit urls to one.
		$_POST['urls'] = array( wp_get_attachment_url( $media_id ) );
		$_FILES        = $this->buildFILES(
			array(
				'webp' => __DIR__ . '/assets/test.png-ibic.webp',
			)
		);

		// Try to process the media with another user role that should have the right permissions.
		$_POST['_wpnonce'] = wp_create_nonce( 'ibic_upload_compressed_media' );
		try {
			$this->_handleAjax( 'ibic_upload_compressed_media' );
		} catch ( WPAjaxDieContinueException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// We check below if an exception has been thrown.
		}

		$this->assertTrue( isset( $e ) );
		$json = json_decode( $this->_last_response, true );
		$this->assertTrue( true === $json['success'] );
		$this->_last_response = '';
		// Check that the media has been marked as processed.
		$medias = ibic_get_media_to_process();
		$this->assertTrue( 0 === count( $medias ) );

		// Check that the optimised file has been uploaded.
		$optimised_media_path = str_replace( '.png', '.png-ibic.webp', $media_path );
		$this->assertTrue( is_file( $optimised_media_path ) );

		// Try to send error.
		$error_sent     = 'test error';
		$_POST['error'] = $error_sent;
		try {
			$this->_handleAjax( 'ibic_upload_compressed_media' );
		} catch ( WPAjaxDieContinueException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// We check below if an exception has been thrown.
		}
		$json = json_decode( $this->_last_response, true );
		$this->assertTrue( false === $json['success'] );

		$error_saved = get_post_meta( $media_id, '_ibic_error', true );
		$this->assertTrue( $error_saved === $error_sent );
	}

	/**
	 * Test `ibic_reset_media` ajax function with a subscriber & an editor user
	 */
	public function test_media_ajax_reset_media_state() {
		$this->_setRole( 'subscriber' );
		// Upload an image to the media library.
		ibic_test_helper_upload_media_to_library( __DIR__ . '/assets/test.png' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( 1 === count( $medias ) );
		$media_id = $medias[0]['id'];

		// Mark media as processed manually.
		update_post_meta( $media_id, '_ibic_processed', '1' );
		delete_post_meta( $media_id, '_ibic_error' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( 0 === count( $medias ) );

		// Try to reset the media state with user that does not have the right permissions.
		$_REQUEST['_wpnonce'] = wp_create_nonce( 'ibic_reset_media' );
		$_POST['id']          = $media_id;
		try {
			$this->_handleAjax( 'ibic_reset_media' );
		} catch ( WPAjaxDieStopException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// We check below if an exception has been thrown.
		}

		$this->assertTrue( isset( $e ) );
		$this->assertTrue( '' === $this->_last_response );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( 0 === count( $medias ) );

		// Switch to editor role.
		$this->_setRole( 'editor' );
		// Upload an image to the media library.
		ibic_test_helper_upload_media_to_library( __DIR__ . '/assets/test.png' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( 1 === count( $medias ) );
		$media_id    = $medias[0]['id'];
		$_POST['id'] = $media_id;

		// Mark media as processed manually.
		update_post_meta( $media_id, '_ibic_processed', '1' );
		delete_post_meta( $media_id, '_ibic_error' );
		$medias = ibic_get_media_to_process();
		$this->assertTrue( 0 === count( $medias ) );

		// Try to reset the media state with another user role that should have the right permissions.
		$_POST['_wpnonce'] = wp_create_nonce( 'ibic_reset_media' );
		try {
			$this->_handleAjax( 'ibic_reset_media' );
		} catch ( WPAjaxDieContinueException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// We check below if an exception has been thrown.
		}

		$this->assertTrue( isset( $e ) );
		$json = json_decode( $this->_last_response, true );
		$this->assertTrue( true === $json['success'] );
		$this->_last_response = '';
		// Check that the media is available to process again.
		$medias = ibic_get_media_to_process();
		$this->assertTrue( 1 === count( $medias ) );
	}

	/**
	 * Helper to build the global $_FILES array
	 *
	 * @param array $files_path The files path to upload.
	 *
	 * @return array[][]
	 */
	public function buildFILES( $files_path ) {
		$files = array(
			'media' => array(
				'name'     => array(),
				'size'     => array(),
				'tmp_name' => array(),
			),
		);
		foreach ( $files_path as $ext => $file_path ) {
			$temp         = tempnam( sys_get_temp_dir(), 'ibic' );
			$temp_content = file_get_contents( $file_path );
			file_put_contents( $temp, $temp_content );
			$files['media']['name'][0][ $ext ]     = $temp_content;
			$files['media']['size'][0][ $ext ]     = filesize( $temp );
			$files['media']['tmp_name'][0][ $ext ] = $temp;
		}
		return $files;
	}

}
