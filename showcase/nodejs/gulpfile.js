/**
 * Created by Ab on 9-4-2015.
 */

var gulp = require('gulp');
var del = require('del');
var merge = require('merge2');
var ts = require('gulp-typescript');
var tslint = require('gulp-tslint');
var sourcemaps = require('gulp-sourcemaps');
var mocha = require('gulp-spawn-mocha');

// swallow errors in watch
function swallowError(error) {

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

gulp.task('build', ['compile', 'test']);
gulp.task('build:clean', ['clean', 'compile', 'test']);

gulp.task('watch', ['clean', 'build'], function () {
  gulp.watch('source/**/*.ts', ['build']);
});

gulp.task('clean', function (cb) {
  del.sync([
    'coverage',
    'result'
  ]);
  cb();
});

gulp.task('compile', function () {
  // compile typescript
  var tsResult = gulp.src('source/**/*.ts')
    .pipe(tslint({
      formatter: 'prose',
      configuration: 'tslint.json'
    }))
    .pipe(tslint.report({
      emitError: false
    }))
    .pipe(sourcemaps.init())
    .pipe(tsProject());

  return merge([
    tsResult.js
      .pipe(sourcemaps.write('.', {
        includeContent: false,
        sourceRoot: '../source/'
      }))
      .pipe(gulp.dest('result')) //,
    // tsResult.dts.pipe(gulp.dest('src'))
  ]);
});

gulp.task('lint', function () {
  return gulp.src('source/**/*.ts')
    .pipe(tslint({
      configuration: 'tslint.json'
    }))
    .pipe(tslint.report('full'));
});

// unit tests, more a fast integration test because at the moment it uses an external AMQP server
gulp.task('test', ['compile'], function () {
  return gulp.src(['result/test/**/*.spec.js'], {
    read: false
  })
    .pipe(mocha({
      r: 'tools/mocha/setup.js',
      reporter: 'dot' // 'spec', 'dot'
    }))
    .on('error', swallowError);
});

gulp.task('test:coverage', ['compile'], function () {
  return gulp.src('result/test/**/*.spec.js', {
    read: false
  })
    .pipe(mocha({
      reporter: 'spec', // 'spec', 'dot'
      istanbul: true
    }));
});