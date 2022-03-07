import React from 'react';
import ReactDOM from 'react-dom';
import ConnectedUi from './ConnectedUi';
import UI from './UI';


const renderIbicUiList = ({ id = 'ibic-ui-placeholder', translations, imageList, retryHandler }) => {
	const ConnectedComponent = ConnectedUi(UI, {
		translations,
		imageList,
		retryHandler
	});
	ReactDOM.render(
		<React.StrictMode>
			<ConnectedComponent />
		</React.StrictMode>,
		document.getElementById(id)
	);
};
if (process.env.NODE_ENV === 'development') {
	const translations = {
		'Retry': 'Retry',
		'All images are compressed.': 'All images are compressed.',
		'Image upload failed': 'Image upload failed',

		'UPLOAD_MAX_SIZE_ERROR': 'The uploaded file exceeds the server max upload size.',
		'CANT_READ_IMAGE_ERROR': 'Can\'t read the image.',
	};
	const retryHandler = (imageId) => {
		return () => {
			console.log(imageId)
		}
	};
	renderIbicUiList({
		imageList: [
			{
				payload: {
					id: 47,
					name: 'test item 47',
					urls: [process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-768x1152.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"],
				},
				state: "processing"
			},
			{
				payload: {
					id: 47,
					name: 'test item 47 unknown state',
					urls: [process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-768x1152.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"],
				},
				state: ""
			},
			{
				payload: {
					id: 47,
					name: 'test item 47',
					urls: [process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-768x1152.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"],
					error: 'test error'
				},
				state: "processed",
			},
			{
				payload: {
					id: 47,
					name: 'test item 47 with errors',
					urls: [process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-768x1152.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"],
					error: 'test error',
					errors: ['UPLOAD_MAX_SIZE_ERROR']
				},
				state: "processed",
			},
			{
				payload: {
					id: 47,
					name: 'test item 47 with errors',
					urls: [process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-768x1152.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"],
					error: 'test error',
					errors: ['CANT_READ_IMAGE_ERROR']
				},
				state: "processed",
			},
			{
				payload: {
					id: 47,
					name: 'test item 47 with errors',
					urls: [process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-768x1152.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"],
					error: 'test error',
					errors: ['ERROR_NOT_TRANSLATED']
				},
				state: "processed",
			},

			{
				payload: {
					id: 33,
					name: 'test item 33',
					urls: [process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-768x1152.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"],
				},
				state: "processed"
			}
		],
		translations,
		retryHandler
	});
	renderIbicUiList({
		id: 'ibic-ui-placeholder--empty',
		imageList: [],
		translations,
		retryHandler
	});

}

window.renderIbicUiList = renderIbicUiList;
