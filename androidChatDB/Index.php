<?php
include ('DBFunctions.php');

if (isset($_POST['tag']) && $_POST['tag'] != '') {
    // get tag
    $tag = $_POST['tag'];
 
    $dbFunctions = new DBFunctions();
 
    // response Array
    $response = array("tag" => $tag, "error" => FALSE);
	
	//Login the user
	if ($tag == 'login') {
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		$result = $dbFunctions->login($username, $password);
		if ($result) {
			$response["error"] = FALSE;
			$response["user"]["username"] = $username;
			$response["user"]["password"] = $password;
			echo json_encode($response);
		}
		else {
			$response["error"] = TRUE;
			$response["error_msg"] = "Incorrect username or password";
			echo json_encode($response);
		}
	}
	//Register the user
	else if ($tag == 'register') {
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		if ($dbFunctions->isUserExist($username)) {
			$response["error"] = TRUE;
			$response["error_msg"] = "User already exist";
			echo json_encode($response);
		}
		else {
			$result = $dbFunctions->register($username, $password);
			if ($result) {
				$response["error"] = FALSE;
				$response["success_msg"] = "Registration successful";
				$response["user"]["username"] = $username;
				$response["user"]["password"] = $password;
				echo json_encode($response);
			}
			else {
				$response["error"] = TRUE;
				$response["error_msg"] = "An error occured during registration";
				echo json_encode($response);
			}
		}
	}
	
	else if ($tag == 'retrieve_Friends') {
		$username = $_POST['username'];
		$result = $dbFunctions->retrieveFriends($username);
		if ($result) {
			$response["error"] = FALSE;
			$response["friends"] = $result;
			echo json_encode($response);
		}
		else {
			$response["error"] = TRUE;
			$response["error_msg"] = "No friends were found";
			echo json_encode($response);
		}
	}
	else if ($tag == 'retrieve_friendInvites') {
		$username = $_POST['username'];
		$result = $dbFunctions->retrieveFriendInvites($username);
		if ($result != false) {
			$response["error"] = FALSE;
			$response["invites"] = $result;
			echo json_encode($response);
		}
		else {
			$response["error"] = TRUE;
			$response["error_msg"] = "No invites found";
			echo json_encode($response);
		}
	}
	
	else if ($tag == "search") {
		$username = $_POST['username'];
		$result = $dbFunctions->search($username);
		if ($result != false) {
			$response["error"] = FALSE;
			$response["usernames"] = $result;
			echo json_encode($response);
		}
		else {
			$response["error"] = TRUE;
			$response["error_msg"] = "No users found";
			echo json_encode($response);
		}
	}
	
	else if ($tag == "friend_invite") {
		$username = $_POST['username'];
		$friend = $_POST['friend'];
		$result = $dbFunctions->friendInvite($username, $friend);
		if ($result != false) {
			$response["error"] = FALSE;
			$response["success_msg"] = "Friend invite sent";
			echo json_encode($response);
		}
		else {
			$response["error"] = TRUE;
			$response["error_msg"] = "Invite already sent";
			echo json_encode($response);
		}
	}
	
	else if ($tag == "accept_invite") {
		$username = $_POST['username'];
		$friend = $_POST['friend'];
		$result = $dbFunctions->acceptInvite($username, $friend);
		if ($result) {
			$response["error"] = FALSE;
			$response["success_msg"] = "Friend invite accepted";
			echo json_encode($response);
		}
		else {
			$response["error"] = TRUE;
			$response["error_msg"] = "Accepting invite failed";
			echo json_encode($response);
		}
	}
	
	else if ($tag == "retrieve_chatRooms") {
		$username = $_POST['username'];
		$result = $dbFunctions->retrieveRooms($username);
		if ($result != false) {
			$response["error"] = FALSE;
			$response["chatrooms"] = $result;
			echo json_encode($response);
		}
		else {
			$response["error"] = TRUE;
			$response["error_msg"] = "No rooms found";
			echo json_encode($response);
		}
	}
	
	else if ($tag == "create_chatRoom") {
		$roomName = $_POST['roomName'];
		$username = $_POST['username'];
		$friendsJobj = json_decode($_POST['friends']);
		$result = $dbFunctions->addRoom($roomName, $username, $friendsJobj->friends);
		if ($result != false) {
			$response["error"] = FALSE;
			$response["chatRoom"]["roomID"] = $result["roomID"];
			$response["chatRoom"]["roomName"] = $result["roomName"];
			$response["success_msg"] = "Room created successfully";
			echo json_encode($response);
		}
		else {
			$response["error"] = TRUE;
			$response["error_msg"] = "Failed to create room";
			echo json_encode($response);
		}
		
	}
	
}
?>