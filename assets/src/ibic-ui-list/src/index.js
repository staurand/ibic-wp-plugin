import React from 'react';
import ReactDOM from 'react-dom';
import ConnectedUi from './ConnectedUi';
import UI from './UI';
import demo from "./demo";


const renderIbicUiList = ({ id = 'ibic-ui-placeholder', translations, imageList, retryHandler }) => {
	const ConnectedComponent = ConnectedUi(UI);
	ReactDOM.render(
		<React.StrictMode>
			<ConnectedComponent translations={ translations } imageList={ imageList } retryHandler={ retryHandler } />
		</React.StrictMode>,
		document.getElementById(id)
	);
};
if (process.env.NODE_ENV === 'development') {
	demo(renderIbicUiList);
}

window.renderIbicUiList = renderIbicUiList;
