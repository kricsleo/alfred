const gulp = require('gulp');
const $ = require('gulp-load-plugins')();
const del = require('del');

const file = 'size.alfredworkflow';

const clean = () => del(['dist']);

const compress = () =>
  gulp.src('dist/*')
    .pipe($.zip(file))
    .pipe(gulp.dest('./'));

const copy = () =>
  gulp.src(['info.plist', 'search.php', 'icon.png'])
    .pipe(gulp.dest('dist'));

const prepare = () => del(['dist', file]);

exports.clean = clean;
exports.compress = compress;
exports.copy = copy;
exports.prepare = prepare;

exports.default = gulp.series(prepare, copy, compress, clean);
