<div>
<form id="loginForm">
  <div class="form-group">
    <label for="loginUsername">Username</label>
    <input type="text" class="form-control" id="loginUsername" placeholder="enter username" name="Username">
  </div>
  <div class="form-group">
    <label for="loginPass">Password</label>
    <input type="password" class="form-control" id="loginPass" placeholder="enter password" name="Password">
  </div>

  <button type="submit" class="btn btn-primary">Login</button>

  <div>
    <div class="loginError" class="center" style="color:red;"></div>
  </div>
</form>
<br>
<div>
    <button class="btn btn-danger" id="ForgotPass">Fogot Password ?</button>
    <div>
    <div class="frogetPassErr" class="center" style="color:red;"></div>
    </div>
</div>
</div>

<script>
  $("#loginForm").submit((e) => {
    e.preventDefault();
    
    var formData = $("#loginForm").serialize();
    $.ajax({
      type: "POST",
      url: "http://localhost/278/project/api/gateway.php/login",
      data: formData,
      success: (res) => {
        if(res === "") {
          window.location = "index.php";
        } else {
          $(".loginError").html(res);
        }
      }
    })
  });

  $("#ForgotPass").click((e) => {
    if ($("#loginUsername").val() === ""){
      alert("Enter UserName before clicking me.");
    }
    else {
      var formData = $("#loginForm").serialize();
      $.ajax({
        type: "POST",
        url: "http://localhost/278/project/api/gateway.php/get-forget-passForm",
        data: formData,
        success: (res) => {
          $(".frogetPassErr").html(res);
        }
      })
    }
  })
</script>
