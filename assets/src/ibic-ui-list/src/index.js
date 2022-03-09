import React from 'react';
import ReactDOM from 'react-dom';
import ConnectedUi from './ConnectedUi';
import UI from './UI';
import demo from "./demo";
import { LOADING } from "./constants";

const renderIbicUiList = ({ id = 'ibic-ui-placeholder', state = LOADING, translations, imageList, retryHandler }) => {
	const ConnectedComponent = ConnectedUi(UI);
	ReactDOM.render(
		<React.StrictMode>
			<ConnectedComponent state={ state } translations={ translations } imageList={ Array.isArray(imageList) ? imageList : [] } retryHandler={ retryHandler } />
		</React.StrictMode>,
		document.getElementById(id)
	);
};
if (process.env.NODE_ENV === 'development') {
	demo(renderIbicUiList);
}

window.renderIbicUiList = renderIbicUiList;
