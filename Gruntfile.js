module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      compile: {
        files: {
          'public/assets/app.css': 'app/assets/stylesheets/app.scss'
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
    watch: {
      options: {
        livereload: true,
      },
      sass: {
        files: ['**/*.scss'],
        tasks: ['sass:compile'],
        debugInfo: true
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['sass:compile', 'watch']);
  grunt.registerTask('production', ['sass:compile', 'sass:minify']);

};
