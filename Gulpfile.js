/**
 * 文件批处理配置
 * powered by nodejs
 */
var gulp = require('gulp'),
    minifycss = require('gulp-minify-css'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    del = require('del'),
    replace = require('gulp-replace');

gulp.task('default', function() {
    gulp.start('desktop');
});
/**
 * 处理desktop 资源文件合并、压缩
 */
gulp.task('desktop', function() {

/**
 * CSS相关
 */
    gulp.src(['public/desktop/com/global/plugins/bootstrap-summernote/summernote.css'])
    .pipe(replace('font/','fonts/'))
    .pipe(gulp.dest('public/desktop/com/global/plugins/bootstrap-summernote/'));

    var desktop_css_list = [
        'public/desktop/com/global/plugins/font-awesome/css/font-awesome.min.css',
        'public/desktop/com/global/plugins/simple-line-icons/simple-line-icons.min.css',
        'public/desktop/com/global/plugins/bootstrap/css/bootstrap.min.css',
        'public/desktop/com/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css',
        'public/desktop/com/global/plugins/bootstrap-datepicker/css/datepicker3.css',
        'public/desktop/com/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
        'public/desktop/com/global/plugins/bootstrap-select/bootstrap-select.min.css',
        'public/desktop/com/global/plugins/bootstrap-summernote/summernote.css',
        'public/desktop/com/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css',
        'public/desktop/com/global/plugins/bootstrap-toastr/toastr.min.css',
        'public/desktop/com/global/plugins/uniform/css/uniform.default.css',
        'public/desktop/com/global/plugins/select2/select2.css',
        'public/desktop/com/global/plugins/jstree/themes/default/style.css',
        'public/desktop/com/global/plugins/jquery-file-upload/css/jquery.fileupload.css',
        'public/desktop/com/global/plugins/jquery-tags-input/jquery.tagsinput.css',
        'public/desktop/com/global/css/components.css',
        'public/desktop/com/global/css/plugins.css',
        'public/desktop/com/admin/layout/css/layout.css',
        'public/desktop/com/admin/layout/css/themes/default.css'
    ];

    var desktop_font_dir1 = [
        'public/desktop/com/global/plugins/simple-line-icons/fonts/*',
        'public/desktop/com/global/plugins/bootstrap-summernote/font/*',
    ];

    var desktop_font_dir2 = [
        'public/desktop/com/global/plugins/font-awesome/fonts/*',
        'public/desktop/com/global/plugins/bootstrap/fonts/*',
    ];

    console.log('process',desktop_css_list);
    //todo 清除
    //合并压缩css
    gulp.src(desktop_css_list)
    .pipe(concat('desktop-theme.css'))
    .pipe(minifycss())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('public/desktop/stylesheets'));
    //移动图标字体
    console.log('process',desktop_font_dir1);
    gulp.src(desktop_font_dir1)
    .pipe(gulp.dest('public/desktop/stylesheets/fonts'));
    console.log('process',desktop_font_dir2);
    gulp.src(desktop_font_dir2)
    .pipe(gulp.dest('public/desktop/fonts'));
    console.log('ok!');

/**
 * script 相关
 */
    var desktop_script_list = [
        'public/desktop/com/global/plugins/jquery.min.js',
        'public/desktop/com/global/plugins/jquery-migrate.min.js',
        'public/desktop/com/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js',
        'public/desktop/com/global/plugins/bootstrap/js/bootstrap.min.js',
        'public/desktop/com/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js',
        'public/desktop/com/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js',
        'public/desktop/com/global/plugins/jquery.blockui.min.js',
        'public/desktop/com/global/plugins/jquery.cokie.min.js',
        'public/desktop/com/global/plugins/uniform/jquery.uniform.min.js',
        'public/desktop/com/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js',
        'public/desktop/com/global/plugins/flot/jquery.flot.min.js',
        'public/desktop/com/global/plugins/flot/jquery.flot.resize.min.js',
        'public/desktop/com/global/plugins/flot/jquery.flot.categories.min.js',
        'public/desktop/com/global/plugins/jquery.pulsate.min.js',
        'public/desktop/com/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js',
        'public/desktop/com/global/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.zh-CN.js',
        'public/desktop/com/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js',
        'public/desktop/com/global/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js',
        'public/desktop/com/global/plugins/bootbox/bootbox.min.js',
        'public/desktop/com/global/plugins/bootstrap-toastr/toastr.min.js',
        'public/desktop/com/global/plugins/bootstrap-select/bootstrap-select.min.js',
        'public/desktop/com/global/plugins/select2/select2.min.js',
        'public/desktop/com/global/plugins/jstree/jstree.js',
        'public/desktop/com/global/plugins/bootstrap-summernote/summernote.js',
        'public/desktop/com/global/plugins/bootstrap-summernote/lang/summernote-zh-CN.js',
        'public/desktop/com/global/plugins/jquery-tags-input/jquery.tagsinput.min.js',
        'public/desktop/com/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js',
        'public/desktop/com/global/plugins/jquery-file-upload/js/jquery.fileupload.js',
        'public/desktop/com/global/scripts/metronic.js',
        'public/desktop/com/admin/layout/scripts/layout.js',
        'public/desktop/desktop.js',
    ];
    console.log('process',desktop_script_list);
    gulp.src(desktop_script_list)
    .pipe(concat('desktop-script.js'))
    .pipe(uglify())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('public/desktop/javascripts'));
    console.log('ok!');


});
