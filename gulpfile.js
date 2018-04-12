/**
 * Gulpfile.
 *
 * A simple implementation of Gulp.
 *
 * Implements:
 * 			1. Live reloads browser with BrowserSync
 * 			2. CSS: Sass to CSS conversion, Autoprixing, Sourcemaps, CSS minification.
 * 			3. JS: Concatenates & uglifies Vendor and Custom JS files.
 * 			4. Images: Minifies PNG, JPEG, GIF and SVG images.
 * 			5. Watches files for changes in CSS or JS
 *
 * @since 1.0.0
 * @author Ahmad Awais (@mrahmadawais)
 */

 /**
  * Configuration.
  *
  * Project Configuration for gulp tasks.
  *
  * In paths you can add <<glob or array of globs>>
  *
  * Edit the variables as per your project requirements.
  */

var styleSRC            = './css/media.css'; // Path to main .scss file.
var styleDestination    = './css/'; // Path to place the compiled CSS file.
								// Defualt set to root folder.



/**
 * Load Plugins.
 *
 * Load gulp plugins and assing them semantic names.
 */
var gulp         = require('gulp'); // Gulp of-course

// CSS related plugins.
var minifycss    = require('gulp-uglifycss'); // Minifies CSS files

// Utility related plugins.
var rename       = require('gulp-rename'); // Renames files E.g. style.css -> style.min.css


/**
 * Task: `styles`.
 *
 * Compiles Sass, Autoprefixes it and Minifies CSS.
 *
 * This task does the following:
 * 		1. Gets the source scss file
 * 		2. Compiles Sass to CSS
 * 		3. Writes Sourcemaps for it
 * 		4. Autoprefixes it and generates style.css
 * 		5. Renames the CSS file with suffix .min.css
 * 		6. Minifies the CSS file and generates style.min.css
 * 		7. Injects CSS or reloads the browser via browserSync
 */
gulp.task('styles', function () {
 	gulp.src( styleSRC )
		//.pipe( sourcemaps.init() )
		// .pipe( sass( {
		// 	errLogToConsole: true,
		// 	outputStyle: 'compact',
		// 	//outputStyle: 'compressed',
		// 	// outputStyle: 'nested',
		// 	// outputStyle: 'expanded',
		// 	precision: 10
		// } ) )
		//.pipe(sass().on('error', sass.logError))
		//.pipe( sourcemaps.write( { includeContent: false } ) )
		//.pipe( sourcemaps.init( { loadMaps: true } ) )
		//.pipe( autoprefixer( AUTOPREFIXER_BROWSERS ) )

		//.pipe( sourcemaps.write ( styleDestination ) )
		//.pipe( gulp.dest( styleDestination ) )


		.pipe( rename( { suffix: '.min' } ) )
		.pipe( minifycss( {
			maxLineLen: 10
		}))
		.pipe( gulp.dest( styleDestination ) )
		//.pipe( browserSync.stream() )
		//.pipe( notify( { message: 'TASK: "styles" Completed!', onLast: true } ) )
});



 /**
  * Watch Tasks.
  *
  * Watches for file changes and runs specific tasks.
  */
 gulp.task( 'default', ['styles'], function () {
 	gulp.watch( styleSRC, [ 'styles' ] );
 	//gulp.watch( vendorJSWatchFiles, [ 'vendorsJs', reload ]  );
 	//gulp.watch( customJSWatchFiles, [ 'customJS', reload ]  );
 });