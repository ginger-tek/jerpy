<?php

require 'config.php';
set_exception_handler(function ($e) {
  if (ob_get_length() > 0) ob_clean();
  $str = date('c') . ': ' . str_replace("\n", "\n" . date('c') . ': ', $e) . "\n";
  file_put_contents('logs/error.log', $str, FILE_APPEND);
  echo '<html><head><title>An Error Has Occurred</title><meta name="viewport" content="width=device-width,initial-scale=1"><style>body {font-family: Arial, Helvetica, sans-serif;padding: 2em 1em;background: lightcoral;margin: 0 auto;max-width: 800px;}body,a {color: darkred;font-weight: bold;}</style></head><body><header><h1>Uh oh! An error has occurred</h1></header><main><h3>The page has failed to load due to an internal error. Click Go Back to return to the previous page.</h3><a href="#" onclick="history.back()">Go Back</a></main></body></html>';
  exit;
});
require 'routes.php';
if (@Config::$timezone) date_default_timezone_set(Config::$timezone);
ob_start();
$url = (object)parse_url($_SERVER['REQUEST_URI']);
if (@$url->query) parse_str($url->query, $url->query);
$pt = fn ($t = null) => (object)['dir' => "themes/$t", 'template' => "themes/$t/template.php", 'assets' => "/themes/$t/assets"];
$page = (object)(@$routes[$url->path] ?? $routes['/404']);
$theme = ($pt)(@$page->theme ?? Config::$theme);
include "pages/$page->page";
$page->content = ob_get_clean();
if (@$page->theme !== false) {
  if (!file_exists($theme->template)) throw new Error('Missing template');
  include $theme->template;
} else echo $page->content;
exit;
