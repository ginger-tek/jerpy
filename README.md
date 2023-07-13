# jerpy

## The smallest, flat-file CMS around!
Jerpy is a simple, flat-file, PHP CMS built for control and simplicity that is easy to install, customize, and maintain.
**The whole templating engine is under 20-lines of code, and the data structure is only 3 directories and 2 files.**

The CMS was built to be as streamlined and stripped-down as possible, so it's meant to be administered directly via the files.

# Getting Started
1. Grab the latest release and upload the files to your web root
2. Create a `layouts` and `pages` directory for your site content, and rename `config.sample.json` to `config.json`
3. Start creating!

# File Structure
There are 2 directories for content, `pages` and `layouts`, and just one file for configuration, `config.json`.

The `pages` directory stores all the content for the site, and can be referenced as a file path on the `page` property to render the page for a route.

The `layouts` directory stores layout templates and their assets. The default global theme is set in `config.json` on the `layout` property.

# Routes
Routes are defined separately from pages to easily manage the content and access of each route.

Each route is defined as a key on the `routes` property in `config.json`, and value is an object whose properties define the route's metadata and page content.

There is an optional `layout` property to render the page with a different layout template than the global default, or without one at all by setting it to `false`.

# Layouts
Each layout directory must have a `index.php` file and an `assets` directory. You can organize your CSS, JavaScript, fonts, and images within the `assets` directory as you see fit.

A handful of global variables are available for use in the `index.php` of a layout:
- `(object) $config` -> the site config object
- `(string) $assets` -> absolute URI to reference CSS, JavaScript, images, etc.
- `(object) $page` -> the current page object, which contains metadata and rendered content

# Documentation

## config.json
```json
{
  "siteName": "My Site", // string: can be used in layout
  "maintenance": true, // bool: toggle site-wide maintenance mode; returns HTTP 503 for all routes
  "layout": "default", // string: default global layout
  "routes": { // object: key->value store of all routes
    "/": { // string: route URI
      "title": "Home", // string: metadata title
      // add any additional metadata/OpenGraph string properties for SEO (i.e., $page->meta->title in layout)
      "page": "pages/home.php", // string: use this to render a page file (takes preference over body)
      "body": "Hello, World!", // string(optional): or use this to specify a raw response body (used if page not defined)
      "layout": false // string|bool(optional): used to override the default global layout; set to false for no layout
    }
  }
}
```