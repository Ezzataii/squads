<?php
function sendAuthEmail($Email, $Username, $Auth_Token) {
  $Subject  = 'Squads Account Authentication';
  $Headers  = 'From: Subpar.Squads.Auth@gmail.com@gmail.com' . "\r\n" .
              'MIME-Version: 1.0' . "\r\n" .
              'Content-type: text/html; charset=utf-8';

  $Message  = "Hello $Username<br>
              Welcome to Squads!<br><br>
              Please press the following link to authenticate your account!<br>            
              <a href=\"http://localhost/278/project/api/gateway.php/authenticate?auth=$Auth_Token\">Authenticate!</a>";          

  if(mail($Email, $Subject, $Message, $Headers))
      echo "Account Created! Please Authenticate.";
  else
      echo "Try Again";
  }


  function sendActivationEmail($Email, $Username, $Activation_Token) {
    $Subject  = 'Squads Account Reactivation';
    $Headers  = 'From: Subpar.Squads.Auth@gmail.com@gmail.com' . "\r\n" .
                'MIME-Version: 1.0' . "\r\n" .
                'Content-type: text/html; charset=utf-8';
  
    $Message  = "Hello $Username<br>
                Welcome back to Squads, we have really missed you!<br><br>
                Please press the following link to reactivate your account and get back all of your information!<br>
                Ignore if you don't want to reactivate your account, although we would be sad :( <br>            
                <a href=\"http://localhost/278/project/api/gateway.php/activate?token=$Activation_Token\">Activate!</a>";          
  
    if(mail($Email, $Subject, $Message, $Headers))
        echo "<br>An email has been sent to you, activate to continue :)";
    else
        echo "Try Again";
    }
    

    function sendForgotPassEmail($Email, $Username, $Reset_Token) {
        $Subject  = 'Squads Account Reactivation';
        $Headers  = 'From: Subpar.Squads.Auth@gmail.com@gmail.com' . "\r\n" .
                    'MIME-Version: 1.0' . "\r\n" .
                    'Content-type: text/html; charset=utf-8';
      
        $Message  = "Hello $Username<br>
                    Welcome to Squads!<br><br>
                    Please press the following link to reset your account password!<br>            
                    <a href=\"http://localhost/278/project/src/forget-password.php?UserName=$Username&&token=$Reset_Token\">Reset Password</a>";          
      
        if(mail($Email, $Subject, $Message, $Headers))
            echo "<br>An email has been sent to reset your password, check it to continue :)";
        else
            echo "Try Again";
        }
?>