<?php
$db = new PDO("mysql:dbname=squad", "root", "");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];
$user = $_REQUEST["u"];
session_start();


//GET FEED
if ($path == "/feed" && $method == "GET") { }

//GET ABOUT
else if ($path == "/about" && $method == "GET") {
  $result = $db->query("SELECT About FROM USERS WHERE username = '$user';")->fetchAll()[0];
  ?>
  <div class="container">
    <h4>Update Profile Picture:</h4>
    <div class="input-group mb-3">
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="profilePictureUpload">
        <label class="custom-file-label" for="profilePictureUpload" aria-describedby="profilePictureUpload">Choose file</label>
      </div>
      <div class="input-group-append">
        <span class="input-group-text" id="profilePictureUpload">Upload</span>
      </div>
    </div>
  </div>

  <?php if ($_SESSION["username"] == $user) : ?>
  <div class="container">
      <div class="form-group">
        <h4>About</h4>
        <textarea class="form-control" id="aboutFormText" rows="3"><?= $result['About'] ?></textarea>
      </div>
  
      <button class="btn btn-primary" id="aboutFormTextBtn">Update</button>
      <div id="aboutStatus"></div>
  </div>


  <style>
 
  </style>


  <script>
    $("#aboutFormTextBtn").click((e) => {
      e.preventDefault();
      $.ajax({
          type: 'POST',
          url: '../api/user-about.php/update/about?u=<?= $user ?>',
          contentType: 'application/json',
          data: JSON.stringify({about: $("#aboutFormText").val()}), 
          succes: (res) => {
            $("#aboutProfileNavBtn").click();
          }
      });
    });
  </script>


  <?php else: ?>
    <div class="container">
      <div class="form-group">
        <label for="aboutFormText">About <?= $user ?>:</label>
        <textarea class="form-control" id="aboutFormText" rows="3" disabled><?= $result['About'] ?></textarea>
      </div>
    </div>
  <?php endif; ?>
  <?php
}

//GET FRIENDS
else if ($path == "/friends" && $method == "GET") {
  if (isLoggedIn()) {

    ?>
    <div class="container">

      <!-- Friend Request Form -->
      <?php if ($_SESSION["username"] == $user) : ?>
        <h3>Send Friend Request:</h3>
        <form id="formRequestFriend">
          <div class="form-group">
            <label for="addFriendFormTxt">Friend Name</label>
            <input type="text" class="form-control" id="sendFriendRequestTxt" placeholder="Enter friend name">
          </div>
          <button type="submit" class="btn btn-primary" id="sendRequestFriendBtn">Send</button>
          <div id="sendFriendRequestStatus" style="color: red;"></div>
        </form>
        <br>

        <script>
          $("#formRequestFriend").submit((e) => {
            e.preventDefault();
            $.ajax({
              url: `../api/user-friend.php/send?from=<?= $user ?>&to=${$("#sendFriendRequestTxt").val()}`,
              type: 'GET',
              success: function(res) {
                $("#sendFriendRequestStatus").html(res);
              }
            });
          })
        </script>
      <?php endif; ?>

      <!-- Friend List -->
      <h3>Friends: </h3>
      <ul class="list-group">
        <?php
        $rows = $db->query("SELECT friend FROM FRIENDS WHERE user = '$user';")->fetchAll();
        if ($rows == false || count($rows) == 0) {
          print("<li class='list-group-item'>$user has no friends.</li>");
        } else {
          foreach ($rows as $row) {
            ?>
            <a href="profile.php?p=<?= $row["friend"] ?>" class="list-group-item clearfix list-group-item-action">
              <span><?= $row["friend"] ?></span>

              <?php if ($_SESSION["username"] == $user) : ?>
                <div class="pull-right">
                  <button type="button" class="btn btn-md btn-danger" id="friendDeleteBtn">Delete</button>
                </div>
              <?php endif; ?>
            </a>
          <?php
        }
      }
      ?>
      </ul>
      <?php if ($_SESSION["username"] == $user) : ?>
        <script>
          $("#friendDeleteBtn").click((e) => {
            e.preventDefault();
            $.ajax({
              url: `../api/user-friend.php/delete?from=<?= $user ?>&to=${$(e.target).parent().prev().html()}`,
              type: 'GET',
              success: function(res) {
                $(e.target).closest('.list-group-item').remove();
                $("#friendsProfileNavBtn").click();
              }
            });
          });
        </script>
      <?php endif; ?>
    </div>
  <?php
}
}

//GET FRIEND REQUEST
else if ($path == "/friend-requests" && $method == "GET") {
  if (isLoggedIn()) {

    ?>
    <div class="container">
      <?php if ($_SESSION["username"] == $user) : ?>
        <h2>Friend Requests:</h2>
        <ul class="list-group">
          <?php
          $rows = $db->query("SELECT from_user FROM FRIEND_REQUESTS WHERE to_user = '$user';")->fetchAll();
          if ($rows == false || count($rows) == 0) {
            print("<li class='list-group-item'>You have no incoming friend requests</li>");
          } else {
            foreach ($rows as $row) {
              ?>
              <a href="profile.php?p=<?= $row["from_user"] ?>" class="list-group-item clearfix list-group-item-action">
                <span><?= $row["from_user"] ?></span>
                <div class="pull-right">
                  <button type="button" class="btn btn-md btn-primary" id="friendRequestAcceptBtn">Accept</button>
                  <button type="button" class="btn btn-md btn-danger" id="friendRequestDeclineBtn">Decline</button>
                </div>
              </a>
            <?php
          }
        }
        ?>
        </ul>


        <script>
          $("#friendRequestAcceptBtn").click((e) => {
            e.preventDefault();
            $.ajax({
              url: `../api/user-friend.php/accept?from=<?= $user ?>&to=${$(e.target).parent().prev().html()}`,
              type: 'GET',
              success: function(res) {
                $(e.target).closest('.list-group-item').remove();
                $("#friendRequestsProfileNavBtn").click();
              }
            });
          });

          $("#friendRequestDeclineBtn").click((e) => {
            e.preventDefault();
            $.ajax({
              url: `../api/user-friend.php/decline?from=<?= $user ?>&to=${$(e.target).parent().prev().html()}`,
              type: 'GET',
              success: function(res) {
                $(e.target).closest('.list-group-item').remove();
                $("#friendRequestsProfileNavBtn").click();
              }
            });
          });
        </script>
      <?php endif; ?>
    </div>
  <?php
}
}



// NO ROUTE MATCHED
else {
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}



function isLoggedIn()
{
  if (!isset($_SESSION["username"]) || !isset($_SESSION["token"])) {
    ?>
    <div class="jumbotron profileError" style="color: red;">
      <div class="display-4">
        Cannot See <?= $_REQUEST["u"] ?>'s page when not logged in.
      </div>
    </div>
    <?php
    return false;
  } else {
    return true;
  }
}
