var gulp         = require('gulp'),
    sass         = require('gulp-sass'),
    plumber      = require('gulp-plumber'),
    notify       = require('gulp-notify');

var config = {
    src           : './web/assets/scss/*.scss',
    dest          : './web/assets/css/'
};

// Error message
var onError = function (err) {
    notify.onError({
        title   : 'Gulp',
        subtitle: 'Failure!',
        message : 'Error: <%= error.message %>',
        sound   : 'Beep'
    })(err);

    this.emit('end');
};

// Compile CSS
gulp.task('styles', function () {
    var stream = gulp
        .src([config.src])
        .pipe(plumber({errorHandler: onError}))
        .pipe(sass().on('error', sass.logError));

    return stream
        .pipe(gulp.dest('./web/assets/css/'));
});