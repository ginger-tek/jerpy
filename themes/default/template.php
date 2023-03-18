<html>

<head>
  <title><?= Config::$siteName ?> - <?= @$page->meta_title ?></title>
  <link rel="stylesheet" href="<?= $theme->assets ?>/style.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="title" content="<?= Config::$siteName ?> - <?= @$page->meta_title ?>">
  <meta name="description" content="<?= @$page->meta_desc ?>">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= @$page->meta_title ?>">
  <meta property="og:description" content="<?= @$page->meta_desc ?>">
  <meta property="og:image" content="<?= @$page->meta_img ?>">
  <meta name="twitter:title" content="<?= Config::$siteName ?>">
  <meta name="twitter:description" content="<?= @$page->meta_desc ?>">
  <meta name="twitter:image" content="<?= @$page->meta_img ?>">
</head>

<body>
  <header>
    <a href="/">Home</a>
    <a href="/about">About</a>
    <a href="/products">Products</a>
    <a href="/asdf">Asdf</a>
  </header>
  <main>
    <?= $page->content ?>
  </main>
  <footer>
    <p>© 2023</p>
  </footer>
</body>

</html>