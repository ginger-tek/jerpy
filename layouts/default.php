<!DOCTYPE html>
<html>

<head>
  <title><?= $config->siteName ?> - <?= $page->title ?></title>
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
