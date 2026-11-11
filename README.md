<div align=center>
  <div style="font-size:2em;font-weight:bold">jerpy</div>
  <p>Simple - Flat-file - Extendable</p>
</div>
<hr>

Jerpy a simple, yet extendable, flat-file CMS that is managed entirely and directly via its files; no database to finagle with! There is no built-in web admin/management portal, but you could make a [plugin](#plugins) for that if necessary. Otherwise, just edit, commit, and deploy!

# Getting Started
## Composer
```
composer create-project ginger-tek/jerpy <directory>
```
## Local Server
While a VSCode launch profile is included, the built-in PHP dev server may not suffice for certain scenarios, such as serving assets from the plugins directory, whereas the included .htaccess config can do so out-of-the-box. If using caddy, an example Caddyfile is included that can also be used for both local development and production deployment, and the "Listen for Xdebug" launch config can be used to debug the php-cgi process.

# Files & Folders
- `config.php`
  Set all configurations and page routes here (see next section)
- `layouts/`
  Stores layout templates, each their own `.php` file
- `assets/`
  For all CSS, JavaScript, fonts, and images to use in the layouts and pages via URL, i.e. `/assets/styles.css` or `/plugins/myplugin/assets/js/scripts.js`
- `content/`
  For all embedded content files that should not be URL-accessible.
- `media/`
  For any files that should be URL-accessible.

# Configurations
- `$timezone` sets the default timezone. For a list of supported timezones, [see the documentation](https://www.php.net/manual/en/timezones.php). The default timezone is `America\New_York`
- `$layout` sets the default layout to use for all pages, unless otherwise overriden on a given route (see next section)
- `$plugins` is an array of enabled plugins by name (see [enabling plugins](#enabling-plugins))
- `$routes` (see next section)

# Pages & Routes
Routes are configured in an associative array of a route key and associative array value. The value array must have at least a `page` entry whose value is a relative path to a page file in the pages folder with no extension. Optionally, a `meta` entry can be set to include an array of metadata key/values for the given route, as well as a `layout` entry to override the default layout, or not use one at all.

Example routes config:
```php
$routes = [
  '/' => [
    'page' => 'home',
    'meta' => [
      'title' => 'Welcome to my site!'
    ]
  ],
  '/products/things' => [
    'page' => 'products/things'
    'meta' => [
      'title' => 'Things Products'
    ]
  ],
  '/use/a/different/layout' => [
    'page' => 'some_page',
    'meta' => [
      'title' => 'Some Page',
      'thumbnail' => '/assets/my_thumbnail.png'
    ],
    'layout' => 'layout_2'
  ],
  '/page/without/layout' => [
    'page' => 'some_page',
    'layout' => false
  ]
  ...
];
```

# Fallback/Error Pages
The last route on the `$routes` array is used as the fallback route when no other routes are matched. By default, the "key" for this route is `404`, and while it can technically be any value, it does have to be a string value of some sort; `null` or no key will not work properly.

The error page is shown any time a throwable exception occurs during code execution. The page contents can be updated as needed, as the actual error details are automatically written to the PHP error log for troubleshooting.

# Page Metadata
When implementing metadata, use null coalescing syntax (`??`) to avoid warnings/errors when a route does't have that metadata property specified:
```html
<head>
  ...
  <title><?= $meta['title'] ?></title>
  <meta name="og:title" content="<?= $meta['title'] ?? '' ?>">
  <meta name="og:description" content="<?= $meta['description'] ?? '' ?>">
  <meta name="og:image" content="<?= $meta['thumbnail'] ?? '' ?>">
  ...
</head>
```

## Dynamic Routes
To use dynamic route parameters, use the `:{name}` syntax in the route key string. All matched values will be accessible from the `$params` variable:
```php
$routes = [
  '/products/:id' => [
    'page' => 'product'
  ]
];
```
`product.php`:
```php
<?php

$product = SomeDataService::getProductById($params['id']);

?>
<p><?= $product->name ?></p>
```

# Templating
Jerpy relies on PHP's built-in templating using `include` and `require` to inject views into the templates. For content parsing, see [plugins](#plugins).

# Global Variables/Constants
The following variables are made globally accessible anywhere in a page or template:
|Name|Data Type|Note|
|---|---|---|
|`$uri`|`string`|Request URI with no query parameters|
|`$params`|`array`|Any URL parameters matched by the route key pattern|
|`$page`|`string`|Path to the page file being rendered|
|`$meta`|`array`|Any metadata key/values specified for the matched route|
|`$layout`|`array`|Path to the current layout file being used|
|ROOT|`string`|Path of the web root, useful in [plugins](#plugins)|

# Plugins
Plugins can be created to extend the functionality of Jerpy. They do not require any specific framework nor follow any particular design pattern. You can use whatever preferred tools and package managers to create the plugin code, such as Composer.

If the plugin is to be enabled globally (see next section), the only requirement is that the entrypoint must be a `.php` file with the same name as the plugin's folder.

```
plugins/
  myPlugin/ <-- plugin dir
    composer.json
    myPlugin.php <-- entrypoint (same name as plugin dir)
    vendor/
      someSupportingPackage/
```

When including/requiring/reading files within a plugin, use the `__DIR__` global to make sure PHP looks ***within*** the plugin directory and not in the root directory of the site. For accessing files outside the plugins folder, use the `ROOT` global constant.

## Enabling Plugins
Plugins can be included/required on a given page file as needed, or you can load it globally to be used on every page. To enable a plugin globally, add it's folder name to the `$plugins` array in `config.php`:
```php
$plugins = [
  'md'
];
```
To enable a plugin only when a certain URI is matched, set a string key to  check if the incoming URI starts with it:
```php
$plugins = [
  'md',
  '/admin' => 'admin' // only loaded if URI starts with "/admin"
];
```

### Example
Below is an example plugin for using Parsedown via a wrapper method:

`plugins/md/md.php`
```php
<?php

require __DIR__ . '/vendor/autoload.php';

function md(string $path): string
{
  return (new Parsedown)->text(file_get_contents(ROOT . "/$path") ?: '');
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
<?= md('content/some-markdown-file.md') ?>
```
