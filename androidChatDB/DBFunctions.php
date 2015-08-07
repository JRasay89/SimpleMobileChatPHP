<?php
include('DBConnect.php');

class DBFunctions {
	private $dbConnect;

    // constructor
    function __construct() {
        // connecting to database
        $this->dbConnect = new DBConnect();
        $this->dbConnect->connect();
    }
 
    // destructor
    function __destruct() {
         
    }
	
	/********************************************************************************
										LOGIN FUNCTIONS
	*********************************************************************************/
	function login($username, $password) {
		$sql_query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
		$result = $this->dbConnect->getConn()->query($sql_query);
		$num_rows = $result->num_rows;
		if ($num_rows > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	
	/********************************************************************************
										REGISTER FUNCTIONS
	*********************************************************************************/
	function register($username, $password) {
		$sql_query = "INSERT INTO users (username, password) VALUES ('$username', '$password')" ;
		$result = $this->dbConnect->getConn()->query($sql_query);
		if ($result) {
			return true;
		}
		else {
			return false;
		}
	}
	
	//Check if the given user exist
	function isUserExist($username) {
		$sql_query = "SELECT username FROM users WHERE username = '$username'";
		$result = $this->dbConnect->getConn()->query($sql_query);
		$num_rows = $result->num_rows;
		if ($num_rows > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	
	/********************************************************************************
										FRIEND FUNCTIONS
	*********************************************************************************/
	
	function retrieveFriends($username) {
		$uid = $this->getIDByUserName($username);
		$sql_query = "SELECT * FROM friendList WHERE (friend_a = $uid OR friend_b = $uid) AND status = 'accepted'";
		$result = $this->dbConnect->getConn()->query($sql_query);
		$num_rows = $result->num_rows;
		if ($num_rows > 0) {
			$result_array = array();
			while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {
				if ($uid == $row['friend_a']) {
					$friendID = $row['friend_b'];
					$result_array[] = $this->getUserNameByID($friendID);
				}
				else {
					$friendID = $row['friend_a'];
					$result_array[] = $this->getUserNameByID($friendID);
				}
			}
			return $result_array;
		}
		else {
			return false;
		}
	}
	
	function friendInvite($friend_a, $friend_b) {
		$friend_a_ID = $this->getIDByUserName($friend_a);
		$friend_b_ID = $this->getIDByUserName($friend_b);
		$sql_query = "INSERT INTO friendlist (friend_a, friend_b) VALUES($friend_a_ID, $friend_b_ID)";
		$result = $this->dbConnect->getConn()->query($sql_query);
		if ($result) {
			return true;
		}
		else {
			return false;
		}
	}
	
	function retrieveFriendInvites($username) {
		$uid = $this->getIDByUserName($username);
		$sql_query = "SELECT friend_a FROM friendlist WHERE friend_b = $uid AND status = 'pending'";
		$result = $this->dbConnect->getConn()->query($sql_query);
		$num_rows = $result->num_rows;
		if ($num_rows > 0) {
			$result_array = array();
			while($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {
				$friend_a = $row["friend_a"];
				$result_array[] = $this->getUserNameByID($friend_a);
			}
			return $result_array;
		}
		else  {
			return false;
		}
	}
	
	function acceptInvite($username, $friend) {
		$uid = $this->getIDByUserName($username);
		$friend_ID = $this->getIDByUserName($friend);
		$sql_query = "UPDATE friendlist SET status = 'accepted' WHERE friend_a = $friend_ID AND friend_b = $uid";
		$result = $this->dbConnect->getConn()->query($sql_query);
		if ($result) {
			return true;
		}
		else {
			return false;
		}
	}
	
	function isFriendAccepted($friend_a, $friend_b) {
		$friend_a_ID = $this->getIDByUserName($friend_a);
		$friend_b_ID = $this->getIDByUserName($friend_b);
		$sql_query = "SELECT * FROM friendlist 
					  WHERE ((friend_a = $friend_a_ID AND friend_b = $friend_b_ID) OR (friend_a = $friend_b_ID AND friend_b = $friend_a_ID)) AND status = 'accepted'";
		$result = $this->dbConnect->getConn()->query($sql_query);
		$num_rows = $result->num_rows;
		if ($num_rows > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/********************************************************************************
										SEARCH FUNCTIONS
	*********************************************************************************/
	function search($username) {
		$sql_query = "SELECT username FROM users WHERE username LIKE '%$username%'";
		$result = $this->dbConnect->getConn()->query($sql_query);
		$num_rows = $result->num_rows;
		if ($num_rows > 0 ) {
			$result_array = array();
			while($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {
				$user = $row["username"];
				$result_array[] = $user;
			}
			return $result_array;
		}
		else {
			return false;
		}
	}
	
	/********************************************************************************
										ROOM FUNCTIONS
	*********************************************************************************/
	function addRoom($roomName, $creator, $friends) {
		$creatorID = $this->getIDByUserName($creator);
		$sql_query = "INSERT INTO chatrooms(roomName, creator) VALUES('$roomName', $creatorID)";
		$result = $this->dbConnect->getConn()->query($sql_query);
		if ($result) {
			$roomID = $this->dbConnect->getConn()->insert_id;
			$this->addUserToRoom($roomID, $creatorID);
			foreach ($friends as $value) {
				$friendID = $this->getIDByUserName($value);
				$this->addUserToRoom($roomID, $friendID);
			}
			
			$room["roomID"] = $roomID;
			$room["roomName"] = $roomName;
			return $room;
		}
		else {
			return false;
		}
	}
	
	function addUserToRoom($roomID, $uid) {
		$sql_query = "INSERT INTO chatroomusers(roomID, uid) VALUES($roomID, $uid)";
		$this->dbConnect->getConn()->query($sql_query);
	}
	
	function retrieveRooms($username) {
		$uid = $this->getIDByUserName($username);
		$sql_query = "SELECT roomID FROM chatroomusers WHERE uid = $uid";
		$result = $this->dbConnect->getConn()->query($sql_query);
		$num_rows = $result->num_rows;
		if ($num_rows > 0) {
			$result_array = array();
			while ($row = mysqli_fetch_array($result, MYSQLI_BOTH)) {
				$roomID = $row["roomID"];
				$roomName = $this->getRoomNameByID($roomID);
				$room["roomID"] = $roomID;
				$room["roomName"] = $roomName;
				$result_array[] = $room;
			}
			return $result_array;
		}
		else {
			return false;
		}
	}
	function getRoomNameByID($roomID) {
		$sql_query = "SELECT roomName FROM chatrooms WHERE roomID = $roomID";
		$result = $this->dbConnect->getConn()->query($sql_query);
		$row = mysqli_fetch_array($result ,MYSQLI_BOTH);
		$roomName = $row["roomName"];
		return $roomName;
	}
	/********************************************************************************
										OTHER FUNCTIONS
	*********************************************************************************/
	function getIDByUserName($username) {
		$sql_query = "SELECT uid FROM users WHERE username = '$username'";
		$result = $this->dbConnect->getConn()->query($sql_query);
		$row = mysqli_fetch_array($result ,MYSQLI_BOTH);
		$uid = $row["uid"];
		return $uid;
	}
	
	function getUserNameByID($uid) {
		$sql_query = "SELECT username FROM users WHERE uid = $uid";
		$result = $this->dbConnect->getConn()->query($sql_query);
		$row = mysqli_fetch_array($result ,MYSQLI_BOTH);
		$username = $row["username"];
		return $username;
	}
}

?>