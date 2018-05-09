<?php

	require_once('db.php');
	
	

	// session_start();
	// if(!isset($_SESSION['chat-log'])){
	// 	$_SESSION['chat-log'] = [];
	// }
	
	//$chatLog = $_SESSION['chat-log'] ;

	global $pass;
	$pass = "password";
	 	

	function userChat($message){
		$userChat = '<div class="chat user">
			<div class="user-photo"></div>
			<p class="chat-message" id="user">' . $message . '</p>
			</div>';
			return $userChat;
	}

	function botAnswer($message){
		$botAnswer = '<div class="chat bot">
			<div class="user-photo"></div>
			<p class="chat-message" id="user">' . $message . '</p>
			</div>';
			return $botAnswer;
	}


	function train($dbcon, $data){
		$trainCheck = $dbcon->prepare("SELECT * FROM chatbot WHERE questions LIKE :question ");
		$trainCheck->bindParam(':question', $data['question']);
		$trainCheck->execute();
		$result = $trainCheck->fetch(PDO::FETCH_ASSOC);
		$rows = $trainCheck->rowCount();
			if($rows === 0){
			$trainQuery = $dbcon->prepare("INSERT INTO chatbot (id, questions, answers) VALUES(null, :q, :a)");
			$trainQuery->bindParam(':q', $data['question']);
			$trainQuery->bindParam(':a', $data['answer']);
			$trainQuery->execute();
			$bot = "Thanks for helping me be better.";

		}elseif($rows !== 0){
			$bot = "I already know how to do that. You can ask me a new question, or teach me something else. Remember, the format is train: question # answer # password";
		}
		echo $bot;
	}
	if($_SERVER['REQUEST_METHOD'] === 'POST'){ 
		echo $_POST['question'];
	
	 	$userInput = strtolower(trim($_POST['question']));
	 	if(isset($userInput)){
	 		$user = $userInput;
	 		 //array_push($_SESSION['chat-log'] , $user);
	 	}
	 	
	 	if(strpos($userInput , 'train:') ===0){
	 		list($t, $r ) = explode(":", $userInput);
			list($trainquestion, $trainanswer, $trainpassword) = explode("#", $r);
			$data['question'] = $trainquestion;
	 		$data['answer'] = $trainanswer;
	 		if($trainpassword === $pass){
	 			$bot = train($conn, $data);
	 			//array_push($_SESSION['chat-log'] , $bot);
	 		}else{
	 			$bot = "You have entered a wrong password. Let's try that again with the right password, shall we?";
	 			//array_push($_SESSION['chat-log'] , $bot);
	 		}
	 		
	 	}elseif($userInput === 'about' || $userInput === 'aboutbot'){
	 		$bot = "Version 1.0";
     		//array_push($_SESSION['chat-log'] , $bot);
	 	}else{
			 $userInputQuery = $conn->query("SELECT * FROM chatbot WHERE questions like '".$userInput."' ");
		     $userInputs = $userInputQuery->fetchAll(PDO::FETCH_ASSOC);
		    $userInputRows = $userInputQuery->rowCount();
		     if($userInputRows == 0){
		     	$bot = "I am unable to answer your question right now. But you can train me to answer this particular question. Use the format train: question #answer #password";
		     //	array_push($_SESSION['chat-log'] , $bot);

		     }else{
		     	$bot = $userInputs[rand(0, count($userInputs)-1)]['answers'];
		     	//$bot = botAnswer($botAnswer);
		     	//array_push($_SESSION['chat-log'] , botAnswer($botAnswer));
		     }
     	}
     	echo $bot;
	}

?>

<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<title>Portfolio | Adeboga Abigail</title>
	<style type="text/css">

		.chatbox{
			width: 500px;
			min-width: 390px;
			height: 600px;
			background: #fff;
			padding: 25px;
			margin: 20px auto;
			border-radius: 10px;
			box-shadow: 3px  3px 3px 3px #ccc;
		}
		#chatlogs{
			padding: 10px;
			width:100%;
			height: 450px;
			border-radius: 10px;
			background: #e7e7e7;
			overflow-x: hidden;
			overflow-y: scroll;
		}
		#chatlogs::-webkit-scrollbar{
			width: 10px;
		}
		#chatlogs::-webkit-scrollbar-thumb{
			border-radius: 5px;
			background: rgba(0, 0, 0, 0.2); 
		}
		.chat{
			display: flex;
			flex-flow: row wrap;
			align-items: flex-start;
			margin:10px;
		}
		.chat .user-photo{
			width: 60px;
			height: 60px;
			background: #ccc;
			border-radius: 50%;
		}
		.chat .chat-message{
			width:70%;
			min-height: 25px;
			padding: 15px;
			margin: 5px 10px 0;
			background: #1ddced;
			border-radius: 25px 25px 0 25px;
			/*color :#fff;*/
			font-size: 18px;
		}
		.user .chat-message{
			background: #1adda4;
			order: -1;
		}
		.bot .chat-message{
			background: #1ddced;	
			height: auto;
			border-radius: 0px 10px 10px 10px	!important;		
		}
		.chat-form{
			margin-top:20px;
			display: flex;
			align-items:flex-start;
		}
		.chat-form textarea{
			background: #fbfbfb;
			width: 90%;
			height: 50px;
			border: 2px solid #eee;
			border-radius: 3px;
			resize: none;
			padding: 10px;
			font-size: 18px;
			/*color: #333;*/
		}
		.chat-form textarea:focus{
			background: #fff;
		}
		.chat-form::-webkit-scrollbar{
			width: 10px;
		}
		.chat-form::-webkit-scrollbar-thunb{
			border-radius: 5px;
			background: rgba(0,0,0,0.1);
		}
		.chat-form button{
			background: #1ddced;
			padding: 5px 5px;
			font-size: 30px;
			border:none;
			margin: 0 10px;
			border-radius: 3px;
			box-shadow: 0 3px 0 #0eb2c1;
			cursor: pointer;
		}
		.chat-form button:hover{
			background: #13c8cd0;
		}
	</style>

</head>
<body>
 	

<div class="chatbox">
		<div id="chatlogs">
			<div class="chat bot">
				<div class="user-photo"></div>
				<p class="chat-message"></p>
			</div>
			
			 <div id="chat-content"></div>
			
				
		</div>
		

		<form class="form-data" method="post" action="">
			<div class="chat-form">
				
				<textarea name="question" id="question"></textarea>
			<!--	<button name="bot-interface" id="bot-interface">Send</button> -->

				<input type="submit" name="bot-interface" value="SEND"/>
				
			</div>
		</form>
</div>

	<script>
		
// 	q
// question.addEventListener("event.keyCode==13", userChat);
// function TextEvent()
// {
//   var destination = document.getElementById("user");
//   var source = document.getElementById("question")
//   destination.innerHTML = source.value;
// }

// function KeyEvent(event)
// {
//   if(event.keyCode==13)//as Pirate suggests or any other char
//   {
//     TextEvent();//call the same method to move text
//   }
// }
// // });

 var btn = document.getElementsByClassName('form-data')[0];
		var question = document.getElementById("question");
		var chatLog = document.getElementById("chatlogs");
		var chatContent = document.getElementById("chat-content");

		btn.addEventListener("submit", chat);


		function chat(e){
		    // var p = document.getElementById('user');
		    // p.innerHTML = data;
		    
		    if (window.XMLHttpRequest) { // Mozilla, Safari, IE7+ ...
			     var xhttp = new XMLHttpRequest();
			} else if (window.ActiveXObject) { // IE 6 and older
			  var  xhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
		   
			xhttp.onreadystatechange = function() {
	          if(this.readyState == 4 && this.status == 200) {
	          	 userChat(question.value);
	          	 //  var response = JSON.parse(this.responseText);
	          	 //chatContent.innerHTML = this.responseText;
	          	 e.preventDefault();
     			console.log(this.response);
     			e.preventDefault();
	          	 // console.log(response);
	          	//var response = xhttp.responseText;
	            // userChat(response);
	            // question.value = xhttp.responseText;
	            question.value = '';
	          }
      	    }
        xhttp.open('POST', 'index.php', true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send('question='+ question.value);
        e.preventDefault();
		}

		function userChat(chats){
			// if(chats === ''){
			// 	var chat = '' ;
			if(question.value !== ''){
				var chat = `<div class="chat user">
				<div class="user-photo"></div>
				<p class="chat-message" id="user">` + chats + `</p>
				</div>`;
			
		    chatContent.innerHTML += chat;	
				
			}

		}
	</script>
	</script>
</body>
</html>