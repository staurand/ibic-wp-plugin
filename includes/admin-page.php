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
		<h1 class="wp-heading-inline"><?php esc_html_e('Image compression state', 'ibic'); ?></h1>
		<hr class="wp-header-end">
		<div id="ibic-ui-placeholder" class="ibic-placeholder"></div>
	</div><?php
}

