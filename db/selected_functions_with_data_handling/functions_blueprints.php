<?php
/* Blueprints main functions */
$errorUrl = 'http://www.blueprintsprograms.com/error.php';
if (!defined('LIB_PATH') || !defined('CONTROLLER_START_TIME')) {header("Status: 200");header("Location: " .$errorUrl);exit();}

/***********************
function checkIdenticalArraysOrStrings($item1,$item2)
function checkIntegerRange($int, $min, $max)
function cleanSlug($slug, $min=1, $max='')
function explodeMultiDelimiters($delimiters,$string)
function fastArrayDiff($array1,$array2)
function getColumnBreaks($count_items,$cols=3)
function html2txt($document,$separator='')
function getLocalVideoStream($file)
function randomIDString($vartype=0,$outlength)
function unsetOldSessionVars()
function validateGet()
function arrayToWhereClause($items,$field,$datatype,$conjunction,$flag='')
function getLabelFromMessage($label,$message_type,$message_content,$break='')
function getMessageFromIdTargetType($db,$target_id,$target_type,$message_type)
function getElementsBySection($db,$section_id)
function getElementsDomains($db,$domains)
function getElementsByDomain($db,$domain_id)
function getDomainsBySection($db,$section_id)
function getPageElements($db,$page)
function getElementsStructure($db,$setting,$e_needed)
function getAttributesStructure($db,$setting)
function getProgramAttributes($db,$p_id,$setting,$e_needed)
function getProgramText($db,$p_id,$e_needed)
function getSections($db,$setting)
function getDomains($db,$setting)
function getProgramInfo($db,$p_id,$page=VIEW_ALL)
***********************/

//**************
// Utilities

//note this returns null if both items are not arrays or strings
//so, must use === to check result
function checkIdenticalArraysOrStrings($item1,$item2)
{
	$are_identical = null;
	if (is_array($item1) && is_array($item2)) {
		$are_identical = (md5(serialize($item1)) == md5(serialize($item2))) ? true : false;
	}
	elseif (is_string($item1) && is_string($item2)) {
		$are_identical = (trim($item1) == trim($item2)) ? true : false;
	}
	elseif (is_array($item1) || is_string($item1) || is_array($item2) || is_string($item2)) {
		$are_identical = false;
	}
	return $are_identical;
}

function checkIntegerRange($int, $min, $max)
{
	if (is_string($int) && !ctype_digit($int)) {
		return false; // contains non digit characters
	}
	if (!is_int((int) $int)) {
		return false; // other non-integer value or exceeds PHP_INT_MAX
	}
	return ($int >= $min && $int <= $max);
}

//our slugs follow a restricted format
function cleanSlug($slug, $min=1, $max='')
{
	$clean_slug = '';
	if (preg_match('/^[a-zA-Z0-9-_]{' . $min . ',' . $max . '}$/',$slug)) {
		$clean_slug = strtolower($slug);
	}
	return $clean_slug;
}

//http://us1.php.net/manual/en/function.explode.php
//php at metehanarslan dot com
//extends explode() to split on array of delimiters
//also cleans invalid items from the delimiter array and returns empty array if input args not correct
function explodeMultiDelimiters($delimiters,$string)
{
	$result = array();
	if(is_array($delimiters) && is_string($string)) {
		foreach($delimiters as $k=>$delimiter) {
			if(!ctype_print($delimiter)) { unset($delimiters[$k]); }
		}
		if(count($delimiters)>1) {
			$ready = str_replace($delimiters,$delimiters[0],$string);
			$result = explode($delimiters[0],$ready);
		}
		//no empty items - could make this optional
		foreach($result as $k=>$v) {
			if(empty($v)) { unset($result[$k]); }
		}
	}
	return $result;
}

//http://php.net/manual/en/function.array-diff.php
//merlyn dot tgz at gmail dot com
//fast array_diff for 2 one-dimensional arrays of ints or strings
function fastArrayDiff($array1,$array2)
{
	$array2 = array_flip($array2);
	foreach ($array1 as $k=>$v) {
		if(isset($array2[$v])) {
			unset($array1[$k]);
		}
	}
	return $array1;
}

function getColumnBreaks($count_items,$cols=3)
{
	$breaks = array();
	$j = $i = ceil((float)$count_items/$cols);
	while ($j<$count_items) {
		$breaks[$j] = 1;
		$j = $j + $i;
	}
	return $breaks;
}

//http://php.net/manual/en/function.strip-tags.php
function html2txt($document,$separator='')
{
	$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
		'@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
		'@<style[^>]*?>.*?</style>@siU',  // Strip style tags properly
		'@<![\s\S]*?--[ \t\n\r]*>@'       // Strip multi-line comments including CDATA
	);
	$text = preg_replace($search,$separator,$document);
	return $text;
}

function getLocalVideoStream($file)
{
	$stream = '';
	if (preg_match("/^[-_a-z0-9]+\.(flv|mpeg|mpg|mp4){1}$/i",$file)) {
		$strings = explode('.',$file);
		//$stream = VIDEO_LOCAL_PATH.DIRECTORY_SEPARATOR.$strings[0];
		$stream = $strings[0];
	}
	return $stream;
}

// returns arbitrary string of numerical (vartype==0) or ascii (vartype==1) characters
function randomIDString($vartype=0,$outlength)
{
	if ($vartype == 0) {
		$a = mt_rand(1, 9);
		while (strlen($a) < $outlength) {
			$a .= mt_rand(0, 9);
		}
		$a = (int)$a;
	}
	elseif ($vartype == 1) {
		$a = chr(mt_rand(97, 122));
		while (strlen($a) < $outlength) {
			$a .= chr(mt_rand(97, 122));
		}
	}
	else {
		$a = '0';
	}
	return $a;
}

function unsetOldSessionVars()
{
	$oldSessionVars = array('p_info','p_info_all','p_info_rating','s_info','selector_search_programs','parsed_selector_terms','previous_search_items','search_vars','fs_info');
	//check if session exists ??
	$i = 0;
	foreach($oldSessionVars as $var) {
		$_SESSION[$var] = null;
		unset($_SESSION[$var]);
		$i++;
	}
	return $i; //no need to check isset first, but gotta return something
}

function validateGet()
{
	$get_ok = false;
	if (!empty($_GET) && !preg_match("/bcc:|cc:|multipart|\[url|Content-Type:/i",implode($_GET)) && !preg_match("/<a|http:|https:/i", implode($_GET))) {
		//could also whitelist characters
		$get_ok = true;
	}
	return $get_ok;
}

//**************
// Program info, elements, attributes, sections, etc

//flag can be empty string or NOT. Should add LIKE also
//conjunction can be empty string or AND or OR
function arrayToWhereClause($items,$field,$datatype,$conjunction,$flag='')
{
	if (!is_array($items) || empty($field)) {return false;}
	$flag = (empty($flag)) ? '' : $flag.' ';
	if ($datatype == 'i') {  //i = int
		$quote = '';
	}
	else {  //s = string
		$quote = "'";
	}
	$c = count($items)-1;
	if (strtoupper($conjunction)=='OR') {
		$clause = '(';
		$k = 0;
		foreach ($items as $item) {
			$clause .= $flag.$field.' = '.$quote.$item.$quote;
			if ($k < $c) {$clause .= ' OR '; }
			$k++;
		}
		$clause .= ')';
	}
	else {
		$clause = '';
		$k = 0;
		foreach ($items as $item) {
			$clause .= $flag.$field.' = '.$quote.$item.$quote;
			if ($k < $c) {$clause .= ' AND '; }
			$k++;
		}
	}
	return $clause;
}

function getLabelFromMessage($label,$message_type,$message_content,$break='')
{
	$break = (empty($break)) ? ' ' : $break;
	if (!empty($label) && !empty($message_type) && !empty($message_content)) {
		if ($message_type == MESSAGE_EDIT_ALIAS || $message_type == MESSAGE_PUBLIC_ALIAS) {
			return $message_content;
		}
		elseif ($message_type == MESSAGE_EDIT_COMMENT || $message_type == MESSAGE_PUBLIC_COMMENT) {
			return $label.$break.$message_content;
		}
		else {
			return $label;
		}
	}
	elseif (empty($label)) {
		return '[label error]';
	}
	else {
		return $label;
	}
}

function getMessageFromIdTargetType($db,$target_id,$target_type,$message_type)
{
	if (empty($target_id) || empty($target_type) || empty($message_type)) { return false; }
	else {
		$params = array('sii',$target_id,$target_type,$message_type);
		$query = "SELECT message_content FROM message WHERE target_id = ? AND target_type = ? AND message_type = ?";
		$rows = mysqliQueryExec($db,$query,$params);
		if (count($rows) !== 1 ) { return false; }
		else {
			$message = $rows[0]['message_content'];
			return $message;
		}
	}
}

function getElementsBySection($db,$section_id)
{
	$params = array('iis',1,1,$section_id);
	$query = "SELECT element_id, element_sort FROM section_element JOIN section USING (section_id) 
 JOIN element USING (element_id) WHERE element.active = ? AND section.active = ? AND section.section_id = ? ORDER BY element_sort";
	$elements = mysqliQueryExec($db,$query,$params);
	if (empty($elements)) {
		return false;
	}
	else {
		$e = array();
		foreach ($elements as $row) {
			$e[$row['element_sort']] = $row['element_id'];
		}
		ksort($e);
		return $e;
	}
}

function getElementsDomains($db,$domains)
{
	$d_clause = (!empty($domains)) ? ' AND '.arrayToWhereClause($domains,'domain_id','s','OR','') : '';
	$params = array('ii',1,1);
	$query = "SELECT domain_id, element_id FROM domain_member JOIN domain USING (domain_id) JOIN element USING (element_id) 
 WHERE domain.active = ? AND element.active = ?".$d_clause." ORDER BY domain_id, element_sort";
	$elements = mysqliQueryExec($db,$query,$params);
	if (empty($elements)) {
		return false;
	}
	else {
		$d = array();
		foreach ($elements as $row) {
			$d[$row['domain_id']][$row['element_id']] = 1;
		}
		return $d;
	}
}

//may need to add domain_sort to domain_member
function getElementsByDomain($db,$domain_id)
{
	$params = array('iis',1,1,$domain_id);
	$query = "SELECT element_id, element_sort FROM domain_member JOIN domain USING (domain_id) 
 JOIN element USING (element_id) WHERE element.active = ? AND domain.active = ? AND domain.domain_id = ? ORDER BY element_sort";
	$elements = mysqliQueryExec($db,$query,$params);
	if (empty($elements)) {
		return false;
	}
	else {
		$e = array();
		foreach ($elements as $row) {
			$e[$row['element_sort']] = $row['element_id'];
		}
		ksort($e);
		return $e;
	}
}

function getDomainsBySection($db,$section_id)
{
	$params = array('iis',1,1,$section_id);
	$query = "SELECT DISTINCT domain_id, domain_label FROM domain JOIN domain_member USING (domain_id)
 JOIN section_element USING (element_id) JOIN section USING (section_id)
 WHERE domain.active = ? AND section.active = ? AND section.section_id = ? ORDER BY domain_id";
	$domains = mysqliQueryExec($db,$query,$params);
	if (empty($domains)) {
		return false;
	}
	else {
		$d = array();
		$i = 1;
		foreach ($domains as $row) {
			$d[$i] = $row['domain_id'];
			$i++;
		}
		return $d;
	}
}

/*---------
Page values (constants):
VIEW_ALL
INDEX_PAGE
ALL_PROGRAMS
EVALUATION_ABSTRACTS
FACT_SHEET_TOP
FACT_SHEET_MAIN
FUNDING_STRATEGIES
PROGRAM_COSTS_PAGE
SEARCH_RESULTS
PROGRAM_SEARCH
PROGRAM_SELECTOR
STEP_1
STEP_2
STEP_3
STEP_4
STEP_5
---------*/
function getPageElements($db,$page)
{
	if (empty($page)) { return false; }
	elseif ($page==VIEW_ALL) {
		return '';
	}
	else {
		global $not_displayed;
		$display_clause = (!empty($not_displayed)) ? ' AND '.arrayToWhereClause($not_displayed,'element.element_id','s','AND','NOT') : '';
		if (PROGRAM_SEARCH == $page) {$page = PROGRAM_SELECTOR;} //PROGRAM_SEARCH not in page_element table, PROGRAM_SELECTOR is same
		switch ($page) {
			case (STEP_1):  //prev also INDEX_PAGE, get the outcome domains for step 1 search
				$outcome_domains = array(OUTCOME_BEHAVIOR,OUTCOME_EDUCATION,OUTCOME_EMOTIONAL,OUTCOME_PHYSICAL,OUTCOME_RELATIONSHIPS);
				$domain_clause = ' AND '.arrayToWhereClause($outcome_domains,'domain.domain_id','s','OR','');
				$params = array('ii',1,1);
				$query = "SELECT domain_member.element_id, element.element_sort FROM domain_member
 JOIN element ON domain_member.element_id = element.element_id
 JOIN domain ON domain_member.domain_id = domain.domain_id
 WHERE domain.active = ? AND element.active = ?".$display_clause.$domain_clause." ORDER BY domain.domain_id, element.element_sort";
				break;
			default:
				$params = array('ii',$page,1);
				$query = "SELECT page_element.element_id, page_element.page_element_sort AS element_sort FROM page_element
 JOIN element ON element.element_id = page_element.element_id
 WHERE page_element.page_id = ? AND element.active = ?".$display_clause." ORDER by page_element.page_element_sort";
				break;
		}
		$elements = mysqliQueryExec($db,$query,$params);
		if (empty($elements)) {
			$e_needed = false;
		}
		else {
			$e_needed = array();
			foreach ($elements as $row) {
				$e_needed[$row['element_sort']] = $row['element_id'];
			}
			ksort($e_needed);
		}
		return $e_needed;
	}
}

/*---------
SELECT e.element_id,e.element_label,e.element_sort,e.control_id,
 (SELECT control_type FROM control WHERE e.control_id = control.control_id) AS control_type,
 (SELECT control_width FROM control WHERE e.control_id = control.control_id) AS control_width,
 (SELECT control_height FROM control WHERE e.control_id = control.control_id) AS control_height,
 (SELECT message_type FROM message WHERE e.element_id = message.target_id AND message.target_type = 1 AND (message.message_type = 1 OR message.message_type = 1)) AS message_type,
 (SELECT message_content FROM message WHERE e.element_id = message.target_id AND message.target_type = 1 AND (message.message_type = 1 OR message.message_type = 1)) AS message_content,
 (SELECT se.section_id FROM section_element se LEFT JOIN section s USING (section_id) WHERE e.element_id = se.element_id AND s.active = 1) AS section_id,
 (SELECT dm.domain_id FROM domain_member dm LEFT JOIN domain d USING (domain_id) WHERE e.element_id = dm.element_id AND d.active = 1) AS domain_id
 FROM element e WHERE active = 1 ORDER BY e.element_sort
---------*/
//Setting can be PUBLIC or EDIT page
//essential that message returns single value, should be OK, can't have both an alias and a comment
//text block messages handled separately
function getElementsStructure($db,$setting,$e_needed)
{
	$e_clause = (!empty($e_needed)) ? ' AND '.arrayToWhereClause($e_needed,'e.element_id','s','OR','') : '';
	if ($setting == EDIT_PAGE) {
		$comment = MESSAGE_EDIT_COMMENT;
		$alias = MESSAGE_EDIT_ALIAS;
	}
	else {
		$comment = MESSAGE_PUBLIC_COMMENT;
		$alias = MESSAGE_PUBLIC_ALIAS;
	}
	$params = array('iiiiiiiii',TARGET_ELEMENT,$comment,$alias,TARGET_ELEMENT,$comment,$alias,1,1,1);
	$query = "SELECT e.element_id,e.element_label,e.element_sort,e.control_id,
 (SELECT control_type FROM control WHERE e.control_id = control.control_id) AS control_type,
 (SELECT control_width FROM control WHERE e.control_id = control.control_id) AS control_width,
 (SELECT control_height FROM control WHERE e.control_id = control.control_id) AS control_height,
 (SELECT message_type FROM message WHERE e.element_id = message.target_id AND message.target_type = ? AND (message.message_type = ? OR message.message_type = ?)) AS message_type,
 (SELECT message_content FROM message WHERE e.element_id = message.target_id AND message.target_type = ? AND (message.message_type = ? OR message.message_type = ?)) AS message_content,
 (SELECT se.section_id FROM section_element se LEFT JOIN section s USING (section_id) WHERE e.element_id = se.element_id AND s.active = ?) AS section_id,
 (SELECT dm.domain_id FROM domain_member dm LEFT JOIN domain d USING (domain_id) WHERE e.element_id = dm.element_id AND d.active = ?) AS domain_id
 FROM element e WHERE active = ?".$e_clause." ORDER BY e.element_sort";
	$elements = mysqliQueryExec($db,$query,$params);
	if (empty($elements)) {
		return false;
	}
	else {
		$e = array();
		//elements ordered by global element_sort
		foreach ($elements as $row) {
			$e[$row['element_id']] = $row;
		}
		//for pages where e_needed is specified, sort return array by e_needed, which handles page_element_sort
		if (!empty($e_needed)) {
			$e_sorted = array();
			foreach ($e_needed as $e_current) {
				$e_sorted[$e_current] = $e[$e_current];
			}
			$e = $e_sorted;
		}
		return $e;
	}
}

//Setting can be PUBLIC or EDIT page
//Get all attributes. Links to elements via control_id
function getAttributesStructure($db,$setting)
{
	global $not_displayed_attributes;
	$display_clause = (!empty($not_displayed_attributes)) ? ' AND '.arrayToWhereClause($not_displayed_attributes,'a.attribute_id','s','AND','NOT') : '';
	if ($setting == EDIT_PAGE) {
		$comment = MESSAGE_EDIT_COMMENT;
		$alias = MESSAGE_EDIT_ALIAS;
	}
	else {
		$comment = MESSAGE_PUBLIC_COMMENT;
		$alias = MESSAGE_PUBLIC_ALIAS;
	}
	$params = array('iiiiiii',TARGET_ATTRIBUTE,$comment,$alias,TARGET_ATTRIBUTE,$comment,$alias,1);
	$query = "SELECT attribute_id,attribute_value,attribute_label,attribute_sort,control_id,control_type,
 (SELECT message_type FROM message WHERE a.attribute_id = message.target_id AND message.target_type = ? AND (message.message_type = ? OR message.message_type = ?)) AS message_type,
 (SELECT message_content FROM message WHERE a.attribute_id = message.target_id AND message.target_type = ? AND (message.message_type = ? OR message.message_type = ?)) AS message_content
 FROM attribute a JOIN control c USING (control_id) WHERE a.active = ?".$display_clause." ORDER BY control_id, attribute_sort";
	$rows = mysqliQueryExec($db,$query,$params);
	if (empty($rows)) {
		$e_attributes = false;
	}
	else {
		$e_attributes = array();
		foreach ($rows as $row) {
			$id_key = $row['control_id'];
			$row_key = $row['attribute_id'];
			$e_attributes[$id_key][$row_key] = $row;
		}
	}
	return $e_attributes;
}

/*---------
SELECT pa.program_id,pa.element_id,pa.attribute_id,a.attribute_label,a.attribute_sort,a.control_id,
 (SELECT element_sort FROM element e WHERE e.element_id = pa.element_id) AS element_sort,
 (SELECT message_type FROM message WHERE pa.attribute_id = message.target_id AND message.target_type = 1 AND (message.message_type = 3 OR message.message_type = 4)) AS message_type,
 (SELECT message_content FROM message WHERE pa.attribute_id = message.target_id AND message.target_type = 1 AND (message.message_type = 3 OR message.message_type = 4)) AS message_content
 FROM program_attribute pa JOIN attribute a USING (attribute_id)
 WHERE pa.program_id = 5 AND a.active = 1 ORDER BY element_sort,a.attribute_sort
---------*/
function getProgramAttributes($db,$p_id,$setting,$e_needed)
{
	global $not_displayed_attributes;
	$display_clause = (!empty($not_displayed_attributes)) ? ' AND '.arrayToWhereClause($not_displayed_attributes,'pa.attribute_id','s','AND','NOT') : '';
	$display_clause .= (!empty($not_displayed_attributes)) ? ' AND '.arrayToWhereClause($not_displayed_attributes,'a.attribute_id','s','AND','NOT') : '';
	//program_attribute
	//program_id 	element_id 	attribute_id
	$e_clause = (!empty($e_needed)) ? ' AND '.arrayToWhereClause($e_needed,'element_id','s','OR','') : '';
	if ($setting == EDIT_PAGE) {
		$comment = MESSAGE_EDIT_COMMENT;
		$alias = MESSAGE_EDIT_ALIAS;
	}
	else {
		$comment = MESSAGE_PUBLIC_COMMENT;
		$alias = MESSAGE_PUBLIC_ALIAS;
	}
	$params = array('iiiiiiii',TARGET_ATTRIBUTE,$comment,$alias,TARGET_ATTRIBUTE,$comment,$alias,$p_id,1);
	$query = "SELECT pa.program_id,pa.element_id,pa.attribute_id,a.attribute_label,a.attribute_sort,a.control_id,
 (SELECT element_sort FROM element e WHERE e.element_id = pa.element_id) AS element_sort,
 (SELECT message_type FROM message WHERE pa.attribute_id = message.target_id AND message.target_type = ? AND (message.message_type = ? OR message.message_type = ?)) AS message_type,
 (SELECT message_content FROM message WHERE pa.attribute_id = message.target_id AND message.target_type = ? AND (message.message_type = ? OR message.message_type = ?)) AS message_content
 FROM program_attribute pa JOIN attribute a USING (attribute_id)
 WHERE pa.program_id = ? AND a.active = ?".$e_clause.$display_clause." ORDER BY element_sort,a.attribute_sort";
	$rows = mysqliQueryExec($db,$query,$params);
	if (empty($rows)) {
		return false;
	}
	else {
		$p_attributes = array();
		foreach ($rows as $row) {
			//NOTE! Many rows per element
			$id_key = $row['element_id'];
			unset($row['element_id']);
			$p_attributes[$id_key][] = $row;
		}
		return $p_attributes;
	}
}

function getProgramText($db,$p_id,$e_needed)
{
	//program_text
	// program_id 	element_id 	text_content
	$e_clause = (!empty($e_needed)) ? ' AND '.arrayToWhereClause($e_needed,'e.element_id','s','OR','') : '';
	$params = array('ii',$p_id,1);
	$query = "SELECT pt.program_id, pt.element_id, e.element_id AS parent_id, pt.text_content
 FROM program_text pt LEFT JOIN element e ON (pt.element_id = e.element_id OR pt.element_id LIKE CONCAT(e.element_id,'-%'))
 WHERE pt.program_id = ? AND e.active = ?".$e_clause." ORDER BY e.element_sort";
	$rows = mysqliQueryExec($db,$query,$params);
	if (empty($rows)) {
		$p_text = array();
	}
	else {
		$p_text = array();
		$multi_temp = array();
		foreach ($rows as $row) {
			//Note only one row per element
			$id_key = $row['element_id'];
			if ($id_key == $row['parent_id']) {
				//std key
				unset($row['element_id']);
				unset($row['parent_id']);
				$p_text[$id_key] = $row;
			}
			else {
				//multi-key
				if (strpos($id_key,'-')===false) {
					//error
					continue;
				}
				else {
					$multikey = explode('-',$id_key);
					if ($multikey[0] != $row['parent_id']) {
						//error
						continue;
					}
					else {
						$multi_temp[$multikey[0]][$row['element_id']] = $row;
						unset($multi_temp[$multikey[0]][$row['element_id']]['element_id']);
						//placeholder
						$p_text[$row['parent_id']] = 1;
					}
				}
			}
		}
		//multikey finished
		if (!empty($multi_temp)) {
			foreach ($multi_temp as $key=>$multi_row) {
				ksort($multi_row);
				if (isset($p_text[$key])) {
					$p_text[$key] = $multi_temp[$key];
				}
			}
		}
	}
	return $p_text;
}

function getSections($db,$setting)
{
	if ($setting == EDIT_PAGE) {
		$comment = MESSAGE_EDIT_COMMENT;
		$alias = MESSAGE_EDIT_ALIAS;
	}
	else {
		$comment = MESSAGE_PUBLIC_COMMENT;
		$alias = MESSAGE_PUBLIC_ALIAS;
	}
	//get section labels and messages if any
	$params = array('iiii',TARGET_SECTION,$comment,$alias,1);
	$query = "SELECT s.section_id, s.section_label, m.message_type, m.message_content FROM section s LEFT OUTER JOIN message m 
 ON (s.section_id = m.target_id AND m.target_type = ? AND (m.message_type = ? OR m.message_type = ?)) WHERE s.active = ?";
	$sections = mysqliQueryExec($db,$query,$params);
	$s = array();
	foreach ($sections as $row) {
		$s[$row['section_id']] = $row;
	}
	return $s;
}

function getDomains($db,$setting)
{
	if ($setting == EDIT_PAGE) {
		$comment = MESSAGE_EDIT_COMMENT;
		$alias = MESSAGE_EDIT_ALIAS;
	}
	else {
		$comment = MESSAGE_PUBLIC_COMMENT;
		$alias = MESSAGE_PUBLIC_ALIAS;
	}
	//get domain labels and messages if any
	$params = array('iiii',TARGET_DOMAIN,$comment,$alias,1);
	$query = "SELECT d.domain_id, d.domain_label, m.message_type, m.message_content FROM domain d LEFT OUTER JOIN message m
 ON (d.domain_id = m.target_id AND m.target_type = ? AND (m.message_type = ? OR m.message_type = ?)) WHERE d.active = ?";
	$domains = mysqliQueryExec($db,$query,$params);
	$d = array();
	foreach ($domains as $row) {
		$d[$row['domain_id']] = $row;
	}
	return $d;
}

//main controller for program information pages: VIEW_ALL, EVALUATION_ABSTRACTS, FACT_SHEET_TOP, FACT_SHEET_MAIN, FUNDING_STRATEGIES, PROGRAM_COSTS_PAGE
function getProgramInfo($db,$p_id,$page=VIEW_ALL)
{
	if (empty($p_id)) {
		echo 'Missing required search parameter.';
		return false;
	}
	$fs_info = array(); //fact sheet (or other program info page) array
	$errors = array();
	$rp_factor_domain_groups = getRiskProtectiveFactorGroups();  //config for sorting the rp factors
	//check for intact session array
	if (!empty($_SESSION['fs_info' . S_TOKEN][$p_id][$page]) && !empty($_SESSION['fs_info' . S_TOKEN][$p_id][$page]['program_name']) && !empty($_SESSION['fs_info' . S_TOKEN][$p_id][$page]['rating'])) {
		$fs_info = $_SESSION['fs_info' . S_TOKEN][$p_id][$page];
		$fs_info['array_loc'] = 'fs_info' . S_TOKEN;
		//error condition - there should be a meta array, even if empty (not all programs have tags)
		if (!isset($fs_info['meta'])) {
			$fs_info['meta'] = array();
		}
	}
	else {
		if (!empty($_SESSION['p_info' . S_TOKEN][$p_id]) && !empty($_SESSION['p_info' . S_TOKEN][$p_id]['program_name']) && !empty($_SESSION['p_info' . S_TOKEN][$p_id]['rating'])) {
			$fs_info['array_loc'] = 'p_info' . S_TOKEN;
			$fs_info['program_name'] = $_SESSION['p_info' . S_TOKEN][$p_id]['program_name'];
			$fs_info['program_slug'] = $_SESSION['p_info' . S_TOKEN][$p_id]['program_slug'];
			$fs_info['rating'] = $_SESSION['p_info' . S_TOKEN][$p_id]['rating'];
			$fs_info['is_international'] = $_SESSION['p_info' . S_TOKEN][$p_id]['is_international'];
		}
		elseif (!empty($_SESSION['p_info_all' . S_TOKEN][$p_id]) && !empty($_SESSION['p_info_all' . S_TOKEN][$p_id]['program_name']) && !empty($_SESSION['p_info_all' . S_TOKEN][$p_id]['rating'])) {
			$fs_info['array_loc'] = 'p_info_all' . S_TOKEN;
			$fs_info['program_name'] = $_SESSION['p_info_all' . S_TOKEN][$p_id]['program_name'];
			$fs_info['program_slug'] = $_SESSION['p_info_all' . S_TOKEN][$p_id]['program_slug'];
			$fs_info['rating'] = $_SESSION['p_info_all' . S_TOKEN][$p_id]['rating'];
			$fs_info['is_international'] = $_SESSION['p_info_all' . S_TOKEN][$p_id]['is_international'];
		}
		else {
			$fs_info['array_loc'] = 'new array';
			$search_programs = getProgramNames($db,$page,array($p_id));
			if (count($search_programs) != 1) {
				echo 'Missing required parameter.';
				return false;
			}
			$fs_info['program_name'] = $search_programs[0]['program_name'];
			$fs_info['program_slug'] = $search_programs[0]['program_slug'];
			$fs_info['rating'] = (empty($search_programs[0]['attribute_id'])) ? $errors['unendorsed'][$p_id] = 1 : getRatingLabel($search_programs[0]['attribute_id']);
			$fs_info['is_international'] = (isset($search_programs[0]['is_international']) && 1===$search_programs[0]['is_international']) ? 1 : 0;
			
		}
		//get meta tags from db. Can be empty array
		$fs_info['meta'] = getProgramMetadata($db,$p_id);
		
		//get elements for the page. Note e_needed can be empty
		$e_needed = getPageElements($db,$page);
		//setting contols what messages the elements, attributes, domains and sections get.
		//Setting can be PUBLIC_PAGE or EDIT_PAGE
		$e = getElementsStructure($db,$setting=PUBLIC_PAGE,$e_needed);  //Note assignment
		//error check for element integrity
		foreach ($e as $element) {
			if (empty($element['control_type'])) {
				$errors['missing_control'][$element['element_id']] = 1;
			}
			if (isset($errors['missing_control'][$element['element_id']])) {
				continue;
			}
		}
		if (!empty($errors)) {
			//write to log or something
			//echo 'Page error.';
			//return false;
		}
		//get program data
		$p_attributes = getProgramAttributes($db,$p_id,$setting,$e_needed);
		$p_text = getProgramText($db,$p_id,$e_needed);
		if (empty($p_attributes)) {$p_attributes = array();}
		if (empty($p_text)) {$p_text = array();}
		//get sections and domains
		$sections = getSections($db,$setting);
		$domains = getDomains($db,$setting);
		//Get the Program Goals aka summary
		$fs_info['summary'] = (empty($p_text[PROGRAM_GOALS])) ? 'Program summary not found.': $p_text[PROGRAM_GOALS]['text_content'];
		//Unset summary on pages where not needed in body
		if (EVALUATION_ABSTRACTS !== $page && VIEW_ALL !== $page) {
			unset($p_text[PROGRAM_GOALS]);
		}
		//testing output
		//$fs_info['e_needed'] = $e_needed;
		//$fs_info['elements'] = $e;
		//$fs_info['p_text'] = $p_text;
		//$fs_info['p_attributes'] = $p_attributes;

		//Initialize pages
		//initialize flag to not re-calculate items that are grabbed from session
		$flags = array('outcomes'=>0,'endorsements'=>0,'rp_factors'=>0,'training_technical_assistance'=>0,);
		//fact_sheet_top
		if ($page===FACT_SHEET_TOP || $page===EVALUATION_ABSTRACTS || $page===VIEW_ALL) {
			//Look at other page's array for shared info. Do not look at current page info - that entire page session would be grabbed above.
			$share_page = ($page===FACT_SHEET_TOP) ? EVALUATION_ABSTRACTS : FACT_SHEET_TOP;
			$e_outcomes = getElementsBySection($db,OUTCOMES);  //needed in all cases to prevent outcomes from being duplicated in the p_attributes loop
			$e_outcomes = (is_array($e_outcomes)) ? array_flip($e_outcomes) : $e_outcomes;
			//Outcomes
			if (!empty($_SESSION['fs_info' . S_TOKEN][$p_id][$share_page]['outcomes'])) {
				$fs_info['outcomes'] = $_SESSION['fs_info' . S_TOKEN][$p_id][$share_page]['outcomes'];
				$_SESSION['fs_info' . S_TOKEN]['share page'] = $share_page;
				$flags['outcomes'] = 1;
			}
			elseif (!empty($_SESSION['p_info' . S_TOKEN][$p_id]) && !empty($_SESSION['p_info' . S_TOKEN][$p_id]['outcomes'])) {
				$fs_info['outcomes']['items'] = $_SESSION['p_info' . S_TOKEN][$p_id]['outcomes'];
				$flags['outcomes'] = 1;
			}
			elseif (!empty($_SESSION['p_info_all' . S_TOKEN][$p_id]) && !empty($_SESSION['p_info_all' . S_TOKEN][$p_id]['outcomes'])) {
				$fs_info['outcomes']['items'] = $_SESSION['p_info_all' . S_TOKEN][$p_id]['outcomes'];
				$flags['outcomes'] = 1;
			}
			if (empty($fs_info['outcomes']['section_label'])) {
				$fs_info['outcomes']['section_label'] = getLabelFromMessage($sections[OUTCOMES]['section_label'],$sections[OUTCOMES]['message_type'],$sections[OUTCOMES]['message_content']);
			}
			if (empty($fs_info['outcomes']['items'])) {
				$fs_info['outcomes']['items'] = array();
			}
			//Endorsements
			$e_endorsements = getElementsBySection($db,ENDORSEMENTS);
			$e_endorsements = (is_array($e_endorsements)) ? array_flip($e_endorsements) : $e_endorsements;
			if (!empty($_SESSION['fs_info' . S_TOKEN][$p_id][$share_page]['endorsements'])) {
				$fs_info['endorsements'] = $_SESSION['fs_info' . S_TOKEN][$p_id][$share_page]['endorsements'];
				$flags['endorsements'] = 1;
			}
			else {
				$fs_info['endorsements']['section_label'] = getLabelFromMessage($sections[ENDORSEMENTS]['section_label'],$sections[ENDORSEMENTS]['message_type'],$sections[ENDORSEMENTS]['message_content']);
				$fs_info['endorsements']['items'] = array();
			}
			//Program Designer/Evaluator Contact -- no sharing due to different elements
			$e_program_designer_contact = getElementsBySection($db,PROGRAM_DESIGNER_CONTACT);
			$e_program_designer_contact = (is_array($e_program_designer_contact)) ? array_flip($e_program_designer_contact) : $e_program_designer_contact;
			$fs_info['program_designer_contact']['section_label'] = getLabelFromMessage($sections[PROGRAM_DESIGNER_CONTACT]['section_label'],$sections[PROGRAM_DESIGNER_CONTACT]['message_type'],$sections[PROGRAM_DESIGNER_CONTACT]['message_content']);
			$fs_info['program_designer_contact']['items'] = array();
		}
		//fact_sheet_main
		if ($page===FACT_SHEET_MAIN || $page===EVALUATION_ABSTRACTS || $page===VIEW_ALL) {
			//Look at other page's array for shared info. Do not look at current page info - that entire page session would be grabbed above.
			$share_page = ($page===FACT_SHEET_MAIN) ? EVALUATION_ABSTRACTS : FACT_SHEET_MAIN;
			//Risk and Protective Factors
			$fs_info['rp_factors']['section_label'] = getLabelFromMessage($sections[RISK_PROTECTIVE_FACTORS]['section_label'],$sections[RISK_PROTECTIVE_FACTORS]['message_type'],$sections[RISK_PROTECTIVE_FACTORS]['message_content']);
			$domains_rp_factors = getDomainsBySection($db,RISK_PROTECTIVE_FACTORS);
			foreach ($domains_rp_factors as $d_needed) {
				$fs_info['rp_factors']['domains'][$d_needed]['domain_label'] = getLabelFromMessage($domains[$d_needed]['domain_label'],$domains[$d_needed]['message_type'],$domains[$d_needed]['message_content']);
			}
			$e_rp_factors = getElementsDomains($db,$domains_rp_factors);
			$temp_rp_factors = array();
			foreach ($e_rp_factors as $d_key=>$rp_factors_list) {
				foreach ($rp_factors_list as $e_key=>$e_rp_list) {
					$temp_rp_factors['domains'][$d_key]['elements'][$e_key] = 1;
				}
			}
		}
		if ($page===FACT_SHEET_MAIN || $page===VIEW_ALL) {
			//Look at other page's array for shared info. Do not look at current page info - that entire page session would be grabbed above.
			$share_page = ($page===FACT_SHEET_MAIN) ? VIEW_ALL : FACT_SHEET_MAIN;
			//Training and technical assistance
			$e_training_technical_assistance = getElementsBySection($db,TRAINING_ASSISTANCE);
			$e_training_technical_assistance = (is_array($e_training_technical_assistance)) ? array_flip($e_training_technical_assistance) : $e_training_technical_assistance;
			if (!empty($_SESSION['fs_info' . S_TOKEN][$p_id][$share_page]['training_technical_assistance'])) {
				$fs_info['training_technical_assistance'] = $_SESSION['fs_info' . S_TOKEN][$p_id][$share_page]['training_technical_assistance'];
				$flags['training_technical_assistance'] = 1;
			}
			else {
				$fs_info['training_technical_assistance']['section_label'] = getLabelFromMessage($sections[TRAINING_ASSISTANCE]['section_label'],$sections[TRAINING_ASSISTANCE]['message_type'],$sections[TRAINING_ASSISTANCE]['message_content']);
				$fs_info['training_technical_assistance']['items'] = array();
			}
		}
		//Program Costs
		if ($page===PROGRAM_COSTS_PAGE || $page===VIEW_ALL) {
			$e_year_one_cost_example = getElementsBySection($db,YEAR_ONE_COST_EXAMPLE);
			$e_year_one_cost_example = (is_array($e_year_one_cost_example)) ? array_flip($e_year_one_cost_example) : $e_year_one_cost_example;
			$fs_info['year_one_cost_example']['section_label'] = getLabelFromMessage($sections[YEAR_ONE_COST_EXAMPLE]['section_label'],$sections[YEAR_ONE_COST_EXAMPLE]['message_type'],$sections[YEAR_ONE_COST_EXAMPLE]['message_content']);
			$fs_info['year_one_cost_example']['items'] = array();
			
			$e_start_up_costs = getElementsBySection($db,START_UP_COSTS);
			$e_start_up_costs = (is_array($e_start_up_costs)) ? array_flip($e_start_up_costs) : $e_start_up_costs;
			$fs_info['start_up_costs']['section_label'] = getLabelFromMessage($sections[START_UP_COSTS]['section_label'],$sections[START_UP_COSTS]['message_type'],$sections[START_UP_COSTS]['message_content']);
			$fs_info['start_up_costs']['items'] = array();
			
			$e_implementation_costs = getElementsBySection($db,IMPLEMENTATION_COSTS);
			$e_implementation_costs = (is_array($e_implementation_costs)) ? array_flip($e_implementation_costs) : $e_implementation_costs;
			$fs_info['implementation_costs']['section_label'] = getLabelFromMessage($sections[IMPLEMENTATION_COSTS]['section_label'],$sections[IMPLEMENTATION_COSTS]['message_type'],$sections[IMPLEMENTATION_COSTS]['message_content']);
			$fs_info['implementation_costs']['items'] = array();
			
			$e_support_monitoring_costs = getElementsBySection($db,SUPPORT_MONITORING_COSTS);
			$e_support_monitoring_costs = (is_array($e_support_monitoring_costs)) ? array_flip($e_support_monitoring_costs) : $e_support_monitoring_costs;
			$fs_info['support_monitoring_costs']['section_label'] = getLabelFromMessage($sections[SUPPORT_MONITORING_COSTS]['section_label'],$sections[SUPPORT_MONITORING_COSTS]['message_type'],$sections[SUPPORT_MONITORING_COSTS]['message_content']);
			$fs_info['support_monitoring_costs']['items'] = array();
		}
		//FUNDING_STRATEGIES
		if ($page===FUNDING_STRATEGIES || $page===VIEW_ALL) {
			$e_financing_strategies = getElementsBySection($db,FINANCING_STRATEGIES);
			$e_financing_strategies = (is_array($e_financing_strategies)) ? array_flip($e_financing_strategies) : $e_financing_strategies;
			$fs_info['financing_strategies']['section_label'] = getLabelFromMessage($sections[FINANCING_STRATEGIES]['section_label'],$sections[FINANCING_STRATEGIES]['message_type'],$sections[FINANCING_STRATEGIES]['message_content']);
			$fs_info['financing_strategies']['items'] = array();
		}
		//Evaluation Abstracts
		if ($page===EVALUATION_ABSTRACTS || $page===VIEW_ALL) {
			$e_program_specifics_full = getElementsBySection($db,PROGRAM_SPECIFICS_FULL);
			$e_program_specifics_full = (is_array($e_program_specifics_full)) ? array_flip($e_program_specifics_full) : $e_program_specifics_full;
			$fs_info['program_specifics_full']['section_label'] = getLabelFromMessage($sections[PROGRAM_SPECIFICS_FULL]['section_label'],$sections[PROGRAM_SPECIFICS_FULL]['message_type'],$sections[PROGRAM_SPECIFICS_FULL]['message_content']);
			$fs_info['program_specifics_full']['items'] = array();
		
			$e_target_population_full = getElementsBySection($db,TARGET_POPULATION_FULL);
			$e_target_population_full = (is_array($e_target_population_full)) ? array_flip($e_target_population_full) : $e_target_population_full;
			$fs_info['target_population_full']['section_label'] = getLabelFromMessage($sections[TARGET_POPULATION_FULL]['section_label'],$sections[TARGET_POPULATION_FULL]['message_type'],$sections[TARGET_POPULATION_FULL]['message_content']);
			$fs_info['target_population_full']['items'] = array();
		}
		
		//init array to contain element_ids, then re-sort it per e_needed
		$sort_elements = array();
		
		//Main data-gathering loop. Looping $p_attribute and $p_text
		foreach ($p_attributes as $e_id=>$p_attribute) {
			foreach($p_attribute as $attribute) {
				if (isset($e_outcomes[$e_id])) {
					if ($attribute['attribute_id']==OUTCOME_ACHIEVED) {
						$sort_elements['outcomes'] = $e_id;
						if($flags['outcomes']!==1) {
							$fs_info['outcomes']['items'][] = $element_label = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
						}
					}
				}
				//raw risk/protective attribute data
				elseif (PROGRAM_FOCUS == $attribute['attribute_id'] OR ACHIEVED == $attribute['attribute_id']) {
					$sort_elements[] = $e_id;
					$fs_info[$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
					$fs_info[$e_id]['content'] = $attribute['attribute_id'];
				}
				elseif (isset($e_endorsements[$e_id])) {
					$sort_elements['endorsements'] = $e_id;
					if($flags['endorsements']!==1) {
						$fs_info['endorsements']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
						$fs_info['endorsements']['items'][$e_id]['content'] = getLabelFromMessage($attribute['attribute_label'],$attribute['message_type'],$attribute['message_content']);
					}
				}
				elseif (isset($e_program_specifics_full[$e_id])) {
					$sort_elements['program_specifics_full'] = $e_id;
					$fs_info['program_specifics_full']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
					$fs_info['program_specifics_full']['items'][$e_id]['items'][] = getLabelFromMessage($attribute['attribute_label'],$attribute['message_type'],$attribute['message_content']);
				}
				elseif (isset($e_target_population_full[$e_id])) {
					$sort_elements['target_population_full'] = $e_id;
					$fs_info['target_population_full']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
					$fs_info['target_population_full']['items'][$e_id]['items'][] = getLabelFromMessage($attribute['attribute_label'],$attribute['message_type'],$attribute['message_content']);
				}
				/*
				elseif (isset($e_target_pop[$e_id])) {
					$sort_elements['target_population'] = $e_id;
					if (empty($target_population[$e_id]['element_label'])) {
						//no message here
						$target_population[$e_id]['element_label'] = $e[$e_id]['element_label'];
					}
					if ($e_id==CONTINUUM_INTERVENTION) {
						//no message here
						$target_population[$e_id]['attribute_labels'][] = $attribute['attribute_label'];
					}
					else {
						$target_population[$e_id]['attribute_labels'][] = getLabelFromMessage($attribute['attribute_label'],$attribute['message_type'],$attribute['message_content']);
					}
				} */
				elseif (isset($e_benefits_costs[$e_id]) && $e_id==COST_DATA_SOURCE) {
					$sort_elements['benefits_costs'] = $e_id;
					$benefits_costs['cost_data_source'] = getLabelFromMessage($attribute['attribute_label'],$attribute['message_type'],$attribute['message_content']);
					$benefits_costs['cost_data_info'] = getMessageFromIdTargetType($db,$attribute['attribute_id'],TARGET_ATTRIBUTE,MESSAGE_PUBLIC_TEXT);
				}
				elseif ($e_id==FUNDING_BRIEF_PDF) {
					$sort_elements[] = $e_id;
					$fs_info[$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
					$fs_info[$e_id]['content'] = $attribute['attribute_id']; //to be checked later for pdf filename
				}
				else {
					//generic element/attributes
					$sort_elements[] = $e_id;
					$fs_info[$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
					$fs_info[$e_id]['items'][] = getLabelFromMessage($attribute['attribute_label'],$attribute['message_type'],$attribute['message_content']);
				}
			}
		}
		foreach ($p_text as $e_id=>$text) {
			if (isset($e_endorsements[$e_id])) {    //>>>>>>>>>>won't be set if existing array data not found above
				$sort_elements['endorsements'] = $e_id;
				$fs_info['endorsements']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
				$fs_info['endorsements']['items'][$e_id]['content'] = $text['text_content'];
			}
			elseif (isset($e_program_designer_contact[$e_id])) {
				$sort_elements['program_designer_contact'] = $e_id;
				$fs_info['program_designer_contact']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
				if ($e_id==WEBSITE) {
					$fs_info['program_designer_contact']['items'][$e_id]['content'] = getHtmlLinkFromUrl($text['text_content'],false,'trackOutboundLink',array('url'));
				}
				elseif ($e_id==EMAIL || $e_id==ALT_EMAIL) {
					$fs_info['program_designer_contact']['items'][$e_id]['content'] = getHtmlLinkFromUrl($text['text_content'],$email=true);  //note assignment
				}
				else {
					$fs_info['program_designer_contact']['items'][$e_id]['content'] = $text['text_content'];
				}
			}
			elseif (isset($e_training_technical_assistance[$e_id])) {
				$sort_elements['training_technical_assistance'] = $e_id;
				if($flags['training_technical_assistance']!==1) {
					$fs_info['training_technical_assistance']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
					$fs_info['training_technical_assistance']['items'][$e_id]['content'] = $text['text_content'];
				}
			}
			elseif (isset($e_benefits_costs[$e_id])) {
				$sort_elements['benefits_costs'] = $e_id;
				$benefits_costs['cost_items'][$e_id]['element_label'] = $e[$e_id]['element_label'];
				$benefits_costs['cost_items'][$e_id]['message'] = $e[$e_id]['message_content'];
				$benefits_costs['cost_items'][$e_id]['amount'] = $text['text_content'];
			}
			elseif (isset($e_target_population_full[$e_id])) {
				$sort_elements['target_population_full'] = $e_id;
				$fs_info['target_population_full']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
				$fs_info['target_population_full']['items'][$e_id]['content'] = $text['text_content'];
			}
			elseif (isset($e_financing_strategies[$e_id])) {
				$sort_elements['financing_strategies'] = $e_id;
				$fs_info['financing_strategies']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
				$fs_info['financing_strategies']['items'][$e_id]['content'] = $text['text_content'];
			}
			elseif (isset($e_start_up_costs[$e_id])) {
				$sort_elements['start_up_costs'] = $e_id;
				$fs_info['start_up_costs']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
				$fs_info['start_up_costs']['items'][$e_id]['content'] = $text['text_content'];
			}
			elseif (isset($e_implementation_costs[$e_id])) {
				$sort_elements['implementation_costs'] = $e_id;
				$fs_info['implementation_costs']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
				$fs_info['implementation_costs']['items'][$e_id]['content'] = $text['text_content'];
			}
			elseif (isset($e_support_monitoring_costs[$e_id])) {
				$sort_elements['support_monitoring_costs'] = $e_id;
				$fs_info['support_monitoring_costs']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
				$fs_info['support_monitoring_costs']['items'][$e_id]['content'] = $text['text_content'];
			}
			elseif ($e[$e_id]['control_type']=='mt' || $e[$e_id]['control_type']=='mta') {
				$element_label = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
				$i = 1;
				foreach ($text as $multi_id=>$multi_row) {
					$multikey = explode('-',$multi_id);
					if ($multikey[0] != $multi_row['parent_id']) {
						//error
						continue;
					}
					else {
						$label = $element_label.' '.$i;
						if ($e_id == YEAR_ONE_COST_ITEM) {
							$sort_elements['year_one_cost_example'] = $e_id;
							//$amount_label = $e[YEAR_ONE_COST_AMOUNT]['element_label'].' '.$i;
							$amount_index = YEAR_ONE_COST_AMOUNT.'-'.$multikey[1];
							$amount_text = (isset($p_text[YEAR_ONE_COST_AMOUNT][$amount_index]['text_content'])) ? $p_text[YEAR_ONE_COST_AMOUNT][$amount_index]['text_content'] : '';
							$fs_info['year_one_cost_example']['items']['multi'][$multi_id]['element_label'] = $multi_row['text_content'];
							$fs_info['year_one_cost_example']['items']['multi'][$multi_id]['content'] = $amount_text;
						}
						elseif ($e_id == YEAR_ONE_COST_AMOUNT) {
							continue;
						}
						else {
							$sort_elements[] = $e_id;
							$fs_info[$e_id]['multi'][$multi_id]['element_label'] = $label;
							$fs_info[$e_id]['multi'][$multi_id]['content'] = $multi_row['text_content'];
						}
						$i++;
					}
				}
			}
			elseif (isset($e_year_one_cost_example[$e_id])) { //This section must be after the multi controls
				$sort_elements['year_one_cost_example'] = $e_id;
				$fs_info['year_one_cost_example']['items'][$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
				$fs_info['year_one_cost_example']['items'][$e_id]['content'] = $text['text_content'];
			}
			else {
				$sort_elements[] = $e_id;
				$fs_info[$e_id]['element_label'] = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
				$fs_info[$e_id]['content'] = $text['text_content'];
			}
		}
		//Organize the risk/protective factors data
		if (!empty($temp_rp_factors)) {
			foreach ($temp_rp_factors['domains'] as $d_key=>$temp_rp_factor) {
				foreach ($temp_rp_factor['elements'] as $e_key=>$temp_rp_element) {
					if (isset($fs_info[$e_key])) {
						$temp_rp_factors['domains'][$d_key]['elements'][$e_key] = $fs_info[$e_key];
						$sort_elements['rp_factors'] = $e_key;
					}
					else {
						unset($temp_rp_factors['domains'][$d_key]['elements'][$e_key]);
					}
					unset($fs_info[$e_key]);
				}
				if (empty($temp_rp_factors['domains'][$d_key]['elements'])) {
					unset($fs_info['rp_factors']['domains'][$d_key]);
				}
				else {
					$fs_info['rp_factors']['domains'][$d_key]['elements'] = $temp_rp_factors['domains'][$d_key]['elements'];
				}
			}
			if (!empty($fs_info['rp_factors']['domains'])) {
				/* Lots of hackery here to mimic the display logic of the orig data array format. Any resemblance to the real db structure is coincidental */
				$domain_sorted_risk = array_intersect_key($fs_info['rp_factors']['domains'],$rp_factor_domain_groups['risk']);
				$domain_sorted_risk = array('domain_label'=>$rp_factor_domain_groups['risk']['label'],'elements'=>$domain_sorted_risk);
				$domain_sorted_protective = array_intersect_key($fs_info['rp_factors']['domains'],$rp_factor_domain_groups['protective']);
				$domain_sorted_protective = array('domain_label'=>$rp_factor_domain_groups['protective']['label'],'elements'=>$domain_sorted_protective);
				$fs_info['rp_factors']['domains'] = array($domain_sorted_risk,$domain_sorted_protective);
				$achieved_flag = 0;  //init footnote display flag
				$temp_domains = array();
				foreach ($fs_info['rp_factors']['domains'] as $k=>$domains) {
					$temp_domains[$k]['domain_label'] = $domains['domain_label'];
					foreach ($domains['elements'] as $e_key=>$d_elements) {
						$temp_elements = array();
						$temp_elements[$e_key]['element_label'] = $d_elements['domain_label'];
						foreach ($d_elements['elements'] as $i_key=>$d_items) {
							if (ACHIEVED == $d_items['content']) {
								$achieved_flag = 1;
								$d_items['element_label'] = $d_items['element_label'] . '<span class="footnote-reference">*</span>';
							}
							$temp_elements[$e_key]['items'][] = $d_items['element_label'];
						}
						$temp_domains[$k]['elements'][$e_key] = $temp_elements[$e_key];
					}
					if (empty($temp_domains[$k]['elements'])) {
						unset($temp_domains[$k]);
					}
				}
				$fs_info['rp_factors']['domains'] = $temp_domains;
				$fs_info['rp_factors']['achieved'] = $achieved_flag;
				/*
				echo '<br clear="left"><pre>';
				echo '1 ';print_r($fs_info['rp_factors']);
				echo '</pre>';
				exit();
				*/
			}
		}
		if (isset($fs_info[LOGIC_MODEL_FILE])) {
			$fs_info['rp_factors'][LOGIC_MODEL_FILE] = $fs_info[LOGIC_MODEL_FILE];
			unset($fs_info[LOGIC_MODEL_FILE]);
		}
		$sorted_elements = array_flip($sort_elements);
		//temp output
		$fs_info['sort_elements'] = $sorted_elements;
		//$fs_info['e_needed'] = $e_needed;
		
		if (!empty($e_needed)) {
			//sort fs_info by e_needed
			foreach ($e_needed as $e_sort) {
				if (isset($sorted_elements[$e_sort]) ) {
					if (is_numeric($sorted_elements[$e_sort])) {
						if (isset($fs_info[$e_sort])) {
							if (isset($fs_info[$e_sort]['multi'])) {
								foreach ($fs_info[$e_sort]['multi'] as $multi_id=>$multi_item) {
									$fs_info[$multi_id] = $multi_item;
									unset($fs_info[$e_sort]);
								}
							}
							else {
								$temp_data = $fs_info[$e_sort];
								unset($fs_info[$e_sort]);
								$fs_info[$e_sort] = $temp_data;
							}
						}
					}
					else { //fs key is a label
						if (isset($fs_info[$sorted_elements[$e_sort]])) {
							if (isset($fs_info[$sorted_elements[$e_sort]]['items']) && !isset($fs_info[$sorted_elements[$e_sort]]['items']['multi'])) { //multi handled above
								$temp_items = array();
								foreach ($e_needed as $e_item_sort) {
									if (isset($fs_info[$sorted_elements[$e_sort]]['items'][$e_item_sort])) {
										$temp_items[$e_item_sort] = $fs_info[$sorted_elements[$e_sort]]['items'][$e_item_sort];
									}
								}
								if (count($temp_items) > 0) {
									$fs_info[$sorted_elements[$e_sort]]['items'] = $temp_items;
								}
							}
							$temp_data = $fs_info[$sorted_elements[$e_sort]];
							unset($fs_info[$sorted_elements[$e_sort]]);
							$fs_info[$sorted_elements[$e_sort]] = $temp_data;
						}
					}
				}
			}
		}
	}  //end loop where fs_info was not already in session
	//put $fs_info in session to check for next time
	$_SESSION['fs_info' . S_TOKEN][$p_id][$page] = $fs_info;
	/*
	if (FACT_SHEET_TOP !== $page) {
		echo '<br clear="left"><pre>';
		//echo '$_SESSION ';print_r($_SESSION);
		print_r($fs_info);
		echo '</pre>';
		exit();
	}
	*/
	return $fs_info;
}
