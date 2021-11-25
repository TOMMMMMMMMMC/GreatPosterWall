const path = require('path')
const mix = require('laravel-mix')

mix
  .setPublicPath('public')
  .sourceMaps(false)
  .webpackConfig({
    resolve: {
      modules: ['node_modules', path.resolve(__dirname, 'src/js')],
    },
  })
  .options({
    processCssUrls: false,
  })
  .copy(
    'src/css/gpw_dark_mono/assets/',
    'public/css/gpw_dark_mono/assets/',
    false
  )
  .copy(
    'node_modules/tooltipster/dist/js/tooltipster.bundle.min.js',
    'public/js'
  )
  .copy(
    'node_modules/tooltipster-discovery/tooltipster-discovery.min.js',
    'public/js'
  )
  .postCss('src/css/gpw_dark_mono/style.css', 'css/gpw_dark_mono', [
    require('cssnano'),
  ])
  .postCss('src/css/global-bundle.css', 'css', [require('cssnano')])
  .js('src/js/pages/global.js', 'js')
  .js('src/js/pages/upload/index.js', 'js/upload')
  .js('src/js/pages/torrents/index.js', 'js/torrents')
  .js('src/js/pages/wiki/index.js', 'js/wiki')
  .js('src/js/pages/sitehistory/index.js', 'js/sitehistory')
