<?php
	require_once("db.php");
	require_once("../config/config.inc");
	session_save_path(SESSION_SAVED);
	session_start();
	header('Content-Type: application/json');

	$reply = array();
    $reply['receive'] = false;
    if (isset($_REQUEST['regi'])) {
        $reply['receive'] = true;
		$data = json_decode($_REQUEST['regi'], true);
		// get the user info

		// HASH FUNCTION USED: sha1()
		// SALT USED: 'DOITNOW'
		
		$name = $data['name'];
		$pwd = sha1($data['password']);
		$email = $data['email'];
		// //connect and query the database
		$dbconn = db_connect();		

		$result = pg_prepare($dbconn, "", 'insert into users values(nextval(\'users_id_seq\'), $1 , $2 , $3, $4, $5);');
		$result = pg_execute($dbconn, "", array($name, $pwd, 1, 0, $email));

		if ($result) {
			$reply['status'] = "Success";
		} else {
			$reply['status'] = "Error";
		}

		$result = pg_prepare($dbconn, "", 'select * from users where name = $1');
		$result = pg_execute($dbconn, "", array($name));

		//store user state in the session
		$user = pg_fetch_array($result);
		$_SESSION['valid_user'] = $user['name'];
		$_SESSION['valid_id'] = $user['id'];
		$_SESSION['valid_level'] = $user['level'];
		$_SESSION['valid_exp'] = $user['exp'];
	}
	
	echo json_encode($reply);
?>