<?php
include("../util/db.php");
include("send-mail.php");

$path = $_SERVER["PATH_INFO"];
$method = $_SERVER['REQUEST_METHOD'];

//LOGIN POST
if ($path == "/login" && $method == "POST") {
  if (isset($_POST["Username"]) &&
      isset($_POST["Password"])) { 
 
    $Username = $_POST["Username"];
    $Password = $_POST["Password"];
    
    $valid = true;

    //UserName Correct
    $statement = "SELECT * FROM USERS WHERE UserName = '$Username';";
    $result = $db->query($statement)->fetchAll(); 
    if(count($result) == 0) {
      $valid = false;
    } else if ($result[0]["Password"] != $Password) {
      $valid = false;
    } else if($result[0]["UserName"] != $Username) {
      $valid = false;
    } else if (!$result[0]["Activated"]) {
      $Username = $result[0]["UserName"];
      $Email = $result[0]["Email"];
      $Activation_Token = $result[0]["Auth_Token"];
      print("Account is not Active.");
      sendActivationEmail($Email, $Username, $Activation_Token);
    } else if(!$result[0]["Authenticated"]) {
      print("Account is not Authenticated.<br>Check your email upon registration");      
      die();
    }

    if($valid) {
      print("");
      //Generate new user token
      $token = random_bytes(32);
      $db->exec("UPDATE USERS SET token = '$token' WHERE UserName = '$Username';");
      
      //set session
      session_start();
      $_SESSION["token"] = $token;
      $_SESSION["username"] = $Username;
      $_SESSION["profilePicture"] = $result[0]["ProfilePicturePath"]; 
    } else {
      print("Invalid Username or Password!");
    }

  } else {
    print("Missing fields");
  }
}

//LOGOUT GET
else if ($path == "/logout" && $method == "GET") {  
  session_start();

  //set token to null
  if(isset($_SESSION["username"])) {
    $Username = $_SESSION["username"];
    $db->exec("UPDATE USERS SET token = null WHERE UserName = '$Username';");
  } 

  session_destroy();
}

//REGISTER POST
else if ($path == "/register" && $method == "POST") {
  if (isset($_POST["FirstName"]) &&
      isset($_POST["LastName"]) &&
      isset($_POST["Username"]) &&
      isset($_POST["Email"]) &&
      isset($_POST["Password"]) &&
      isset($_POST["ConfirmPassword"])) { 

    $FirstName = $_POST["FirstName"];
    $LastName = $_POST["LastName"];
    $Username = $_POST["Username"];
    $Email = $_POST["Email"];
    $Password = $_POST["Password"];
    $ConfirmPassword = $_POST["ConfirmPassword"];
    
    $valid = true;
    $error = "";

    //FirstName Correct
    if (!preg_match("/^[A-Z][A-Za-z]+$/", $FirstName)) {
      $error .= " Invalid Firstname.";
      $valid = false;
    }
    //LastName Correct
    if (!preg_match("/^[A-Z][A-Za-z]+$/", $LastName)) {
      $error .= " Invalid LastName.";
      $valid = false;
    }
    //UserName Correct
    $statement = "SELECT * FROM USERS WHERE UserName = '$Username';";
    $result = $db->query($statement); 
    if($result->rowCount() != 0) {
      $error .= " Username Taken.";
      $valid = false;
    }
    //Email Correct
    if (!preg_match("/^[^@]+@[^@]+$/", $Email)) {
      $error .= " Invalid Email.";
      $valid = false;
    }
    //Confirm Password Correct
    if ($Password != $ConfirmPassword){
      $error .= " Confirm Password Does Not Match.";
      $valid = false;
    }


    if($valid) {
      $Auth_Token = izrand(32);

      $statement = "INSERT INTO USERS  (Username, FirstName, LastName,  Email, Password, Authenticated, Activated, Auth_Token, ProfilePicturePath, LevelOfAccess) 
      VALUES ('$Username', '$FirstName', '$LastName',  '$Email', '$Password', FALSE, TRUE, '$Auth_Token', '../assets/default-profile.png', 'public');";
      $db->exec($statement); 

      sendAuthEmail($Email, $Username, $Auth_Token);

      print("");
    } else {
      print($error);
    }
  } else {
    print("Missing fields");
  }
}

else if($path == "/reset-password" && $method == "POST") {
  session_start(); 
  if (!isset($_SESSION["username"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
    die();
  }

  $user = $_SESSION["username"];

  $newPassowrd = $_POST["newPassword"];

  if($newPassowrd != $_POST["confirmNewPassword"]) {
    print("passwords do not match");
  } else {
    $db->exec("UPDATE USERS SET password = '$newPassowrd' WHERE UserName = '$user';");
    print("password changed!");
  }
}


// Deactivate account
else if ($path == "/deactivate-account" && $method == "POST") {
  
  $user = $_REQUEST["u"];  
  session_start(); 
  if (!isset($_SESSION["username"]) || $_SESSION["username"] != $user) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
    die();
  }

  $token = izrand(32);
  $result = $db->exec("UPDATE USERS SET Activated = 0, Auth_Token = '$token' WHERE UserName = '$user';");
  if ($result){
    print "Account deactivated successfully, and now has a token to help in activating it!";
    session_destroy();
  } else {
    print "Something went wrong while deactivating";
  }
} 


// Delete account
else if ($path == "/delete-account" && $method == "POST") {
  
  $user = $_REQUEST["u"];  
  session_start(); 
  if (!isset($_SESSION["username"]) || $_SESSION["username"] != $user) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
    die();
  }

  $result = $db->exec("DELETE from users WHERE UserName='$user';");
  if ($result){
    print("Account deleted successfully!");
    session_destroy();
  }else{
      echo "Something went wrong while deleting";
  }
}



//AUTHENTICATE GET
else if ($path == "/authenticate" && $method == "GET") {  
  
  if(!isset($_REQUEST["auth"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  } 
  $authToken = $_REQUEST["auth"];
  
  $result = $db->query("SELECT UserName FROM USERS WHERE auth_token = '$authToken';")->fetchAll();

  if(count($result) == 0) {
    header('Location: ../../src/alert.php?msg=NotAuthenticated');
  } else {
    $Username = $result[0]["UserName"];
    $db->exec("UPDATE USERS SET Authenticated = TRUE WHERE UserName = '$Username'");
    header('Location: ../../src/alert.php?msg=Authenticated');
  }
}

// Reactivation
else if ($path == "/activate" && $method == "GET") {  
  
  if(!isset($_REQUEST["token"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  } 
  $ActivationToken = $_REQUEST["token"];
  $result = $db->query("SELECT UserName FROM USERS WHERE auth_token = '$ActivationToken';")->fetchAll();

  if(count($result) == 0) {
    header('Location: ../../src/alert.php?msg=NotAuthenticated');
  } else {
    $Username = $result[0]["UserName"];
    $db->exec("UPDATE USERS SET Activated=1  WHERE UserName = '$Username'");
    header("Location: ../../src/alert.php?msg=activate");
  }
}

// getting forget password form
else if ($path == "/get-forget-passForm" && $method == "POST") {  
  
  if($_POST["Username"] == ""){
    print "please Enter a userName";
  } else {
    $user = $_POST["Username"];
    $result = $db->query("SELECT * FROM USERS WHERE UserName = '$user'")->fetchAll();

    if (count($result) == 0){
      print "Account doesn't exist";
      die();
    }

    $Reset_Token = izrand(32);
    $newResult = $db->exec("UPDATE USERS SET Reset_Token = '$Reset_Token' WHERE UserName = '$user';");

    if (!$newResult){
      print "Some error while setting the token";
      die();
    }
 
    $Email = $result[0]["Email"];
    sendForgotPassEmail($Email, $user, $Reset_Token);
  }
}



// Reseting password
else if ($path == "/forget-password" && $method == "POST") {  
  
  if(!isset($_REQUEST["token"]) || !isset($_REQUEST["u"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  }
  
  $user = $_REQUEST["u"];
  $ResetToken = $_REQUEST["token"];
  $result = $db->query("SELECT * FROM USERS WHERE Reset_Token = '$ResetToken';")->fetchAll();
  
  if(count($result) > 0) {
    if ($result[0]["UserName"] != $user){
      $user = $result["UserName"];
      $db->exec("UPDATE users SET Reset_Token=null where UserName = '$user'");
      print("MissMatch btn token and user");
      die();
    } else {
      // Your code goes here
      $pass = $_POST["newPassword"];
      $repPass = $_POST["repNewPassword"];
      if ($pass == $repPass){
        $db->exec("UPDATE users SET Password='$pass' where UserName='$user'");
        print "OK";
      } else {
        print "Passwords are not matching!";
      }



    }
  } else if (count($result) == 0) {
    print "Account doesn't exist";
  } else {
    print "There is something wrong";
  }
}



// NO ROUTE MATCHED
else {
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}



function izrand($length = 32) {

  $random_string="";
  while(strlen($random_string)<$length && $length > 0) {
          $randnum = mt_rand(0,61);
          $random_string .= ($randnum < 10) ?
                  chr($randnum+48) : ($randnum < 36 ? 
                          chr($randnum+55) : $randnum+61);
   }
  return $random_string;
}