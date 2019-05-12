<?php
include("../util/db.php");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];

if(!isset($_REQUEST["r"]) || !isset($_REQUEST["s"])) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
  die();
}

// setting sender and receiver
$sender = $_REQUEST["s"];
$receiver = $_REQUEST["r"];

if(count($db->query("SELECT username FROM USERS WHERE UserName = '$receiver';")->fetchAll()) == 0) {
  print("User, \"$receiver\", Does Not Exist.");
  die();
}

// checking sessions
session_start();
if(!isset($_SESSION["username"]) || $_SESSION["username"] != $sender) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
  die();
}



if ($path == "/get-convo" && $method == "GET") {
  $messages = $db->query("SELECT * FROM MESSAGES WHERE (Sender='$sender' and Receiver='$receiver') or (Sender='$receiver' and Receiver='$sender') ORDER BY Date_Created ASC;")->fetchAll();
  echo json_encode($messages);

  
} 


else if ($path == "/send-message" && $method == "POST") {
  $message = $_POST["Message"];
  
  $result = $db->exec("INSERT INTO `messages` (`Sender`, `Receiver`, `Message`, `Date_Created`) 
            VALUES ('$sender', '$receiver', '$message',CURRENT_TIMESTAMP);");
  
  echo $result?"Message sent successfully":"Some error, meybe friend is not selected";
} 

 





else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
?>


