<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="../src/login.php">
    <img src="../assets/Logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
    Squads
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">

      <?php if (!isset($_SESSION["username"]) || !isset($_SESSION["token"])) : ?>
      <li class="nav-item">
        <a class="nav-link" href="about.php">About</a>
      </li>
      <?php endif; ?>

      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>

      <?php if (isset($_SESSION["username"]) && isset($_SESSION["token"])) : ?>
      <li class="nav-item">
        <a class="nav-link" href="feed.php">Feed</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="chat-room.php">Chat Rooms</a>
      </li>
      <?php endif; ?>

    </ul>
  

    <?php if (!isset($_SESSION["username"]) && !isset($_SESSION["token"])) : ?>
      <div class="form-inline">
        <button class="btn btn-outline-primary my-2 my-sm-0" type="button" data-toggle="modal" data-target="#modalLogin">Login</button> &nbsp; &nbsp;
        <button class="btn btn-outline-primary my-2 my-sm-0" type="button" data-toggle="modal" data-target="#modalSignup">Sign Up</button>
      </div>
    <?php else: ?>
      <div class="form-inline">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="profile.php?p=<?= $_SESSION["username"] ?>"><?= $_SESSION["username"] ?></a>
          </li>
        </ul>

        &nbsp;
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="inbox.php"><i class="fa fa-inbox" style="color: #909DA0;"></i></a>
          </li>
        </ul>
        

        &nbsp;&nbsp;&nbsp;
        <button class="btn btn-outline-primary my-2 my-sm-0" type="button" id="logoutBtn">Log Out</button>
      </div>
    <?php endif; ?>

  </div>
</nav>





<?php if (!isset($_SESSION["username"]) && !isset($_SESSION["token"])) : ?>
  <div id="modalLogin" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Login</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <?php include("../components/loginForm.php") ?>
        </div>
      </div>
    </div>
  </div>

  <div id="modalSignup" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Sign Up</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <?php include("../components/registerForm.php") ?>
        </div>
      </div>
    </div>
</div>
<?php endif; ?>


<!-- SCRIPT -->
<script>
  <?php if (isset($_SESSION["username"]) && isset($_SESSION["token"])) : ?>
    $("#logoutBtn").click((e) => {
      $.get("http://localhost/278/project/api/gateway.php/logout", () => {
        window.location = "login.php";
      });

    });
  <?php endif;?>

  $(document).ready(function() {
    var href = window.location.pathname.split("/").pop();
    console.log(href);
    $('.navbar-nav > li > a[href="'+href+'"]').parent().addClass('active');
  }) 

</script>




