<?php
include("../util/defHeader.php");
include("../util/db.php");


if (!isset($_REQUEST["p"])) { 

} else {
  $profile = $_REQUEST["p"];

  $rows = $db->query("SELECT username FROM USERS WHERE username = '$profile';")->fetchAll();
  if (count($rows) == 0) {
  ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <?php include("../util/bootJQ.php") ?>
      <title><?= $_REQUEST["p"] ?></title>
    </head>

    <body>
      <?php include("../components/navbar.php"); ?>

      <div style="display: inline-block; padding-top: 50px;">
        <h1 class="display-4" style="float:left; color: red;"> &nbsp; <?= $_REQUEST["p"] ?>Does Not Exist</h1>
      </div>

      <?php include("../components/footer.php"); ?>
    </body>

    </html>

  <?php
  } else {
  ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">

      <?php 
        include("../util/bootJQ.php"); 
        $user = $_REQUEST["p"];
      ?>

      <title><?= $_REQUEST["p"] ?></title>
    </head>

    <body>
      <?php include("../components/navbar.php"); ?>

      <!-- Profile Header -->
      <div class="jumbotron profile-header">

        <div style="display: inline-block; padding-top: 50px;">
          <img src="<?= $db->query("SELECT profilePicturePath FROM USERS WHERE username = '$user';")->fetchAll()[0]["profilePicturePath"] ?>" 
          alt="Profile Picture" style="float:left; width: 100px; margin: 0px 30px; border: 4px solid black;" id="profilePicture">
          <h1 class="display-4" style="float:left">  <?= $_REQUEST["p"] ?>'s Profile</h1>
          <h4>(<?= $db->query("SELECT LevelOfAccess FROM USERS WHERE UserName = '$user';")->fetchAll()[0]["LevelOfAccess"] ?>)</h4>
        </div>

        <nav class="nav nav-tabs profile-nav">
          <li class="nav-item">
            <a class="nav-link profile-navbtn active" href="timeline">Timeline</a>
          </li>
          <li class="nav-item">
            <a class="nav-link profile-navbtn" href="about" id="aboutProfileNavBtn">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link profile-navbtn" href="friends" id="friendsProfileNavBtn">Friends</a>
          </li>
          <?php if (isset($_SESSION["username"]) && $_REQUEST["p"] == $_SESSION["username"]) : ?>
            <li class="nav-item">
              <a class="nav-link profile-navbtn" href="friend-requests" id="friendRequestsProfileNavBtn">Friend Requests</a>
            </li>
          <?php endif; ?>
        </nav>
      </div>


      <!-- Profile Body -->
      <div class="profile-body">

      </div>

      <?php include("../components/footer.php"); ?>
    </body>

    </html>

    <script>
      $(() => {
        $(".profile-navbtn[href=timeline]")[0].click();
      });

      $(".profile-navbtn").click((e) => {
        e.preventDefault();
        $(".profile-navbtn").removeClass("active");
        $(e.target).addClass("active");

        $.ajax({
          url: `../api/profileTab.php/${$(e.target).attr('href')}?u=<?= $_REQUEST["p"] ?>`,
          type: 'GET',
          success: (res) => {
            $(".profile-body").html(res);
          }
        });
      });
    </script>







    <style>
      .profile-header {
        position: relative;
        padding: 0;
        height: 250px;
      }

      .profile-nav {
        position: absolute;
        bottom: 0;
        margin: 0;
        padding: 0;
        width: 100%;
        font-size: 16pt;
      }
    </style>

  <?php
  }
}
?>