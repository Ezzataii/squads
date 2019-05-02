<?php 
  include("../util/authHeader.php");
  include("../util/db.php");
  $user = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <?php include("../util/bootJQ.php") ?>

  <title>Feed</title>
</head>
<body>
  <?php include("../components/navbar.php"); ?>

  
  <div class="jumbotron">
    <h1 class="display-4"><?= $_SESSION["username"] ?>'s Feed</h1>
  </div>


  <!-- FEED  -->
  <div class="container">
    <?php 
    include("../components/postForm.php"); 


    $posts = $db->query("SELECT DISTINCT p.User as User, Date_Created, Text, MediaType, MediaPath, Post_ID FROM POSTS p JOIN FRIENDS f ON p.User = f.user JOIN USERS u ON u.UserName = p.User WHERE u.LevelOfAccess = 'public' OR u.LevelOfAccess = 'friends-only' ORDER BY Date_Created DESC;")->fetchAll();
    include("../components/post.php");
    foreach ($posts as $post) {
      createPost($post["User"], $post["Date_Created"], $post["Text"], $post["MediaType"], $post["MediaPath"], $post["Post_ID"]);
    }
    ?>
  </div>




  <?php include("../components/footer.php"); ?>
</body>
</html>