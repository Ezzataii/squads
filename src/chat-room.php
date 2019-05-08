<?php 
include("../util/authHeader.php");
$colors = array('#007AFF','#FF7000','#FF7000','#15E25F','#CFC700','#CFC700','#CF1100','#CF00BE','#F00');
$color_pick = array_rand($colors);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <?php include("../util/bootJQ.php") ?>

  <title>Chat Room</title>
</head>

<body>
  <?php include("../components/navbar.php"); ?>

  <div class="container">
    <br>
    <h3>Chat Room</h3>
  </div>

  <div class="container chat-wrapper">

    <div class="msg-box" id="msg-box"></div>

    <div class="panel">

      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text" id="Message-Username"><?= $_SESSION["username"] ?>:</span>
        </div>
        <input type="text" class="form-control" placeholder="Enter your message here" aria-label="Message" aria-describedby="Message-Username" id="message-input">
        <div class="input-group-append">
          <button class="btn btn-outline-primary" type="button" id="send-msg-btn">Send</button>
        </div>
      </div>

    </div>
  </div>


  <?php include("../components/footer.php"); ?>
</body>

</html>

<script>

    //Open a WebSocket connection.
    var wsUri = "ws://localhost:9000/278/project/util/socket.php";
    websocket = new WebSocket(wsUri);

    var msgBox = $("#msg-box");

    function showMessage(messageHTML) {
      msgBox.append(messageHTML);
	  }

    websocket.onopen = (event) => {
      showMessage("<div class='system_msg' style='color:#bbbbbb'>Connected to ChatRoom!</div>");	
    }

    websocket.onmessage = (event) => {
      var response 		= JSON.parse(event.data); //PHP sends Json data
		
      var res_type 		= response.type; //message type
      var user_message 	= response.message; //message text
      var user_name 		= response.name; //user name
      var user_color 		= response.color; //color
      switch(res_type){
        case 'usermsg':
          msgBox.append('<div><span class="user_name" style="color:' + user_color + '">' + user_name + '</span> : <span class="user_message">' + user_message + '</span></div>');
          break;
        case 'system':
          msgBox.append('<div style="color:#bbbbbb">' + user_message + '</div>');
          break;
      }
    };

    websocket.onerror	= (event) => { msgBox.append('<div class="system_error">Error Occurred - ' + event.data + '</div>'); }; 
	  websocket.onclose = (event) => { msgBox.append('<div class="system_msg">Connection Closed</div>'); }; 



    //Send a Message
    $('#send-msg-btn').click((event) => { 
      event.preventDefault();	
      
      if($('#message-input').val() == "") {
        alert("Please insert a message!");
      } else {
        var msg = {
				name: '<?= $_SESSION["username"] ?>',
        message: $('#message-input').val(),
        color : '<?php echo $colors[$color_pick]; ?>'
      };
			websocket.send(JSON.stringify(msg));	
		  $('#message-input').val(''); 
      }	
    });
    
    $('#message-input').keypress((event) => {
      var key = event.which;
      if(key == 13)  {
        $('#send-msg-btn').click();
      }
    })

</script>


<style>
.chat-wrapper {
  margin-bottom: 50px;
}

.msg-box {
  margin: 30px 0px;
  padding: 10px;
  height: 700px;
  overflow-y: scroll;

  border: solid #343A40 2px;
  background-color: #E9ECEF;
}

.connection {
  color: green;
}
.disconnection {
  color: red;
}
</style>