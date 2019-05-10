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
      <input type="hidden" name="PostID" value="<?= $PostID ?>">
      <input type="text" class="form-control comment-input" placeholder="Enter your comment here" aria-label="Comment">
      <div class="input-group-append">
        <button class="btn btn-outline-primary comment-btn" type="button">Comment</button>
      </div>
    </div>

    <div class="row justify-content-center" style="margin-top: 10px;">
      <button type="button" class="btn btn-link" data-toggle="modal" data-target="#post-<?= $PostID ?>-comments">View Comments</button>
    </div>

  </div>

  <div id="post-<?= $PostID ?>-comments" class="modal fade" role="dialog" style="height: 100%; overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Comments</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <?= createComments($PostID, $Username); ?>
        </div>
      </div>
    </div>
  </div>



<?php
}


function createComments($PostID, $Username)
{
  $db = $GLOBALS["db"];

  $comments = $db->query("SELECT * FROM COMMENTS WHERE post = '$PostID';")->fetchAll();
  foreach ($comments as $comment) {
    $comment_user = $comment["User"];
    ?>
    <div class="card comment">

      <?php if (isset($_SESSION["username"]) &&  $_SESSION["username"] == $Username) : ?>
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
}
?>

<script>
  $(() => {
    function refreshPage() {
      var page_y = document.getElementsByTagName("body")[0].scrollTop;
      window.location.href = window.location.href.split('?')[0] + '?page_y=' + page_y;
    }

    setTimeout(refreshPage, 35000);
    if (window.location.href.indexOf('page_y') != -1) {
      var match = window.location.href.split('?')[1].split("&")[0].split("=");
      document.getElementsByTagName("body")[0].scrollTop = match[1];
    }

    $(".deletePostBtn").click((e) => {
      e.preventDefault();
      var postid = $(e.target).prev().val();
      $.get(`../api/user-post.php/delete/post?u=<?= $_SESSION["username"] ?>&postid=${postid}`, (data) => {
        if (data == "post deleted.") {
          $(e.target).parent().parent().remove();
        }
      });
    });

    $(".comment-btn").click((e) => {
      e.preventDefault();
      var target = $(e.target);
      var formData = new FormData();
      formData.append("postId", $(target.parent().parent().children()[0]).val());
      formData.append("commentText", $(target.parent().parent().children()[1]).val());


      $.ajax({
        type: "POST",
        url: "../api/user-comment.php/comment?u=<?= $_SESSION["username"] ?>",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: (res) => {
          $(target.parent().parent().children()[1]).val("");
          location.reload();
        }
      });
    });


    $(".toggle-thumbs").click((e) => {
      $(e.target).find("i").toggleClass("black blue");
    });

  });
</script>


<style>
  .post {
    margin: 10px;
    padding: 10px;
  }

  .comment {
    margin: 10px;
    padding: 10px;
  }

  .black {
    color: black;
  }

  .blue {
    color: blue;
  }

  .modal-lg {
    max-width: 50% !important;
  }
</style>