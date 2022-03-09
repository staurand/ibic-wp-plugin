import defaultThumbnail from './default-thumbnail.png';
import React from "react";
import TranslationsContext from './TranslationsContext';

function ConnectedUi(UIComponent, { translations, imageList, retryHandler }) {

	return function () {
		return <TranslationsContext.Provider value={ translations }>
			<UIComponent imageList={ prepareImageList(imageList) } retryHandler={ retryHandler }  />
		</TranslationsContext.Provider>;
	}

}
export default ConnectedUi;

export const prepareImageList = (imageList) => {
	return imageList.map((imageItem) => {
		if (!imageItem.payload.thumbnail) {
			const smallestImageInSet = findSmallestImage(imageItem.payload.urls);
			imageItem.payload.thumbnail = smallestImageInSet ? smallestImageInSet : defaultThumbnail;
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