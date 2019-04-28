<?php include("../util/authHeader.php") ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <?php include("../util/bootJQ.php") ?>

  <title>Home</title>
</head>
<body>
  <?php include("../components/navbar.php"); ?>

  
  <div class="jumbotron">
    <h1 class="display-4">Hello, <?= $_SESSION["username"] ?></h1>
  </div>


  <?php include("../components/footer.php"); ?>
</body>
</html>