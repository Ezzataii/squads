<?php
include("../util/db.php");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];



session_start();



//GET GAMES
if ($path == "/games" && $method == "GET") {

  $games = $db->query("SELECT * FROM GAMES;")->fetchAll();

  echo(json_encode($games));
}

//GET GAME SERVERS
else if ($path == "/servers" && $method == "GET") {
  if(!isset($_REQUEST["game"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }

  $game = $_REQUEST["game"];

  
  $servers = $db->query("SELECT Server FROM GAME_SERVERS WHERE Game = '$game';")->fetchAll();

  echo(json_encode($servers));
}




else { 
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

?>