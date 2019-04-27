<?php
include("../util/db.php");

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
    $result = $db->query($statement); 
    if($result->rowCount() == 0) {
      $valid = false;
    } else if ($result->fetchAll()[0]["Password"] != $Password) {
      $valid = false;
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
      $statement = "INSERT INTO USERS  (Username, FirstName, LastName,  Email, Password) VALUES ('$Username', '$FirstName', '$LastName',  '$Email', '$Password');";
      $db->exec($statement); 
      print("");
    } else {
      print($error);
    }
  } else {
    print("Missing fields");
  }
} 




// NO ROUTE MATCHED
else {
  
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
