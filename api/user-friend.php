<?php
include("../util/db.php");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];

if(!isset($_REQUEST["to"]) || !isset($_REQUEST["from"])) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
  die();
}
$to = $_REQUEST["to"];
$from = $_REQUEST["from"];
session_start();
if(!isset($_SESSION["username"]) || $_SESSION["username"] != $from) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
  die();
}



//GET SEND FRIEND
if ($path == "/send" && $method == "GET") {
  if($to == $from) {
    print("Cannot Add Yourself.");
  } else if(count($db->query("SELECT username FROM USERS WHERE UserName = '$to';")->fetchAll()) == 0) {
    print("User, \"$to\", Does Not Exist.");
  } else if (count($db->query("SELECT * FROM FRIENDS WHERE User = '$to' AND friend = '$from';")->fetchAll()) != 0) {
    print("Already Friends!");
  } else {
    $rows = $db->exec("INSERT INTO FRIEND_REQUESTS (`From_User`, `To_User`) VALUES ('$from', '$to');");
    print("Friend Request Sent!");
  }
}

//GET ACCEPT FRIEND
else if ($path == "/accept" && $method == "GET") {
  if($to == $from) {
    print("Cannot Accept Yourself.");
  } else if(count($db->query("SELECT username FROM USERS WHERE UserName = '$to';")->fetchAll()) == 0) {
    print("User, \"$to\", Does Not Exist.");
  } else if(count($db->query("SELECT * FROM FRIEND_REQUESTS WHERE From_User = '$to' AND To_User = '$from';")->fetchAll()) == 0) {
    print("No incoming friend request from this user");
  } else {
    $db->exec("INSERT INTO FRIENDS (`user`, `friend`) VALUES ('$from', '$to');");
    $db->exec("INSERT INTO FRIENDS (`user`, `friend`) VALUES ('$to', '$from');");
    $db->exec("DELETE FROM FRIEND_REQUESTS WHERE From_User = '$to' AND To_User = '$from';");
    print("Friend Request Accepted!");
  }
}

//GET DECLINE FRIEND
else if ($path == "/decline" && $method == "GET") {
  if($to == $from) {
    print("Cannot Decline Yourself.");
  } else if(count($db->query("SELECT username FROM USERS WHERE UserName = '$to';")->fetchAll()) == 0) {
    print("User, \"$to\", Does Not Exist.");
  } else if(count($db->query("SELECT * FROM FRIEND_REQUESTS WHERE From_User = '$to' AND To_User = '$from';")->fetchAll()) == 0) {
    print("No incoming friend request from this user");
  } else {
    $db->exec("DELETE FROM FRIEND_REQUESTS WHERE From_User = '$to' AND To_User = '$from';");
    print("Friend Request Accepted!");
  }
}


//GET DELETE FRIEND
else if ($path == "/delete" && $method == "GET") {
  if($to == $from) {
    print("Cannot Decline Yourself.");
  } else {
    $rows = $db->query("SELECT username FROM USERS WHERE UserName = '$to';")->fetchAll();
    if(count($rows) == 0) {
      print("User, \"$to\", Does Not Exist.");
    } else {
      $db->exec("DELETE FROM FRIENDS WHERE User = '$to' AND Friend = '$from';");
      $db->exec("DELETE FROM FRIENDS WHERE User = '$from' AND Friend = '$to';");
      print("Friend Deleted.");
    }
  }
}



else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}