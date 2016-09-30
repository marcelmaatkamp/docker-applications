/**
 * Created by Ab on 9-4-2015.
 */

var gulp = require('gulp');
var del = require('del');
var merge = require('merge2');
var ts = require('gulp-typescript');
var tslint = require('gulp-tslint');
var sourcemaps = require('gulp-sourcemaps');

// swallow errors in watch
function swallowError (error) {

  //If you want details of the error in the console
  console.log(error.toString());

  this.emit('end');
}

//define typescript project
var tsProject = ts.createProject({
  module: 'commonjs',
  target: 'ES5',
  declaration: true
});

gulp.task('default', ['build:clean']);

gulp.task('build', ['compile']);
gulp.task('build:clean', ['clean', 'compile']);

gulp.task('watch', ['clean', 'build'], function () {
  gulp.watch('code/**/*.ts', ['build']);
});

gulp.task('clean', function (cb) {
  del.sync([
    'code/**/*.js',
    'code/**/*.js.map'
  ]);
  cb();
});

gulp.task('compile', function () {
  // compile typescript
  var tsResult = gulp.src('code/**/*.ts')
    .pipe(tslint({
      configuration: 'tools/tslint/tslint-node.json'
    }))
    .pipe(tslint.report('prose', {
      emitError: false
    }))
    .pipe (sourcemaps.init())
    .pipe (ts(tsProject));

  return merge([
    tsResult.js
      .pipe(sourcemaps.write('.', {
        includeContent: false,
        sourceRoot: '../code/'
      }))
      .pipe(gulp.dest('code')) //,
    // tsResult.dts.pipe(gulp.dest('src'))
  ]);
});

gulp.task('lint', function () {
  return gulp.src('code/**/*.ts')
    .pipe(tslint({
      configuration: 'tools/tslint/tslint-node.json'
    }))
    .pipe(tslint.report('full'));
});
