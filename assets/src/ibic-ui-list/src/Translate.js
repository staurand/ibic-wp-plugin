import TranslationsContext from './TranslationsContext';

const Translate = ({ string }) => {
	return <TranslationsContext.Consumer>{ translations => translations[string] ? translations[string] : string }</TranslationsContext.Consumer>
}

export default Translate;
