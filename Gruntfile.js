module.exports = function (grunt) {
    var path = require('path'),
        ROOT_DIR = './';

    // Load tasks.
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    // Project configuration.
    grunt.initConfig({
        debug: {
            options: {
                // do not open node-inspector in Chrome automatically
                open: false
            }
        },
        autoprefixer: {
            options: {
                browsers: [
                    'Android >= 2.1',
                    'Chrome >= 21',
                    'iOS >= 3',
                    'Explorer >= 8',
                    'Firefox >= 17',
                    'Opera >= 12.1',
                    'Safari >= 5.0'
                ]
            },
            plugin: {
                expand: true,
                cwd: ROOT_DIR,
                dest: ROOT_DIR,
                src: [
                    'assets/css/*.css',
                    // Exceptions
                    '!assets/css/*.min.css'
                ],
                options: {
                    map: 'prev'
                }
            }
        },
        cssmin: {
            plugin: {
                options: {
                    processImport: true
                },
                expand: true,
                cwd: ROOT_DIR,
                dest: ROOT_DIR,
                ext: '.min.css',
                src: [
                    'assets/css/*.css',
                    // Exceptions
                    '!assets/css/*.min.css'
                ]
            }
        },
        jshint: {
            grunt: {
                src: ['Gruntfile.js']
            },
            plugin: {
                expand: true,
                cwd: ROOT_DIR,
                src: [
                    'assets/js/*.js',
                    // Exceptions
                    '!assets/js/*.min.js'
                ]
            }
        },
        sass: {
            plugin: {
                expand: true,
                cwd: ROOT_DIR + 'assets/scss/',
                dest: ROOT_DIR + 'assets/css/',
                ext: '.css',
                options: {
                    style: 'expanded',
                    lineNumbers: false,
                    noCache: true
                },
                src: [
                    'style.scss'
                ]
            }
        },
        uglify: {
            plugin: {
                expand: true,
                cwd: ROOT_DIR,
                dest: ROOT_DIR,
                rename: function (destBase, destPath) {
                    // Fix for files with mulitple dots
                    destPath = destPath.replace(/(\.[^\/.]*)?$/, '.min.js');
                    return path.join(destBase || '', destPath);
                },
                src: [
                    'assets/js/*.js',
                    // Exceptions
                    '!assets/js/*.min.js'
                ]
            }
        },
        lineending: {
            plugin: {
                expand: true,
                cwd: ROOT_DIR,
                dest: ROOT_DIR,
                src: [
                    'assets/css/*.css',
                    // Exceptions
                    '!assets/css/*.min.css'
                ],
                options: {
                    eol: 'lf',
                    overwrite: true
                }
            }
        },
        combine_mq  : {
            default_options: {
                expand: true,
                    cwd   : ROOT_DIR,
                    src   : [
                    'assets/css/*.css',
                    // Exceptions
                    '!assets/css/*.min.css'
                ],
                    dest  : './'
            }
        }
    });

    // Register tasks

    grunt.registerTask('javascript-testing', ['jshint']);
    grunt.registerTask('javascript', ['uglify:plugin']);
    grunt.registerTask('css', ['sass:plugin', 'autoprefixer:plugin', 'lineending:plugin', 'combine_mq', 'cssmin:plugin']);

    // Default task
    grunt.registerTask('production', ['javascript', 'css']);
};
