<?php

/**
 * @author      GingerTek
 * @copyright   Copyright (c), GingerTek
 * @license     MIT public license
 */

set_exception_handler(function (Throwable $ex) {
  ob_clean();
  error_log("[" . date('r') . "] {$ex->getMessage()}: {$ex->getTraceAsString()}");
  exit('<style>body{font-family:monospace;color:#fff;font-size:2em;background:#444;padding:5em;text-align:center}</style>
  <p>⚠<br>Something went wrong while showing this page<br>Contact the site admin for assistance</p>');
});
require 'config.php';
date_default_timezone_set($timezone ?? 'America/New_York');
$uri = parse_url(rtrim($_SERVER['REQUEST_URI'], '/') ?: '/', PHP_URL_PATH);
$params = [];
foreach ($plugins as $r => $p)
  if ((is_string($r) && str_starts_with($uri, $r) && $p) || (!is_string($r) && $p))
    @include "plugins/$p/$p.php";
if (
  !($res = $routes[$uri] ?? current(array_filter($routes, function ($k) use (&$params, $uri) {
    return preg_match('#^' . preg_replace('#:(\w+)#', '(?<$1>[\w\@\#\%\&\+\=\_\-]+)', $k) . '$#', $uri, $m)
      ? $params = $m : false; }, ARRAY_FILTER_USE_KEY)) ?? false)
)
  [http_response_code(404), $res = end($routes)];
ob_start();
(is_array($res) ? extract($res) : $page = $res);
$page = "pages/$page";
$meta ??= [];
include $layout === false ? $page : "layouts/$layout.php";
ob_end_flush();