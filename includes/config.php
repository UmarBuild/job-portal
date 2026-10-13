<?php
require __DIR__ . "/../vendor/autoload.php";
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// Site ka folder path khud pata lagta hai:
//   XAMPP htdocs/job-portal  -> "/job-portal"
//   hosting ka root (htdocs) -> ""
// Zaroorat ho to .env mein APP_BASE_PATH likh kar override kar sakte hain.
if (!defined('BASE_PATH')) {
  $envBase = trim($_ENV["APP_BASE_PATH"] ?? "");
  if ($envBase !== "") {
    define('BASE_PATH', "/" . trim($envBase, "/"));
  } else {
    $root = str_replace("\\", "/", (string) realpath(__DIR__ . "/.."));
    $docRoot = str_replace("\\", "/", (string) realpath($_SERVER["DOCUMENT_ROOT"] ?? ""));
    $base = "";
    if ($docRoot !== "" && stripos($root, $docRoot) === 0) {
      $base = substr($root, strlen($docRoot));
    }
    define('BASE_PATH', rtrim($base, "/"));
  }
}

// Poora site address (reset-password email link ke liye)
if (!defined('APP_URL')) {
  $envUrl = trim($_ENV["APP_URL"] ?? "");
  if ($envUrl !== "") {
    define('APP_URL', rtrim($envUrl, "/"));
  } else {
    $https = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") || (($_SERVER["SERVER_PORT"] ?? "") == 443);
    define('APP_URL', ($https ? "https://" : "http://") . ($_SERVER["HTTP_HOST"] ?? "localhost"));
  }
}

$conn = mysqli_connect($_ENV["DB_HOST"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"], $_ENV["DB_NAME"]);

if (!$conn) {
  die("Connection Failed" . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
