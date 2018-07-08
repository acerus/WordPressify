'use strict';
module.exports = function(grunt) {

	grunt.initConfig({

		dirs: {
			js: 'js',
			css: 'css',
			wp_job_manager: 'inc/integrations/wp-job-manager/js',
		},

		watch: {
			options: {
				livereload: 1234,
			},
			js: {
				files: [
				'Gruntfile.js',
				'js/vendor/**/*.js',
				'js/app/*.js',
				'inc/integrations/**/*.coffee',
				'inc/integrations/**/*.js',
				'js/**/*.coffee'
				],
				tasks: [ 'coffee', 'uglify' ]
			},
			css: {
				files: [
					'css/sass/**/*.scss',
					'css/sass/*.scss'
				],
				tasks: [ 'sass', 'concat', 'cssmin' ]
			}
		},

		coffee: {
			dist: {
				options: {
					sourceMap: true,
				},
				files: {
					'<%= dirs.wp_job_manager %>/map/app.js': [
						'<%= dirs.wp_job_manager %>/map/app.coffee'
					],
					'<%= dirs.js %>/widgets/widgets.js': [
						'<%= dirs.js %>/widgets/*.coffee'
					]
				}
			}
		},

		// uglify to concat, minify, and make source maps
		uglify: {
			dist: {
				files: {
					'inc/integrations/wp-job-manager/js/wp-job-manager.js': [
						'inc/integrations/wp-job-manager/js/source/wp-job-manager.js',
						'inc/integrations/wp-job-manager/js/source/wp-job-manager-apply-with.js'
					],
					'inc/integrations/wp-job-manager/js/map/app.min.js': [
						'inc/integrations/wp-job-manager/js/map/vendor/**/*.js',
						'inc/integrations/wp-job-manager/js/map/app.js'
					],
					'inc/integrations/wp-job-manager-favorites/js/wp-job-manager-favorites.min.js': [
						'inc/integrations/wp-job-manager-favorites/js/wp-job-manager-favorites.js',
					],
					'js/jobify.min.js': [
						'js/vendor/**/*.js',
						'inc/integrations/wp-job-manager/js/wp-job-manager.js',
						'inc/integrations/woocommerce/js/woocommerce.js',
						'js/widgets/widgets.js',
						'js/app/app.js',
						'!js/vendor/salvattore/*'
					],
				}
			}
		},

		sass: {
			dist: {
				files: {
					'css/style.css' : 'css/sass/style.scss'
				}
			}
		},

		concat: {
			dist: {
				files: {
					'css/style.css': [ 
						'css/_theme.css', // theme header
						'js/vendor/**/*.css', // js libs
						'css/style.css' // base
					]
				}
			}
		},

		cssmin: {
			dist: {
				files: {
					'style.css': [ 'css/style.css' ]
				}
			}
		},

		cssjanus: {
			theme: {
				options: {
					swapLtrRtlInUrl: false
				},
				files: [
					{
						src: 'style.css',
						dest: 'style-rtl.css'
					}
				]
			}
		},

		jsonlint: {
			dist: {
				src: [ 'inc/setup/import-content/**/*.json' ],
				options: {
					formatter: 'prose'
				}
			}
		},

		makepot: {
			theme: {
				options: {
					type: 'wp-theme'
				}
			}
		},

		glotpress_download: {
			theme: {
				options: {
					url: 'http://astoundify.com/glotpress',
					domainPath: 'languages',
					slug: 'jobify',
					textdomain: 'jobify',
					formats: [ 'mo', 'po' ],
					file_format: '%domainPath%/%wp_locale%.%format%',
					filter: {
						translation_sets: false,
						minimum_percentage: 50,
						waiting_strings: false
					}
				}
			}
		},

		checktextdomain: {
			standard: {
				options:{
					force: true,
					text_domain: 'jobify',
					create_report_file: false,
					correct_domain: true,
					keywords: [
						'__:1,2d',
						'_e:1,2d',
						'_x:1,2c,3d',
						'esc_html__:1,2d',
						'esc_html_e:1,2d',
						'esc_html_x:1,2c,3d',
						'esc_attr__:1,2d', 
						'esc_attr_e:1,2d', 
						'esc_attr_x:1,2c,3d', 
						'_ex:1,2c,3d',
						'_n:1,2,4d', 
						'_nx:1,2,4c,5d',
						'_n_noop:1,2,3d',
						'_nx_noop:1,2,3c,4d'
					]
				},
				files: [{
					src: ['**/*.php','!node_modules/**'],
					expand: true,
				}],
			},
		},

	});

	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-coffee' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-sass' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-cssjanus' );
	grunt.loadNpmTasks( 'grunt-exec' );
	grunt.loadNpmTasks( 'grunt-potomo' );
	grunt.loadNpmTasks( 'grunt-jsonlint' );
	grunt.loadNpmTasks( 'grunt-glotpress' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );

	// register task
	grunt.registerTask('default', ['watch']);

	grunt.registerTask( 'tx', ['exec:txpull', 'potomo']);
	grunt.registerTask( 'i18n', [ 'checktextdomain', 'makepot', 'glotpress_download' ] );
	grunt.registerTask( 'rtl', ['cssjanus']);

	grunt.registerTask( 'build', [
		'jsonlint',
		'coffee', 'uglify', // JS
		'sass', 'concat', 'cssmin', // CSS
		'i18n',
		'cssjanus', // RTL
	]);
};
