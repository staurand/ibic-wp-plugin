import React from "react";
import ListItem from "./ListItem";
import List from "./List";
import { setUUIDs, retryHandler } from "../storybook/helper";

const imageFile = {
	src: "./eugene-chystiakov-TCQmflzrZRQ-unsplash-150x150.jpg"
}

export default {
	component: (args) => {
		return <List>
			{
				setUUIDs(args.list).map(({ id, ...listItemArgs}) => <ListItem key={ id } {...listItemArgs} />)
			}

		</List>
	},
};


export const Idle = {
	args: {
		list: [
			{
				error: false,
				errors: [],
				name: 'Idle',
				state: '',
				thumbnail: imageFile.src,
				retryHandler
			},
			{
				error: false,
				errors: [],
				name: 'Processed',
				state: 'processed',
				thumbnail: imageFile.src,
				retryHandler
			}
		]
	},
};
