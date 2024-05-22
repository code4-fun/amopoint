<?php
session_start();

if (isset($_SESSION['uploaded_file'])) {
  $filePath = $_SESSION['uploaded_file'];
  $delimiter = ",";
  $fileContent = file_get_contents($filePath);
  $lines = explode($delimiter, $fileContent);

  echo "<pre>";
  foreach ($lines as $line) {
    $trimmedLine = trim($line);
    if (!empty($trimmedLine)) {
      $lineWithSpaces = str_replace("\r\n", ' ', $trimmedLine);
      $lineWithSpaces = str_replace(["\r", "\n"], ' ', $lineWithSpaces);
      $numCount = preg_match_all('/\d/', $lineWithSpaces);
      echo "Строка: \"$lineWithSpaces\" = $numCount цифр\n";
    }
  }
  echo "</pre>";

//  unlink($filePath);
//  unset($_SESSION['uploaded_file']);
}
