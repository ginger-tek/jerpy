<?php

$timezone = 'America/New_York';

$layout = 'default';

$plugins = [];

$routes = [
  '/' => [
    'page' => 'home',
    'meta' => ['title' => 'Home']
  ],
  '/about' => [
    'page' => 'about',
    'meta' => ['title' => 'About']
  ],
  '404' => [
    'page' => 'notFound',
    'meta' => ['title' => 'Page Not Found']
  ]
];