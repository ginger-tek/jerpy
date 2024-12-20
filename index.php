<?php

/**
 * @author      GingerTek
 * @copyright   Copyright (c), GingerTek
 * @license     MIT public license
 */

set_exception_handler(function ($e) {
  ob_clean();
  $_err = date('c') . ' | ' . $e->getMessage() . ': ' . $e->getTraceAsString() . "\n";
  file_put_contents(__DIR__ . '/logs/' . date('Y-m-d') . '.err', $_err, FILE_APPEND);
  exit('<style>@media(prefers-color-scheme:dark){body{padding:5em;background:#333;color:white;}}</style>
  <pre><h1>Uh Oh!</h1><h2>An unexpected error has ocurred. Please contact the site admin.</h2></pre>');
});
$req = new class {
  public string $method;
  public string $uri;
  public object $query;
  public object $params;
  function __construct()
  {
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';
    $this->query = (object) $_GET;
    $this->params = (object) [];
  }
  function body(string $mime = null): mixed
  {
    return isset($_POST) ? (object) $_POST : match ($mime) {
      'application/json' => json_decode(file_get_contents('php://input')),
      default => file_get_contents('php://input')
    };
  }
  function files(string $field): array
  {
    $files = [];
    foreach ($_FILES[$field] as $k => $vs) foreach ($vs as $i => $v) {
        $files[$i] ??= (object) [];
        $files[$i]->{$k} = $v;
      }
    return $files;
  }
};
require 'config.php';
if (isset($timezone))
  date_default_timezone_set($timezone);
foreach ($plugins as $p)
  @include "plugins/$p/$p.php";
if (
  !($res = $routes[$req->uri]
    ?? array_values(array_filter($routes, function ($k) use (&$req) {
      if (!preg_match('#^' . preg_replace('#:(\w+)#', '(?<$1>[\w\@\#\%\&\+\=\_\-]+)', $k) . '$#', $req->uri, $params))
        return false;
      $req->params = (object) $params;
      return true;
    }, ARRAY_FILTER_USE_KEY))[0]
    ?? null)
) {
  http_response_code(404);
  $res = end($routes);
}
ob_start();
(is_array($res) ? extract($res) : $page = $res);
$page = "pages/$page";
$meta ??= [];
include $layout === false ? $page : "layouts/$layout.php";
ob_end_flush();