import React from "react";
import ListItem from "./ListItem";
import List from "./List";
import { retryHandler } from "../storybook/helper";

const imageFile = {
	src: "./eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"
}
export default {
	component: (args) => {
		return <List>
			<ListItem {...args} />
		</List>
	},
};


export const Idle = {
	args: {
		error: false,
		errors: [],
		name: 'Idle',
		state: '',
		thumbnail: imageFile.src,
		retryHandler
	},
};


export const Processing = {
	args: {
		error: false,
		errors: [],
		name: 'Processing',
		state: 'processing',
		thumbnail: imageFile.src,
		retryHandler
	},
};


export const Processed = {
	args: {
		error: false,
		errors: [],
		name: 'Processed',
		state: 'processed',
		thumbnail: imageFile.src,
		retryHandler
	},
};

export const GenericError = {
	args: {
		error: 'test error',
		errors: [],
		name: 'Generic error',
		state: 'processed',
		thumbnail: imageFile.src,
		retryHandler
	},
};

export const UploadMaxSizeError = {
	args: {
		error: 'Upload max size error',
		errors: ['UPLOAD_MAX_SIZE_ERROR'],
		name: 'Upload max size error',
		state: 'processed',
		thumbnail: imageFile.src,
		retryHandler
	},
};
export const CantReadImageError = {
	args: {
		error: 'Can’t read image error',
		errors: ['CANT_READ_IMAGE_ERROR'],
		name: 'Image error',
		state: 'processed',
		thumbnail: imageFile.src,
		retryHandler
	},
};

export const MultipleErrors = {
	args: {
		error: 'Multiple errors',
		errors: ['CANT_DECODE_IMAGE_TOO_BIG_ERROR', 'UNSUPPORTED_IMAGE_TYPE', 'CANT_OPTIMISE_IMAGE_ERROR'],
		name: 'Multiple errors',
		state: 'processed',
		thumbnail: imageFile.src,
		retryHandler
	},
};
