<?php
include("../util/authHeader.php");
include("../util/db.php");


if (!isset($_SESSION["ign"])) {
  if (!isset($_POST["ign"]) || !isset($_POST["gameTitle"]) || !isset($_POST["gameServer"]) || !isset($_POST["status"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  } else {
    $_SESSION["ign"] = $_POST["ign"];
    $_SESSION["gameTitle"] = $_POST["gameTitle"];
    $_SESSION["gameServer"] = $_POST["gameServer"];
    $_SESSION["status"] = $_POST["status"];
  };
}




if (!isset($_POST["ign"]) || !isset($_POST["gameTitle"]) || !isset($_POST["gameServer"]) || !isset($_POST["status"])) {
  header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
  die();
}

$ign = $_SESSION["ign"];
$gameTitle = $_SESSION["gameTitle"];
$gameServer = $_SESSION["gameServer"];
$status = $_SESSION["status"];
$sessionUser = $_SESSION["username"];



if ($status != "LFG" && $status != "LFM") {
  header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
  die();
}


$db->exec("INSERT INTO SOLOS (ign, user, server, status, game) VALUES ('$ign', '$sessionUser', '$gameServer', '$status', '$gameTitle');");

if($status == "LFM") {
  $db->exec("INSERT INTO GROUPS (Name, Admin, MaxSize, Status, Description, Game, Server) VALUES ('$ign', '$sessionUser', '$gameServer', '$status', '$gameTitle');");
}
 ?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <?php include("../util/bootJQ.php") ?>

  <title>Home</title>
</head>

<body>
  <?php include("../components/navbar.php"); ?>
  <!-- LFG -->
  <?php if ($status == "LFG") : ?>
    <div class="jumbotron">
      <h1 class="display-4">Looking For Group</h1>
    </div>

    <div class="container">
      <h4>Groups</h4>
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Admin</th>
            <th scope="col">Size</th>
            <th scope="col">Game</th>
            <th scope="col">Server</th>
          </tr>
        </thead>
        <tbody id="group-table-body">

        </tbody>
      </table>
    </div>


    <script>
      $(() => {
        function getGroups() {
          $.get(`../api/looking-for.php/groups?game=<?= $gameTitle ?>&server=<?= $gameServer ?>`, (res) => {
            console.log(res);
            var json = JSON.parse(res);
            console.log(json);
            
            for (var i = 0; i < json.length; i++) {
              $("#group-table-body").append($(`<tr id='group-table-row-${i}'></td>`));
              var tr = $(`#group-table-row-${i}`);
              tr.append($(`<td>${i + 1}</td>`));
              tr.append($(`<td>${json[i].Name}</td>`));
              tr.append($(`<td>${json[i].Admin}</td>`));
              tr.append($(`<td>${json[i].Size} / ${json[i].MaxSize}</td>`));
              tr.append($(`<td>${json[i].Game}</td>`));
              tr.append($(`<td>${json[i].Server}</td>`));
            }
          })
        }

        getGroups()


      })
    </script>


    <!-- LFM -->
  <?php elseif ($status == "LFM") : ?>
    <div class="jumbotron">
      <h1 class="display-4">Looking For More</h1>
    </div>

    <div class="container">
      <h4>Members</h4>
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">IGN</th>
            <th scope="col">Game Title</th>
            <th scope="col">Game Server</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody id="member-table-body">

        </tbody>
      </table>
    </div>

    <script>
      $(() => {

        function getMembers() {
          $.get(`../api/looking-for.php/members?game=<?= $gameTitle ?>&server=<?= $gameServer ?>`, (res) => {
            var json = JSON.parse(res);
            for (var i = 0; i < json.length; i++) {
              console.log(json[i].IGN, json[i].Game, json[i].Server, json[i].Status);
              $("#member-table-body").append($(`<tr id='member-table-row-${i}'></td>`));
              var tr = $(`#member-table-row-${i}`);
              tr.append($(`<td>${i}</td>`));
              tr.append($(`<td>${json[i].IGN}</td>`));
              tr.append($(`<td>${json[i].Game}</td>`));
              tr.append($(`<td>${json[i].Server}</td>`));
              tr.append($(`<td>${json[i].Status}</td>`));
            }
          })
        }

        getMembers();

      })
    </script>
  <?php endif; ?>

  <?php include("../components/footer.php"); ?>
</body>

</html>


<script>

</script>