import './UI.css';
import List from "./List";
import ListItem from "./ListItem";
function UI({ imageList, retryHandler }) {
	return [
		<List key="list">
			{
				imageList.map((imageItem, index) => {
					const { id, error, errors, name, thumbnail } = imageItem.payload;
					return <ListItem
						key={ index }
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
