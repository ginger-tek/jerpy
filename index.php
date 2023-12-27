<?php

/**
 * @author      GingerTek
 * @copyright   Copyright (c), 2023 GingerTek
 * @license     MIT public license
 */

class JerpyException extends Exception {}
class PluginException extends Exception {}
function eh($e) { ob_clean(); echo '<pre><h1>An error has occrurred</h1><h2>❌' . get_class($e) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . '</h2></pre>'; exit; }
function st(int $c) { http_response_code($c); exit; }
set_exception_handler('eh');
date_default_timezone_set($config->timezone ?? 'America/New_York');
if (!file_exists('config.json')) throw new \JerpyException('Missing config file');
$config = json_decode(file_get_contents('config.json'), false, 10, JSON_THROW_ON_ERROR);
if ($config->maintenance) st(503);
$req = (object)parse_url(rtrim($_SERVER['REQUEST_URI'], '/') ?: '/'); $req->query = $_REQUEST ?? []; $req->params = [];
$page = $config->routes->{$req->path} ?? $config->routes->{@array_values(array_filter(array_keys(get_object_vars($config->routes)), function($r) use($req) { if (preg_match('#^' . preg_replace('#:(\w+)#', '(?<$1>[\w\-]+)', $r) . '$#', $req->path, $params)) { $req->params = $params; return true; } return false; }))[0]} ?? $config->routes->{'404'} ?? st(404);
if (file_exists('plugins')) { foreach (glob('plugins/*') as $p) include "$p/" . basename($p) . '.php'; }
if (property_exists($page, 'file') && file_exists($page->file)) { ob_start(); include $page->file; $page->body = ob_get_clean(); }
else if (!property_exists($page, 'body')) throw new \JerpyException('File nor body property set on route');
if (property_exists($page, 'layout') && !($l = $page->layout)) { echo $page->body; exit; } else $l = $config->layout ?? throw new \JerpyException('No global layout defined');
if (@$l) include "layouts/$l.php";
