import React from "react";
import UI from "./UI";
import { LOADING, READY } from "./constants";
import { setUUIDs, retryHandler } from "../storybook/helper";

const imageFile = {
	src: "./eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"
}
export default {
	component: (args) => {
		return <UI state={ args.state } imageList={setUUIDs(args.imageList)} retryHandler={retryHandler} />;
	},
};


export const Loading = {
	args: {
		state: LOADING,
		imageList: [
			{
				payload: {
					error: false,
					errors: [],
					name: 'Idle',
					thumbnail: imageFile.src,
				},
				state: '',
			},
			{
				payload: {
					error: false,
					errors: [],
					name: 'Processed',
					thumbnail: imageFile.src,
				},
				state: 'processed',
			}
		]
	},
};

export const Processing = {
	args: {
		state: READY,
		imageList: [
			{
				payload: {
					error: false,
					errors: [],
					name: 'Idle',
					thumbnail: imageFile.src,
				},
				state: '',
			},
			{
				payload: {
					error: false,
					errors: [],
					name: 'Processed',
					thumbnail: imageFile.src,
				},
				state: 'processed',
			}
		]
	},
};


export const Completed = {
	args: {
		state: READY,
		imageList: []
	},
};

