<?php
require __DIR__."/../vendor/autoload.php";  
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__."/../");
$dotenv->load();

$conn = mysqli_connect($_ENV["DB_HOST"],$_ENV["DB_USER"],$_ENV["DB_PASSWORD"],$_ENV["DB_NAME"]);

if(!$conn){ 
    die("Connection Failed".mysqli_connect_error()) ; 
}
else{
    // echo "Connection Successfull" ; 
 } 
?>