<?php
include("../util/db.php");



function createPost($Username, $Date_Created, $Text, $MediaType, $MediaPath, $PostID)
{
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  ?>
  <div class="card post">

    <?php if ($_SESSION["username"] == $Username): ?>
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
            <small class="text-muted">
              Posted on <?= $Date_Created ?>
            </small>
          </footer>
        </blockquote>
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

</script>


<style>
  .post {
    margin: 10px;
    padding: 10px;
  }
</style>