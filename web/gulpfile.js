
var gulp = require('gulp');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
//var minifycss = require('gulp-minify-css');
var ngAnnotate = require('gulp-ng-annotate');
//var minifyhtml = require('gulp-minify-html');

//var browserSync = require('browser-sync').create();

var DEST_JS = 'js/';
var DEST_CSS = 'css/';
var DEST_HTML = 'templates/';


gulp.task('scripts', function() {
    return gulp.src(['js/services.js'])
            // This will output the non-minified version
            //.pipe(gulp.dest(DEST))
            // This will minify and rename to all.min.js
            .pipe(concat('all.js'))
            .pipe(ngAnnotate())
            .pipe(uglify())
            .pipe(rename({ extname: '.min.js' }))
            .pipe(gulp.dest(DEST_JS))
});

gulp.task('default', ['scripts'], function() {

    // watch for JS changes
    //gulp.watch('./js/*.js', ['scripts']);
});