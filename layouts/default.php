<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'My Site' ?></title>
  <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@2.0.6/css/pico.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="og:title" content="<?= $title ?? '' ?>">
  <meta name="og:description" content="<?= $description ?? '' ?>">
  <meta name="og:image" content="<?= $thumbnail ?? '' ?>">
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