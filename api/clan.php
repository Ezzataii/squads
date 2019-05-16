<?php
include("../util/db.php");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];




//GET FIND CLAN
if ($path == "/find-clan" && $method == "GET") {
  if(!isset($_REQUEST["n"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }

  $clanName = $_REQUEST["n"];

  $result = $db->query("SELECT ClanName FROM CLANS WHERE ClanName LIKE '%$clanName%';")->fetchAll();

  print(json_encode($result));
}

//POST CREATE CLAN
else if($path == "/create-clan" && $method == "POST") {
  if(!isset($_POST["clanName"]) && !isset($_POST["clanDescription"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  session_start();
  if (!isset($_SESSION["username"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
    die();
  }
  $username = $_SESSION["username"];
  

  $clanName = $_POST["clanName"];
  $clanDesc = $_POST["clanDescription"];

  $checkDuplicate = $db->query("SELECT * FROM CLANS WHERE ClanName = '$clanName';");
  if($checkDuplicate ->rowCount() != 0) {
    print(" Clan Name Taken");
    die();
  }
  $insertClan = $db->exec("INSERT INTO CLANS VALUES ('$clanName', '$username', '$clanDesc');");
  $insertUserClan = $db->exec("INSERT INTO USER_CLAN VALUES ('$username', '$clanName');");


  print("Clan Created");
}

//GET DELETE CLAN
else if($path == "/delete-clan" && $method == "GET") {
  if(!isset($_REQUEST["n"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  session_start();
  if (!isset($_SESSION["username"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
    die();
  }
  $username = $_SESSION["username"];
  $clanName = $_REQUEST["n"];

  $result = $db->query("SELECT ClanName, UserAdmin FROM CLANS WHERE clanName = '$clanName';")->fetchAll()[0];
  if(count($result) == 0){
    print("Clan does not exist");
    die();
  }
  
  $db->exec("DELETE FROM CLANS WHERE ClanName = '$clanName';");
  
  print("Clan Deleted");
}




else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}