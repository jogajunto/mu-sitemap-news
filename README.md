# WP Sitemap News

A class that embeds a Provider as a "minified" version of the default WordPress Providers, for the creation and management of sitemaps.

# Usage

## Install

Update the installer-paths in the main project's composer.json: Adjust or add an installation path in installer-paths that directs packages of type wordpress-muplugin to the mu-plugins folder.

```json
"extra": {
    "installer-paths": {
        "public/wp-content/mu-plugins/{$name}/": ["type:wordpress-muplugin"]
    }
}
```

Add the repository and the package to your main project: In your main composer.json, add the repository containing the class and then require it as a dependency.

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/jogajunto/mu-sitemap-news"
    }
],
"require": {
    "jogajunto/mu-sitemap-news": "version"
}
```

Add scripts in composer.json your main project to create load.php in mu-plugins canical folder
```json
"scripts": {
    "post-install-cmd": [
      "php -r \"file_put_contents('public/wp-content/mu-plugins/load.php', '<?php\rrequire WPMU_PLUGIN_DIR . \\'/mu-sitemap-news/wp_sitemap_news.php\\';');\r\""
    ],
    "post-update-cmd": [
      "php -r \"file_put_contents('public/wp-content/mu-plugins/load.php', '<?php\rrequire WPMU_PLUGIN_DIR . \\'/mu-sitemap-news/wp_sitemap_news.php\\';');\r\""
    ]
},
```

## Add in hook init WordPress for create sitemap to Google news

Em seu arquivo `<CURRENT_THEME>/functions.php` adicione o hook init com a instrução
```php
/**
 * Register custom sitemap to google news
 * 
 * Class inserted by mu-plugins.
 */
function register_news_sitemap_provider() {
  if (class_exists('WP_Sitemap_News')) {
    // Register new provider of sitemaps news
    new WP_Sitemap_News();
  } else {
    error_log('The WP_Sitemap_News class was not found.');
  }
}
add_action('init', 'register_news_sitemap_provider', 100);
```