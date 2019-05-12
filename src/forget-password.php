<?php
include("../util/db.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <?php include("../util/bootJQ.php") ?>

  <title>Forgot Password</title>
</head>
<body>
  <?php 
    include("../components/navbar.php");

    if(!isset($_REQUEST["token"])) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
        die();
    }

    if(!isset($_REQUEST["UserName"])) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
        die();
    }

    $token = $_REQUEST["token"];
    $result = $db->query("SELECT * FROM users WHERE Reset_Token='$token'")->fetchAll();
    if (count($result) == 0){
        print("no results <br>");
        print $token;
        die();
    }
    $user = $_REQUEST["UserName"];

    if(count($result) > 0 && $result[0]["UserName"] == $user) { ?>
        <div class="container formClass">
        <form id="resetPassForm">
        <div class="form-group">
            <label for="newPass">New Password</label>
            <input type="password" class="form-control" id="newPass" placeholder="enter password" name="newPassword">
        </div>
        <div class="form-group">
            <label for="repNewPass">Repeat Password</label>
            <input type="password" class="form-control" id="repNewPass" placeholder="renter password" name="repNewPassword">
        </div>

        <button type="submit" class="btn btn-primary">save</button>

        <div>
            <div class="loginError" class="center" style="color:red;"></div>
        </div>
        </form>
        </div>

        <script>
        $("#resetPassForm").submit((e) => {
            e.preventDefault();
            
            if ($("#newPass").val() === ""){
                alert("Enter password before clicking me.");
            } else {
                var formData = $("#resetPassForm").serialize();
                $.ajax({
                type: "POST",
                url: "http://localhost/278/project/api/gateway.php/forget-password?u=<?=$user?>&token=<?= $token?>",
                data: formData,
                success: (res) => {
                    if(res == "OK") {
                        window.location = "index.php";
                    } else {
                        $(".loginError").html(res);
                    }
                }
                })
            }
        });



        </script> 
    <?php
    } else if(count($result) > 0 && $result[0]["UserName"] != $user) {
        $user = $result[0]["UserName"];
        $db->exec("UPDATE users SET Reset_Token=null where UserName = '$user'");
        echo "
            <div class=\"jumbotron\">
                <h1 class=\"display-4\">Token dissmatch, can't access this page</h1>
            </div>
             ";
    } else {
        echo "
            <div class=\"jumbotron\">
                <h1 class=\"display-4\">Wrong Token, can't access this page</h1>
            </div>
             ";
    }
  ?>


  <?php include("../components/footer.php"); ?>
</body>
</html>


<style>
.formClass{
    margin-top: 50px;
}
</style>