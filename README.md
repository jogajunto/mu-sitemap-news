# WP Sitemap News

A class that embeds a Provider as a "minified" version of the default WordPress Providers, for the creation and management of sitemaps.

# Usage

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