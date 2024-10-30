import './UI.css';
import List from "./List";
import ListItem from "./ListItem";
import { LOADING, READY } from "./constants";
import { __ } from '@wordpress/i18n';

function UI({ state, imageList, retryHandler }) {
	let statusText = '';
	if (state === LOADING) {
		statusText = __('Loading...', 'ibic');
	} else if (state === READY && imageList.length === 0) {
		statusText = __('All images have been processed.', 'ibic');
	}

	if (statusText !== '') {
		return <div className="ibic-list">{ statusText }</div>;
	}

	return [
		<List key="list">
			{
				[...imageList].sort(function (a, b) {
					if (a.payload.error) {
						return -1;
					} else if ('processing' === a.state && 'processing' !== b.state && !b.payload.error) {
						return -1;
					}
					return 0;
				}).map((imageItem, index) => {
					const { id, error, errors, name, thumbnail } = imageItem.payload;
					return <ListItem
						key={ imageItem.id }
						state={ imageItem.state }
						id={ id }
						name={ name }
						thumbnail={ thumbnail }
						error={ error }
						errors={ errors }
						retryHandler={ retryHandler }
					/>;
				})
			}
		</List>
	];
}
export default UI;
