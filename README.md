# jerpy

## The smallest, flat-file CMS around!
Jerpy is a simple, flat-file, PHP CMS built for control and simplicity that is easy to install, customize, and maintain.
**The whole templating engine is under 20-lines of code, and the data structure is only 3 directories and 2 files.**

The CMS was built to be as streamlined and stripped-down as possible, so it's meant to be administered directly via the files.

# Getting Started
1. Grab the latest release and upload the files to your web root
2. Create `layouts`, `pages`, and `assets` directories, and rename `config.sample.json` to `config.json`
3. Add your first layout file inside `layouts`
4. Start adding routes to the `config.json`

# File Structure
There are 3 directories for content, `pages`, `layouts`, and `assets`, and just one file for configuration, `config.json`.

## Pages
The `pages` directory stores all the content for the site, and can be referenced as a file path on the `page` property to render the page for a route.
```
🗀 pages
  🗋 home.php
  🗋 about.php
  🗋 404.php
```

## Layouts
The `layouts` directory stores layout templates, each their own `.php`. The default global theme is set in `config.json` on the `layout` property.
```
🗀 layouts
  🗋 layoutName.php
```

## Assets
This is the global assets directory, in which you can organize your CSS, JavaScript, fonts, and images to use in your layouts and pages via absolute URI:
```html
<link href="/assets/css/styles.css" rel="stylesheet">
```

# Routes
Routes are defined separately from pages to easily manage the content and access of each route.

## Route Properties
Each route is defined as a key on the `routes` property in `config.json` whose value is an object with properties that define the route's metadata and page content.
```json
{
  "routes": {
    "/": {
      "title": "Page Title",
      "description": "This is a description of the page for SEO",
      "metaImage": "https://domain.com/path/to/image/for/seo.jpg",
      "file": "pages/page.php",
      "body": "Some text"
    }
  }
}
```
|Name|Data Type|Required?|Note|
|---|---|---|---|
|`title`|`string`|Yes|Page title|
|`file`|`string`|Conditional|Required if `body` not set. Overwrites `body` value with rendered content|
|`body`|`string`|Conditional|Required if `file` not set. Throws error if neither `file` and `body` set|
|`layout`|`string`|No|If set to valid path, will override default `layout`. If set to false, no layout is used|

# Templating
Jerpy relies on the built-in templating functionality of PHP, so use `include` and `require` as you would normally, parsing content as needed, i.e. using [Parsedown](https://github.com/erusev/parsedown) or other utilities.

## Global Variables
There are 4 global variables you can use in a layout or page file: `$config`, `$req`, `$page`, and `$assets`.
|Name|Data Type|Note|
|---|---|---|
|`$config`|`object`|The stdClass object of `config.json`|
|`$req`|`object`|The current request, contains properties `path` (string of URI) and `query` (associative array of URL query parameters)|
|`$page`|`object`|Contains the body content, as well as a `meta` property that inherits all the properties defined by the route object|

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

The only requirements for plugins are the following:
- The entrypoint to be loaded globally at runtime must be a `.php` file with the same name as the plugin's folder
- Any supporting files must be plainly included/required from within the plugin folder; <span style="color:orangered"><strong>DO NOT use autoloading in your plugin as Jerpy does not use autoloading/bootstrapping</strong></span>

Example plugin structure:
```
🗀 plugins
  🗀 myPlugin
    🗋 myPlugin.php
    🗀 vendor
      🗋 someSupportingFile.php
```

Plugins are loaded globally and their top-level objects, functions, and/or classes are made accessible in templates and pages.

To add a plugin, make a new directory in the root called `plugins` and add the plugin folder to it.

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
<?= md('path/to/markdown.file') ?>
```
