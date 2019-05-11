<?php
include("../util/db.php");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];

if (!isset($_REQUEST["u"])) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
  die();
}
$user = $_REQUEST["u"];

session_start();
if (!isset($_SESSION["username"]) || $_SESSION["username"] != $user) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
  die();
}


if ($path == "/comment" && $method == "POST") {
  if (!isset($_POST["postId"]) || !isset($_POST["commentText"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $postId = $_POST["postId"];
  $commentText = $_POST["commentText"];
  $db->exec("INSERT INTO `Comments` (`Post`, `User`, `Comment`,`Date_Created`) 
  VALUES ('$postId', '$user', '$commentText', CURRENT_TIMESTAMP);");

  print("commented!");
} 


else if ($path == "/comments" && $method == "GET") {

  if (!isset($_REQUEST["postId"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $postId = $_REQUEST["postId"];

  $comments = $db->query("SELECT * FROM COMMENTS WHERE post = '$postId' ORDER BY Date_Created DESC;")->fetchAll();


  foreach ($comments as $comment) {
    $comment_user = $comment["User"];
    ?>
    <div class="card comment">

      <?php if (isset($_SESSION["username"]) &&  $_SESSION["username"] == $user) : ?>
        <div class="post-header">
          <input type="hidden" name="commentID" value="<?= $comment["Comment_ID"] ?>">
          <button type="button" class="close deleteCommentBtn">
            &times;
          </button>
        </div>
      <?php endif; ?>


      <div class="row">
        <div class="col-1">
          <img src="<?= $GLOBALS["db"]->query("SELECT profilePicturePath FROM USERS WHERE username = '$comment_user';")->fetchAll()[0]["profilePicturePath"] ?>" alt="Profile Picture" style="float:left; width: 50px; border: 4px solid black;">
        </div>
        <div class="col-11">
          <a href="profile.php?p=<?= $comment_user ?>"><b><?= $comment_user ?></b></a>

          <blockquote class="blockquote mb-0">

            <?= $comment["Comment"] ?>

            <footer>
              <div>
                <a class="toggle-thumbs"><i class="fa fa-thumbs-up black"></i> <span class="thumbs-up-count">60</span></a>
                &nbsp;
                <a class="toggle-thumbs"><i class="fa fa-thumbs-down black"></i> <span class="thumbs-up-down">68</span></a>
              </div>

              <small class="text-muted">
                Posted on <?= $comment["Date_Created"] ?>
              </small>
            </footer>
          </blockquote>
        </div>
      </div>
    </div>
  <?php
  }


} else if ($path == "/delete/comment" && $method == "GET") {
  if (!isset($_REQUEST["postid"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $PostID = $_REQUEST["postid"];

  $post = $db->query("SELECT MediaType, MediaPath FROM POSTS WHERE Post_ID = '$PostID';");

  if ($post == false) {
    print("post does not exist.");
    die();
  }

  $post = $post->fetchAll()[0];

  if ($post["MediaType"] == "Image" || $post["MediaType"] == "Video") {
    if (file_exists($post["MediaPath"])) {
      unlink($post["MediaPath"]);
    }
  }

  $db->query("DELETE FROM POSTS WHERE Post_ID = '$PostID';");

  print("post deleted.");
} else {
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
?>