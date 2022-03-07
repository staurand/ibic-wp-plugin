import Translate from "./Translate";

function List({ children }) {

	return <ul className="ibic-list">{
		children.length > 0 ? children : <Translate string="All images are compressed." />
	}</ul>;
}

export default List;
