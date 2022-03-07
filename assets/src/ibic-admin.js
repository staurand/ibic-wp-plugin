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

			'UPLOAD_MAX_SIZE_ERROR': i18n.__('The uploaded file exceeds the server max upload size.', 'ibic'),
			'CANT_READ_IMAGE_ERROR': i18n.__('Can\'t read the image.', 'ibic'),
		};
		eventHandler.addEventListener('message', function (event) {
			if (event.data.command === 'queue-updated') {
				const imageList = event.data.queue;
				renderIbicUiList({ imageList: event.data.queue, translations, retryHandler })
				//ui.update({ list: event.data.queue });
			}
		});
		renderIbicUiList({ imageList: [], translations, retryHandler })
	}

	/*$('#wp-media-grid a.page-title-action[href$="media-new.php"]').each(function () {
		const placeholder = $('<div id="ibic-ui-placeholder" class="ibic-placeholder"></div>');
		const retry = function (id) {
			$.post(config.image_reset_url, { id  })
				.then(() => {
					sendMessage({command: 'get-update'});
				})
				.catch(() => {
					renderError(i18n.__('The retry failed, maybe the image does not exist anymore.', 'ibic'));
				});

		};
		$(this).after(placeholder);
		const ui = renderUI({
			i18n,
			placeholder,
			$,
			retry
		});

		eventHandler.addEventListener('message', function (event) {
			if (event.data.command === 'queue-updated') {
				//ui.update({ list: event.data.queue });
			}
		});


		list format:
		[
			{
				payload: {
					id: 47,
					name: 'test',
					urls: ["https://ibic.dev.tsjm.fr/wp-content/uploads/2022/01/stephen-phillips-hostreviews-co-uk-zs98a0DtKL4-unsplash-1-150x150.jpg", "https://ibic.dev.tsjm.fr/wp-content/uploads/2022/01/stephen-phillips-hostreviews-co-uk-zs98a0DtKL4-unsplash-1-300x200.jpg", "https://ibic.dev.tsjm.fr/wp-content/uploads/2022/01/stephen-phillips-hostreviews-co-uk-zs98a0DtKL4-unsplash-1-768x512.jpg"],
				},
				state: "processing"
			}
		]

		window.testUI = function (list) {
			ui.update({ list })
		};

	});
*/

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
