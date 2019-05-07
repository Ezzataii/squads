<?php
include("../util/db.php");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];
$user = $_REQUEST["u"];
session_start();


//GET FEED
if ($path == "/timeline" && $method == "GET") {
  ?> 
  <div class="container">
  <?php if (isset($_SESSION["username"]) && $_SESSION["username"] == $user) : 
    include("../components/postForm.php"); 

    $posts = $db->query("SELECT * FROM POSTS WHERE user = '$user' ORDER BY Date_Created DESC;")->fetchAll();
    include("../components/post.php");
    foreach ($posts as $post) {
      createPost($post["User"], $post["Date_Created"], $post["Text"], $post["MediaType"], $post["MediaPath"], $post["Post_ID"]);
    }
    ?>
  <?php else :

    //TODO ADD VISIBILITY 

    $posts = $db->query("SELECT * FROM POSTS p JOIN USERS u ON p.User = u.Username WHERE user = '$user' AND u.LevelOfAccess = 'public' ORDER BY Date_Created DESC;")->fetchAll();
    include("../components/post.php");
    foreach ($posts as $post) {
      createPost($post["User"], $post["Date_Created"], $post["Text"], $post["MediaType"], $post["MediaPath"], $post["Post_ID"]);
    }
    endif;?>

  </div>

<?php
}

//GET ABOUT
else if ($path == "/about" && $method == "GET") {
  $result = $db->query("SELECT About FROM USERS WHERE username = '$user';")->fetchAll()[0];
  ?>

  <?php if ($_SESSION["username"] == $user) : ?>

    <div class="container">
      <h4>Update Profile Picture</h4>
      <div class="input-group mb-3">
        <div class="custom-file">
          <input type="file" class="custom-file-input" id="profilePictureFileInput">
          <label class="custom-file-label" for="profilePictureFileInput">Choose file</label>
        </div>
        <div class="input-group-append">
          <button class="btn btn-primary" id="uploadProfilePictureBtn">Upload</button>
        </div>
      </div>
      <span id="profilePictureStatus"></span>
    </div>

    <div class="container">
      <h4>Update Profile Level of Access</h4>
      
        
      <label class="radio-inline">
        <input type="radio" name="levelOfAccessRadio" id="levelOfAccessRadioPublic" value="public">
        Public
      </label> &nbsp;
      <label class="radio-inline">
        <input type="radio" name="levelOfAccessRadio" id="levelOfAccessRadioFriendsOnly" value="friends-only">
        Friends Only
      </label> &nbsp;
      <label>
        <input type="radio" name="levelOfAccessRadio" id="levelOfAccessRadioPrivate" value="private">
        Private
      </label>
      <br>
      <button class="btn btn-primary" id="levelOfAccessBtn">Update</button>
      <span id="levelOfAccessStatus"></span>
    </div>
    <br>


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
      $(() => {
        $.get("../api/user-about.php/level-of-access?u=<?= $user ?>", (data) => {
          $(`input:radio[name="levelOfAccessRadio"][value=${data}]`).prop("checked", true);
        });
        
      }); 

      $('#profilePictureFileInput').change((e) => {
        var fileName = e.target.files[0].name;
        $(e.target).next('.custom-file-label').html(fileName);
      });

      $("#uploadProfilePictureBtn").click((e) => {
        var imageData = new FormData();
        imageData.append('profilePicture', $("#profilePictureFileInput").prop('files')[0]);
        $.ajax({
          type: 'POST',
          url: '../api/user-about.php/update/profile-picture?u=<?= $user ?>',
          data: imageData,
          cache: false,
          contentType: false,
          processData: false,
          success: (res) => {
            $("#profilePictureStatus").html(res);
          }
        });
      });

      $("#levelOfAccessBtn").click((e) => {
        e.preventDefault();
        var levelOfAccess = $(`input:radio[name="levelOfAccessRadio"]:checked`).val();
        $.get(`../api/user-about.php/update/level-of-access?u=<?= $user ?>&l=${levelOfAccess}`, (data) => {
          $("#levelOfAccessStatus").html(data);
        });
      });

      $("#aboutFormTextBtn").click((e) => {
        e.preventDefault();
        $.ajax({
          type: 'POST',
          url: '../api/user-about.php/update/about?u=<?= $user ?>',
          contentType: 'application/json',
          data: JSON.stringify({
            about: $("#aboutFormText").val()
          }),
          success: (res) => {
            $("#aboutStatus").html(res);
          }
        });
      });
    </script>


  <?php else : ?>
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
