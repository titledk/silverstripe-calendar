
var gulp = require('gulp');
var $ = require('gulp-load-plugins')();


gulp.task('build-css', function() {
	gulp.src('./scss/**/*')
		.pipe($.sass({
			includePaths : ['./scss']
		})
			.on('error', $.sass.logError))
		.pipe(gulp.dest('./css'));

});

gulp.task('watch', function() {
	gulp.watch('./scss/**/*', ['build-css']);
});

gulp.task('default', [
	'build-css',
	'watch'
]);