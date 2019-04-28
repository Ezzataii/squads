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
    $statement = "SELECT * FROM USERS WHERE username = '$Username';";
    $result = $db->query($statement)->fetchAll(); 
    if(count($result) == 0) {
      $valid = false;
    } else if ($result[0]["Password"] != $Password) {
      $valid = false;
    } else if(!$result[0]["Authenticated"]) {
      print("Not Authenticated");
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
    $statement = "SELECT * FROM USERS WHERE username = '$Username';";
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

      $statement = "INSERT INTO USERS  (Username, FirstName, LastName,  Email, Password, Authenticated, Auth_Token, ProfilePicturePath) 
      VALUES ('$Username', '$FirstName', '$LastName',  '$Email', '$Password', FALSE, '$Auth_Token', '../assets/default-profile.png');";
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

//AUTHENTICATE GET
else if ($path == "/authenticate" && $method == "GET") {  
  
  if(!isset($_REQUEST["auth"])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
    die();
  } 
  $authToken = $_REQUEST["auth"];
  
  $result = $db->query("SELECT username FROM USERS WHERE auth_token = '$authToken';")->fetchAll();

  if(count($result) == 0) {
    header('Location: ../../src/alert.php?msg=NotAuthenticated');
  } else {
    $Username = $result[0]["username"];
    $db->exec("UPDATE USERS SET Authenticated = TRUE WHERE username = '$Username'");
    header('Location: ../../src/alert.php?msg=Authenticated');
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