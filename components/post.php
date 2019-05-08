<?php
include("../util/db.php");



function createPost($Username, $Date_Created, $Text, $MediaType, $MediaPath, $PostID)
{
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  ?>
  <div class="card post">

    <?php if (isset($_SESSION["username"]) &&  $_SESSION["username"] == $Username) : ?>
      <div class="post-header">
        <input type="hidden" name="PostID" value="<?= $PostID ?>">
        <button type="button" class="close deletePostBtn">
          &times;
        </button>
      </div>
    <?php endif; ?>


    <div class="row">
      <div class="col-1">
        <img src="<?= $GLOBALS["db"]->query("SELECT profilePicturePath FROM USERS WHERE username = '$Username';")->fetchAll()[0]["profilePicturePath"] ?>" alt="Profile Picture" style="float:left; width: 50px; border: 4px solid black;">
      </div>
      <div class="col-11">
        <a href="profile.php?p=<?= $Username ?>"><b><?= $Username ?></b></a>

        <blockquote class="blockquote mb-0">
          <p> <?= $Text ?> </p>

          <?php if ($MediaType == "Text") : ?>

          <?php elseif ($MediaType == "Image") : ?>
            <img src="<?= $MediaPath ?>">
          <?php elseif ($MediaType == "Video") : ?>
            <video width="320" height="240" controls>
              <source src="<?= $MediaPath ?>" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          <?php endif; ?>



          <footer>
            <div>
              <a class="toggle-thumbs"><i class="fa fa-thumbs-up black"></i> <span class="thumbs-up-count">60</span></a>
              &nbsp;
              <a class="toggle-thumbs"><i class="fa fa-thumbs-down black"></i> <span class="thumbs-up-down">68</span></a>
            </div>

            <small class="text-muted">
              Posted on <?= $Date_Created ?>
            </small>
          </footer>
        </blockquote>
      </div>
    </div>
    <div style="height: 20px;"></div>
    <div class="input-group">
      <input type="text" class="form-control comment-input" placeholder="Enter your comment here" aria-label="Comment">
      <div class="input-group-append">
        <button class="btn btn-outline-primary" type="button" id="send-msg-btn">Comment</button>
      </div>
    </div>
  </div>



<?php
}
?>

<script>
  $(".deletePostBtn").click((e) => {
    e.preventDefault();
    var postid = $(e.target).prev().val();
    $.get(`../api/user-post.php/delete/post?u=<?= $_SESSION["username"] ?>&postid=${postid}`, (data) => {
      if (data == "post deleted.") {
        $(e.target).parent().parent().remove();
      }
    });
  });

  $(".toggle-thumbs").click((e) => {
    $(e.target).find("i").toggleClass("black blue");
  })
</script>


<style>
  .post {
    margin: 10px;
    padding: 10px;
  }

  .black {
    color: black;
  }

  .blue {
    color: blue;
  }
</style>