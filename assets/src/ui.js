const STATE_LOADING = 'loading';
const STATE_IDLE = 'idle';
const STATE_PROCESSED = 'processed';

export const renderUI = ({ i18n, placeholder, $, retry }) => {
	const $stateButton = $('<button class="page-title-action ibic-list-toggle-btn" aria-expanded="false" aria-haspopup="true" role="button"></button>');
	const $list = $('<ul class="ibic-list" aria-hidden="true"></ul>');
	const closeList = () => {
		$stateButton.attr({ 'aria-expanded': 'false' });
		$list.attr({ 'aria-hidden': 'true' });
	};
	let state = STATE_LOADING;

	const updateListItemState = ($listItem, data) => {
		$listItem.empty().removeClass('is-processed is-error');
		if (data.payload.error) {
			$listItem
				.append($('<span class="screen-reader-text"></span>').text(i18n.__(data.payload.error, 'ibic')))
				.append(
					$('<button class="button"></button>').append(
						$('<span class="screen-reader-text"></span>').text(i18n.__('Retry', 'ibic')),
						'<span class="dashicons-before dashicons-controls-repeat"></span>'
					).click(function () {
						retry(data.payload.id);
					})
				)
				.addClass('is-error');
		} else if (data.state === 'processed') {
			$listItem
				.append($('<span class="screen-reader-text"></span>').text(i18n.__('Image compression completed', 'ibic')))
				.addClass('is-processed');
		}
	}

	const updateListState = (newState) => {
		switch (newState) {
			case STATE_LOADING:
				$stateButton.text(i18n.__('loading...', 'ibic'));
				break;
			case STATE_IDLE:
				$stateButton.text(i18n.__('Image compression in progress', 'ibic'));
				break;
			case STATE_PROCESSED:
				$stateButton.text(i18n.__('Image compression completed', 'ibic'));
				break;
		}
		state = newState;
	};

	// Toggle list on button click
	$stateButton.click(function () {
		if ($(this).attr('aria-expanded') === 'false') {
			$stateButton.attr({ 'aria-expanded': 'true' });
			$list.attr({ 'aria-hidden': 'false' });
		} else {
			closeList();
		}
	});

	// if the user clicks outside the placeholder close the list
	$(document).click(function (e) {
		if ($(e.target).closest(placeholder).length === 0) {
			closeList();
		}
	});

	placeholder.append($stateButton, $list);
	updateListState(STATE_LOADING);

	return {
		update: ({ list }) => {
			const processed = list.reduce((result, item) => result && item.state === 'processed', true);
			if (processed) {
				updateListState(STATE_PROCESSED);
			} else {
				updateListState(STATE_IDLE);
			}

			let $lastItem = null;
			$list.empty();
			if (list.length > 0) {
				list.forEach((item) => {
					const $item = $('<li class="ibic-list__item"><span class="ibic-list__item__thumbnail"></span><span class="ibic-list__item__name"></span><span class="ibic-list__item__state"></span></li>');

					if (!item.payload.thumbnail) {
						item.payload.thumbnail = findSmallestImage(item.payload.urls);
					}
					if (item.payload.thumbnail) {
						$item.find('.ibic-list__item__thumbnail').css('background-image', 'url("' + item.payload.thumbnail + '")');
					} else {
						$item.find('.ibic-list__item__thumbnail').css('background-image', '');
					}
					$item.find('.ibic-list__item__name').text(item.payload.name);
					updateListItemState($item.find('.ibic-list__item__state'), item);

					$list.append($item);
					$lastItem = $item;
				});
			} else {
				const $item = $('<li class="ibic-list__item ibic-list-no-result-item"></li>').text(i18n.__('All images are compressed.', 'ibic'));
				$list.empty().append($item);
			}
		}
	};
}

export const renderErrorFactory = ({ $ }) => {
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

export const findSmallestImage = (urls) => {
	let smallestImageSize = null;
	let smallestImage = null;
	urls.forEach((url) => {
		const urlParts = url.match(/-(\d+)x(\d+)\.[a-z0-9]+$/);
		if (!urlParts) {
			if (!smallestImage) {
				smallestImage = url;
			}
			return ;
		}
		const imageSize = { width: urlParts[1], height: urlParts[2] };
		if (!smallestImageSize || smallestImageSize.width + smallestImageSize.height > imageSize.width + imageSize.height) {
			smallestImageSize = imageSize;
			smallestImage = url;
		}
	});
	return smallestImage;
}
