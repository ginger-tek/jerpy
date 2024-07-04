<?php

/**
 * @author      GingerTek
 * @copyright   Copyright (c), GingerTek
 * @license     MIT public license
 */

class JerpyException extends Exception {}
class PluginException extends Exception {}
function eh($e) { ob_clean(); echo '<pre><h1>An error has occrurred</h1><h2>❌' . get_class($e) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . '</h2></pre>'; exit; }
function sc(int $c) { http_response_code($c); exit; }
set_exception_handler('eh');
date_default_timezone_set($config->timezone ?? 'America/New_York');
if (preg_match('#\.(?:css|js|jpeg|jpg|png|gif|webp)$#', $_SERVER['REQUEST_URI'])) return false;
if (!file_exists('config.json')) throw new \JerpyException('Missing config file');
$config = json_decode(file_get_contents('config.json'), false, 10, JSON_THROW_ON_ERROR);
if ($config->maintenance) sc(503);
$req = (object)parse_url(rtrim($_SERVER['REQUEST_URI'], '/') ?: '/'); $req->query = $_REQUEST ?? []; $req->params = [];
$req->method = $_SERVER['REQUEST_METHOD'];
$page = $config->routes->{$req->path} ?? $config->routes->{@array_values(array_filter(array_keys(get_object_vars($config->routes)), function($r) use($req) { if (preg_match('#^' . preg_replace('#:(\w+)#', '(?<$1>[\w\-\+\%]+)', $r) . '$#', $req->path, $params)) { $req->params = $params; return true; } return false; }))[0]} ?? $config->routes->{'404'} ?? sc(404);
if (file_exists('plugins')) { foreach (glob('plugins/*', GLOB_ONLYDIR) as $p) include "$p/" . basename($p) . '.php'; }
if (property_exists($page, 'file') && file_exists($page->file)) { ob_start(); include $page->file; $page->body = ob_get_clean(); }
else if (!property_exists($page, 'body')) throw new \JerpyException('Route missing file and body property');
if (property_exists($page, 'layout') && !($l = $page->layout)) { echo $page->body; exit; } else $l = $config->layout ?? throw new \JerpyException('No global layout defined');
if (@$l) include "layouts/$l.php";
