<?php
// include("../util/db.php");

// $path = $_SERVER["PATH_INFO"];
// $method = $_SERVER['REQUEST_METHOD'];

// if (!isset($_REQUEST["u"])) {
//     header($_SERVER["SERVER_PROTOCOL"] . ' 422 (Unprocessable Entity)');
//     die();
// }

// $user = $_REQUEST["u"];


// session_start(); 
// if (!isset($_SESSION["username"]) || $_SESSION["username"] != $user) {
//   header($_SERVER["SERVER_PROTOCOL"] . ' 403 (Access Denied)');
//   die();
// }



// if ($path == "/deactivate-account" && $method == "POST") {
//   $result = $db->exec("UPDATE USERS SET Authenticated = 0 WHERE UserName = '$user';");
//   if ($result){
//     print "Account deactivated successfully!";
//     session_destroy();
//   } else {
//     print "Something went wrong while deactivating";
//   }
// } 


// else if ($path == "/delete-account" && $method == "POST") {
//   $result = $db->exec("DELETE from users WHERE UserName='$user';");
//   if ($result){
//     print("Account deleted successfully!");
//     session_destroy();
//   }else{
//       echo "Something went wrong while deleting";
//   }
// }


// else { 
//   header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
// }
?>


