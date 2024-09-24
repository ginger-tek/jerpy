<div align=center>
  <h1>jerpy</h1>
  <i>"The little CMS that could!"</i>
</div>
<hr>

Jerpy is one of the smallest, flat-file PHP content management systems (CMS) built for control and simplicity that is easy to installs, customize, and maintain.

**This was built to be as streamlined and stripped-down as possible, so it's meant to be administered directly via the files and there's no admin web panel.**

# Getting Started
## Composer
Jerpy is super easy to get setup. Simply run the following to get started:
```
composer create-project ginger-tek/jerpy <directory>
```

# File/Folder Structure
## `config.php`
All site settings are set in the `config.php` file, including timezone, selected layout, enabled plugins, and page routes.

## Layouts
The `layouts` directory stores layout templates, each their own `.php` file. The default global theme is set in `config.php` via the `$layout` property.
```
🗀 layouts
  🗋 default.php
```
```php
$layout = 'default';
```

## Assets
This is the global assets directory, in which you can organize your CSS, JavaScript, fonts, and images to use in your layouts and pages via absolute URI:
```
🗀 assets
  🗀 css
    🗋 styles.css
```
```html
<head>
  ...
  <link href="/assets/css/styles.css" rel="stylesheet">
  ...
</head>
```

## Pages & Routes
The `pages` directory stores the page contents for the site, and are configured for each route in `config.php`.
```
🗀 pages
  🗋 home.php
```
```php
$routes = [
  '/' => 'pages/home.php'
];
```
You can define a route as a key with either a string pointing to the file, or an associative array with a `page` key and other options to change how the page is rendered.

To use a different layout than the default, set the `layout` option:
```php
$routes = [
  '/about' => [
    'page' => 'pages/about.php',
    'layout' => 'layouts/layout2.php'
  ]
];
```
If you don't want any layout to be used at all, set the `layout` option to `false`, which will just render the page by itself.

Additional arbitrary properties can also be set for metadata/SEO purposes:
```php
$routes = [
  '/about' => [
    'page' => 'pages/about.php',
    'title' => 'About',
    'thumbnail' => '/assets/my_thumbnail.png'
  ]
];
```
You can then implement your additional properties in your layout, such as for social media SEO tags. Use the `@` warning suppressing syntax for when some routes don't have the property specified:
```html
<head>
  ...
  <meta name="og:title" content="<?= $title ?>">
  <meta name="og:description" content="<?= @$description ?>">
  <meta name="og:image" content="<?= @$thumbnail ?>">
  ...
</head>
```

### Dynamic Routes
You can also specify non-static matching routes for the key string. Use the `:param` syntax to dynamically match a route and have its parameters set to the parsed values from the incoming URI:
```php
$routes = [
  '/products/:id' => 'pages/product.php'
];
```
```php
<p>ID: <?= $req->params->id ?></p>
```

# Templating
PHP's built-in templating is still sufficient for most user-cases nowadays. As such, just use `include` and `require` as you would normally for templating your site, parsing content as needed (see [plugins](#plugins) below).

## Global Variables
There are a couple of global variables you can always reference in a layout or page file: `$req` and `$page`
|Name|Data Type|Note|
|---|---|---|
|`$req`|`object`|The current request, contains properties `uri`, `method` (HTTP Method), `query` (object of URL query parameters), and `params` (object of dynamic route parameters)|
|`$page`|`string`|Path to the page file being rendered|

# Plugins
Plugins can be made to extend or add functionality to Jerpy. They do not require any specific framework nor follow any particular design pattern. The only requirement for plugins is that the entrypoint is a `.php` file with the same name as the plugin's folder. From there, you can use whatever preferred tools and package managers to create the plugin code.
```
🗀 plugins
  🗀 myPlugin <-- plugin dir
    🗋 composer.json
    🗋 myPlugin.php <-- entrypoint (same as plugin dir)
    🗀 vendor
      🗋 someSupportingFile.php
```

Plugins are loaded globally, and their top-level objects, functions, and/or classes are accessible from all layouts and pages.

To add a plugin, simply copy/upload the plugin's folder to the `plugins` directory.

To enable a plugin, add it's folder name to the `$plugins` array in `config.php`.

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

`config.php`
```php
$plugins = [
  'md'
];
```

`pages/some-page.php`
```php
<?= md('path/to/markdown-file.md') ?>
```
