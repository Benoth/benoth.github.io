let mix = require('laravel-mix');

mix.sass('src/sass/main.scss', 'dist/css')
   .js('src/js/main.js', 'dist/js')
   .setPublicPath('dist');

mix
  .browserSync({
    open:  false,
    proxy: 'localhost:8080'
  })
  .options({
    processCssUrls: false
  })
  .sourceMaps()
  .disableNotifications();
;
