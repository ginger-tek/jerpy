<?php

$routes = [
  '/' => [
    'page' => 'home.php',
    'meta' => [
      'title' => 'Welcome!',
      'desc' => 'This is my website!',
      'img' => 'https://picsum.photos/400/300'
    ]
  ],
  '/about' => [
    'page' => 'about.php',
    'meta' => [
      'title' => 'About Us'
    ]
  ],
  '/products' => [
    'page' => 'products.php',
    'meta' => [
      'title' => 'Products'
    ]
  ],
  '/404' => [
    'page' => '404.php',
    'meta' => [
      'title' => 'Page Not Found'
    ]
  ]
];
