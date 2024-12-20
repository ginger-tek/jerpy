<?php

$timezone = 'America/New_York';

$layout = 'default';

$plugins = [];

$routes = [
  '/' => [
    'page' => 'home.php',
    'meta' => ['title' => 'Home']
  ],
  '/about' => [
    'page' => 'about.php',
    'meta' => ['title' => 'About']
  ],
  '404' => 'notFound.php'
];