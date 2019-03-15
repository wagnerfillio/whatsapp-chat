<?php 
	include('../functions.php');

	if (!isAdmin()) {
		$_SESSION['msg'] = "You must log in first";
		header('location: ../login.php');
	}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<title>Whatsapp</title>
	<link rel="stylesheet" href="../assets/framework/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" href="../assets/framework/fontawesome/v5.0.10/css/all.css">	
	<link rel="stylesheet" href="../assets/css/chat.css">
	<link rel="stylesheet" href="../assets/css/chat-adapter-bootstrap-4.css">
	
	<link rel="icon" type="image/ico" href="../assets/images/favicon-64x64.ico" />
</head>

<body class="chat-body">
	
	<div class="container-fluid" id="main-container">
		<div class="chat-row h-100">
		
			<div class="col-xs-12 col-sm-5 col-md-4 d-flex flex-column" id="chat-list-area" style="position:relative;">
				<!-- Navbar Left-->
				<div class="chat-row d-flex flex-row align-items-center p-2" id="navbar">
					<img alt="Profile Photo" class="img-fluid rounded-circle mx-2 mr-2" style="height:50px; cursor:pointer;" onclick="showProfileSettings()" id="display-pic">
					<div class="text-black font-weight-bold" id="username" style="display:none"></div>					
					<div class="d-flex flex-row align-items-center ml-auto">
						<span href="#"><i class="fas fa-power-off mx-3 text-muted d-none d-md-block"></i></span>
						<span href="#"><i class="fas fa-comment mx-3 text-muted d-none d-md-block"></i></span>
						<div class="nav-item dropdown ml-auto">
						    <span class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
						        <i class="fas fa-ellipsis-v text-muted"></i>
						    </span>
						    <ul class="dropdown-menu dropdown-menu-right">
							    <li>
								    <a class="dropdown-item" href="#" onclick="checkNewMessage()" id="check-message" style="color:white; background-color:#337ab7">Check new message</a>
							        <a class="dropdown-item" href="#">New Group</a>
							        <a class="dropdown-item" href="#">Archived</a>
							        <a class="dropdown-item" href="#">Starred</a>
							        <a class="dropdown-item" href="#">Settings</a>
								<a class="dropdown-item" href="home.php">Back Home</a>
							        <a class="dropdown-item" href="chat.php?logout='1'" style="color:red;">Log Out</a>
								</li>
						    </ul>
					    </div>
					</div>
				</div>
				<div id="chat-search" class="chat-row p-2" style="border-bottom: 1px solid #dadbdb;">
					<div class="form-search form-inline" style="width:100%">
					    <input type="text" class="search-query border-0" placeholder="Search or start new chat" style="width:100%; height: 32px; font-size: 14px;  border-radius: 20px;"/>
					</div>
				</div>
				<!-- Chat List -->
				<div class="chat-row" id="chat-list" style="overflow:auto;"></div>
				
				<!-- Profile Settings -->
				<div class="d-flex flex-column w-100 h-100" id="profile-settings" style="z-index:2">
					<div class="chat-row d-flex flex-row align-items-center p-2 m-0" style="background:#009688; min-height:65px;">
						<i class="fas fa-arrow-left p-2 mx-3 my-1 text-white" style="font-size: 24px; cursor: pointer;" onclick="hideProfileSettings()"></i>
						<div class="text-white font-weight-bold">Profile</div>
					</div>
					<div class="d-flex flex-column" style="overflow:auto;">
						<img alt="Profile Photo" class="img-fluid rounded-circle my-5 justify-self-center mx-auto" id="profile-pic">
						<input type="file" id="profile-pic-input" class="d-none">
						<div class="bg-white px-3 py-2">
							<div class="text-muted mb-2"><label for="input-name">Your Name</label></div>
							<input type="text" name="name" id="input-name" class="w-100 border-0 py-2 profile-input">
						</div>
						<div class="text-muted p-3 small">
							This is not your username or pin. This name will be visible to your WhatsApp contacts.
						</div>
						<div class="bg-white px-3 py-2">
							<div class="text-muted mb-2"><label for="input-about">About</label></div>
							<input type="text" name="name" id="input-about" value="" class="w-100 border-0 py-2 profile-input">
						</div>
					</div>
				</div>				
			</div>
			
			<!-- Message Area -->
			<div class="d-none d-sm-flex flex-column col-xs-12 col-sm-7 col-md-8 p-0 h-100" id="message-area">
				<div class="w-100 h-100 overlay"></div>
				<!-- Navbar Right-->
				<div class="chat-row d-flex flex-row align-items-center p-2 m-0 w-100" id="navbar" style="border-bottom: 1px solid #d7d0ca;">
					<div class="d-block d-sm-none">
						<i class="fas fa-arrow-left p-2 mr-2 text-white" style="font-size: 24px; cursor: pointer;" onclick="showChatList()"></i>
					</div>
					<a href="#"><img src="" alt="Profile Photo" class="img-fluid rounded-circle mx-2 mr-2" style="height:50px;" id="pic"></a>
					<div class="d-flex flex-column">
						<div class="text-black font-weight-bold-apagar" id="name"></div>
						<div class="text-black small" id="details" style="color: rgba(0, 0, 0, 0.6);"></div>
					</div>
					<div class="d-flex flex-row align-items-center ml-auto">
						<a href="#"><i class="fas fa-search mx-3 text-muted d-none d-md-block"></i></a>
						<a href="#"><i class="fas fa-paperclip mx-3 text-muted d-none d-md-block"></i></a>
						<a href="#"><i class="fas fa-ellipsis-v mr-2 mx-sm-3 text-muted"></i></a>
					</div>
				</div>
				<!-- Messages -->
				<div class="d-flex flex-column messages-bg" id="messages"></div>

				<!-- Input -->
				<div class="d-none justify-self-end align-items-center flex-row" id="input-area">
					<i class="far fa-smile text-muted px-4" style="font-size:24px; cursor:pointer;"></i>
					<input type="text" name="message" id="input" placeholder="Type a message" class="flex-grow-1 border-0 px-3 py-2 my-3 rounded-20 -shadow-sm; word-wrap: break-word;">
					<i class="fas fa-paper-plane text-muted px-4" style="cursor:pointer;" onclick="sendMessage()"></i>
				</div>
			</div>
			
		</div>
	</div>

	<script src="../assets/framework/jquery/jquery.min.js?ver=2.2.4"></script>
	<!--<script src="assets/js/jquery-3.3.1.slim.min.js"></script>
	<script src="../assets/js/popper.min.js"></script>-->
	<script src="../assets/framework/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script src="../assets/js/datastore.js"></script>
	<script src="../assets/js/date-utils.js"></script>
	<script src="../assets/js/script.js"></script>
</body>

</html>
