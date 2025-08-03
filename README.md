<div align=center>
  <div style="font-size:2em;font-weight:bold">jerpy</div>
  <p>Simple - Extendable - Flat-file</p>
</div>
<hr>

Jerpy a simple, extendable, flat-file simple website system built for control and ease-of-use that is easy to install, customize, and maintain.

**NOTE: Jerpy isn't a traditional CMS and doesn't have a management interface or web portal (but there *could* be a [plugin](#plugins) for that...). Everything is managed directly via the files themselves.**

# Getting Started
## Composer
```
composer create-project ginger-tek/jerpy <directory>
```

# Files & Folders
- ## `config.php`
  Set the timezone override, selected layout, enabled global plugins, and page routes here.
- ## `layouts`
  Stores layout templates, each their own `.php` file. The default global theme is set in `config.php` via the `$layout` property. The value is just the file name with no extension.
- ## `assets`
  Organize your CSS, JavaScript, fonts, and images to use in your layouts and pages via absolute URI here
- ## `content`
  For all your embedded content files, such as Markdown text files, and is not URL-accessible.

## Pages & Routes
Routes are configured in an associative array of a route key and page value. The value can be either a string or an associative array. If a string, the value is rendered using the default template, and the string is expected to be the filename of a page in the pages folder. If an associative array, there must be at least a `page` key and value. Optionally, a `meta` key and value can be set to include metadata for the given route, as well as a `layout` key and value to override the default layout, or not use one at all.

Example routes config:
```php
$routes = [
  '/' => [
    'page' => 'home.php',
    'meta' => [
      'title' => 'Welcome to my site!'
    ]
  ],
  '/about' => [
    'page' => 'about.php',
    'meta' => [
      'title' => 'About Us',
      'thumbnail' => '/assets/my_thumbnail.png'
    ],
    'layout' => 'layout_2'
  ],
  '/simple/page' => 'simple_page.php',
  '/page/without/layout' => [
    'page' => 'some_page.php',
    'layout' => false
  ]
]
```

When implementing metadata, use null coalescing syntax to avoid warnings when a route does't have that metadata property specified:
```html
<head>
  ...
  <meta name="og:title" content="<?= $meta['title'] ?? '' ?>">
  <meta name="og:description" content="<?= $meta['description'] ?? '' ?>">
  <meta name="og:image" content="<?= $meta['thumbnail'] ?? '' ?>">
  ...
</head>
```

## Dynamic Routes
To use dynamic route parameters, use the `:param` syntax in the route key string. All matches values will be accessible from the `$params` variable:
```php
$routes = [
  '/products/:id' => [
    'page' => 'product.php'
  ]
];
```
`product.php`:
```php
<p>ID: <?= $params['id'] ?></p>
```

# Templating
PHP's built-in templating is sufficient for most websites. As such, just use `include` and `require` as you would normally for templating your site, parsing content as needed (see [plugins](#plugins)).

# Global Variables
|Name|Data Type|Note|
|---|---|---|
|`$uri`|`object`|Clean URI value|
|`$params`|`array`|Any metadata key/values specified for the matched route|
|`$page`|`string`|Path to the page file being rendered|
|`$meta`|`array`|Any metadata key/values specified for the matched route|
|`$layout`|`array`|Current layout|

# Plugins
Plugins can be created to extend or add functionality to Jerpy. They do not require any specific framework nor follow any particular design pattern. The only requirement for plugins is that the entrypoint is a `.php` file with the same name as the plugin's folder. From there, you can use whatever preferred tools and package managers to create the plugin code, such as Composer.

```
🗀 plugins
  🗀 myPlugin <-- plugin dir
    🗋 composer.json
    🗋 myPlugin.php <-- entrypoint (same name as plugin dir)
    🗀 vendor
      🗀 someSupportingPackage
```

## Enabling Plugins
Plugins can be included/required on a given page file as needed, or you can load it globally to be used on every page. To add a plugin, simply copy/upload the plugin's folder to the `plugins` directory.

To enable a plugin globally, add it's folder name to the `$plugins` array in `config.php`:
```php
$plugins = [
  'md'
];
```
To enable a plugin only when a certain URI is matched, set a string key to  check if the incoming URI starts with it:
```php
$plugins = [
  '/admin' => 'admin' // only loaded when /admin* is requested
];
```

## Plugin Example
Below is an example plugin for using Parsedown via a wrapper method:

**NOTE: When including/requiring files within a plugin, make sure to use the `__DIR__` global to ensure PHP looks *within* the plugin directory and not in the root directory of the site**

`plugins/md/md.php`
```php
<?php

require __DIR__ . '/vendor/autoload.php';

function md(string $path): string
{
  return (new Parsedown)->text(file_get_contents($path));
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
<?= md('content/markdown-file.md') ?>
```
