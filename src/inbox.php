<?php
include("../util/authHeader.php");
include("../util/db.php");
$user = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <?php include("../util/bootJQ.php") ?>

  <title>Inbox</title>
</head>

<body>
  <?php include("../components/navbar.php"); ?>


  <div class="container-fluid" style="height: 100px; background-color: #E9ECEF;">
    <br>
    <h1 class="center">Inbox</h1>
  </div>


  <div class="container-fluid" style="margin-top: 30px;">
    <div class="row">
      <div class="col-2 ">
        <h4 class="container">Friends</h4>
        <ul class="list-group friends-list-container">
          <?php
          $rows = $db->query("SELECT friend FROM FRIENDS WHERE user = '$user';")->fetchAll();
          if ($rows == false || count($rows) == 0) {
            print("<li class='list-group-item'>$user has no friends.</li>");
          } else {
            foreach ($rows as $row) {
              ?>
              <a href="" class="list-group-item clearfix list-group-item-action friend-btn">
                <span><?= $row["friend"] ?></span>
              </a>
            <?php
          }
        }
        ?>
      </div>

      <div class="col-10">
        <div class="msg-box-container">
          <div id="msg-box"></div>
        </div>

        <div class="panel">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" id="Message-Username"><?= $_SESSION["username"] ?>:</span>
            </div>
            <input type="text" class="form-control" placeholder="Enter your message here" aria-label="Message" aria-describedby="Message-Username" id="message-input">
            <div class="input-group-append">
              <button class="btn btn-outline-primary" type="button" id="send-msg-btn">Send</button>
            </div>
          </div>
        </div>

      </div>



    </div>
  </div>



  <?php include("../components/footer.php"); ?>
</body>

</html>

<script>
  var friendName;
  var friendtab;

  $(".friend-btn").click((e) => {
    e.preventDefault();
    friendtab = $(e.target);
    friendName = friendtab.find("span").html();

    $url = `../api/user-messaging.php/get-convo?s=<?= $_SESSION["username"] ?>&r=${friendName}`;
    var userName = "<?= $_SESSION["username"] ?>";

    $.ajax({
      url: $url,
      type: 'GET',
      success: (res) => {
        var msgs = JSON.parse(res);
        $("#msg-box").html("");
        var name;
        var spanClass = "sender-name";
        var divClass = "sender-div";

        msgs.forEach(msg => {
          if (msg.Sender == userName) {
            name = "You";
            spanClass = "sender-span";
            divClass = "sender-div msg";
          } else {
            name = msg.Sender;
            spanClass = "receiver-span";
            divClass = "receiver-div msg";
          }
          $("#msg-box").html($("#msg-box").html() + `<div class = \"${divClass}\"><span class = \"${spanClass}\"> ${name}:</span> ${msg.Message} <br> <small class="text-muted" style="font-size: 10pt;">${msg.Date_Created}</small> </div>`);
        });
      }
    });
  });


  $("#send-msg-btn").click((e) => {
    e.preventDefault();
    
    var message = $("#message-input").val();

    if (message == ""){
      alert("Enter a message to be sent!");
    }

    else {
      $url = `../api/user-messaging.php/send-message?s=<?= $_SESSION["username"] ?>&r=${friendName}`;
      var userName = "<?= $_SESSION["username"] ?>";


      $.ajax({
      url: $url,
      type: 'POST',
      data:{
        Message: message
      },
      success: (res) => {
        // alert(res);
        $("#message-input").val("");
        friendtab.click();
      }
    });
    }

  });

  $('#message-input').keypress((event) => {
    var key = event.which;
    if(key == 13)  {
      $("#send-msg-btn").click();
    }
  })

</script>


<style>
  .friends-list-container {
    height: 700px;
    overflow-y: scroll;
  }

  .msg-box-container {
    margin-top: 35px;
    margin-bottom: 10px;
    height: 650px;
    overflow-y: scroll;

    border: solid #343A40 2px;
    background-color: #D9DCDF;
  }

  .sender-span {
    color: red
  }

  .receiver-span {
    color: blue
  }


  .sender-div {
    background-color: #F9FCFF;

  }

  .receiver-div {
    background-color: #E9ECEF;
  }

  .msg {
    margin-top: 5px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 30px;

    font-size: 18pt;

    border-radius: 15px;
  }
</style>