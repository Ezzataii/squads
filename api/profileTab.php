<?php
include("../util/db.php");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];
$user = $_REQUEST["u"];
session_start();


//GET TIMELINE
if ($path == "/timeline" && $method == "GET") {
  ?>
  <div class="container">
    <?php if (isset($_SESSION["username"]) && $_SESSION["username"] == $user) :
      include("../components/postForm.php");

    endif;

    $posts = [];

    if (isset($_SESSION["username"])) {
      $sessionUser = $_SESSION["username"];
    }

    if (isset($_SESSION["username"]) && $_SESSION["username"] == $user) {
      $posts = $db->query("SELECT p.User as User, Date_Created, Text, MediaType, MediaPath, Post_ID FROM POSTS p WHERE p.User = '$user' ORDER BY Date_Created DESC;")->fetchAll();
    } else if (isset($_SESSION["username"]) && count($db->query("SELECT * FROM FRIENDS WHERE User = '$user' AND Friend = '$sessionUser';")->fetchAll()) != 0) {
      $posts = $db->query("SELECT p.User as User, Date_Created, Text, MediaType, MediaPath, Post_ID FROM POSTS p WHERE p.User = '$user' AND (p.LevelOfAccess = 'friends-only' OR p.LevelOfAccess = 'public') ORDER BY Date_Created DESC;")->fetchAll();
    } else {
      $posts = $db->query("SELECT p.User as User, Date_Created, Text, MediaType, MediaPath, Post_ID FROM POSTS p WHERE p.User = '$user' AND p.LevelOfAccess = 'public' ORDER BY Date_Created DESC;")->fetchAll();
    }

    include("../components/post.php");

    foreach ($posts as $post) {

      $postId = $post["Post_ID"];
      if ($postId == NULL) {
        continue;
      }
      $sessionUser = $_SESSION["username"];
      $react = $db->query("SELECT * FROM POST_REACTIONS WHERE Post = '$postId' AND User = '$sessionUser'")->fetchAll();
      $value = 0;
      if (count($react) != 0) {
        $value = $react[0]["Value"];
      }

      $react = $db->query("SELECT IFNULL(COUNT(CASE WHEN pr.Value = '1' THEN 1 ELSE NULL END), 0) AS LikeCount,IFNULL(COUNT(CASE WHEN pr.Value = '-1' THEN 1 ELSE NULL END), 0) AS DislikeCount FROM POSTS p JOIN POST_REACTIONS pr ON p.Post_ID = pr.Post WHERE p.Post_ID = '$postId';")->fetchAll()[0];

      createPost($post["User"], $post["Date_Created"], $post["Text"], $post["MediaType"], $post["MediaPath"], $post["Post_ID"], $react["LikeCount"], $react["DislikeCount"], $value);
    }

    ?>


  </div>

<?php
}

//GET ABOUT
else if ($path == "/about" && $method == "GET") {
  $result = $db->query("SELECT About FROM USERS WHERE UserName = '$user';")->fetchAll()[0];
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
    <br>
    <br>


    <div class="container">
      <h4>Reset Password</h4>

      <form id="resetPasswordForm">
        <div class="form-group">
          <label for="newPassword">New Password</label>
          <input type="password" class="form-control" id="newPassword" placeholder="enter new password" name="newPassword">
        </div>
        <div class="form-group">
          <label for="confirmNewPassword">Confirm New Password</label>
          <input type="password" class="form-control" id="confirmNewPassword" placeholder="confirm new password" name="confirmNewPassword">
        </div>

        <button type="submit" class="btn btn-primary">Reset</button>

        <div>
          <div id="resetPasswordStatus"></div>
        </div>
      </form>
    </div>
    <br>
    <br>


    <div class="container">
      <h4>Account Status</h4>
      <button class="btn btn-danger" id="deactivateAccountBtn">Deactivate Account</button>
      &nbsp;
      &nbsp;
      <button class="btn btn-danger" id="deleteAccountBtn"> Delete Account</button>
    </div>

    <style>

    </style>


    <script>
      $(() => {
        $.get("../api/user-about.php/level-of-access?u=<?= $user ?>", (data) => {
          $(`input:radio[name="levelOfAccessRadio"][value=${data}]`).prop("checked", true);
        });

      });

      $("#resetPasswordForm").submit((e) => {
        e.preventDefault();

        var formData = $("#resetPasswordForm").serialize();
        $.ajax({
          type: "POST",
          url: "http://localhost/278/project/api/gateway.php/reset-password",
          data: formData,
          success: (res) => {
            $("#resetPasswordStatus").html(res);
          }
        })
      });

      $("#deactivateAccountBtn").click((e) => {
        if (confirm("Are you sure you want to deactivate your account?")) {
          $url = `../api/gateway.php/deactivate-account?u=<?= $_SESSION["username"] ?>`;

          $.ajax({
            url: $url,
            type: 'POST',
            success: (res) => {
              alert(res);
              window.location = "login.php";
            }
          });
        }
      });


      $("#deleteAccountBtn").click((e) => {
        if (confirm("Are you sure you want to delete your account?")) {
          $url = `../api/gateway.php/delete-account?u=<?= $_SESSION["username"] ?>`;

          $.ajax({
            url: $url,
            type: 'POST',
            success: (res) => {
              alert(res);
              window.location = "login.php";
            }
          });
        }
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
            $.get("../api/user-data.php/profile-picture?u=<?= $user ?>", (res) => {
              $("#pageProfilePicture").attr("src", res)
            });
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
        $rows = $db->query("SELECT Friend FROM FRIENDS WHERE User = '$user';")->fetchAll();
        if ($rows == false || count($rows) == 0) {
          print("<li class='list-group-item'>$user has no friends.</li>");
        } else {
          foreach ($rows as $row) {
            ?>
            <a href="profile.php?p=<?= $row["Friend"] ?>" class="list-group-item clearfix list-group-item-action">
              <span><?= $row["Friend"] ?></span>

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
          $rows = $db->query("SELECT From_User FROM FRIEND_REQUESTS WHERE To_User = '$user';")->fetchAll();
          if ($rows == false || count($rows) == 0) {
            print("<li class='list-group-item'>You have no incoming friend requests</li>");
          } else {
            foreach ($rows as $row) {
              ?>
              <a href="profile.php?p=<?= $row["From_User"] ?>" class="list-group-item clearfix list-group-item-action">
                <span><?= $row["From_User"] ?></span>
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


//GET CLANS
else if ($path == "/clans" && $method == "GET") {
  ?>
  <div class="container">

  <?php if ($_SESSION["username"] == $user) : ?>
  <!-- CREATE CLAN -->
    <h3>Create Clan:</h3>
    <form id="createClanForm">
      <h4>Clan Name</h4>
      <input type="text" name="clanName" id="clanNameCreateForm" class="form-control" placeholder="Enter clan name">
      <br>
      <h4>Clan Description</h4>
      <textarea name="clanDescription" id="clanDescriptionCreateForm" cols="123" rows="5" class="form-control" placeholder="Enter clan description"></textarea>
      <br>
      <button type="submit" class="btn btn-primary" id="createClanBtn">Create</button>
      <div id="createClanStatus" style="color: red;"></div>
    </form>
    <br>
    <script>
      $("#createClanBtn").click((e) => {
        e.preventDefault();
        var formData = $("#createClanForm").serialize();
        $.ajax({
          url: `../api/clan.php/create-clan`,
          type: 'POST',
          dataType: 'json',
          data: formData,
          success: (res) => {
            console.log(formData.clanName);
            if(res == "Clan Created") {
              window.location.href = `clan.php?n=${formData.clanName}`;
            } else {
              $("#createClanStatus").html(res);
            }
          }
        });
      });
    </script>
    <hr>
  <!-- FIND CLAN -->
    <h3>Find Clan:</h3>
    <form id="formFindClan">

      <div class="form-group">
        <input list="clanNameList" class="form-control" name="clan" id="clanNameText" placeholder="Enter clan name">
        <datalist id="clanNameList">
          <option value="Hello"></option>
        </datalist>
      </div>
      
      <button type="submit" class="btn btn-primary" id="findClanSubmitBtn">Send</button>
      <div id="findClanStatus" style="color: red;"></div>
    </form>
    <br>


    <script>
      $("#clanNameText").keypress((e) => {
        $.ajax({
          url: `../api/clan.php/find-clan?n=${$("#clanNameText").val()}`,
          type: 'GET',
          success: (res) => {
            var json = JSON.parse(res);
            console.log(json);
            var clanList = $("#clanNameList");
            clanList.html("");

            json.forEach(clan => {
              clanList.append(`<option value='${clan.ClanName}'>`);
            });
          }
        });
      });



      $("#findClanSubmitBtn").click((e) => {
        e.preventDefault();
        window.location.href = `clan.php?n=${$("#clanNameText").val()}`;
      });
    </script>
  <?php endif; ?>



  <!-- Clan List -->
    <hr>
    <h3>Clans: </h3>
    <ul class="list-group">

      <?php
      $rows = $db->query("SELECT ClanName FROM CLANS c JOIN USER_CLAN uc ON c.ClanName = uc.Clan WHERE uc.User = '$user';")->fetchAll();
      if ($rows == false || count($rows) == 0) {
        print("<li class='list-group-item'>$user is not in any clan.</li>");
      } else {
        foreach ($rows as $row) {
          ?>

          <a href="clan.php?n=<?= $row["ClanName"] ?>" class="list-group-item clearfix list-group-item-action">
            <span><?= $row["ClanName"] ?></span>
          </a>

        <?php
      }
    }
    ?>
    </ul>
  </div>





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
  <?php
endif;
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
