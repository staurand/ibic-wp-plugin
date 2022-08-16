export default function demo(renderIbicUiList) {
	const i18n = { __: string => string };
	const translations = {
		'Retry': i18n.__('Retry', 'ibic'),
		'All images are compressed.': i18n.__('All images are compressed.', 'ibic'),
		'Image upload failed': i18n.__('Image upload failed', 'ibic'),

		'UPLOAD_MAX_SIZE_ERROR': i18n.__('The uploaded file exceeds the server max upload size.', 'ibic'),
		'CANT_READ_IMAGE_ERROR': i18n.__('Can\'t read the image.', 'ibic'),
		'CANT_DECODE_IMAGE_TOO_BIG_ERROR':  i18n.__('The image is too big and can\'t be compressed.', 'ibic'),
		'UNSUPPORTED_IMAGE_TYPE': i18n.__('Unsupported image type.', 'ibic'),
		'CANT_OPTIMISE_IMAGE_ERROR': i18n.__('An error has occurred during the image compression.', 'ibic'),
	};
	const retryHandler = (imageId) => {
		return () => {
			console.log(imageId)
		}
	};
	const setUUIDs = (imageList) => {
		return imageList.map((item, index) => {
			return {
				...item,
				id: 'fake-uuid-' + index
			}
		})
	}
	renderIbicUiList({
		imageList: setUUIDs([
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
		]),
		translations,
		retryHandler
	});

	renderIbicUiList({
		id: 'ibic-ui-placeholder--empty',
		imageList: [],
		translations,
		retryHandler
	});

	let sourceImageList = [];
	const updateDynamicList = (imageList) => {
		renderIbicUiList({
			id: 'ibic-ui-placeholder--dynamic',
			imageList: imageList,
			translations,
			retryHandler
		});
	}
	updateDynamicList(sourceImageList);
	const addButton = document.createElement('button');
	addButton.appendChild(document.createTextNode('Add image'));
	addButton.addEventListener('click', function () {
		sourceImageList.unshift({
			payload: {
				id: sourceImageList.length,
				name: 'test item ' + sourceImageList.length,
				urls: [process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-768x1152.jpg", process.env.PUBLIC_URL + "/static/eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"],
			},
			state: sourceImageList.length % 2 === 0 ? "processing" : "processed",
			id: 'fake-uuid-' + sourceImageList.length
		})
		updateDynamicList(sourceImageList)
	})
	document.body.insertBefore(addButton, document.getElementById('ibic-ui-placeholder--dynamic'));


}
