<?php

function eh($e,$s=0,$f=0,$l=0) { ob_clean(); echo '<pre><h1>An error occurred</h1><h2>❌'
  .($e instanceof Exception ? $e->getMessage() : "$f:$l - $s").'</h2></pre>'; exit; }
set_error_handler('eh'); set_exception_handler('eh');
if(!file_exists('config.json')) throw new Exception('Missing config file');
$config = json_decode(file_get_contents('config.json'),false, 10, JSON_THROW_ON_ERROR);
if($config->maintenance) { http_response_code(503); exit; }
date_default_timezone_set($config->timezone ?? 'America/New_York');
$req = (object)parse_url(rtrim($_SERVER['REQUEST_URI'],'/') ?: '/');
if(property_exists($req,'query')) parse_str($req->query, $req->query); else $req->query = [];
$r = $config->routes->{$req->path} ?? $config->routes->{'/404'} ?? throw new Exception('Missing 404 route');
ob_start();
if(property_exists($r,'page')) file_exists($r->page) ? include $r->page : throw new Exception('Missing page file');
else echo $r->body ?? throw new Exception('Missing page body');
$page = (object)['meta'=>$r,'body'=>ob_get_clean()];
if(property_exists($r,'layout') && !($l = $r->layout)) { echo $page->body; exit; }
else $l = $config->layout ?? throw new Exception('No global layout defined');
if(@$l) { $assets = "/layouts/$l/assets"; include "layouts/$l/index.php"; }
