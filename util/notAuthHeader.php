<?php
  session_start();

  if (isset($_SESSION["username"]) && isset($_SESSION["token"])) {
    header("Location: ../src/index.php");
    die();
  }
?>
