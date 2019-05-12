<?php
include("../util/db.php");

function generateThumb($actual, $value) {
  if($value == 0) {
    print("thumb-off");
  } else if ($actual == $value) {
    print("thumb-on");
  } else {
    print("thumb-off");
  }
}

function createPost($Username, $Date_Created, $Text, $MediaType, $MediaPath, $PostID, $LikeCount, $DislikeCount, $ReactValue)
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
        <img src="<?= $GLOBALS["db"]->query("SELECT ProfilePicturePath FROM USERS WHERE UserName = '$Username';")->fetchAll()[0]["ProfilePicturePath"] ?>" alt="Profile Picture" style="float:left; width: 50px; border: 4px solid black;">
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
              <i class="fa fa-thumbs-up <?php generateThumb(1, $ReactValue) ?> toggle-thumbs-post" type="button"></i> <span class="thumbs-up-count"><?= $LikeCount ?></span>
              &nbsp;
              <i class="fa fa-thumbs-down <?php generateThumb(-1, $ReactValue) ?> toggle-thumbs-post" type="button"></i> <span class="thumbs-up-down"><?= $DislikeCount ?></span>
              <input type="hidden" name="PostID" value="<?= $PostID ?>">
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
      <button type="button" class="btn btn-link view-comments-btn" data-toggle="modal" data-target="#post-<?= $PostID ?>-comments">View Comments</button>
    </div>

  </div>

  <div id="post-<?= $PostID ?>-comments" class="modal fade" role="dialog" style="height: 100%; overflow-y: scroll;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Comments</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div id="post-<?= $PostID ?>-comments-body" class="modal-body">

        </div>
      </div>
    </div>
  </div>



<?php
}
?>


<script>
  $(() => {

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
        }
      });
    });


    $(".view-comments-btn").click((e) => {
      var target = $(e.target);

      var postID = $(target.parent().prev().children()[0]).val();

      $.get(`../api/user-comment.php/comments?u=<?= $_SESSION["username"] ?>&postId=${postID}`, (res) => {
        $(`#post-${postID}-comments-body`).html(res);
      });
    })


    $(".toggle-thumbs-post").click((e) => {
      var target = $(e.target);
      var thumbClass;
      var value;

      if (target.hasClass("fa-thumbs-up")) {
        thumbClass = "fa-thumbs-up";
      } else {
        thumbClass = "fa-thumbs-down";
      }

      target.toggleClass("thumb-on thumb-off");


      if(target.hasClass("thumb-off")) {
        target.next().html(parseInt(target.next().html()) - 1);

        value = 0;
      } else {
        target.next().html(parseInt(target.next().html()) + 1);

        if(thumbClass == "fa-thumbs-up") {
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
      formData.append("postId", $(target.parent().children()[4]).val());
      formData.append("user", "<?= $_SESSION["username"] ?>");
      formData.append("value", value);

      $.ajax({
        type: "POST",
        url: "../api/user-reaction.php/react/post?u=<?= $_SESSION["username"] ?>",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: (res) => {
          console.log(res);
        }
      });
    });


  });
</script>


<style>
  .modal-lg {
    max-width: 50% !important;
  }

  .post {
    margin: 10px;
    padding: 10px;
  }

  .comment {
    margin: 10px;
    padding: 10px;
  }



  .thumb-on {
    color: blue;
  }

  .thumb-off {
    color: black;
  }


  .toggle-thumbs-post {
    border: none;
  }

  .toggle-thumbs-comment {
    border: none;
  }
</style>