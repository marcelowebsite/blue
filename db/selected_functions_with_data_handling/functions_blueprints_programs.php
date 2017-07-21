<?php
/* Blueprints functions for program identification: names, slugs, links, etc */
$errorUrl = 'http://www.blueprintsprograms.com/error.php';
if (!defined('LIB_PATH') || !defined('CONTROLLER_START_TIME')) {header("Status: 200");header("Location: " .$errorUrl);exit();}

/***********************
function createSlugFromName($name,$separator='-')
function getAllModelPrograms($db)
function getPageProgramMetadata($page_meta,$program_meta,$program_name)
function getProgramBasicInfo($db,$hash)
function getProgramHash($pid)
function getProgramMetadata($db,$p_id)
function getProgramID($db,$hash)
function getProgramNames($db,$page,$list = false)
function getRatingLabel($attribute_id)
function getVideoURL($url,$hash)
***********************/

function createSlugFromName($name,$separator='-')
{
	$slug = '';
	if (!empty($name) && (is_string($name) || is_numeric($name))) {
		$slug = strtolower(trim(html2txt($name)));
		$slug = preg_replace("/&#?[a-z0-9]{2,8};/", ' ',$slug);
		$slug = trim(preg_replace('/[^\da-z]/', ' ',$slug));
		$slug = preg_replace('/[\s]{1,}/',$separator,$slug);
	}
	return $slug;
}

//Note model program constant serves as a flag - it gets model and model plus. 2015-06-02
function getAllModelPrograms($db)
{
	$programs = array(); //for results
	if (!empty($_SESSION['p_info' . S_TOKEN])) {  //check for usable session
		foreach ($_SESSION['p_info' . S_TOKEN] as $p_id=>$p) {
			if ($p['rating'] == 'Model' || $p['rating'] == 'Model Plus') {
				$programs[$p_id]['program_slug'] = $p['program_slug'];
				$programs[$p_id]['program_name'] = $p['program_name'];
			}
		}
	}
	else {
		$p_names = getProgramNames($db,ALL_PROGRAMS,MODEL_PROGRAM);
		foreach ($p_names as $p) {
			$programs[$p['program_id']]['program_slug'] = $p['program_slug'];
			$programs[$p['program_id']]['program_name'] = $p['program_name'];
		}
	}
	return $programs;
}

//Get final meta tags for a program by combining default and custom tags for title, description, keywords
//Return $page_meta otherwise intact! It contains other config items, eg., body id, page heading, etc
function getPageProgramMetadata($page_meta,$program_meta,$program_name)
{
	//Error check - page_meta strings must exist
	$page_meta['title'] = (isset($page_meta['title']) && is_string($page_meta['title'])) ? $page_meta['title']: 'Page title';
	$page_meta['description'] = (isset($page_meta['description']) && is_string($page_meta['description'])) ? $page_meta['description']: 'Page description';
	$page_meta['keywords'] = (isset($page_meta['keywords']) && is_string($page_meta['keywords'])) ? $page_meta['keywords']: 'Page keywords';
	//replace html in custom tags, in case of editing mistakes
	$search = array('&amp;','&nbsp;','&mdash;','&ndash;','&reg;','&copy;',);
	$replace = array('and',' ',' ',' ',);
	$program_name = (is_string($program_name) && !empty($program_name)) ? str_replace($search,$replace,html2txt($program_name)) : '';
	$titlePhrase = $program_name;
	if (isset($program_meta['title']) && is_string($program_meta['title'])) {
		$titlePhrase .= ' ' . trim(str_replace($search,$replace,html2txt($program_meta['title'])));
	}
	$page_meta['title'] = $titlePhrase . ' | ' . $page_meta['title'];
	if (isset($program_meta['description']) && is_string($program_meta['description'])) {
		$phrase = str_replace($search,$replace,html2txt($program_meta['description']));
		$page_meta['description'] = $phrase;
	}
	if (isset($program_meta['keywords']) && is_string($program_meta['keywords'])) {
		$phrase = $program_name . ', ' . str_replace($search,$replace,html2txt($program_meta['keywords']));
		$page_meta['keywords'] = $phrase;
	}
	else {
		$page_meta['keywords'] = $program_name . ', ' . $page_meta['keywords'];
	}
	return $page_meta;
}

function getProgramBasicInfo($db,$slug)
{
	$info = array();
	if ($p_id = getProgramId($db,$slug) ) {
		$params = array('ssii',BLUEPRINTS_RATING,VIDEO_URL,$p_id,1);
		$query = "SELECT program_id, program_name, program_slug, (SELECT attribute_id FROM program_attribute a WHERE p.program_id = a.program_id AND a.element_id = ?) AS attribute_id,
		  (SELECT text_content FROM program_text t WHERE p.program_id = t.program_id AND t.element_id = ?) AS video_url 
		  FROM program p WHERE p.program_id = ? AND p.active = ?";
		$row = mysqliQueryExec($db,$query,$params);
		if (count($row)==1) {
			$info = array($row[0]['program_id'],'program_name'=>$row[0]['program_name'],'program_slug'=>$row[0]['program_slug'],'rating'=>getRatingLabel($row[0]['attribute_id']),'video_url'=>htmlentities($row[0]['video_url'],ENT_QUOTES,'UTF-8'));
		}
	}
	return $info;
}

//****** DEPRECATED
function getProgramHash($pid)
{
	$hash = false;
	if (!empty($pid)) {
		$hash = sha1($pid);
	}
	return $hash;
}

// get program ID from slug
function getProgramID($db,$slug)
{
	$p_ok = false;
	if (!empty($slug)) {
		$params = array('si',$slug,1);
		$query = "SELECT program_id FROM program WHERE program_slug = ? AND active = ?";
		$programs = mysqliQueryExec($db,$query,$params);
		if (1==count($programs) && !empty($programs[0]['program_id'])) {
			$p_ok = $programs[0]['program_id'];
		}
	}
	return $p_ok;
}

//****** DEPRECATED
function getProgramIdFromHash($db,$hash)
{
	$p_ok = false;
	if (isset($hash) && $hash!='') {
		$params = array('i',1);
		$query = "SELECT program_id FROM program WHERE active = ?";
		$programs = mysqliQueryExec($db,$query,$params);
		foreach ($programs as $p) {
			if (sha1($p['program_id']) == $hash) {
				$p_ok = $p['program_id'];
				break;
			}
		}
	}
	return $p_ok;
}

//Get meta tag content for a program from db
function getProgramMetadata($db,$p_id)
{
	$tags = array();
	if (true===checkIntegerRange($p_id,0,1000000)) {
		$params = array('i',$p_id);
		$query = "SELECT p.program_id, p.meta_content,n.meta_name_label FROM program_meta p JOIN meta_name n USING (meta_name_id) WHERE p.program_id = ?";
		$rows = mysqliQueryExec($db,$query,$params);
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				if (isset($row['meta_name_label']) && isset($row['meta_content'])) {
					$tags[$row['meta_name_label']] = $row['meta_content'];
				}
			}
		}
	}
	return $tags;
}

//Set $list to true to get all programs, or model programs attribute to get Model only. Defaults to model/promising
//Can also set $list to be array, empty array = no search results
//"Model" includes Model Plus, as of 2015-06-02
function getProgramNames($db,$page,$list = false)
{
	//$order_by = ($page==SEARCH_RESULTS) ? 'attribute_id ASC, p.program_name ASC' : 'p.program_name ASC';
	$order_by = 'p.program_name ASC';
	$programs = array();
	$ok_programs_mp = 0;
	if ($list===true) {
		$params = array('sssi',BLUEPRINTS_RATING,ENDORSEMENT_STATUS,IS_INTERNATIONAL,1);
		//$query = "SELECT program_id, program_name, program_slug FROM program WHERE active = ? ORDER BY program_name";
		$query = "SELECT program_id, program_name, program_slug, (SELECT attribute_id FROM program_attribute a WHERE p.program_id = a.program_id AND a.element_id = ?) AS attribute_id, 
(SELECT 1 FROM program_attribute a WHERE p.program_id = a.program_id AND a.element_id = ? AND a.attribute_id = ?) AS is_international FROM program p WHERE p.active = ? ORDER BY ".$order_by;
	}
	elseif ($list===MODEL_PROGRAM) {
		$params = array('sssssi',ENDORSEMENT_STATUS,IS_INTERNATIONAL,BLUEPRINTS_RATING,MODEL_PROGRAM,MODEL_PLUS_PROGRAM,1);
		$query = "SELECT p.program_id, p.program_name, program_slug, a.attribute_id, 
(SELECT 1 FROM program_attribute a WHERE p.program_id = a.program_id AND a.element_id = ? AND a.attribute_id = ?) AS is_international 
FROM program p JOIN program_attribute a ON p.program_id = a.program_id WHERE a.element_id = ? AND (a.attribute_id = ? OR a.attribute_id = ?) AND p.active = ? ORDER BY ".$order_by;
	}
	elseif (is_array($list) && !empty($list)) {
		$p_clause = ' AND '.arrayToWhereClause($list,'p.program_id','i','OR','');
		$params = array('sssi',BLUEPRINTS_RATING,ENDORSEMENT_STATUS,IS_INTERNATIONAL,1);
		$query = "SELECT program_id, program_name, program_slug, (SELECT attribute_id FROM program_attribute a WHERE p.program_id = a.program_id AND a.element_id = ?) AS attribute_id, 
(SELECT 1 FROM program_attribute a WHERE p.program_id = a.program_id AND a.element_id = ? AND a.attribute_id = ?) AS is_international 
FROM program p WHERE p.active = ?".$p_clause." ORDER BY ".$order_by;
	}
	elseif (is_array($list) && empty($list)) {
		$query = '';
	}
	else { // $list is false or empty string or ...? 
		if ($page===ALL_PROGRAMS && !empty($_SESSION['programs_mp'])) {
			$programs = $_SESSION['programs_mp'];
		}
		else {
			if (SEARCH_RESULTS == $page) {
				$params = array('sssssssssi',MODEL_PLUS_PROGRAM,MODEL_PROGRAM,PROMISING_PROGRAM,ENDORSEMENT_STATUS,IS_INTERNATIONAL,BLUEPRINTS_RATING,MODEL_PLUS_PROGRAM,MODEL_PROGRAM,PROMISING_PROGRAM,1);
				$query = "SELECT CASE a.attribute_id WHEN ? THEN 1 WHEN ? THEN 2 WHEN ? THEN 3 ELSE 4 END AS sortcol, p.program_id, p.program_name AS name, p.program_slug, a.attribute_id, 
(SELECT 1 FROM program_attribute a WHERE p.program_id = a.program_id AND a.element_id = ? AND a.attribute_id = ?) AS is_international FROM program p 
JOIN program_attribute a ON p.program_id = a.program_id WHERE a.element_id = ? AND (a.attribute_id = ? OR a.attribute_id = ? OR a.attribute_id = ?) AND p.active = ? ORDER BY sortcol, name ASC";
			}
			else {
				$params = array('ssssssi',ENDORSEMENT_STATUS,IS_INTERNATIONAL,BLUEPRINTS_RATING,MODEL_PROGRAM,MODEL_PLUS_PROGRAM,PROMISING_PROGRAM,1);
				$query = "SELECT p.program_id, p.program_name, p.program_slug, a.attribute_id, 
(SELECT 1 FROM program_attribute a WHERE p.program_id = a.program_id AND a.element_id = ? AND a.attribute_id = ?) AS is_international 
FROM program p JOIN program_attribute a ON p.program_id = a.program_id WHERE a.element_id = ? AND (a.attribute_id = ? OR a.attribute_id = ? OR a.attribute_id = ?) AND p.active = ? ORDER BY ".$order_by;
			}
			$ok_programs_mp = 1;
		}
	}
	if (isset($query) && $query!=='' && !empty($params) && is_array($params)) {
		$programs = mysqliQueryExec($db,$query,$params);
		if ($page===ALL_PROGRAMS && $ok_programs_mp===1) {
			$_SESSION['programs_mp'] = $programs;
		}
	}
	return $programs;
}

function getRatingLabel($attribute_id)
{
	switch ($attribute_id) {
		case (PROMISING_PROGRAM):
			$v = 'Promising';
			break;
		case (MODEL_PROGRAM):
			$v = 'Model';
			break;
		case (MODEL_PLUS_PROGRAM):
			$v = 'Model Plus';
			break;
		default:
			$v = '';
			break;
	}
	return $v;
}

function getVideoURL($url,$slug)
{
	$video_url = '';
	if (!empty($url)) {
		$video_url = array();
		if (preg_match("/^((https|http)\:\/\/){1}/",$url)) {
			$video_url['url'] = $url;
			$video_url['local'] = 0;
		}
		elseif (preg_match("/^[-_a-z0-9]+\.[a-z0-9]{2,5}$/i",$url)) {
			$url =  BASE_URL . '/video/' . $slug;
			$video_url['url'] = $url;
			$video_url['local'] = 1;
		}
	}
	return $video_url;
}
