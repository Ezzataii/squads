<form id="registerForm">
  <div class="form-group">
    <label for="registerFName">First Name</label>
    <input type="text" class="form-control" id="registerFName" placeholder="enter first name" name="FirstName">
  </div>
  <div class="form-group">
    <label for="registerLName">Last Name</label>
    <input type="text" class="form-control" id="registerLName" placeholder="enter last name" name="LastName">
  </div>
  <div class="form-group">
    <label for="registerUsername">Username</label>
    <input type="text" class="form-control" id="registerUsername" placeholder="enter username" name="Username">
  </div>
  <div class="form-group">
    <label for="registerEmail">Email</label>
    <input type="email" class="form-control" id="registerEmail" placeholder="enter firstname" name="Email">
  </div>
  <div class="form-group">
    <label for="registerPass">Password</label>
    <input type="password" class="form-control" id="registerPass" placeholder="enter password" name="Password">
  </div>
  <div class="form-group">
    <label for="registerPassC">Confirm Password</label>
    <input type="password" class="form-control" id="registerPassC" placeholder="confirm password" name="ConfirmPassword">
  </div>


  <button type="submit" class="btn btn-primary">Register</button>

  <div>
    <div class="registerError" class="center" style="color:red;"></div>
  </div>

</form>


<script>
  $("#registerForm").submit((e) => {
  e.preventDefault();
  
  var formData = $("#registerForm").serialize();
  $.ajax({
    type: "POST",
    url: "http://localhost/278/project/api/gateway.php/register",
    data: formData,
    success: (res) => {
      if(res === "") {
        $(".error").html("");
        alert("Registered!");
      } else {
        $(".registerError").html(res);
      }
    }
  })
});
</script>