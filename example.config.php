<?php

$timezone = 'America/New_York';

$layout = 'layouts/default.php';

$plugins = [];

$routes = [
  '/' => 'pages/home.php',
  '/about' => [
    'page' => 'pages/about.php',
    'title' => 'About'
  ],
  '404' => 'pages/notFound.php'
];