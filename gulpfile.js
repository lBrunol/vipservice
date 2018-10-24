'use strict';

var gulp = require('gulp');
var postcss = require('gulp-postcss');
var autoprefixer = require('autoprefixer');
var cssnano = require('cssnano');
var stylus = require('gulp-stylus');
var sourcemaps = require('gulp-sourcemaps');
var plumber = require('gulp-plumber');
var uglify = require('gulp-uglify');
var pump = require('pump');

gulp.task('watch', function(){
    gulp.watch('assets/stylus/**/*.styl', ['stylus']);
    gulp.watch('assets/js/*.js', ['javascript']);
});

gulp.task('stylus', function(){
    return gulp.src('./assets/stylus/style.styl')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(stylus())
        .pipe(postcss([
            autoprefixer({ browsers: ['last 10 versions', '> 1%', 'ie 9'] }),
            cssnano()
        ]))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./css/'));
});

gulp.task('javascript', function () {
    pump([
        gulp.src('./assets/js/*.js'),
        // uglify(),
        gulp.dest('./js')
    ]);
});

gulp.task('default', ['watch']);