<?php
include("../util/db.php");

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


if ($path == "/react/post" && $method == "POST") {
  if(!isset($_POST["postId"]) || !isset($_POST["value"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }

  $postId = $_POST["postId"];
  $value = $_POST["value"];

  if($value == 0) {
    $db->query("DELETE FROM POST_REACTIONS WHERE Post = '$postId' AND User = '$user'");
  } else if ($value == -1 || $value == 1) {
    $db->query("INSERT INTO POST_REACTIONS (Post, User, Value) VALUES ('$postId', '$user', '$value')");
  }

  print("reacted!");
}

else if ($path == "/react/comment" && $method == "POST") {
  
}





else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
?>