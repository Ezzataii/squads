<?php
include("../util/authHeader.php");
include("../util/db.php");

if (!isset($_REQUEST["n"])) { } else {
  $clanName = $_REQUEST["n"];

  $rows = $db->query("SELECT ClanName FROM CLANS WHERE ClanName = '$clanName';")->fetchAll();
  if (count($rows) == 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <?php include("../util/bootJQ.php") ?>
      <title><?= $_REQUEST["n"] ?></title>
    </head>

    <body>
      <?php include("../components/navbar.php"); ?>

      <div style="display: inline-block; padding-top: 50px;">
        <h1 class="display-4" style="float:left; color: red;"> &nbsp; <?= $clanName ?> Is Gone 🦀</h1>
      </div>
      <script>
      $(() => {
        new Audio('D:\\Data\\Downloads\\CrabRave.mp3').play();
      });
      
      </script>
      <?php include("../components/footer.php"); ?>
    </body>

    </html>

  <?php
} else {

  $clanMembers = $db->query("SELECT User FROM USER_CLAN WHERE Clan = '$clanName';")->fetchAll();
  $clanInfo = $db->query("SELECT Description, UserAdmin FROM CLANS WHERE ClanName = '$clanName';")->fetchAll()[0];

  ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">

      <?php include("../util/bootJQ.php") ?>

      <title>Clan</title>
    </head>

    <body>
      <?php include("../components/navbar.php"); ?>



      <div class="jumbotron">
        <h1 class="display-4"> <?= $clanName ?></h1>
        <p><?= $clanInfo["Description"] ?></p>

        

        <?php if($clanInfo["UserAdmin"] == $_SESSION["username"]): ?>   
          <button class="btn btn-danger" id="deleteClanBtn">Delete Clan</button>

          <script>

          $("#deleteClanBtn").click((e) => {
            $.get("../api/clan.php/delete-clan?n=<?= $clanName ?>", (res) => {
              console.log(res);
              location.reload();
            })
          })
          </script>
        <?php endif; ?>
      </div>


      <div class="container">
        <h3>Clan Members: </h3>
        <ul class="list-group" col="4">
          <?php
          foreach ($clanMembers as $member) {
            ?>
            <a href="clan.php?n=<?= $member["User"] ?>" class="list-group-item clearfix list-group-item-action">
              <span><?= $member["User"] ?></span>
            </a>
          <?php
        }
        ?>
        </ul>
      </div>

      <?php include("../components/footer.php"); ?>
    </body>

    </html>


    <script>

    </script>

  <?php
}
}
?>