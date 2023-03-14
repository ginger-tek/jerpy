# jerpy

## Smallest, flat-file CMS around :)
Jerpy is a simple, flat-file, PHP CMS built for control and simplicity that is easy to install, customize, and maintain.
**The whole templating engine is just 12-lines of code, and the data structure is only 3 directories and 2 files.**

The CMS was built to be as streamlined and stripped-down as possible, so it's meant to be administered directly via the files.
However, there is a rudimentary admin panel to manage the files via the browser, just in case.

## Getting Started
1. Download release
2. Upload the files to your web root
3. Profit

## Understanding the Structure
There are 3 directories for content, **pages, themes, and media**, and there are 2 files for configuration, **`config.php` and `routes.php`**.

- The pages directory stores the content for each page, while the routing and metadata for the pages is stored in `routes.php`.
- The themes directory stores each themes' template layout and it's assets, organized by the name of the theme. The current theme is set in `config.php`.
- The media directory is a place to store any public facing files that you want to host, such as images, PDFs, etc.
- The `routes.php` file contains an associative array of URI paths and their respective page data. Each entry tells the route what page to load, as well as metadata for SEO.
- The `config.php` file contains a global class with static properties that determine certain behaviors of the site, such as the site name, timezone, theme, and whether to show a friendly error page.

## Themes
Each theme directory must have a `template.php` file and an `assets` directory.

### Partials
To help with organization, a `partials` directory is optional to store template sections, such as for the header and footer; these can be included using the `$theme-dir` template var with normal syntax:
```php
<?php include "$theme-dir/partials/header.php" ?>
```

## Theme Template Variables
You can access the theme and page objects via the `$theme` and `$page` variables within a template or page. Each one is a stdObject with the following properties each:
- `$theme`
  - `template (string)`: relative path to the theme's `template.php` file
  - `assets (string)`: absolute path to the theme's `assets` directory to `<link>` resources, i.e. css/js
- `$page`
  - `page (string)`: name of the page file to load
  - `meta_title (string)`: title of the page
  - `meta_<name>`: You can specify any kind of SEO properties to use in your template

## admin.php
There is one other file: `admin.php`. This is an alternative to managing the site's file directly, so it's pretty rudimentary.

When first logging in, enter the password you want to use and it will be set in the `config.php` file.

This web interface will provide a simple file manager to:
- Edit themes and pages files
- Edit `routes.php` and `config.php`
- Add/remove pages and themes files
- Add/remove file and directories under `media`
- Upload text, image, and document files under `media`
