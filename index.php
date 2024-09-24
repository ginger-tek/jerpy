<?php

/**
 * @author      GingerTek
 * @copyright   Copyright (c), GingerTek
 * @license     MIT public license
 */

$req = (object) ['uri' => parse_url(rtrim($_SERVER['REQUEST_URI'], '/') ?: '/', PHP_URL_PATH), 'method' => $_SERVER['REQUEST_METHOD'], 'params' => []];
if (is_file($req->uri))
  return false;
set_exception_handler(function ($e) {
  ob_clean();
  exit('<style>@media(prefers-color-scheme:dark){body{background:#333;color:white;}}</style><pre><h1>❌Unhandled Exception</h1><h2>' . get_class($e) . ": " . $e->getMessage() . "\n" . $e->getTraceAsString() . '</h2></pre>');
});
require 'config.php';
if (isset($timezone))
  date_default_timezone_set($timezone);
foreach ($plugins as $p)
  @include "plugins/$p/index.php";
$page = $routes[$req->uri] ?? array_values(array_filter($routes, function ($k) use ($req) {
  if (preg_match('#^' . preg_replace('#:(\w+)#', '(?<$1>[\w@%&+=_-]+)', $k) . '$#', $req->uri, $params))
    return $req->params = (object) $params;
  return false;
}, ARRAY_FILTER_USE_KEY))[0] ?? $routes['404'] ?? null;
ob_start();
if ($page === null)
  return http_response_code(404);
if (is_array($page))
  extract($page);
include $layout === false ? $page : $layout;
ob_end_flush();