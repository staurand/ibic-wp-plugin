import { initSw } from './sw/sw-init.js';
const $ = window.jQuery;
const config = window.IBIC_ADMIN_CONFIG;
const { __ } = window.wp.i18n;

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
					renderError(__('The retry failed, maybe the image does not exist anymore.', 'in-browser-image-compression'));
				});
		};
		const retryHandler = (imageId) => {
			return () => {
				retry(imageId);
			}
		};

		const updateCompletionStatus = function () {
			$('#ibic-completion-placeholder').load(ajaxurl + '?action=ibic_get_media_completion_status');
		};
		const refreshErrorList = function () {
			$.get(config.image_error_list_url).then(function (response) {
				if (response.length === 0) {
					renderIbicUiList({ id: 'ibic-ui-placeholder-errors', state: 'LOADING', retryHandler });
					$('#ibic-ui-placeholder-errors-wrapper').hide();
				} else {
					$('#ibic-ui-placeholder-errors-wrapper').show();
				}
				const imageList = response.map((image) => {
					return {
						state: 'error',
						payload: {
							...image,
							errors: $.isArray(image.errors) ? image.errors : [image.errors]
						}
					};
				});
				renderIbicUiList({ imageList, id: 'ibic-ui-placeholder-errors', state: 'READY', retryHandler });
			});

		};
		eventHandler.addEventListener('message', function (event) {
			if (event.data.command === 'queue-updated') {
				renderIbicUiList({ imageList: event.data.queue, state: 'READY', retryHandler });
				// Update the completion status
				updateCompletionStatus();
				refreshErrorList();
			}
		});
		renderIbicUiList({ retryHandler });
		refreshErrorList();
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
		renderError(__('Sorry, the image compression is not supported by your browser.', 'in-browser-image-compression'));
	};
	initSw({
		sw_url: config.assets_path + 'sw/sw.js',
		scope: '/wp-admin',
		config: config.sw_config
	}).then(init).catch(onError);
});
