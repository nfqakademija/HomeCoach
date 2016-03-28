'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

var dir = {
    assets: './app/Resources/',
    dist: './web/',
    node : './node_modules/'
};

gulp.task('styles', function() {
    gulp.src(dir.assets + 'style/*.css')
        .pipe(gulp.dest(dir.dist + 'css'));
});

gulp.task('scripts',function()
{
    gulp.src(dir.assets + 'scripts/*.js')
        .pipe(gulp.dest(dir.dist +'js'));
});

gulp.task('assets',function()
{
    gulp.src(dir.node + 'angular/angular.min.js')
        .pipe(gulp.dest(dir.dist + 'assets'));
    gulp.src(dir.node +'bootstrap/dist/css/bootstrap.min.css')
        .pipe(gulp.dest(dir.dist +'assets'));
    gulp.src(dir.node + 'bootstrap/dist/js/bootstrap.min.js')
        .pipe(gulp.dest(dir.dist +'assets'));
    gulp.src(dir.node + 'jquery/dist/jquery.min.js')
        .pipe(gulp.dest(dir.dist + 'assets'));
});

gulp.task('fonts',function()
{
   gulp.src(dir.node +'bootstrap/fonts/glyphicons-halflings-regular.ttf')
       .pipe(gulp.dest(dir.dist +'fonts'));
    gulp.src(dir.assets + 'fonts/*.ttf')
        .pipe(gulp.dest(dir.dist +'fonts'));
});

gulp.task('default', ['styles', 'scripts', 'assets','fonts']);




