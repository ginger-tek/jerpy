# jerpy

## The smallest, flat-file CMS around!
Jerpy is a simple, flat-file, PHP CMS built for control and simplicity that is easy to install, customize, and maintain.
**The whole templating engine is under 20-lines of code, and the data structure is only 3 directories and 2 files.**

The CMS was built to be as streamlined and stripped-down as possible, so it's meant to be administered directly via the files.

# Getting Started
1. Grab the latest release and upload the files to your web root
2. Create a `layouts` and `pages` directory for your site content, and rename `config.sample.json` to `config.json`
3. Add your first layout directory inside `layouts`
4. Start adding routes to the `config.json`

# File Structure
There are 2 directories for content, `pages` and `layouts`, and just one file for configuration, `config.json`.

## Pages
The `pages` directory stores all the content for the site, and can be referenced as a file path on the `page` property to render the page for a route.
```
🗀 pages
  🗋 home.php
  🗋 about.php
  🗋 404.php
```

## Layouts
The `layouts` directory stores layout templates and their assets. The default global theme is set in `config.json` on the `layout` property.

Each layout directory must have a `index.php` file and an `assets` directory. You can organize your CSS, JavaScript, fonts, and images within the `assets` directory as you see fit.
```
🗀 layouts
  🗀 layoutName
    🗀 assets
    🗋 index.php
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
      "desc": "This is a description of the page for SEO",
      "img": "https://domain.com/path/to/image/for/seo.jpg",
      "page": "pages/page.php",
      "body": "Some text"
    }
  }
}
```
|Name|Data Type|Required?|Note|
|---|---|---|---|
|`title`|`string`|Yes|Page title|
|`page`|`string`|Conditional|Required if `body` not set. Overwrites `body` value with rendered content|
|`body`|`string`|Conditional|Required if `page` not set|
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
|`$assets`|`string`|Absolute path to the current layout's assets directory|

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