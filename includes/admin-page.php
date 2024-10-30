<?php
/**
 * Image compression admin page.
 *
 * @package Ibic
 */

/**
 * Register Image compression admin page.
 *
 * @return void
 */
function ibic_admin_page_register() {
	add_submenu_page(
		'upload.php',
		__( 'Image compression state', 'in-browser-image-compression' ),
		__( 'Image compression', 'in-browser-image-compression' ),
		'publish_posts',
		'ibic-image-compression',
		'ibic_admin_page_render'
	);
}

/**
 * Render Image compression admin page.
 *
 * @return void
 */
function ibic_admin_page_render() {
	?><div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Image compression', 'in-browser-image-compression' ); ?></h1>
		<hr class="wp-header-end">

		<p><?php esc_html_e( 'The compression occurs in the background in your browser, no action needed, you just need to be connected to the admin area.', 'in-browser-image-compression' ); ?> </p>
		<p><?php esc_html_e( 'Once the work is completed, the newly optimized files will be added to the uploads folder and they will be used automatically by your website.', 'in-browser-image-compression' ); ?> </p>
		<p><?php esc_html_e( 'You can check the image compression status below.', 'in-browser-image-compression' ); ?> </p>

		<h2 class="ibic-section__title wp-heading-inline"><?php esc_html_e( 'Latest', 'in-browser-image-compression' ); ?></h2>
		<div id="ibic-completion-placeholder" class="ibic-section__description completion-placeholder"></div>
		<div id="ibic-ui-placeholder" class="ibic-section__body ibic-placeholder"></div>

		<div id="ibic-ui-placeholder-errors-wrapper" style="display: none;">
			<h2 class="ibic-section__title wp-heading-inline"><?php esc_html_e( 'Errors', 'in-browser-image-compression' ); ?></h2>
			<div id="ibic-ui-placeholder-errors" class="ibic-section__body ibic-placeholder"></div>
		</div>
	</div>
	<?php
}

