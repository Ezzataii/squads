<?php
include("../util/db.php");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];

if(!isset($_REQUEST["u"])) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
  die();
}


$user = $_REQUEST["u"];

if(count($db->query("SELECT username FROM USERS WHERE UserName = '$user';")->fetchAll()) == 0) {
  print("User, \"$user\", Does Not Exist.");
  die();
}


if ($path == "/profile-picture" && $method == "GET") {
  print($db->query("SELECT profilePicturePath FROM USERS WHERE UserName = '$user';")->fetchAll()[0]["profilePicturePath"]);
} 



else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
?>


