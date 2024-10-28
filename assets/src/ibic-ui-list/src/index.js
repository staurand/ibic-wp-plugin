import React from 'react';
import ReactDOM from 'react-dom';
import ConnectedUi from './ConnectedUi';
import UI from './UI';
import { LOADING } from "./constants";

const renderIbicUiList = ({ id = 'ibic-ui-placeholder', state = LOADING, imageList, retryHandler }) => {
	const ConnectedComponent = ConnectedUi(UI);
	ReactDOM.render(
		<React.StrictMode>
			<ConnectedComponent state={ state } imageList={ Array.isArray(imageList) ? imageList : [] } retryHandler={ retryHandler } />
		</React.StrictMode>,
		document.getElementById(id)
	);
};
window.renderIbicUiList = renderIbicUiList;
