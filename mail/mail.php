<?php
include "./includes/config.php";
require "./vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer; // namespace\class;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function sendMail($to, $subject, $body)
{
  $mail = new PHPMailer(true); // Class name hay toh pascal case use kari hay PHPMailer 
  try {
    $mail->isSMTP();
    $mail->Host = $_ENV["SMTP_HOST"];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV["SMTP_USERNAME"];
    $mail->Password = $_ENV["SMTP_PASSWORD"];
      $mail->SMTPSecure = $_ENV["SMTP_ENCRYPTION"];
    $mail->Port = $_ENV["SMTP_PORT"];
    $mail->setFrom($_ENV["SMTP_USERNAME"],$_ENV["APP_NAME"]);
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    echo $e->getMessage();
  };
}
