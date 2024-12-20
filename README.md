<div align=center>
  <h1>jerpy</h1>
  <i>"The little CMS that could!"</i>
</div>
<hr>

Jerpy is one of the smallest, flat-file PHP content management systems (CMS) built for control and simplicity that is easy to installs, customize, and maintain.

**This was built to be as streamlined and stripped-down as possible, so it's meant to be administered directly via the files and there's no admin web panel.**

# Getting Started
## Composer
Jerpy is super easy to get setup. Simply run the following to create a new project:
```
composer create-project ginger-tek/jerpy <directory>
```

# File/Folder Structure
## Configuration
All site settings are set in the `config.php` file, including timezone override, selected layout, enabled plugins, and page routes.

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

## Media
The `media` directory is for any and all URL-accessible files.
  
## Content
The `content` directory is for all your embedded content files, such as Markdown text files, and is not URL-accessible.

## Pages & Routes
The `pages` directory stores the page contents for the site, and are configured for each route in `config.php`.

Each route is an associative array of a `page` key, and optional `meta` and/or `layout` keys.
The `page` key value is the path to your page file within the `pages` directory.
The `meta` key value is an associative array of whatever metadata you want to use in your layout/page, i.e. title, description, etc.
The `layout` key value is either `false`, meaning no layout is used and the page is rendered as is, or the filename of a different layout than the default.

See the example routes below:
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
    'layout' => 'different_layout'
  ],
  '/page/without/layout' => [
    'page' => 'some_page.php',
    'layout' => false
  ]
]
```

You can implement your metadata properties in your layout, such as for social media SEO tags, using the `<meta>` tag. Use the `@` warning suppressing syntax for times when a route does't have that metadata property specified:
```html
<head>
  ...
  <meta name="og:title" content="<?= @$meta['title'] ?>">
  <meta name="og:description" content="<?= @$meta['description'] ?>">
  <meta name="og:image" content="<?= @$meta['thumbnail'] ?>">
  ...
</head>
```

### Dynamic Routes
You can also specify non-static matching routes for the key string. Use the `:param` syntax to dynamically match a route and have its parameters set to the parsed values from the incoming URI:
```php
$routes = [
  '/products/:id' => [
    'page' => 'product.php'
  ]
];
```
`product.php`:
```php
<p>ID: <?= $req->params->id ?></p>
```

# Templating
PHP's built-in templating is sufficient for most websites. As such, just use `include` and `require` as you would normally for templating your site, parsing content as needed (see [plugins](#plugins) below).

## Global Variables
There are a couple of global variables you can always reference in a layout or page file: `$req` and `$page`.
|Name|Data Type|Note|
|---|---|---|
|`$req`|`object`|The current request (see [request](#request) below)|
|`$page`|`string`|Path to the page file being rendered|
|`$meta`|`array`|Any metadata key/values specified for the matched route|

# Request
The request object (`$req`) contains a handful of useful properties and methods:
|Name|Data Type|Note|
|---|---|---|
|`$req->uri`|`string`|The current requested route, i.e. `/about`|
|`$req->method`|`string`|The request method, i.e. `GET`, `POST`, `PUT`, `DELETE` etc.|
|`$req->query`|`object`|Any query parameters passed in the URL|
|`$req->params`|`object`|Any dynamic parameters parsed out from the requested route|
|`$req->body(string $mime)`|`method`|Returns any data sent with a `POST` or `PUT` request. Specifying a MIME type argument will return a parsed object of the data|
|`$req->files(string $field)`|`method`|Returns an array of any uploaded files for a form field sent with a `multipart/form-data` type request|

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

Plugins are loaded globally, and their top-level objects, functions, and/or classes are accessible from all layouts and pages.

To add a plugin, simply copy/upload the plugin's folder to the `plugins` directory.

To enable a plugin, add it's folder name to the `$plugins` array in `config.php`:

Below is an example plugin for using Parsedown via a wrapper method:

**NOTE: When including/requiring files within a plugin, make sure to use the `__DIR__` global to ensure PHP looks *within* the plugin directory and not in the root directory**

`plugins/md/md.php`
```php
<?php

require __DIR__ . '/vendor/autoload.php';

function md(string $path) {
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
<?= md('path/to/markdown-file.md') ?>
```
