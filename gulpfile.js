const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');

gulp.task('default', ['sass']);

gulp.task('sass', function () {
    return gulp.src('./public/src/scss/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./public/assets/css'));
});

gulp.task('sass:watch', function () {
    gulp.watch('./public/src/scss/**/*.scss', ['sass']);
});