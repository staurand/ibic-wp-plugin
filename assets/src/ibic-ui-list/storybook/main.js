export default {

	framework: {
		name: '@storybook/react-webpack5',
		options: {},
	},
	swc: (config, options) => ({
		jsc: {
			transform: {
				react: {
					runtime: 'automatic',
				},
			},
		},
	}),
    stories: [
		'../src/*.story.js',
	],

    staticDirs: ['./static', ],
    addons: [
        "@storybook/addon-webpack5-compiler-swc",

    ],

};
