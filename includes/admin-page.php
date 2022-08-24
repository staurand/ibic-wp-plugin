<?php

function ibic_admin_page_register() {
	add_submenu_page(
		'upload.php',
		__('Image compression state', 'ibic'),
		__('Image compression', 'ibic'),
		'publish_posts',
		'ibic-image-compression',
		'ibic_admin_page_render'
	);
}

function ibic_admin_page_render() {
	?><div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e('Image compression', 'ibic'); ?></h1>
		<hr class="wp-header-end">

		<p><?php esc_html_e('The compression occurs in the background in your browser, no action needed, you just need to be connected to the admin area.', 'ibic'); ?> </p>
		<p><?php esc_html_e('Once the work is completed, the newly optimized files will be added to the uploads folder and they will be used automatically by your website.', 'ibic'); ?> </p>
		<p><?php esc_html_e('You can check the image compression status below.', 'ibic'); ?> </p>

		<h2 class="wp-heading-inline"><?php esc_html_e('Status', 'ibic'); ?></h2>
		<div id="ibic-completion-placeholder" class="completion-placeholder"></div>
		<div id="ibic-ui-placeholder" class="ibic-placeholder"></div>
	</div><?php
}

