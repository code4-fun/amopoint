<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = 'files/';
    if (!file_exists($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }
    $uuid = uniqid('', true);
    $uploadedFile = $uploadDir . $uuid . '.txt';

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFile)) {
      session_start();
      $_SESSION['uploaded_file'] = $uploadedFile;
      header('Location: index.php?status=success');
      exit;
    }
  }
  header('Location: index.php?status=error');
  exit;
}
