<?php

$timezone = 'America/New_York';

$layout = 'default';

$plugins = [];

$routes = [
  '/' => 'pages/home.php',
  '/about' => [
    'page' => 'pages/about.php',
    'title' => 'About'
  ],
  '404' => 'pages/notFound.php'
];