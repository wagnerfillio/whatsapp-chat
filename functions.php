<?php 
	session_start();
	
	// connect to database
	$db = mysqli_connect('localhost', 'root', 'vertrigo', 'whats_clone');

	// variable declaration
	$username = "";
	$email    = "";
	$errors   = array(); 

	// call the register() function if register_btn is clicked
	if (isset($_POST['register_btn']))
	{
		register();
	}

	// call the login() function if register_btn is clicked
	if (isset($_POST['login_btn']))
	{
		login();
	}

	if (isset($_GET['logout']))
	{
		session_destroy();
		unset($_SESSION['user']);
		header("location: ../login.php");
	}

	// REGISTER USER
	function register()
	{
		global $db, $errors;

		// receive all input values from the form
		$username    =  e($_POST['username']);
		$email       =  e($_POST['email']);
		$password_1  =  e($_POST['password_1']);
		$password_2  =  e($_POST['password_2']);

		// form validation: ensure that the form is correctly filled
		if (empty($username))
		{ 
			array_push($errors, "Username is required"); 
		}
		if (empty($email))
		{ 
			array_push($errors, "Email is required"); 
		}
		if (empty($password_1))
		{ 
			array_push($errors, "Password is required"); 
		}
		if ($password_1 != $password_2)
		{
			array_push($errors, "The two passwords do not match");
		}

		// register user if there are no errors in the form
		if (count($errors) == 0)
		{
			$password = md5($password_1);//encrypt the password before saving in the database

			if (isset($_POST['user_type']))
			{
				$user_type = e($_POST['user_type']);
				$query = "INSERT INTO users (username, email, user_type, password) 
						  VALUES('$username', '$email', '$user_type', '$password')";
				mysqli_query($db, $query);
				$_SESSION['success']  = "New user successfully created!!";
				header('location: home.php');
			}
			else
			{
				$query = "INSERT INTO users (username, email, user_type, password) 
						  VALUES('$username', '$email', 'user', '$password')";
				mysqli_query($db, $query);

				// get id of the created user
				$logged_in_user_id = mysqli_insert_id($db);

				$_SESSION['user'] = getUserById($logged_in_user_id); // put logged in user in session
				$_SESSION['success']  = "You are now logged in";
				header('location: index.php');				
			}
		}
	}

	// return user array from their id
	function getUserById($id)
	{
		global $db;
		
		$query = "SELECT * FROM users WHERE id=" . $id;
		
		$result = mysqli_query($db, $query);

		$user = mysqli_fetch_assoc($result);
		return $user;
	}	

	// LOGIN USER
	function login()
	{
		global $db, $username, $errors;

		// grap form values
		$username = e($_POST['username']);
		$password = e($_POST['password']);
		
		// make sure form is filled properly
		if (empty($username))
		{
			array_push($errors, "Username is required");
		}
		if (empty($password))
		{
			array_push($errors, "Password is required");
		}

		// attempt login if no errors on form
		if (count($errors) == 0)
		{
			$password = md5($password);
			
			$query = "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1";
			
			$results = mysqli_query($db, $query);

			if (mysqli_num_rows($results) == 1)
			{ // user found
				// check if user is admin or user
				$logged_in_user = mysqli_fetch_assoc($results);
				if ($logged_in_user['user_type'] == 'admin') {

					$_SESSION['user'] = $logged_in_user;
					$_SESSION['success']  = "You are now logged in";
					header('location: admin/home.php');		  
				}else{
					$_SESSION['user'] = $logged_in_user;
					$_SESSION['success']  = "You are now logged in";

					header('location: index.php');
				}
			}
			else
			{
				array_push($errors, "Wrong username/password combination");
			}
		}
	}

	function isLoggedIn()
	{
		if (isset($_SESSION['user']))
		{
			return true;
		}else
		{
			return false;
		}
	}

	function isAdmin()
	{
		if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] == 'admin' )
		{
			return true;
		}else
		{
			return false;
		}
	}

	// escape string
	function e($val)
	{
		global $db;
		return mysqli_real_escape_string($db, trim($val));
	}

	function display_error()
	{
		global $errors;

		if (count($errors) > 0)
		{
			echo '<div class="error">';
				foreach ($errors as $error) {
					echo $error .'<br>';
				}
			echo '</div>';
		}
	}
	
	
	// CHAT FUNCTIONS	
	if(isset($_GET["action"]))
	{
       if($_GET["action"] == "getUser")
	   {
          getUser();
       }
	   else if($_GET["action"] == "getAllContacts")
	   {
          getAllContacts();
       }
	   else if($_GET["action"] == "getAllGroups")
	   {
          getAllGroups();
       }
	   else if($_GET["action"] == "getAllMessages")
	   {
          getAllMessages();
       }
	   else if($_GET["action"] == "sendMessage")
	   {
          sendMessage();
       }
	   else if($_GET["action"] == "checkNewMessage")
	   {
          checkNewMessage();
       }
	   else if($_GET["action"] == "markRead")
	   {
          markRead();
       }
    }
	
	function getUser()
	{
		$myId	= $_SESSION['user']['id'];
		
		$return = getUserByIdRow($myId);
		
		$user = [
	       'id'     => (int) $return->id,
		   'name'   => $return->username,
		   'number' => $return->number,
		   'pic'    => '../assets/images/'.$return->pic
	    ];
	    header('Content-Type: application/json');
	    echo json_encode($user);
	}
	
	function getAllContacts()
	{
		global $db;		
		$query = "SELECT * FROM users";
		$result = mysqli_query($db, $query);
		
		$list = [];
		foreach($result as $contact)
		{			
			$user = [
				'id'       => (int) $contact['id'],
				'name'     => $contact['username'],
				'number'   => $contact['number'],
				'pic'      => '../assets/images/'.$contact['pic'],
				'lastSeen' => date('Y-m-d h:i:s a', time())
			];
			array_push($list, $user);
		}
		header('Content-Type: application/json');
	    echo json_encode($list);
	}
	
	function getAllGroups()
	{
		$groups = [
	        [
	            'id'       => 1,
		        'name'     => 'Programers',
		        'number'   => '+5531975999387',
			    'members'  => [0, 1, 3, 5],
		        'pic'      => 'assets/images/0923102932_aPRkoW.jpg',
		    ],
		];
		header('Content-Type: application/json');
		echo json_encode($groups);
	}
	
	function getAllMessages()
	{
		global $db;		
		$query = "SELECT * FROM message";
		$result = mysqli_query($db, $query);
		
		$thread = [];
		foreach($result as $message)
		{			
			$chat = [
				'id' 		  => (int) $message['id'],
				'sender' 	  => (int) $message['sender'],
				'recvId'      => (int) $message['recvId'],
				'body' 		  => $message['body'],
				'status' 	  => (int) $message['status'],
				'recvIsGroup' => false,
				'time' 		  => $message['time'],
			];
			array_push($thread, $chat);
		}
		header('Content-Type: application/json');
	    echo json_encode($thread);
	}
	
	function sendMessage()
	{
		$msg = $_POST['msg'];		
			
		$id = 0;
		$sender      = $msg['sender'];
		$recvId      = $msg['recvId'];
		$body        = $msg['body'];
		$status      = $msg['status'];
		$recvIsGroup = 0;
								
		if ($id == 0)
		{			
			global $db;
			$query = "INSERT INTO message (sender, recvId, body, status, recvIsGroup)
						  VALUES('$sender', '$recvId', '$body', '$status' , '$recvIsGroup')";
			mysqli_query($db, $query);

			// get last id of the created message
			$messageLastId = mysqli_insert_id($db);		
			
			$message = returnLastMessage($messageLastId);
			$arr['id']          = (int) $message['id'];
			$arr['sender']      = (int) $message['sender'];
			$arr['recvId']      = (int) $message['recvId'];		
			$arr['body']        = $message['body'];
			$arr['status']      = (int) $message['status'];
			$arr['recvIsGroup'] = false;
			$arr['time']        = $message['time'];
		}
		header('Content-Type: application/json');
		echo json_encode($arr);
	}
	
	function checkNewMessage()
	{
		global $db;
		$myId = $_SESSION['user']['id'];
		
		$new_exists = false;		
		$query = "SELECT * FROM last_seen WHERE user_id ='$myId'";
		$result = mysqli_query($db, $query) or die (mysqli_error($db));
		$object = mysqli_fetch_assoc($result);
		$messageId = empty($object) ? 0 : $object['message_id'];
		
		$exists = latestMessage($messageId);
		
		if($exists)
		{
			$new_exists = true;
		}
		// THIS WHOLE SECTION NEED A GOOD OVERHAUL TO CHANGE THE FUNCTIONALITY
		if ($new_exists)
		{
			$new_messages = unreadMessage();
			$thread = [];
		    foreach($new_messages as $message)
		    {			
			    $chat = [
				    'id' 		  => (int) $message['id'],
				    'sender' 	  => (int) $message['sender'],
				    'recvId'      => (int) $message['recvId'],
				    'body' 		  => $message['body'],
					'status' 	  => (int) $message['status'],
				    'recvIsGroup' => false,
				    'time' 		  => $message['time'],
			    ];
			    array_push($thread, $chat);
		    }
			
			updateLastSeen();
			
		    header('Content-Type: application/json');
	        echo json_encode($thread);
		}
	}
	
	//ANOTHER FUNCTIONS FOR CHAT	
	// return object user logged by session "$myId"
	function getUserByIdRow($myId)
	{
		global $db;
		
		$query = "SELECT * FROM users WHERE id='$myId' LIMIT 1";		
		$result = mysqli_query($db, $query) or die (mysqli_error($db));
		
		while ($obj = mysqli_fetch_object($result))
		{
			return $obj;
		}
	}
	
	// return last message
	function returnLastMessage($messageLastId)
	{
		global $db;
		
		$query = "SELECT * FROM users WHERE id='$messageLastId' LIMIT 1";		
		$result = mysqli_query($db, $query) or die (mysqli_error($db));
		
		while ($obj = mysqli_fetch_object($result))
		{
			return $obj;
		}
	}
	
		function latestMessage($messageId)
	{
		global $db;
		$myId = $_SESSION['user']['id'];
		
		$query = "SELECT * FROM message WHERE recvId='$myId' AND id>'$messageId' ORDER BY time desc LIMIT 1";
		
		$result = mysqli_query($db, $query) or die (mysqli_error($db));

		if (mysqli_num_rows($result) > 0)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	function unreadMessage()
	{	
		global $db;
		$myId = $_SESSION['user']['id'];
		
		$query = "SELECT * FROM message WHERE recvId='$myId' AND status = 1 ORDER BY time asc";		
		$result = mysqli_query($db, $query) or die (mysqli_error($db));
		
		return $result;
	}
	
	function updateLastSeen()
	{
		global $db;
		$myId = $_SESSION['user']['id'];
		
		$query = "SELECT * FROM message WHERE recvId='$myId' ORDER BY time desc LIMIT 1";
		
		$result = mysqli_query($db, $query) or die (mysqli_error($db));
		$lastMessage = mysqli_fetch_assoc($result);
		$messageId = empty($lastMessage) ? 0 : $lastMessage['id'];
				
		$record = getLastUser();
		
		if(empty($record))
		{
			$query = "INSERT INTO last_seen (user_id, message_id)
						  VALUES('$myId', '$messageId')";
			$result = mysqli_query($db, $query) or die (mysqli_error($db));
		}
		else
		{
			$record = $record->id;
			$query = "UPDATE last_seen SET user_id='$myId', message_id='$messageId' WHERE id='$record'";
			$result = mysqli_query($db, $query) or die (mysqli_error($db));
		}
	}
	
	function getLastUser()
	{
		global $db;
		$myId = $_SESSION['user']['id'];
		
		$query = "SELECT * FROM last_seen WHERE user_id ='$myId' ORDER BY id desc LIMIT 1";
		$result = mysqli_query($db, $query) or die (mysqli_error($db));
		
		while ($lastUser = mysqli_fetch_object($result))
		{
			return $lastUser;
		}
	}
	
	function markRead()
	{
		global $db;
		$id = $_POST['id'];		
		$query = "UPDATE message SET status = 2 WHERE id='$id'";
		print_r($query);
		$result = mysqli_query($db, $query) or die (mysqli_error($db));		
	}

?>