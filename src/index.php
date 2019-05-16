<?php
include("../util/authHeader.php");
include("../util/db.php");


unset($_SESSION["ign"]);
unset($_SESSION["gameTitle"]);
unset($_SESSION["gameServer"]);
unset($_SESSION["status"]);
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


  <div class="jumbotron">
    <h1 class="display-4">Hello, <?= $_SESSION["username"] ?></h1>
  </div>

  <div class="container">
    <form action="looking-for-results.php" method="POST">

      <h3>LFM/LFG</h3>
      <br>

      <h4>In Game Name:</h4>
      <input type="text" class="form-control" id="ignInput" placeholder="enter in game name" name="ign">
      <br>


      <h4>Select Game:</h4>
      <select id="gameTitleSelect" name="gameTitle">

      </select>
      <br>
      <br>

      <h4>Select Game Server:</h4>
      <select id="gameServerSelect" name="gameServer" disabled>

      </select>
      <br>
      <br>

      <h4>What do you need?</h4>
      <label class="radio-inline">
        <input type="radio" name="status" value="LFG" checked>
        Group
      </label> &nbsp;
      <label class="radio-inline">
        <input type="radio" name="status" value="LFM">
        Players
      </label> &nbsp;
      <br>

      <div id="maxSizeInput" hidden>
        <h4>Max Size:</h4>
        <input type="number" name="maxSize" placeholder="5" >
      </div>

      <div id="descriptionInput" hidden>
        <h4>Description</h4>
        <input type="text" name="description" placeholder="enter description">
      </div>
      <br>

      <input id="lookingForFormSubmit" class="btn btn-primary" type="submit" value="Submit" disabled>
    </form>
  </div>










  <?php include("../components/footer.php"); ?>
</body>

</html>


<script>
  $(() => {

    function fillGameTitleSelect() {
      var select = $("#gameTitleSelect");

      $.get("../api/games.php/games", (res) => {
        var json = JSON.parse(res);

        for (var i = 0; i < json.length; i++) {
          select.append($('<option>', {
            value: json[i].Title,
            text: json[i].Title
          }));
        }
      });
    }

    function fillGameServerSelect(game) {
      var select = $("#gameServerSelect");
      $("#lookingForFormSubmit").prop("disabled", false);
      select.prop("disabled", false);
      select.html("");

      $.get(`../api/games.php/servers?game=${game}`, (res) => {
        var json = JSON.parse(res);

        for (var i = 0; i < json.length; i++) {
          select.append($('<option>', {
            value: json[i].Server,
            text: json[i].Server
          }));
        }
      });
    }


    fillGameTitleSelect();


    $("#gameTitleSelect").change((e) => {
      fillGameServerSelect($('select[id="gameTitleSelect"]').val());
    })

    $('input:radio[name=\"status\"]').change(() => {
      var radio = $('input[name=\"status\"]:checked');
      if(radio.val() == "LFG") {
        $("#maxSizeInput").prop("hidden", true);
        $("#descriptionInput").prop("hidden", true);
      } else {
        $("#maxSizeInput").prop("hidden", false);
        $("#descriptionInput").prop("hidden", false);
      }
    });

  })
</script>