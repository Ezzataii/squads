<?php
include("../util/db.php");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];



session_start();
if(!isset($_SESSION["username"])) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
  die();
}

$username = $_SESSION["username"];


//GET GAMES
if ($path == "/members" && $method == "GET") {
  $statement = "SELECT * FROM SOLOS WHERE Status = 'LFG'";

  if(isset($_REQUEST["game"])) {
    $game = $_REQUEST["game"];
    $statement = $statement." AND Game = '$game'";
  }

  if(isset($_REQUEST["server"])) {
    $server = $_REQUEST["server"];
    $statement = $statement." AND Server = '$server'";
  }
  $statement = $statement.";";

  $members = $db->query($statement)->fetchAll();

  echo(json_encode($members));
}


//GET GAME SERVERS
else if ($path == "/groups" && $method == "GET") {
  //$statement = "SELECT Name, Admin, MaxSize, Status, Description, Game, Server, COUNT(sg.Solo) as Size FROM GROUPS g LEFT OUTER JOIN SOLO_GROUP sg ON g.Name = sg.Group";
  $statement = "SELECT * FROM `groups_count` WHERE Status = 'lfm'";

  if(isset($_REQUEST["game"])) {
    $game = $_REQUEST["game"];
    $statement = $statement." AND Game = '$game'";
  }

  if(isset($_REQUEST["server"])) {
    $server = $_REQUEST["server"];
    $statement = $statement." AND Server = '$server'";
  }
  $statement = $statement.";";
  $groups = $db->query($statement)->fetchAll();

  echo(json_encode($groups));
}




else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

?>