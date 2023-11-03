const gulp = require("gulp")
const less = require("gulp-less")
const cssClean = require("gulp-clean-css")
const autoprefixer = require("gulp-autoprefixer")
const eslint = require("gulp-eslint")
const imagemin = require("gulp-imagemin")
const concat = require("gulp-concat")
const babel = require("gulp-babel")
const uglify = require("gulp-uglify")

const SRC_DIR = "resources"
const DEST_DIR = "www/assets/front"
const NPM_DIR = "node_modules"

const JS_CONCAT_FILES = [
  `${NPM_DIR}/jquery/dist/jquery.min.js`,
  `${NPM_DIR}/select2/dist/js/select2.min.js`,
  `${NPM_DIR}/select2/dist/js/i18n/cs.js`,
  `${NPM_DIR}/bxslider/dist/jquery.bxslider.min.js`,
  `${NPM_DIR}/tooltipster/dist/js/tooltipster.bundle.min.js`,
  `${NPM_DIR}/nouislider/distribute/nouislider.min.js`,
  `${NPM_DIR}/slick-carousel/slick/slick.min.js`,
  `${DEST_DIR}/js/main.min.js`,
]

function buildCSS() {
  return gulp
    .src([`${SRC_DIR}/less/main.less`])
    .pipe(
      less({
        paths: [NPM_DIR],
      })
    )
    .pipe(autoprefixer())
    .pipe(concat("main.css"))
    .pipe(gulp.dest(`${DEST_DIR}/css`))
}

function minifyCSS() {
  return gulp
    .src(`${DEST_DIR}/css/main.css`)
    .pipe(cssClean())
    .pipe(concat("main.min.css"))
    .pipe(gulp.dest(`${DEST_DIR}/css`))
}

function minifyJS() {
  return gulp
    .src(`${SRC_DIR}/js/**/*.js`)
    .pipe(babel())
    .pipe(uglify())
    .pipe(concat("main.min.js"))
    .pipe(gulp.dest(`${DEST_DIR}/js`))
}

function concatJS() {
  return gulp
    .src(JS_CONCAT_FILES)
    .pipe(concat("main.min.js"))
    .pipe(gulp.dest(`${DEST_DIR}/js`))
}

function watchCSS() {
  gulp.watch(`${SRC_DIR}/less/**/*.less`, gulp.parallel("build-css"))
}

//Build Tasks
gulp.task("build-css", gulp.series(buildCSS, minifyCSS))
gulp.task("build-js", gulp.series(minifyJS, concatJS))

//Dev Tasks
gulp.task("watch", gulp.parallel(watchCSS))
