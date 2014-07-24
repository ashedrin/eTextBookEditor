module.exports = function (grunt) {
    grunt.initConfig({
        concat: {
            main: {
                src: [
                    'bower_components/jquery/dist/jquery.min.js'
                    , 'bower_components/bootstrap/dist/js/bootstrap.min.js'
                    , 'bower_components/underscore/underscore.js'
                    , 'bower_components/backbone/backbone.js'
                    , 'bower_components/angularjs/angular.min.js'
                    , 'web/js/app.js'
                    , 'web/js/eTextBook/*.js'
                    , 'web/js/eTextBook/inline/*.js'
                    , 'web/js/eTextBook/widget/*.js'
                ],
                dest: 'web/js/script.js'
            }
//            ,template: {
//                src: [
//                    'bower_components/jquery/dist/jquery.min.js'
//                    , 'bower_components/underscore/underscore.js'
//                    , 'bower_components/backbone/backbone.js'
//                    , 'web/js/app.js'
//                    , 'web/js/eTextBook/utils.js'
//                    , 'web/js/eTextBook/widget.js'
//                    , 'web/js/eTextBook/widgetRepository.js'
//                    , 'web/js/eTextBook/widget/*.js'
//                    , 'web/js/html5Player.js'
//                ],
//                dest: 'template/js/script.js'
//            }
        }, concat_css: {
            main: {
                src: [
                    'bower_components/bootstrap/dist/css/bootstrap.min.css'
                    , 'web/css/style.css'
                ], dest: 'web/css/main-style.css'
            }
//            ,template: {
//                src: [
//                    'bower_components/bootstrap/dist/css/bootstrap.min.css'
//                    , '/css/style.css'
//                    , 'css/html5Player.css'
//                ], dest: 'template/css/main-style.css'
//            }
        }, cssmin: {
            main: {
                files: {
                    'web/css/main-style.min.css': ['web/css/main-style.css']
                }
            }
//            ,template: {
//                files: {
//                    'template/css/main-style.min.css': ['template/css/main-style.css']
//                }
//            }
        }, uglify: {
            main: {
                files: {
                    'web/js/script.min.js': '<%= concat.main.dest %>'
                }
            }
//            ,template: {
//                files: {
//                    'template/js/script.min.js': '<%= concat.template.dest %>'
//                }
//            }
        }
    });

    require('load-grunt-tasks')(grunt);
    require('time-grunt')(grunt);

    grunt.registerTask('default', ['newer:concat', 'newer:concat_css', 'newer:cssmin', 'newer:uglify']);
    grunt.registerTask('full', ['concat', 'concat_css', 'cssmin', 'uglify']);

};