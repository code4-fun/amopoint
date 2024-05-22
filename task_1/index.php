<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>File Upload</title>
</head>
<body>
<h1>Upload a Text File</h1>
<form action="upload.php" method="post" enctype="multipart/form-data">
  <!--<input type="hidden" name="MAX_FILE_SIZE" value="1">-->
  <input type="file" name="file" accept=".txt" required>
  <button type="submit">Upload</button>
  <button type="button" onclick="resetPage()">Reset</button>
</form>
<div id="result">
  <?php
  if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
      echo '<div class="circle green"></div>';
      include 'process.php';
    } else {
      echo '<div class="circle red"></div>';
    }
  }
  ?>
</div>
<script>
  function resetPage() {
    window.location.href = 'index.php';
  }
</script>
</body>
</html>
