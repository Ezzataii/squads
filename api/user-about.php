<?php
$db = new PDO ("mysql:dbname=squad", "root", "");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];

if(!isset($_REQUEST["u"])) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
  die();
}
$user = $_REQUEST["u"];

session_start();
if(!isset($_SESSION["username"]) || $_SESSION["username"] != $user) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
  die();
}

//GET SEND FRIEND
if ($path == "/update/about" && $method == "POST") {
  $json = json_decode(file_get_contents('php://input'));
  if(!isset($json->about)) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  } else {
    $about = $json->about;
    $db->exec("UPDATE USERS SET About = '$about' WHERE username = '$user'");
    print("About Updated!");
  }
}





else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}