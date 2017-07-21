<?php
/* Functions for creating/registering website users and user sessions */
$errorUrl = 'http://www.blueprintsprograms.com/error.php';
if (!defined('LIB_PATH') || !defined('CONTROLLER_START_TIME')) {header("Status: 200");header("Location: " .$errorUrl);exit();}

/***********************
function createNewUserID($db, $outlength = 10, $table, $field)
function getUserFromSessionId($db,$session_id,$table,$session_field,$user_table,$user_field,$user_null_field)
function registerUserSession($db)
***********************/

//creates numeric user id and recursively checks that it doesn't already exist
function createNewUserID($db, $outlength = 10, $table, $field)
{
	$newuser = 0;
	$newuser = randomIDString(0, $outlength);  //the 1st argument, 0, means will return numeric
	$existingUserQuery = "SELECT " . $field . " FROM " . $table . " WHERE " . $field . " = ?";
	$paramArray = array('i', $newuser);
	$rows = mysqliQueryRowExec($db, $existingUserQuery, $paramArray);
	if (count($rows) > 0) { //count rows must == 0 or new ID is not unique
		$newuser = createNewUserID($db, $outlength, $table, $field);
		return $newuser;
	}
	else {
		return $newuser;
	}
}

// SELECT * FROM `bp_session` s LEFT JOIN `bp_user_min` u USING (bp_user_id) WHERE s.`bp_session_id` = 'abcde'
//returns user id of current session, if valid user exists
function getUserFromSessionId($db,$session_id,$table,$session_field,$user_table,$user_field,$user_null_field)
{
	$user_id = 0;
	if (!empty($session_id) && is_string((string)$session_id)) {
		$params = array('s',$session_id);
		$query = "SELECT * FROM ".$table." s LEFT JOIN ".$user_table." u USING (".$user_field.") WHERE s.".$session_field." = ?";
		$users = mysqliQueryExec($db,$query,$params);
		if (count($users) === 1 && isset($users[0][$user_null_field]) && $users[0][$user_null_field] > 0) {
			$user_id = $users[0][$user_field];
		}
	}
	return $user_id;
}

//Creates new user or recognizes existing session. Inserts new user in user and session tables or updates existing session table record.
//Sets session user_id.
function registerUserSession($db)
{
	$user_id = 0;
	$session_user_id = 0;
	if (isset($_SESSION)) {
		$session_id = session_id();
		//ipv4 address. To insert as int(10) unsigned: INET_ATON(ip string). To select in standard format: INET_NTOA(ip int). Both fcts return null if invalid.
		//ibv6 support could be added when we upgrade to PHP version 5.6.3
		//INET_ATON binding problem. See http://dev.mysql.com/doc/refman/5.0/en/miscellaneous-functions.html
		//$integer_ip = (substr($ip, 0, 3) > 127) ? ((ip2long($ip) & 0x7FFFFFFF) + 0x80000000) : ip2long($ip);
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SESSION['user_id'])) {
			$session_user_id = $_SESSION['user_id'];
			$_SESSION['user_id'] = 0;  //for security
			$user_id = getUserFromSessionId($db,$session_id,'bp_session','bp_session_id','bp_user_min','bp_user_id','user_created');
			if (0 != $user_id && $session_user_id==$user_id) {
				$is_new_user = false;
				//echo $user_id.'not new user<br>';
			}
			else {
				$is_new_user = true;
			}
		}
		if (0 == $user_id) { //create new user
			@session_regenerate_id(true); //for security
			$session_id = session_id();
			$user_id = createNewUserID($db,9,'bp_user_min','bp_user_id');  //MySQL max unsigned int is 4294967295
			$is_new_user = true;
			//echo $user_id.' new user<br>';
		}
		if (0 != $user_id && isset($is_new_user)) {
			//update bp_user: bp_user_id, user_created (if new)
			//update bp_session: bp_session_id, bp_user_id, session_ip_address, session_created (if new)
			// add later: session_page_id, session_page_token, session_name,
			if ($is_new_user === true) {
				//INSERT INTO bp_user (bp_user_id,user_created) VALUES(1234,NOW());
				//INSERT INTO bp_session (bp_session_id, user_editor_id, session_ip_address, session_created) VALUES(123456789,1234,INET_ATON('128.138.130.30'),NOW());
				//Using bp_user_min table to minimize storage until full user accounts enabled
				$table1_data = array($user_id);
				$table1_params = 'i';
				$query1 = "INSERT INTO bp_user_min (bp_user_id) VALUES(?)";
				$table2_data = array($session_id,$user_id);
				$table2_params = 'si';
				$query2 = "INSERT INTO bp_session (bp_session_id, bp_user_id, session_ip_address, session_created) VALUES(?,?,INET_ATON('$ip'),NOW())";
				$transaction = mysqliTransactionTwoTables($db,$query1,$query2,$table1_data,$table1_params,$table2_data,$table2_params);
			}
			elseif ($is_new_user === false) {  //existing user
				//UPDATE `bp_session` SET `session_ip_address` = INET_ATON('128.138.130.3') WHERE `bp_session_id` = '123456789' AND `bp_user_id` = 1234
				$table_data = array($session_id,$user_id);
				$table_params = 'si';
				$query = "UPDATE bp_session SET session_ip_address = INET_ATON('$ip'), session_updated = NOW() WHERE bp_session_id = ? AND bp_user_id = ?";
				$_SESSION['user_id'] = $user_id;
				$transaction = mysqliTransactionOneTable($db,$query,$table_data,$table_params);
			}
			//check if db transactions ok
			if (isset($transaction) && is_array($transaction)) {
				switch (count($transaction)) {
					case 1: $ok = 1 === $transaction[0];break;
					case 2: $ok = 1 === $transaction[0] && 1 === $transaction[1];break;
					default: $ok = false;break;
				}
				$user_id = (true === $ok) ? $user_id : 0;
			}
			else {
				$user_id = 0;
			}
		}
		else { //error with session
			$errorURL = (defined('BASE_URL')) ? BASE_URL : 'http://www.blueprintsprograms.com';
			header("Status: 200");
			header("Location: ".$errorURL);
			exit();
		}
		$_SESSION['user_id'] = $user_id;
	}
	return $user_id;
}
