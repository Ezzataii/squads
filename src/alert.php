<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <?php include("../util/bootJQ.php") ?>

  <title>Alert</title>
</head>

<body>
  <?php include("../components/navbar.php"); ?>

  
  <div class="jumbotron">
    <h1 class="display-4">
      <?php 
        if(isset($_REQUEST["msg"])) {
          if($_REQUEST["msg"] = "Authenticated") {
            print("Authenticated! Please login.");
          } else if($_REQUEST["msg"] = "NotAuthenticated") {
            print("Authentication link is dead or expired.");
          } else {
            print("What");
          }
        }
      ?>
    </h1>
  </div>


  <?php include("../components/footer.php"); ?>
</body>
</html>
<?php
