# jerpy

## The smallest, flat-file CMS around!
Jerpy is a flat-file PHP CMS built for control and simplicity that is easy to install, customize, and maintain.
**The whole templating engine is under 1700 characters of PHP code, and the data structure is only 4 directories and 2 files.**

This was built to be as streamlined and stripped-down as possible, so it's meant to be administered directly via the files and there's no admin web panel.

# Getting Started
## Composer
Jerpy is super easy to get setup. Simply run the following to download and extract, then copy or rename `config.sample.json` to `config.json`.
```
composer create-project ginger-tek/jerpy <directory>
```

# File Structure
There are 3 directories for content, one directory for plugins, and one file for configuration. The `pages`, `layouts`, and `assets` directories will hold page contents, layout templates, and site assets, respectively.

## Pages
The `pages` directory stores the page contents for the site, and can be referenced as a file path on the `file` property of a route.
```
🗀 pages
  🗋 home.php
```
```json
{
  "routes": {
    "/": {
      "title": "Home",
      "file": "pages/home.php"
    }
  }
}
```

## Layouts
The `layouts` directory stores layout templates, each their own `.php` file. The default global theme is set in `config.json` on the `layout` property.
```
🗀 layouts
  🗋 default.php
```
```json
{
  "layout": "default"
}
```

## Assets
This is the global assets directory, in which you can organize your CSS, JavaScript, fonts, and images to use in your layouts and pages via absolute URI:
```
🗀 assets
  🗀 css
    🗋 styles.css
```
```html
<link href="/assets/css/styles.css" rel="stylesheet">
```

# Routes
Routes are defined separately from pages to easily manage the content and access of each route.

## Route Properties
Each route is defined as a key on the `routes` property in `config.json` whose value is an object with properties that define the route's page title and content.

|Name|Data Type|Required?|Note|
|---|---|---|---|
|`title`|`string`|Yes|Page title|
|`file`|`string`|Conditional|Required if `body` not set. Overwrites `body` value with rendered content|
|`body`|`string`|Conditional|Required if `file` not set. Throws error if neither `file` and `body` set|
|`layout`|`string`|No|If set to valid path, will override default `layout`. If set to false, no layout is used and page body is echoed as is|

Additional arbitrary properties can be set for metadata/SEO purposes, but the title and file/body properties are the only necessary properties:
```json
{
  "routes": {
    "/": {
      "title": "Page Title",
      "file": "pages/page.php",
      "description": "This is a description of the page for SEO",
      "thumbnail": "/assets/img/seo.jpg"
    }
  }
}
```
You can then implement your additional properties in your layout, such as for social media SEO tags. Use the `@` warning suppressing syntax for when some routes don't have the property specified:
```html
<head>
  <title><?= $config->siteName ?> - <?= $page->title ?></title>
  <meta name="og:title" content="<?= $page->title ?>">
  <meta name="og:description" content="<?= @$page->description ?>">
  <meta name="og:image" content="<?= @$page->thumbnail ?>">
</head>
```

## Dynamic Routes
You can also specify non-static matching routes for the key string. Use the `:param` syntax to dynamically match a route and have its parameters set to the parsed values from the incoming URI:
```json
{
  "routes": {
    "/products/:id": {
      "file": "pages/product.php"
    }
  }
}
```
```php
<p>ID: <?= $req->params['id'] ?></p>
```

# Templating
Page files are included, rendered on the buffer, and their output is assigned to `$page->body`. To include the page content in a template, simply echo the value of `$page-body`:
```php
<body>
  <?= $page->body ?>
</body>
```

Aside from page content, Jerpy relies on the built-in templating functionality of PHP, so use `include` and `require` as you would normally for everything else, parsing content as needed (see [plugins](#plugins) below).

## Global Variables
There are 4 global variables you can use in a layout or page file: `$config`, `$req`, `$page`, and `$assets`.
|Name|Data Type|Note|
|---|---|---|
|`$config`|`object`|The stdClass object of `config.json`|
|`$req`|`object`|The current request, contains properties `path` (string of URI), `method` (string of HTTP method), `query` (object of URL query parameters), and `params` (object of dynamic URI parameters)|
|`$page`|`object`|Contains the `body` content property, as well as all the properties defined by the route object|

# Config.json
```json
{
  "siteName": "My Site",
  "maintenance": false,
  "layout": "default",
  "routes": {}
}
```
|Name|Data Type|Note|
|---|---|---|
|`siteName`|`string`|Name for site|
|`maintenance`|`boolean`|Toggle site-wide maintenance mode. When true, returns HTTP 503 for all routes|
|`layout`|`string`|The default layout to use for routes|
|`routes`|`object`|Key:value object of all defined routes|

# Plugins
Plugins can be made for Jerpy, but they do not follow any specific framework or design pattern. This is left up to the developer to ensure that the plugin works and tests succesfully with all the existing features of Jerpy.

The only requirements for plugins are that the entrypoint that is included globally at runtime must be a `.php` file with the same name as the plugin's folder:
```
🗀 plugins
  🗀 myPlugin <-- plugin dir
    🗋 myPlugin.php <-- entrypoint (same as plugin dir)
    🗀 vendor
      🗋 someSupportingFile.php
```

Plugins are loaded globally and their top-level objects, functions, and/or classes are made accessible in templates and pages.

To add a plugin, simply copy/upload the plugin's folder to the `plugins` directory.

Below is an example plugin for using Parsedown via a wrapper method:

`plugins/md/md.php`
```php
<?php

require 'vendor/Parsedown.php';

function md($p)
{
  $pd = new \Parsedown();
  return $pd->text(file_get_contents($p));
}
```

`pages/some-page.php`
```php
<?= md('path/to/markdown-file.md') ?>
```
