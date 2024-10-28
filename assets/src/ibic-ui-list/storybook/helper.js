
export const setUUIDs = (imageList) => {
	return imageList.map((item, index) => {
		return {
			...item,
			id: 'fake-uuid-' + index
		}
	})
}

export const retryHandler = (imageId) => {
	return () => {
		console.log(imageId)
	}
};
