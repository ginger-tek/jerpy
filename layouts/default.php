<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?= $config->siteName ?> - <?= $page->title ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="og:title" content="<?= $page->title ?>">
  <meta name="og:description" content="<?= @$page->description ?>">
  <meta name="og:image" content="<?= @$page->thumbnail ?>">
</head>

<body>
  <header>
    <a href="/">Home</a>
    <a href="/products/1234">Dynamic</a>
    <a href="/asdf">Not Found</a>
  </header>
  <main>
    <?= $page->body ?>
  </main>
</body>

</html>