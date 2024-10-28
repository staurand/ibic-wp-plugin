import defaultThumbnail from './default-thumbnail.png';
import React from "react";

function ConnectedUi(UIComponent) {
	return function ({ state, imageList, retryHandler }) {
		return <UIComponent state={ state } imageList={ prepareImageList(imageList) } retryHandler={ retryHandler }  />;
	}

}
export default ConnectedUi;

export const prepareImageList = (imageList) => {
	return imageList.map((imageItem) => {
		if (!imageItem.payload.thumbnail) {
			const smallestImageInSet = findSmallestImage(imageItem.payload.urls);
			imageItem.payload.thumbnail = smallestImageInSet ? smallestImageInSet : defaultThumbnail;
		}
		if (Array.isArray(imageItem.payload.errors)) {
			imageItem.payload.errors = [ ...new Set(imageItem.payload.errors) ]; // ...new Set(imageItem.errors) => make array items unique
		}
		return imageItem;
	});
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
