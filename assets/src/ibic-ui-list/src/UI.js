import './UI.css';
import List from "./List";
import ListItem from "./ListItem";
import Translate from "./Translate";
import { LOADING, READY } from "./constants";
function UI({ state, imageList, retryHandler }) {
	let statusText = '';
	if (state === LOADING) {
		statusText = <Translate string="Loading..." />;
	} else if (state === READY && imageList.length === 0) {
		statusText = <Translate string="All images are compressed." />;
	}

	if (statusText !== '') {
		return <div className="ibic-list">{ statusText }</div>;
	}

	return [
		<List key="list">
			{
				imageList.map((imageItem, index) => {
					const { id, error, errors, name, thumbnail } = imageItem.payload;
					return <ListItem
						key={ imageItem.id }
						state={ imageItem.state }
						id={ id }
						name={ name }
						thumbnail={ thumbnail }
						error={ error }
						errors={ errors }
						retryHandler={ retryHandler }
					/>;
				})
			}
		</List>
	];
}
export default UI;
