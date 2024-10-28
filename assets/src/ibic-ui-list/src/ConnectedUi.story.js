import React, { useState } from "react";
import UI from "./UI";
import { LOADING, READY } from "./constants";
import ConnectedUi from "./ConnectedUi";
import { setUUIDs, retryHandler } from "../storybook/helper";

const imageFile = {
	src: "./eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"
}
export default {
	component: (args) => {
		const [state, setState] = useState(args.state);
		const [imageList, setImageList] = useState(args.imageList);
		const ConnectedComponent = ConnectedUi(UI);
		return <div>
			<ConnectedComponent state={ state } imageList={setUUIDs(imageList)} retryHandler={retryHandler} />
			<div style={{display: 'flex', gap: '15px', margin: '40px 0'}}>
				<button onClick={() => {
					setImageList([
						...[
							{
								payload: {
									error: false,
									errors: [],
									name: 'Idle',
									urls: []
								},
								state: '',
							}
						],
						...imageList,
					])
				}}>Add item
				</button>
				<button onClick={() => {
					setImageList(imageList.map((imageItem, index) => {
						if (index === 0) {
							return {
								...imageItem,
								state: 'processed',
							};
						}
						return imageItem;
					}))
				}}>Set first item processed</button>
			</div>
		</div>;
	},
	decorators: [],
};

export const ConnectedUIWithControls = {
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
					urls: [
						'./eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg'
					]
				},
				state: 'processed',
			}
		]
	}
}
