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

//UPDATE ABOUT
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

//UPDATE PROFILE PICTURE
else if ($path == "/update/profile-picture" && $method == "POST") {
  if(!isset($_FILES["profilePicture"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  } else {

    $target_dir = "../db_blob/$user.profilePicture.";
    $target_file = $target_dir . time() .".". basename($_FILES["profilePicture"]["name"]);
    $uploadOk = true;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $target_file)) {
      $db->exec("UPDATE USERS SET profilePicturePath = '$target_file' WHERE username = '$user'");
      $_SESSION["profilePicture"] = $target_file; 
      print("The file ". basename( $_FILES["profilePicture"]["name"]). " has been uploaded.");
    } else {
      print("Sorry, there was an error uploading your file.");
    }
  }
} 

//GET LEVEL OF ACCESS
else if ($path == "/level-of-access" && $method == "GET") {
  $response = $db->query("SELECT LevelOfAccess FROM USERS WHERE UserName = '$user';")->fetchAll()[0];

  print($response["LevelOfAccess"]);
}

//UPDATE LEVEL OF ACCESS
else if ($path == "/update/level-of-access" && $method == "GET") {
  if(!isset($_REQUEST["l"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $levelOfAccess = $_REQUEST["l"];

  if(  $levelOfAccess != "public" 
    && $levelOfAccess != "private" 
    && $levelOfAccess != "friends-only") {
      header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
      die();
  }


  $response = $db->exec("UPDATE USERS SET LevelOfAccess = '$levelOfAccess' WHERE UserName = '$user';");

  print("Updated Level of Access!");
}




else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

?>