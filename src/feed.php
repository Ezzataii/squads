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

    $posts = $db->query("SELECT Users AS User, Date_Created, Text, MediaType, MediaPath, Post_ID FROM ( SELECT f.Friend AS Users FROM USERS u RIGHT JOIN FRIENDS f ON u.UserName = f.User WHERE u.Username = '$user' UNION SELECT UserName FROM USERS WHERE Username = '$user') u JOIN POSTS p ON p.User = u.Users LEFT JOIN POST_REACTIONS pr ON pr.Post = p.Post_ID ORDER BY Date_Created DESC;")->fetchAll();

    // $posts = $db->query("SELECT DISTINCT ")

    include("../components/post.php");
    foreach ($posts as $post) {

      $postId = $post["Post_ID"];
      if($postId == NULL) {
        continue;
      }
      $react = $db->query("SELECT * FROM POST_REACTIONS WHERE Post = '$postId' AND User = '$user'")->fetchAll();
      $value = 0;
      if(count($react) != 0) {
        $value = $react[0]["Value"];
      }

      $react = $db->query("SELECT IFNULL(COUNT(CASE WHEN pr.Value = '1' THEN 1 ELSE NULL END), 0) AS LikeCount,IFNULL(COUNT(CASE WHEN pr.Value = '-1' THEN 1 ELSE NULL END), 0) AS DislikeCount FROM POSTS p JOIN POST_REACTIONS pr ON p.Post_ID = pr.Post WHERE p.Post_ID = '$postId';")->fetchAll()[0];

      createPost($post["User"], $post["Date_Created"], $post["Text"], $post["MediaType"], $post["MediaPath"], $post["Post_ID"], $react["LikeCount"], $react["DislikeCount"], $value);
    }
    ?>
  </div>




  <?php include("../components/footer.php"); ?>
</body>
</html>