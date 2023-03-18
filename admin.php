<?php
session_start();

if (@$_GET['logout']) {
  session_destroy();
  header('location: admin.php');
} ?>
<html>

<head>
  <title>Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://unpkg.com/@picocss/pico">
  <style>
    input[type=radio]+form {
      display: none;
    }

    input[type=radio]:checked+form {
      display: flex
    }

    article {
      margin-top: 0;
    }

    textarea {
      font-size: 12pt;
      width: 100%;
      font-family: monospace;
    }
  </style>
</head>

<body>
  <div class="container">
    <?php
    require 'config.php';

    if (@$_POST['pw']) {
      if (Config::$adminPw && !password_verify($_POST['pw'], Config::$adminPw)) {
        $err = 'Incorrect Password';
      } else {
        if (!Config::$adminPw) {
          $cf = file_get_contents('config.php');
          file_put_contents('config.php', str_replace('$adminPw = \'\'', '$adminPw = \'' . password_hash($_POST['pw'], PASSWORD_BCRYPT) . '\'', $cf));
        }
        file_put_contents('logs/access.log', date('c') . ': logged in');
        $_SESSION['user'] = true;
      }
    }
    if (!@$_SESSION['user']) { ?>
      <br>
      <form method="POST" style="margin:0 auto;max-width:400px">
        <?= @$err ?>
        <input name="pw" type="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>
    <?php exit;
    }

    if (@$_GET['rename']) {
      ob_clean();
      rename($_GET['path'], dirname($_GET['path']) . '/' . $_GET['rename']);
      echo 'success';
      exit;
    }

    if (@$_GET['delete']) {
      ob_clean();
      if (is_dir($_GET['path'])) rmdir($_GET['path']);
      elseif (is_file($_GET['path'])) unlink($_GET['path']);
      echo 'success';
      exit;
    }

    $f = '#config.php|media|pages|themes|routes.php#';
    $path = @$_GET['path'] ?? '';
    if ($path && !file_exists($path)) { ?>
      <div class="error">File not found</div>
      <a onclick="history.back()">Go Back</a>
    <?php exit;
    }

    if ($name = @$_POST['name']) {
      if (@$_POST['dir']) mkdir($path . '/' . $name);
      else touch($path . '/' . $name);
      header("location: ?path=$path");
    }

    if ($upload = @$_FILES['upload']) {
      move_uploaded_file($upload['tmp_name'], $path . '/' . basename($upload["name"]));
      header("location: ?path=$path");
    }

    $pat = ['{*,.[!.]*,..?*}'];
    if (($path && is_dir($path)) || !$path) {
      if ($path) array_unshift($pat, $path);
      $items = array_filter(glob(join('/', $pat), GLOB_BRACE), fn ($i) => preg_match($f, $i));
    } else $items = [];

    $trail = ['<a href="admin.php">Home</a>'];
    $crumbs = explode('/', $path);
    foreach ($crumbs as $i => $c) {
      $t = join('/', array_slice($crumbs, 0, $i + 1));
      if (is_dir($t)) $trail[] = '<a href="admin.php?path=' . $t . '">' . $c . '</a>';
    } ?>
    <header>
      <nav>
        <ul>
          <li>
            <div><?= join(' &gt; ', $trail) ?></div>
          </li>
        </ul>
        <ul>
          <li><a href="/" target="_blank">View</a></li>
          <li><a href="?logout=true">Logout</a></li>
        </ul>
      </nav>
    </header>
    <article>
      <?php if ($path && is_dir($path) || !$path) {
        if (!count($items)) echo '<p>No items</p>';
        else { ?>
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Modified</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $i) { ?>
                <tr>
                  <td><a href="?path=<?= $i ?>"><?= basename($i) ?></a></td>
                  <td><?= date('Y-m-d H:i:s', filemtime($i)) ?></td>
                  <td>
                    <a onclick="rename('<?= $i ?>')">Rename</a>
                    <a onclick="remove('<?= $i ?>')">Delete</a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php }
        if ($path) { ?>
          <div style="display:flex;gap:1em">
            <div><label role="link" for="tab1">Create New</label></div>
            <?php if (strstr($path, 'media')) { ?>
              <div><label role="link" for="tab2">Upload File</label></div>
            <?php } ?>
          </div>
          <div>
            <input type="radio" id="tab1" name="tabs" hidden>
            <form method="POST" style="gap:1em">
              <?php if (!in_array($path, ['pages'])) { ?>
                <label style="display:flex;align-items:center"><input name="dir" type="checkbox"> Directory?</label>
              <?php } ?>
              <input style="margin:0" name="name" placeholder="New Item Name" required>
              <button style="margin:0;width:auto" type="submit">Create</button>
            </form>
            <input type="radio" id="tab2" name="tabs" hidden>
            <?php if (strstr($path, 'media')) { ?>
              <form method="POST" style="gap:1em" enctype="multipart/form-data">
                <input style="margin:0" name="upload" type="file" accept="image/png,image/jpeg,text/*" required>
                <button style="margin:0;width:auto" type="submit">Upload</button>
              </form>
            <?php } ?>
          </div>
        <?php }
      }

      if (is_file($path)) {
        if ($c = @$_POST['content']) {
          file_put_contents($path, $c);
          $msg = ' - Saved!';
        }

        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
          ob_clean();
          unlink($path);
          echo 'success';
          exit;
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array($ext, ['jpeg', 'jpg', 'png'])) { ?>
          <h4><?= basename($path) ?> -
            <a href="/<?= $path ?>" target="_blank">Link</a>
          </h4>
          <img src="<?= $path ?>">
        <?php } else {
          $cont = file_get_contents($path);
        ?>
          <form method="POST">
            <div style="display:flex;align-items:center;margin-bottom:1em">
              <h4 style="margin:0;flex:1"><?= basename($path) ?><span>&nbsp;<?= @$msg ?></span></h4>
              <div>
                <button style="display:inline-block;width:auto;margin:0" type="submit">Save</button>
              </div>
            </div>
            <textarea name="content" rows="25" spellcheck="false" onkeydown="handleKeys(event)"><?= $cont ?></textarea>
          </form>
      <?php }
      } ?>
    </article>
  </div>
  <script>
    async function rename(path) {
      const name = prompt('New Name')
      if (!name) return
      const url = new URL(location.href)
      url.searchParams.set('path', path)
      url.searchParams.set('rename', name)
      const res = await fetch(url.toString()).then(r => r.text())
      if (res == 'success') location.reload()
      else alert('Failed to rename')
    }
    async function remove(path) {
      if (!confirm('Are you sure you want to delete?')) return
      const url = new URL(location.href)
      url.searchParams.set('path', path)
      url.searchParams.set('delete', true)
      const res = await fetch(url.toString()).then(r => r.text())
      if (res == 'success') location.reload()
      else alert('Failed to delete')
    }

    function handleKeys(e) {
      if (e.ctrlKey && e.key == 's') {
        e.preventDefault()
        document.querySelector('form').submit()
      }
      if (e.key == 'Tab') {
        e.preventDefault()
        e.target.setRangeText(
          '  ',
          e.target.selectionStart,
          e.target.selectionStart,
          'end'
        )
      }
    }
  </script>
</body>

</html>