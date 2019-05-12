<?php
include("../util/db.php");

function generateThumb($actual, $value)
{
  if ($value == 0) {
    print("thumb-off");
  } else if ($actual == $value) {
    print("thumb-on");
  } else {
    print("thumb-off");
  }
}

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
} else if ($path == "/comments" && $method == "GET") {

  if (!isset($_REQUEST["postId"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $postId = $_REQUEST["postId"];

  $comments = $db->query("SELECT * FROM COMMENTS WHERE post = '$postId' ORDER BY Date_Created DESC;")->fetchAll();


  foreach ($comments as $comment) {
    $comment_user = $comment["User"];

    $commentID = $comment["Comment_ID"];
    if ($commentID == NULL) {
      continue;
    }
    $react = $db->query("SELECT * FROM COMMENT_REACTIONS WHERE Comment = '$commentID' AND User = '$user'")->fetchAll();
    $value = 0;
    if (count($react) != 0) {
      $value = $react[0]["Value"];
    }

    $react = $db->query("SELECT IFNULL(COUNT(CASE WHEN cr.Value = '1' THEN 1 ELSE NULL END), 0) AS LikeCount, IFNULL(COUNT(CASE WHEN cr.Value = '-1' THEN 1 ELSE NULL END), 0) AS DislikeCount FROM COMMENTS c JOIN COMMENT_REACTIONS cr ON c.COMMENT_ID = cr.Comment WHERE c.COMMENT_ID = '$commentID';")->fetchAll()[0];
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
          <img src="<?= $GLOBALS["db"]->query("SELECT profilePicturePath FROM USERS WHERE UserName = '$comment_user';")->fetchAll()[0]["profilePicturePath"] ?>" alt="Profile Picture" style="float:left; width: 50px; border: 4px solid black;">
        </div>
        <div class="col-11">
          <a href="profile.php?p=<?= $comment_user ?>"><b><?= $comment_user ?></b></a>

          <blockquote class="blockquote mb-0">

            <?= $comment["Comment"] ?>

            <footer>
              <div>
                <i class="fa fa-thumbs-up <?php generateThumb(1, $value) ?> toggle-thumbs-comment" type="button"></i> <span class="thumbs-up-count"><?= $react["LikeCount"] ?></span>
                &nbsp;
                <i class="fa fa-thumbs-down <?php generateThumb(-1, $value) ?> toggle-thumbs-comment" type="button"></i> <span class="thumbs-up-down"><?= $react["DislikeCount"] ?></span>
                <input type="hidden" name="PostID" value="<?= $commentID ?>">
              </div>

              <small class="text-muted">
                Posted on <?= $comment["Date_Created"] ?>
              </small>
            </footer>
          </blockquote>
        </div>
      </div>
    </div>


    <script>
      $(".deleteCommentBtn").click((e) => {
        e.preventDefault();
        var commentid = $(e.target).prev().val();
        $.get(`../api/user-comment.php/delete/comment?u=<?= $_SESSION["username"] ?>&commentid=${commentid}`, (data) => {
          if (data == "comment deleted.") {
            $(e.target).parent().parent().remove();
          }
        });
      });

      $(".toggle-thumbs-comment").click((e) => {
        console.log("hjere");
        var target = $(e.target);
        var thumbClass;
        var value;

        if (target.hasClass("fa-thumbs-up")) {
          thumbClass = "fa-thumbs-up";
        } else {
          thumbClass = "fa-thumbs-down";
        }

        target.toggleClass("thumb-on thumb-off");


        if (target.hasClass("thumb-off")) {
          target.next().html(parseInt(target.next().html()) - 1);

          value = 0;
        } else {
          target.next().html(parseInt(target.next().html()) + 1);

          if (thumbClass == "fa-thumbs-up") {
            value = 1;
          } else {
            value = -1;
          }
        }

        var other;
        if (thumbClass == "fa-thumbs-down") {
          other = $(target.parent().children()[0]);
          if (other.hasClass("thumb-on") && target.hasClass("thumb-on")) {
            other.click();
          }

        } else {
          other = $(target.parent().children()[2]);
          if (other.hasClass("thumb-on") && target.hasClass("thumb-on")) {
            other.click();
          }
        }

        var formData = new FormData();
        formData.append("commentId", $(target.parent().children()[4]).val());
        formData.append("user", "<?= $_SESSION["username"] ?>");
        formData.append("value", value);

        $.ajax({
          type: "POST",
          url: "../api/user-reaction.php/react/comment?u=<?= $_SESSION["username"] ?>",
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          success: (res) => {
            console.log(res);
          }
        });
      });
    </script>
  <?php
  }

} else if ($path == "/delete/comment" && $method == "GET") {
  if (!isset($_REQUEST["commentid"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  $CommentID = $_REQUEST["commentid"];

  $db->query("DELETE FROM COMMENTS WHERE Comment_ID = '$CommentID';");

  print("comment deleted.");
} else {
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
?>