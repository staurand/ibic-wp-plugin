import classNames from 'classnames';
import Translate from './Translate';
function ListItem({ error, errors, state, id, name, thumbnail, retryHandler }) {
	const hasError = !!error;
	const itemStateClassName = classNames({
		'ibic-list__item__state': true,
		'is-processed': state === 'processed' && !hasError,
		'is-error': hasError
	});
	return <li className="ibic-list__item">
		<span className={ itemStateClassName }></span>
		<img className="ibic-list__item__thumbnail" src={ thumbnail } alt="" />
		<span className="ibic-list__item__name">{ name }</span>
		{ hasError ? <span className="ibic-list__item__error">{
			errors ? errors.map((error, index) => <div key={ index }><Translate key={ index } string={ error } /></div>) : error
		}</span> : null }
		{ hasError ? <button className="button" onClick={ retryHandler(id) }>
			<span className="dashicons-before dashicons-controls-repeat"></span>
			<span className="screen-reader-text"><Translate string="Retry" /></span>
		</button> : null}
	</li>
}
export default ListItem;
