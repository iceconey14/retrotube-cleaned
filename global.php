<?php
$mysqli = new mysqli("put mysql host here", "database name", "password", "user");
session_start();
// again, its for horrible people
if (!empty($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $mysqli->prepare("SELECT id FROM banned_ips WHERE ip_address = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("Location: /banned.php");
        exit();
    }
    $stmt->close();
}

function idFromUser($nameuser){
	global $mysqli;
	$uid = 0;
	$username = $mysqli->real_escape_string($nameuser);
	$statement = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
	$statement->bind_param("s", $username);
	$statement->execute();
	$statement->bind_result($uid);
	$statement->fetch();
	$statement->close();
	return (int)$uid;
}

function getUserPic($uid){
	$userpic = (string)$uid;
	if (!file_exists("./pfp/" . $userpic)) {
		$userpic = "default";
	}
	return $userpic;
}

$loggedIn = isset($_SESSION['profileuser3']);
?>
