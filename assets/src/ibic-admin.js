import { initSw } from './sw/sw-init.js';
//import { renderUI, renderErrorFactory } from './ui.js';
const $ = window.jQuery;
const config = window.IBIC_ADMIN_CONFIG;
const i18n = window.wp.i18n;

const renderErrorFactory = ({ $ }) => {
	return (error) => {
		const $ibicNotice = $('.ibic-notice');
		if ($ibicNotice.length > 0) {
			$ibicNotice.remove();
		}
		$('#wp-media-grid').before(
			$('<div class="ibic-notice notice notice-error inline">').append($('<p></p>').text(error))
		);
	};
}
const renderError = renderErrorFactory({ $ });

const init = function ({ sendMessage, eventHandler, update }) {
	const isUpdatePage = location.href.match(/\/(plugins|update-core).php(\?.*)?$/);

	// If we are on the plugins or core-update page try to update the Service Worker
	if (isUpdatePage) {
		update()
			.then((willUpdate) => {
				// service worker will be updated
			})
			.catch((e) => {
				console.error(e);
			});
		return ;
	}

	if (window.renderIbicUiList) {
		const retry = function (id) {
			sendMessage({ command: 'remove-item', id });
			$.post(config.image_reset_url, { id  })
				.then(() => {
					sendMessage({command: 'get-update'});
				})
				.catch(() => {
					renderError(i18n.__('The retry failed, maybe the image does not exist anymore.', 'ibic'));
				});
		};
		const retryHandler = (imageId) => {
			return () => {
				retry(imageId);
			}
		}
		const translations = {
			'Retry': i18n.__('Retry', 'ibic'),
			'All images are compressed.': i18n.__('All images are compressed.', 'ibic'),
			'Image upload failed': i18n.__('Image upload failed', 'ibic'),
			'Loading...': i18n.__('Loading...', 'ibic'),

			'UPLOAD_MAX_SIZE_ERROR': i18n.__('The uploaded file exceeds the server max upload size.', 'ibic'),
			'CANT_READ_IMAGE_ERROR': i18n.__('Can\'t read the image.', 'ibic'),
			'CANT_DECODE_IMAGE_TOO_BIG_ERROR':  i18n.__('The image is too big and can\'t be compressed.', 'ibic'),
			'UNSUPPORTED_IMAGE_TYPE': i18n.__('Unsupported image type.', 'ibic'),
			'CANT_OPTIMISE_IMAGE_ERROR': i18n.__('An error has occurred during the image compression.', 'ibic'),
		};
		eventHandler.addEventListener('message', function (event) {
			if (event.data.command === 'queue-updated') {
				renderIbicUiList({ imageList: event.data.queue, state: 'READY', translations, retryHandler })
			}
		});
		renderIbicUiList({ translations, retryHandler })
	}
	sendMessage({command: 'get-update'});

	// Ask service worker to check the media list when a new media is uploaded
	if (wp.Uploader && wp.Uploader.queue) {
		wp.Uploader.queue.on('reset', function() {
			sendMessage({command: 'get-update'});
		});
	}
	// Ask service worker to check the media list when a new media is uploaded through the rest API
	if (wp.apiFetch) {
		wp.apiFetch.use( ( options, next ) => {
			if (options.path === '/wp/v2/media' && options.method === 'POST') {
				const result = next( options );
				sendMessage({command: 'get-update'});
				return result;
			}
			return next( options );
		} );
	}


};

$(function () {
	const onError = (error) => {
		console.error(error)
		renderError(i18n.__('Sorry, the image compression is not supported by your browser.', 'ibic'));
	};
	initSw({
		sw_url: config.assets_path + 'sw/sw.js',
		scope: '/wp-admin',
		config: config.sw_config
	}).then(init).catch(onError);
});
