<?php
/* Blueprint functions for program selector and conducting selector/step searches */
$errorUrl = 'http://www.blueprintsprograms.com/error.php';
if (!defined('LIB_PATH') || !defined('CONTROLLER_START_TIME')) {header("Status: 200");header("Location: " .$errorUrl);exit();}

/***********************
function getProgramSelector($db,$page=PROGRAM_SELECTOR)
function getSelectorSearchPrograms($db)
function getSelectorSearchResults($db,$vars)
***********************/

//main controller for program selector pages (PROGRAM_SELECTOR or PROGRAM_SEARCH), and for the step search pages
function getProgramSelector($db,$page=PROGRAM_SELECTOR)
{
	// $page is always set to PROGRAM_SELECTOR - for now, program selector and step search pages are all handled the same
	$page = PROGRAM_SELECTOR;
	$s_info = array();
	$errors = array();
	if (!empty($_SESSION['s_info' . S_TOKEN]['selector_search_ids']) && 
  !empty($_SESSION['s_info' . S_TOKEN]['selector_domains']) && 
  !empty($_SESSION['s_info' . S_TOKEN]['search_outcome_domains']) && 
  !empty($_SESSION['s_info' . S_TOKEN]['search_outcomes']) && 
  !empty($_SESSION['s_info' . S_TOKEN]['search_target_pop']) && 
  !empty($_SESSION['s_info' . S_TOKEN]['search_specifics']) && 
  !empty($_SESSION['s_info' . S_TOKEN]['search_rp_factors'])) {
		$s_info = $_SESSION['s_info' . S_TOKEN];  //check postvars later
		//echo 'same array';
	}
	else {
		//echo 'new array';
		//get elements for program selector and step pages. Note e_needed can be empty (but not here)
		$e_needed = getPageElements($db,$page);
		//setting contols what messages the elements, attributes, domains and sections get.
		//Setting can be PUBLIC_PAGE or EDIT_PAGE
		$e = getElementsStructure($db,$setting=PUBLIC_PAGE,$e_needed);  //Note assignment
		//get all attributes for each element. If e_needed is empty, return all
		$a = getAttributesStructure($db,$setting);
		$e_attributes = array();
		foreach ($e as $e_id=>$element) {
			if (empty($element['control_id'])) {
				$errors['missing_control'][$element['element_id']] = 1;
				continue;
			}
			if (isset($a[$element['control_id']])) {
				$e_attributes[$element['control_id']] = $a[$element['control_id']];
			}
		}
		if (!empty($errors)) {
			echo 'Page error.';
			return false;
		}
		$sections = getSections($db,$setting);
		$domains = getDomains($db,$setting);
		//accumulate variable ids for program search arrays
		$selector_search_ids = array();
		$i_ids = 0;

		$selector_domains = array();
		$search_outcome_domains = array();  //step 1
		$search_outcomes = array();  //step 2
		$search_target_pop = array();  //step 3
		$search_specifics = array();  //step 4
		$search_rp_factors = array();  //step 5
		//step 1 search_outcome_domains
		//check session and postvars first
		$domains_needed = getDomainsBySection($db,OUTCOMES);
		foreach ($domains_needed as $d_needed) {
			$search_outcome_domains[$d_needed]['domain_id'] = $domains[$d_needed]['domain_id'];
			$search_outcome_domains[$d_needed]['label'] = getLabelFromMessage($domains[$d_needed]['domain_label'],$domains[$d_needed]['message_type'],$domains[$d_needed]['message_content']);
		}
		//step 2 search_outcomes
		//check session and postvars first
		//if any outcome domains are checked, only show them, otherwise show all
		$search_outcomes = getElementsDomains($db,$domains_needed);
		//error check
		if (count($search_outcomes)!=count($search_outcome_domains)) {
				$errors['outcome_domains_missing'][] = count($search_outcomes).'|'.count($search_outcome_domains);
		}
		foreach ($search_outcomes as $d_key=>$search_outcome_list) {
			foreach ($search_outcome_list as $e_key=>$e_search_outcome) {
				$search_outcomes[$d_key][$e_key] = $e[$e_key];
				//check post/session - add $search_outcomes[$d_key][$e_key]['checked'] = isset(postvars[$e_key][outcome achieved])
				$i_key = 'a'.$e[$e_key]['element_id'].'a'.OUTCOME_ACHIEVED;
				$selector_search_ids[$i_ids][$i_key] = 1;
			}
			$i_ids++;
		}
		//step 3 search_target_pop
		$selector_domains[TARGET_POPULATION]['domain_id'] = $domains[TARGET_POPULATION]['domain_id'];
		$selector_domains[TARGET_POPULATION]['label'] = getLabelFromMessage($domains[TARGET_POPULATION]['domain_label'],$domains[TARGET_POPULATION]['message_type'],$domains[TARGET_POPULATION]['message_content']);
		$search_target_pop = getElementsDomains($db,array(TARGET_POPULATION));
		foreach ($search_target_pop as $d_key=>$target_pop_list) {
			foreach ($target_pop_list as $e_key=>$e_target_pop) {
				$search_target_pop[$d_key][$e_key] = $e[$e_key];
				$search_target_pop[$d_key][$e_key]['attributes'] = $e_attributes[$e[$e_key]['control_id']];
				//check post - add $search_target_pop[$d_key][$e_key][$a_key]['checked'] = isset(postvars[$e_key][item]);
				foreach($e_attributes[$e[$e_key]['control_id']] as $current_e_attributes) {
					$i_key = 'a'.$e[$e_key]['element_id'].'a'.$current_e_attributes['attribute_id'];
					$selector_search_ids[$i_ids][$i_key] = 1;
				}
				$i_ids++;
			}
		}
		//step 4 search_specifics
		$selector_domains[PROGRAM_SPECIFICS]['domain_id'] = $domains[PROGRAM_SPECIFICS]['domain_id'];
		$selector_domains[PROGRAM_SPECIFICS]['label'] = getLabelFromMessage($domains[PROGRAM_SPECIFICS]['domain_label'],$domains[PROGRAM_SPECIFICS]['message_type'],$domains[PROGRAM_SPECIFICS]['message_content']);
		$search_specifics = getElementsDomains($db,array(PROGRAM_SPECIFICS));
		foreach ($search_specifics as $d_key=>$search_specifics_list) {
			foreach ($search_specifics_list as $e_key=>$e_search_specifics) {
				$search_specifics[$d_key][$e_key] = $e[$e_key];
				$search_specifics[$d_key][$e_key]['attributes'] = $e_attributes[$e[$e_key]['control_id']];
				//check post - add $search_specifics[$d_key][$e_key][$a_key]['checked'] = isset(postvars[$e_key][item]);
				foreach($e_attributes[$e[$e_key]['control_id']] as $current_e_attributes) {
					$i_key = 'a'.$e[$e_key]['element_id'].'a'.$current_e_attributes['attribute_id'];
					$selector_search_ids[$i_ids][$i_key] = 1;
				}
				$i_ids++;
			}
		}
		//step 5 search_rp_factors
		$rp_search_ids = array();
		$domains_needed = getDomainsBySection($db,RISK_PROTECTIVE_FACTORS);
		//Hack: get the domain groups from config, mimicking the old array structure
		$rp_factor_domain_groups = getRiskProtectiveFactorGroups();
		foreach ($rp_factor_domain_groups as $k=>$domain_group) {
			$selector_domains[$k]['domain_id'] = $k;
			$selector_domains[$k]['label'] = $domain_group['label'];
		}
		/* foreach ($domains_needed as $d_needed) {
			$selector_domains[$d_needed]['domain_id'] = $domains[$d_needed]['domain_id'];
			$selector_domains[$d_needed]['label'] = getLabelFromMessage($domains[$d_needed]['domain_label'],$domains[$d_needed]['message_type'],$domains[$d_needed]['message_content']);
		} */
		$search_rp_factors = getElementsDomains($db,$domains_needed);
		$temp_rp_factors = array();
		foreach ($search_rp_factors as $d_key=>$search_rp_factors_list) {
			$rp_key = getRiskProtectiveFactorGroupKey($d_key);
			$temp_rp_factors[$rp_key]['label'] = getLabelFromMessage($domains[$d_key]['domain_label'],$domains[$d_key]['message_type'],$domains[$d_key]['message_content']);
			foreach ($search_rp_factors_list as $e_key=>$e_search_rp_factors) {
				$temp_rp_factors[$rp_key][$d_key][] = $e[$e_key];
				foreach($e_attributes[$e[$e_key]['control_id']] as $current_e_attributes) {
					if (PROGRAM_FOCUS == $current_e_attributes['attribute_id']) {
						$i_key = 'a' . $e[$e_key]['element_id'] . 'a' . PROGRAM_FOCUS;
						$rp_search_ids[$rp_key][$i_key] = 1;
					}
				}
			}
		}
		$search_rp_factors = $temp_rp_factors;
		$selector_search_ids = array_merge($selector_search_ids,$rp_search_ids);
		/*
		echo '<br clear="left"><pre>';
		echo '$search_rp_factors';print_r($search_rp_factors);
		echo '$selector_domains';print_r($selector_domains);
		echo '$rp_search_ids';print_r($rp_search_ids);
		echo '</pre>';
		exit();
		*/
		//set session array of programs that match each selector_search_id, for search results, counter and, graying out Outcomes and RP factors that have no programs
		if (empty($_SESSION['selector_search_programs' . S_TOKEN])) {
			$_SESSION['selector_search_programs' . S_TOKEN] = getSelectorSearchPrograms($db);
		}
		$s_info['errors'] = $errors;
		$s_info['selector_search_ids'] = $selector_search_ids;
		$s_info['selector_domains'] = $selector_domains;
		$s_info['search_outcome_domains'] = $search_outcome_domains;  //step 1
		$s_info['search_outcomes'] = $search_outcomes;  //step 2
		$s_info['search_target_pop'] = $search_target_pop;  //step 3
		$s_info['search_specifics'] = $search_specifics;  //step 4
		$s_info['search_rp_factors'] = $search_rp_factors;  //step 5
	}
	//$s_info['e_needed'] = $e_needed;
	//$s_info['AttributesStructure'] = $a;
	//$s_info['e_attributes'] = $e_attributes;
	
	//put $s_info in session to check for next time
	$_SESSION['s_info' . S_TOKEN] = $s_info;
	//echo '<pre>';
	//echo 'SESSIONs_info ';print_r($_SESSION['s_info' . S_TOKEN]);
	//echo '</pre>';
	//exit();
	return $s_info;
}

//gets array of programs that match each selector_search_id, for search results, counter and, graying out elements that have no programs
//convert r/p factors to program focus variable
function getSelectorSearchPrograms($db)
{
	$selector_search_programs = array();
	if (!empty($_SESSION['selector_search_programs' . S_TOKEN])) {
		$selector_search_programs = $_SESSION['selector_search_programs' . S_TOKEN];
	}
	else {
		$params = array('ssssssiiii',CONTENT_PRIMARY_FOCUS,CONTENT_SECONDARY_FOCUS,BLUEPRINTS_RATING,MODEL_PROGRAM,MODEL_PLUS_PROGRAM,PROMISING_PROGRAM,1,1,1,PROGRAM_SELECTOR);
		$query = "SELECT pa.element_id, pa.attribute_id, pa.program_id FROM program_attribute pa
JOIN program_attribute pa2 USING (program_id) 
JOIN page_element pe ON pe.element_id = pa.element_id 
JOIN program p USING (program_id)
JOIN element e ON e.element_id = pa.element_id 
JOIN attribute a ON a.attribute_id = pa.attribute_id 
WHERE pa.attribute_id != ? AND pa.attribute_id != ? AND pa2.element_id = ? AND (pa2.attribute_id = ? OR pa2.attribute_id = ? OR pa2.attribute_id = ?) 
AND p.active = ? AND e.active = ? AND a.active = ? AND pe.page_id = ?";
		$rows = mysqliQueryExec($db,$query,$params);
		foreach ($rows as $row) {
			if (ACHIEVED===$row['attribute_id']) {
				$row['attribute_id'] = PROGRAM_FOCUS;
			}
			$var_key = 'a' . $row['element_id'] . 'a' . $row['attribute_id'];
			$selector_search_programs[$var_key][] = $row['program_id'];
		}
		//put in SESSION for re-use
		$_SESSION['selector_search_programs' . S_TOKEN] = $selector_search_programs;
	}
	return $selector_search_programs;
}

/*******
calculates array of program ids resulting from a Program Selector search
*******/
function getSelectorSearchResults($db,$vars)
{
	$programs = array();
	//array of programs for each selector search item
	$selector_search_programs = (empty($_SESSION['selector_search_programs' . S_TOKEN])) ? getSelectorSearchPrograms($db) : $_SESSION['selector_search_programs' . S_TOKEN];
	//array of selector search items arranged by logical AND/OR per program selector
	if (empty($_SESSION['s_info' . S_TOKEN]['selector_search_ids'])) {
		$s_info = getProgramSelector($db);
		$selector_search_ids = $s_info['selector_search_ids'];
	}
	else {
		$selector_search_ids = $_SESSION['s_info' . S_TOKEN]['selector_search_ids'];
	}
	$selected_programs = array_intersect_key($selector_search_programs,array_flip($vars));
	$k = 0;
	foreach ($selector_search_ids as $id_group) {
		if (count($id_group_programs = array_intersect_key($selected_programs,$id_group)) > 0 ) {  //note assignment
			//$programs[$k] = array_unique(call_user_func_array('array_merge',$id_group_programs));
			$temp = array();
			foreach ($id_group_programs as $this_group) {
				$temp = array_merge($temp,$this_group);
			}
			$programs[$k] = array_unique($temp);
			$k++;
		}
	}
	$temp = array_shift($programs);
	foreach ($programs as $program_group) {
		$temp = array_intersect($temp,$program_group);
	}
	$programs = $temp;
	/*
	echo '<pre>';
	echo '$vars ';print_r($vars);
	print_r($programs);
	echo '********************************<br />';
	print_r($selected_programs);
	echo '$selector_search_ids ';print_r($selector_search_ids);
	echo '$selector_search_programs ';print_r($selector_search_programs);
	echo '</pre>';
	*/
	return $programs;
}
