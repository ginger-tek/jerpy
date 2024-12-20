<?php

if ($req->method == 'POST') {
  $files = $req->files('myFiles');
  echo '<pre>' . json_encode($files, JSON_PRETTY_PRINT);
}

?>
<h1>About</h1>
<form enctype="multipart/form-data" method="POST">
  <input type="file" name="myFiles[]" multiple>
  <button type="submit">Upload</button>
</form>