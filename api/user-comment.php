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


if ($path == "/comment" && $method == "POST") {
  if(!isset($_POST["postId"]) || !isset($_POST["commentText"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $postId = $_POST["postId"];
  $commentText = $_POST["commentText"];
  $db->exec("INSERT INTO `Comments` (`Post`, `User`, `Comment`,`Date_Created`) 
  VALUES ('$postId', '$user', '$commentText', CURRENT_TIMESTAMP);");

  print("commented!");
}


else if ($path == "/delete/comment" && $method == "GET") {
  if(!isset($_REQUEST["postid"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $PostID = $_REQUEST["postid"];

  $post = $db->query("SELECT MediaType, MediaPath FROM POSTS WHERE Post_ID = '$PostID';");

  if($post == false) {
    print("post does not exist.");
    die();
  }

  $post = $post->fetchAll()[0];

  if($post["MediaType"] == "Image" || $post["MediaType"] == "Video") {
    if (file_exists($post["MediaPath"])) {
      unlink($post["MediaPath"]);
    }
  }

  $db->query("DELETE FROM POSTS WHERE Post_ID = '$PostID';");

  print("post deleted.");
}




else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
?>