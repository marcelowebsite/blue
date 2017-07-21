<?php
/* Functions for saving user searches in the database */
$errorUrl = 'http://www.blueprintsprograms.com/error.php';
if (!defined('LIB_PATH') || !defined('CONTROLLER_START_TIME')) {header("Status: 200");header("Location: " .$errorUrl);exit();}

/***********************
function saveKeywordSearch($db,$search_string,$limit)
function saveSelectorSearch($db,$search_vars,$is_step_search=null)
***********************/

function saveKeywordSearch($db,$search_string,$limit)
{
	$search_id = 0;
	$search_string = (string)$search_string;
	$limit_arg = $limit;
	$limit = 0;
	if (defined('KEYWORD_LIMIT_OUTCOMES') && defined('KEYWORD_LIMIT_PROGRAM_TYPE')) {
		$limits = array(KEYWORD_LIMIT_OUTCOMES=>1,KEYWORD_LIMIT_PROGRAM_TYPE=>2);
		$limit = (isset($limits[$limit_arg])) ? $limits[$limit_arg] : 0;
	}
	if (is_string($search_string) && '' != $search_string) {
		if (isset($_SESSION['user_id']) && 0 != $_SESSION['user_id']) {
			$session_id = session_id();
			$user_id = $_SESSION['user_id'];
			/* more secure but more db usage - I'll try without and see if we end up with any orphans
			$session_user_id = $_SESSION['user_id'];
			$user_id = getUserFromSessionId($db,$session_id,'bp_session','bp_session_id','bp_user_min','bp_user_id','user_created');
			if (0 != $user_id && $session_user_id==$user_id) {
			*/
			if (0 != $user_id) {
				//generate new search_id and store search in db
				$search_id = createNewUserID($db,9,'bp_search_user','bp_search_id');
				if (0 != $search_id) {
					//INSERT INTO bp_search_user (bp_user_id,bp_search_id,bp_session_id) VALUES(1234,5678,'abc123');
					//INSERT INTO bp_search_keyword (bp_search_id,bp_search_text) VALUES(5678,'bullying');
					$table1_data = array($user_id,$search_id,$session_id);
					$table1_params = 'iis';
					$query1 = "INSERT INTO bp_search_user (bp_user_id,bp_search_id,is_keyword_search,bp_session_id) VALUES(?,?,1,?)";
					if (0 != $limit) {
						$table2_data = array($search_id,$limit,$search_string);
						$table2_params = 'iis';
						$query2 = "INSERT INTO bp_search_keyword (bp_search_id,search_keyword_limit,bp_search_text) VALUES(?,?,?)";
					}
					else {
						$table2_data = array($search_id,$search_string);
						$table2_params = 'is';
						$query2 = "INSERT INTO bp_search_keyword (bp_search_id,bp_search_text) VALUES(?,?)";
					}
					$transaction = mysqliTransactionTwoTables($db,$query1,$query2,$table1_data,$table1_params,$table2_data,$table2_params);
				}
				//check if db transactions ok
				if (isset($transaction) && is_array($transaction)) {
					switch (count($transaction)) {
						case 2: $ok = 1 === $transaction[0] && 1 === $transaction[1];break;
						default: $ok = false;break;
					}
					$search_id = (true === $ok) ? $search_id : 0;
				}
				else {
					$search_id = 0;
				}
			}
		}
	}
	return $search_id;
}

function saveSelectorSearch($db,$search_vars,$is_step_search=null)
{
	$search_id = 0;
	$user_id = 0;
	if (!empty($search_vars) && is_array($search_vars)) {
		if (isset($_SESSION['user_id']) && 0 != $_SESSION['user_id']) {
			$session_id = session_id();
			$user_id = $_SESSION['user_id'];
			/* more secure but more db usage
			$session_user_id = $_SESSION['user_id'];
			$user_id = getUserFromSessionId($db,$session_id,'bp_session','bp_session_id','bp_user_min','bp_user_id','user_created');
			*/
			$search_data = array();
			$search_params = array();
			$placeholders = array();
			//generate new search_id and store search in db
			$search_id = createNewUserID($db,9,'bp_search_user','bp_search_id');
			if (0 != $search_id) {
				foreach ($search_vars as $ea_vars) {
					if (is_array($ea_vars) && 2 == count($ea_vars)) {
						$e = $ea_vars[0];
						$a = $ea_vars[1];
						if ((is_string($e) && ctype_digit($e)) || (is_int((int)$e)) && (is_string($a) && ctype_digit($a)) || (is_int((int)$a))) {
							$search_data[] = $search_id;
							$search_data[] = $e;
							$search_data[] = $a;
							$placeholders[] = '(?,?,?)';
							$search_params[] = 'iii';
						}
						else {
							$search_id = 0;
							$user_id = 0;
							break;
						}
					}
				}
			}
			if (0 != $user_id && 0 != $search_id && count($search_vars) == count($search_params)) {  //secure mode add: ($session_user_id==$user_id)
				//INSERT INTO bp_search_user (bp_user_id,bp_search_id,bp_session_id) VALUES(1234,5678,'abc123');
				//INSERT INTO bp_search_keyword (bp_search_id,bp_search_text) VALUES(5678,'bullying');
				if (checkIntegerRange($is_step_search,1,127)) {
					$table1_data = array($user_id,$search_id,$is_step_search,$session_id);
					$table1_params = 'iiis';
					$query1 = "INSERT INTO bp_search_user (bp_user_id,bp_search_id,is_step_search,bp_session_id) VALUES(?,?,?,?)";
				}
				else {
					$table1_data = array($user_id,$search_id,$session_id);
					$table1_params = 'iis';
					$query1 = "INSERT INTO bp_search_user (bp_user_id,bp_search_id,bp_session_id) VALUES(?,?,?)";
				}
				$table2_data = $search_data;
				$table2_params = implode($search_params);
				$placeholders = implode(',',$placeholders);
				$query2 = "INSERT INTO bp_search (bp_search_id,bp_search_element,bp_search_attribute) VALUES ".$placeholders;
				$transaction = mysqliTransactionTwoTables($db,$query1,$query2,$table1_data,$table1_params,$table2_data,$table2_params);
			}
			//check if db transactions ok
			if (isset($transaction) && is_array($transaction)) {
				switch (count($transaction)) {
					case 2: $ok = 1 === $transaction[0] && 1 <= $transaction[1];break;
					default: $ok = false;break;
				}
				$search_id = (true === $ok) ? $search_id : 0;
			}
			else {
				$search_id = 0;
			}
		}
	}
	return $search_id;
}
