import classNames from 'classnames';
import { __ } from '@wordpress/i18n';
function ListItem({ error, errors, state, id, name, thumbnail, retryHandler }) {
	const hasError = !!error;
	let computedState = 'unknown';
	if (hasError) {
		computedState = 'error';
	} else if (state === 'processing' || state === 'processed') {
		computedState = state;
	}
	const itemClassName = classNames({
		'ibic-list__item': true,
		'is-processing ': computedState === 'processing',
		'is-processed': computedState === 'processed',
		'is-error': computedState === 'error'
	});
	const itemStateTextClassName = classNames({
		'ibic-list__item__state-text dashicons-before': true,
		'dashicons-clock': computedState === 'unknown',
		'dashicons-format-gallery': computedState === 'processing',
		'dashicons-yes': computedState === 'processed',
		'dashicons-warning': computedState === 'error'
	});

	return <li className={ itemClassName }>
		<img className="ibic-list__item__thumbnail" src={ thumbnail } alt="" />

		<span className="ibic-list__item__state">
			<span className={ itemStateTextClassName }>{ getStateText(computedState) }</span>
		</span>


		<span className="ibic-list__item__name">
			{ name }
			{ hasError ? <span className="ibic-list__item__error">{
				errors ? errors.map((error, index) => <div key={ index }>{ getErrorText(error) }</div>) : error
			}</span> : null }
		</span>


		{ hasError ? <button className="ibic-list__item__retry ibic-split-button" onClick={ retryHandler(id) }>
			<span className="ibic-split-button__text">{ __('Retry', 'ibic') }</span>
			<span className="ibic-split-button__icon dashicons-before dashicons-controls-repeat"></span>
		</button> : null}

	</li>
}

const getStateText = function (computedState) {
	switch (computedState) {
		case 'error':
			return __('Error', 'ibic');
		case 'processing':
			return __('Processing', 'ibic');
		case 'processed':
			return __('Processed', 'ibic');
		case 'unknown':
			return __('Waiting', 'ibic');
	}
}

const getErrorText = function (errorMessage) {
	const knownErrorMessages = {
		'UPLOAD_MAX_SIZE_ERROR': __('The uploaded file exceeds the server max upload size.', 'ibic'),
		'CANT_READ_IMAGE_ERROR': __('Can’t read the image.', 'ibic'),
		'CANT_DECODE_IMAGE_TOO_BIG_ERROR': __('The image is too big and can’t be compressed.', 'ibic'),
		'UNSUPPORTED_IMAGE_TYPE': __('Unsupported image type.', 'ibic'),
		'CANT_OPTIMISE_IMAGE_ERROR': __('An error has occurred during the image compression.', 'ibic'),
		'IMAGE_UPLOAD_FAILED_ERROR': __('An error has occurred during the image upload.', 'ibic'),
	};
	if (knownErrorMessages[errorMessage]) {
		return knownErrorMessages[errorMessage];
	}
	return errorMessage;
}

export default ListItem;
