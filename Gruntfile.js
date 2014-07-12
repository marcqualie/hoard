module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      compile: {
        files: {
          'public/assets/app.css': 'app/assets/stylesheets/app.scss'
        },
        options: {
          loadPath: [
            'app/assets/stylesheets',
            'public/vendor/bootstrap-sass-official/vendor/assets/stylesheets'
          ]
        }
      },
      minify: {
        files: {
          'public/assets/app.css': 'public/assets/app.css'
        },
        options: {
          style: 'compressed'
        }
      }
    },
    uglify: {
      compile: {
        files: {
          'public/assets/jquery.js': 'public/vendor/jquery/dist/jquery.js',
          'public/assets/bootstrap.js': [
            'public/vendor/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/alert.js',
            'public/vendor/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/button.js',
            'public/vendor/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/collapse.js',
            'public/vendor/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/dropdown.js',
            'public/vendor/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/modal.js',
            'public/vendor/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/tab.js',
            'public/vendor/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/tooltip.js',
            'public/vendor/bootstrap-sass-official/vendor/assets/javascripts/bootstrap/transition.js'
          ],
          'public/assets/app.js': 'app/assets/javascripts/app.js',
        }
      }
    },
    watch: {
      options: {
        livereload: true,
      },
      sass: {
        files: ['**/*.scss'],
        tasks: ['sass:compile'],
        debugInfo: true
      },
      uglify: {
        files: ['app/assets/javascripts/*.js'],
        tasks: ['uglify:compile'],
        debugInfo: true
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', [
    'sass:compile',
    'uglify:compile',
    'watch'
  ]);
  grunt.registerTask('production', [
    'sass:compile',
    'sass:minify',
    'uglify:compile'
  ]);

};
