const fs = require('fs');
const { exec } = require("child_process");
module.exports = function( grunt ) {

	'use strict';

	// Project configuration
	// noinspection JSUnresolvedFunction
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		wp_readme_to_markdown: {
			your_target: {
				files: {
					'README.md': 'readme.txt'
				}
			},
		},

		copy: {
			vendor: {
				files: [
					{
						expand: true,
						src: ['**'],
						dest: 'assets/src/sw/',
						cwd: './node_modules/@staurand/ibic/dist/',
					},

				]
			},
			to_dist: {
				files: [
					{
						expand: true,
						src: ['**', '!ibic-ui-list/**'],
						dest: 'assets/dist/',
						cwd: './assets/src/',
					},
				]
			}
		},

		uglify: {
			build: {
				options: {
					sourceMap: true,
					mangle: false, // required or uglify will break sw.js
				},
				files: [{
					expand: true,
					cwd: './assets/dist',
					src: ['**/*.*.js', './sw/sw.js', './ibic-admin.js'],
					dest: './assets/dist',
				}]
			}
		},

		cacheBust: {
			taskName: {
				options: {
					baseDir: './assets/dist/',
					assets: ['./**/*.js', '!./sw/sw.js', '!./ibic-admin.js', '!./ui/*'],
				},
				files: [{
					expand: true,
					cwd: './assets/dist/',
					src: ['ibic-admin.js'],
				}]
			}
		},

		clean: {
			build: ['./assets/dist/sw/', './assets/src/sw/', './assets/src/ui/']
		}
	} );

	grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-cache-bust');
	grunt.registerTask('makepot', function () {
		const done = this.async();
		exec("wp i18n make-pot ./ languages/in-browser-image-compression.pot --domain=in-browser-image-compression --include=\"*.php,assets/dist/*.js,assets/dist/*/*.js\"", (error, stdout, stderr) => {
			if (error) {
				console.log(`error: ${error.message}`);
				return;
			}
			if (stderr) {
				console.log(`stderr: ${stderr}`);
				return;
			}
			console.log(`stdout: ${stdout}`);
			done();
		});

	})
	grunt.registerTask('increment_build_version', 'Increment the build version', function () {
		var done = this.async();
		fs.readFile('build-number.php', 'utf8', function (err, data) {
			if (err) {
				console.log(err);
				done();
				return;
			}
			var result = data.replace(/\<\?php return (\d+);/g, function (str, number, rest) {
				const newNumber = parseInt(number, 10) + 1;
				return `<?php return ${newNumber};`;
			});

			fs.writeFile('build-number.php', result, 'utf8', function (err) {
				if (err) {
					console.log(err);
				}
				done();
			});
		});
	});
	grunt.registerTask( 'default', [ 'i18n','readme' ] );
	grunt.registerTask( 'i18n', ['makepot'] );
	grunt.registerTask( 'readme', ['wp_readme_to_markdown'] );
	grunt.registerTask( 'build', ['clean', 'copy:vendor', 'copy:to_dist', 'cacheBust', 'uglify', 'increment_build_version',] );

	grunt.util.linefeed = '\n';

};
