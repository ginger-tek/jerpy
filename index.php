<?php

/**
 * @version     4.7.1
 * @author      GingerTek
 * @copyright   Copyright (c), GingerTek
 * @license     ISC public license
 */

declare(strict_types=1);
const ROOT = __DIR__;
ini_set('display_errors', '0');
header_remove('X-Powered-By');
header_remove('Server');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
function render(array $res)
{
  global $layout, $params, $uri;
  ob_start() && extract($res, EXTR_OVERWRITE);
  $page = ROOT . "/pages/{$res['page']}.php";
  include $layout == false ? $page : ROOT . "/layouts/$layout.php";
  header('Content-Type: text/html; charset=UTF-8');
  exit(ob_get_clean());
}
set_exception_handler(function (Throwable $ex) {
  ob_get_status() && ob_end_clean();
  error_log("{$ex->getMessage()}: {$ex->getTraceAsString()}");
  render(['page' => 'error', 'meta' => ['title' => 'Error']]);
});
require ROOT . '/config.php';
date_default_timezone_set($timezone ?? 'America/New_York');
$uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';
$params = [];
foreach ($plugins as $r => $p)
  if ((is_string($r) && str_starts_with($uri, $r) && $p) || (!is_string($r) && $p))
    is_file(ROOT . "/plugins/$p/$p.php") && include ROOT . "/plugins/$p/$p.php";
if (
  !($res = $routes[$uri] ?? array_find($routes, function ($_, $k) use (&$params, $uri) {
    return preg_match('#^' . preg_replace('#:(\w+)#', '(?<$1>[\w\@\#\%\&\+\=\_\-]+)', (string) $k) . '$#', $uri, $m)
      ? $params = $m : false; }) ?? false)
)
  [http_response_code(404), $res = end($routes)];
render($res);