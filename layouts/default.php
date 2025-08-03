<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>My Site - <?= $meta['title'] ?? '' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="og:title" content="<?= $meta['title'] ?? '' ?>">
  <meta name="og:description" content="<?= $meta['description'] ?? '' ?>">
  <meta name="og:image" content="<?= $meta['thumbnail'] ?? '' ?>">
  <link rel="stylesheet" href="/assets/styles.css">
</head>

<body class="container">
  <header>
    <nav>
      <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/about">About</a></li>
        <li><a href="/asdf">Not Found</a></li>
      </ul>
    </nav>
  </header>
  <main>
    <?php include $page ?>
  </main>
</body>

</html>