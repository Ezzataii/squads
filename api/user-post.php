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


if ($path == "/post/text" && $method == "POST") {
  if(!isset($_POST["text"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $text = $_POST["text"];
  $db->exec("INSERT INTO `posts` (`Post_ID`, `User`, `Date_Created`, `Text`, `MediaType`, `MediaPath`) 
  VALUES (NULL, '$user', CURRENT_TIMESTAMP, '$text', 'Text', NULL);");

  print("posted!");
}


else if ($path == "/post/image" && $method == "POST") {
  $allowedExts = array("jpg", "jpeg", "gif", "png");
  if(!isset($_POST["text"]) || !isset($_FILES["image"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $text = $_POST["text"];

  $target_dir = "../db_blob/$user.postImage.";
  $target_file = $target_dir . time() .".". basename($_FILES["image"]["name"]);
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    $db->exec("INSERT INTO `posts` (`Post_ID`, `User`, `Date_Created`, `Text`, `MediaType`, `MediaPath`) 
    VALUES (NULL, '$user', CURRENT_TIMESTAMP, '$text', 'Image', '$target_file');");

    print("posted!");
  } else {
    print("posting failed.");
  }
}


else if ($path == "/post/video" && $method == "POST") {
  $allowedExts = array("mp4");
  if(!isset($_POST["text"]) || !isset($_FILES["video"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $text = $_POST["text"];

  $target_dir = "../db_blob/$user.postVideo.";
  $target_file = $target_dir . time() .".". basename($_FILES["video"]["name"]);
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  if (move_uploaded_file($_FILES["video"]["tmp_name"], $target_file)) {
    $db->exec("INSERT INTO `posts` (`Post_ID`, `User`, `Date_Created`, `Text`, `MediaType`, `MediaPath`) 
    VALUES (NULL, '$user', CURRENT_TIMESTAMP, '$text', 'Video', '$target_file');");

    print("posted!");
  } else {
    print("posting failed.");
  }
}




else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
?>