<?php
/* Blueprint functions for calculating search results and values needed for displaying them */
$errorUrl = 'http://www.blueprintsprograms.com/error.php';
if (!defined('LIB_PATH') || !defined('CONTROLLER_START_TIME')) {header("Status: 200");header("Location: " .$errorUrl);exit();}

/***********************
function getHtmlSearchTerms($search_terms,$page=SEARCH_RESULTS)
function getJsExpandedLists($lists,$tag,$suffix)
function getListIdFromLabel($label)
function getSearchResults($db,$search_programs,$is_search,$e_target_pop,$page=ALL_PROGRAMS)
function getSelectorSearch($db,$vars,$is_step_search,$page=PROGRAM_SELECTOR)
function mergeSelectorSearchTerms($search_terms)
***********************/

function getHtmlSearchTerms($search_terms,$page=SEARCH_RESULTS)
{
	$html = '';
	if ($page==SEARCH_RESULTS) {
		if (isset($search_terms['keyword'])) {
			$search_terms['selector'] = $search_terms['keyword'];
			unset($search_terms['keyword']);
		}
		else {
			$search_terms = mergeSelectorSearchTerms($search_terms);
		}
		$selector_terms = '';
		$risk_terms = '';
		$protective_terms = '';
		$html = '<p class="rightPadding25"><span class="blue uppercase bold">You searched on the following criteria:</span>';
		if (isset($search_terms['selector'])) {
			$selector_terms = implode(', ',$search_terms['selector']);
			$html .= '&nbsp;<span class="twelvePx">'.htmlentities($selector_terms,ENT_QUOTES,'UTF-8').'</span>';
		}
		$html .= '</p>'."\n";
		if (isset($search_terms['risk']) || isset($search_terms['protective'])) {
			$html .= '<p class="rightPadding25"><span class="blue bold">Risk and Protective Factors:</span>&nbsp;<span class="twelvePx">';
			if (isset($search_terms['risk'])) {
				$risk_terms = implode(', ',$search_terms['risk']);
				$html .= '<strong>Risk Factors</strong>: '.htmlentities($risk_terms,ENT_QUOTES,'UTF-8');
			}
			if (isset($search_terms['protective'])) { 
				$protective_terms = implode(', ',$search_terms['protective']);
				$conjunction = ($risk_terms=='') ? '' : '; and ' ;
				$html .= $conjunction.'<strong>Protective Factors</strong>: '.htmlentities($protective_terms,ENT_QUOTES,'UTF-8');
			}
			$html .= '</span></p>'."\n";
		}
	}
	else {  //Step Search pages
		$open = '<span class="blue uppercase bold">';
		$close = '</span> ';
		$titles = array(STEP_1=>'Step 1 - Program Outcomes&nbsp;&gt;&gt; ',
			STEP_2=>'Step 2 - Target Population&nbsp;&gt;&gt; ',
			STEP_3=>'Step 3 - Program Specifics&nbsp;&gt;&gt; ',
			STEP_4=>'Step 4 - Risk and Protective Factors&nbsp;&gt;&gt; ',);
		
		//$outcome_domain_terms = (isset($_SESSION['step']['outcome_domain_terms'])) ? implode(', ',$_SESSION['step']['outcome_domain_terms']) : '';
		$outcomes_terms = (isset($search_terms['outcomes'])) ? implode(', ',$search_terms['outcomes']) : '';
		$target_pop_terms = (isset($search_terms['target_pop'])) ? implode(', ',$search_terms['target_pop']) : '';
		$specifics_terms = (isset($search_terms['specifics'])) ? implode(', ',$search_terms['specifics']) : '';
		$risk_terms = (isset($search_terms['risk'])) ? implode(', ',$search_terms['risk']) : '';
		$protective_terms = (isset($search_terms['protective'])) ? implode(', ',$search_terms['protective']) : '';
		$rp_terms = '';
		if ($risk_terms != '') { $rp_terms = '<strong>Risk Factors</strong>: '.htmlentities($risk_terms,ENT_QUOTES,'UTF-8'); }
		if ($protective_terms != '') {
			$conjunction = ($risk_terms=='') ? '' : '; and ';
			$rp_terms .= $conjunction.'<strong>Protective Factors</strong>: '.htmlentities($protective_terms,ENT_QUOTES,'UTF-8');
		}
		
		$rest_are_empty = array();
		//controls new line for empty Step 4
		$rest_are_empty[STEP_2] = $target_pop_terms=='' && $specifics_terms=='' && $rp_terms=='';
		$rest_are_empty[STEP_1] = $outcomes_terms=='' && $rest_are_empty[STEP_2];
		//Step 1
		if ($outcomes_terms != '') { $html .= '<p>'.$open.$titles[STEP_1].$close.htmlentities($outcomes_terms,ENT_QUOTES,'UTF-8').'</p>'."\n"; }
		else { $html .= '<p class="uppercase">'.$titles[STEP_1]; }
		//Step 2
		if ($target_pop_terms != '') {
			$previous = ($outcomes_terms=='') ? "</p>\n" : '';
			$html .= $previous.'<p>'.$open.$titles[STEP_2].$close.htmlentities($target_pop_terms,ENT_QUOTES,'UTF-8').'</p>'."\n";
		}
		else {
			$previous = ($outcomes_terms=='') ? ' ' : "</p>\n".'<p class="uppercase">';
			$html .= $previous.$titles[STEP_2];
		}
		//Step 3
		if ($specifics_terms != '') {
			$previous = ($target_pop_terms=='') ? "</p>\n" : '';
			$html .= $previous.'<p>'.$open.$titles[STEP_3].$close.htmlentities($specifics_terms,ENT_QUOTES,'UTF-8').'</p>'."\n";
		}
		else {
			$previous = ($target_pop_terms=='') ? ' ' : "</p>\n".'<p class="uppercase">';
			$html .= $previous.$titles[STEP_3];
		}
		//Step 4
		if ($rp_terms != '') {
			$previous = ($specifics_terms=='') ? "</p>\n" : '';
			$html .= $previous.'<p>'.$open.$titles[STEP_4].$close.$rp_terms.'</p>'."\n";
		}
		else {
			if ($rest_are_empty[STEP_1]===true) { $previous = "\n<br />"; }
			elseif ($rest_are_empty[STEP_2]===true) { $previous = "\n"; }
			else { $previous = ($specifics_terms=='') ? ' ' : "</p>\n".'<p class="uppercase">'; }
			$html .= $previous.$titles[STEP_4];
		}
		$final_close = ($rp_terms=='') ? "</p>\n" : "\n";
		$html .= $final_close;
	}
	return $html;
}

function getJsExpandedLists($lists,$tag,$suffix)
{
	$js = '';
	foreach ($lists as $id) {
		$js .= "$('#".$suffix.$id."').show();\n";
		$js .= "$('#".$suffix.$id."').prev('".$tag."').toggleClass('opened');\n";
	}
	return $js;
}

function getListIdFromLabel($label)
{
	$label = strtolower(substr(str_replace(array(' ','/',',','&','_','-'),'',$label),0,16));
	return $label;
}

//main controller for search results pages, ALL_PROGRAMS, SEARCH_RESULTS
// See page table and function getPageElements if any other allowable $page values.
function getSearchResults($db,$search_programs,$is_search,$e_target_pop,$page=ALL_PROGRAMS)
{
	$errors = array();
	if (empty($search_programs)) {
		$search_programs = array();
		$errors['search_programs'][] = 1;
	}
	if ($is_search===true) { //flag needed programs as search results
		$search_flags = array();
		foreach ($search_programs as $p) {
			$search_flags[$p['program_id']] = 1;
		}
	}
	$p_info = $p_info_rating = array();   //init program-sorted array p_info, rating-sorted array p_info_rating
	$need_p_info = $need_p_info_rating = 1;   //init flags to create p_info, p_info_rating
	//Do we have the program-sorted array
	$sort_key = 'programs_mp';
	$p_info_key = 'p_info' . S_TOKEN;
	if (!empty($_SESSION[$sort_key])) { //contains all M/P from getProgramNames
		$all_programs = $_SESSION[$sort_key];
	}
	else {
		$search_results = ''; //returns all M/P (getProgramNames default)
		$all_programs = getProgramNames($db,ALL_PROGRAMS,$search_results);  //if $page==ALL_PROGRAMS, results will be sorted by name. If $page==SEARCH_RESULTS, will be sorted by rating
		$_SESSION[$sort_key] = $all_programs;
	}
	if (isset($_SESSION[$p_info_key]) && count($all_programs)===count($_SESSION[$p_info_key])) {
		$need_p_info = 0;
		foreach($all_programs as $program) {
			$p_id = $program['program_id'];
			if (!isset($_SESSION[$p_info_key][$p_id]) || empty($_SESSION[$p_info_key][$p_id]['idx']) || empty($_SESSION[$p_info_key][$p_id]['program_name'])) {
				$need_p_info = 1;
				break;
			}
		}
		if ($need_p_info===0) {
			$p_info = $_SESSION[$p_info_key];
			if ($is_search===true) { //flag needed programs as search results
				foreach ($p_info as $p_key=>$p) {
					$p_info[$p_key]['search'] = (isset($search_flags[$p_key])) ? 1 : 0;
				}
			}
		}
		if ($is_search===false && count($all_programs)!==count($search_programs)) {
			$errors['all_programs'][] = 1;
		}
	}
	//Do we have the rating-sorted array
	$sort_key = 'programs_mp_rating';
	$p_info_key = 'p_info_rating' . S_TOKEN;
	if (!empty($_SESSION[$sort_key])) { //contains all M/P from getProgramNames
		$rating_programs = $_SESSION[$sort_key];
	}
	else {
		$search_results = ''; //returns all M/P (getProgramNames default)
		$rating_programs = getProgramNames($db,SEARCH_RESULTS,$search_results);  //sorted by rating
		$_SESSION[$sort_key] = $rating_programs;
	}
	if (isset($_SESSION[$p_info_key]) && count($all_programs)===count($rating_programs) && count($all_programs)===count($_SESSION[$p_info_key])) {
		$need_p_info_rating = 0;
		foreach($rating_programs as $program) {
			$p_id = $program['program_id'];
			if (!isset($_SESSION[$p_info_key][$p_id]) || empty($_SESSION[$p_info_key][$p_id]['idx']) || empty($_SESSION[$p_info_key][$p_id]['program_name'])) {
				$need_p_info_rating = 1;
				break;
			}
		}
		if ($need_p_info_rating===0) {
			$p_info_rating = $_SESSION[$p_info_key];
			if ($is_search===true) { //flag needed programs as search results
				foreach ($p_info_rating as $p_key=>$p) {
					$p_info_rating[$p_key]['search'] = (isset($search_flags[$p_key])) ? 1 : 0;
				}
			}
		}
		if ($is_search===false && count($rating_programs)!==count($search_programs)) {
			$errors['rating_programs'][] = 1;
		}
	}
	// create the new program array if needed
	if ($need_p_info===1) {
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
			exit('Error: Unspecified.');
		}
		//Get outcomes and benefits-costs. should add tests for error condition
		$e_outcomes = getElementsBySection($db,OUTCOMES);
		$e_outcomes = (is_array($e_outcomes)) ? array_flip($e_outcomes) : $e_outcomes;
		$e_benefits_costs = getElementsByDomain($db,BENEFITS_COSTS);
		$e_benefits_costs = (is_array($e_benefits_costs)) ? array_flip($e_benefits_costs) : $e_benefits_costs;
		$idx = 1; //for javascript suffix
		$compare_rows = array();
		foreach ($all_programs as $program) {
			$p_id = $program['program_id'];
			$p_attributes = getProgramAttributes($db,$p_id,$setting,$e_needed);
			$p_text = getProgramText($db,$p_id,$e_needed);
			$p_info[$p_id]['idx'] = $idx;
			$compare_rows['idx'] = $p_id;
			if ($is_search===true) {
				$p_info[$p_id]['search'] = (isset($search_flags[$p_id])) ? 1 : 0;
			}
			$p_info[$p_id]['program_name'] = $program['program_name'];
			$p_info[$p_id]['program_slug'] = $program['program_slug'];
			$p_info[$p_id]['rating'] = (empty($program['attribute_id'])) ? $errors['unendorsed'][$p_id] = 1 : getRatingLabel($program['attribute_id']);
			$p_info[$p_id]['is_international'] = (isset($program['is_international']) && 1===$program['is_international']) ? 1 : 0;
			$p_info[$p_id]['summary'] = $p_text[PROGRAM_GOALS]['text_content'];
			$outcomes_achieved = array();
			$target_population = array();
			$benefits_costs = array();
			foreach ($p_attributes as $e_id=>$p_attribute) {
				foreach($p_attribute as $attribute) {
					if (isset($e_outcomes[$e_id])) {
						if ($attribute['attribute_id']==OUTCOME_ACHIEVED) {
							$outcomes_achieved[] = $element_label = getLabelFromMessage($e[$e_id]['element_label'],$e[$e_id]['message_type'],$e[$e_id]['message_content']);
						}
					}
					elseif (isset($e_target_pop[$e_id])) {
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
					}
					elseif (isset($e_benefits_costs[$e_id]) && $e_id==COST_DATA_SOURCE) {
						$benefits_costs['cost_data_source'] = getLabelFromMessage($attribute['attribute_label'],$attribute['message_type'],$attribute['message_content']);
						$benefits_costs['cost_data_info'] = getMessageFromIdTargetType($db,$attribute['attribute_id'],TARGET_ATTRIBUTE,MESSAGE_PUBLIC_TEXT);
					}
				}
			}
			foreach ($p_text as $e_id=>$text) {
				if (isset($e_benefits_costs[$e_id])) {
					$benefits_costs['cost_items'][$e_id]['element_label'] = $e[$e_id]['element_label'];
					$benefits_costs['cost_items'][$e_id]['message'] = $e[$e_id]['message_content'];
					$benefits_costs['cost_items'][$e_id]['amount'] = $text['text_content'];
				}
			}
			//re-sort target pop to match special group order
			$sorted = array();
			foreach ($e_target_pop as $k=>$v) {
				$sorted[$k] = $target_population[$k];
			}
			$target_population = $sorted;
			//put finished subarrays in p_info
			$p_info[$p_id]['outcomes']=$outcomes_achieved;
			$p_info[$p_id]['target_population']=$target_population;
			$p_info[$p_id]['benefits_costs']=$benefits_costs;
			$idx++;
		}  // end each program
	}
	//Create the rating-sorted array p_info_rating if needed. Search is updated already per p_info
	if ($need_p_info_rating===1) {
		foreach($rating_programs as $p) {
			$p_info_rating[$p['program_id']] = $p_info[$p['program_id']];
		}
	}
	//Put finished arrays in session
	$_SESSION['p_info' . S_TOKEN] = $p_info;
	$_SESSION['p_info_rating' . S_TOKEN] = $p_info_rating;
	if ($page===SEARCH_RESULTS) {
		return $p_info_rating;
	}
	else {
		return $p_info;
	}
	//echo '<pre>';
	//echo 'errors ';print_r($errors);
	//echo 'search_programs ';print_r($search_programs);
	//echo 'SESSIONp_info ';print_r($_SESSION['p_info' . S_TOKEN]);
	//echo 'e_needed ';print_r($e_needed);
	//echo 'elements ';print_r($e);
	//print_r($sections);
	//print_r($domains);
	//echo '</pre>';
}

/*******
//Get search terms and results and save the searches
*******/
function getSelectorSearch($db,$vars,$is_step_search,$page=PROGRAM_SELECTOR)
{
	$ok = true;
	$programs = array(); //default - no results
	$search_vars = array(); // Accumulate the ea pairs and then pass it to be the saved search
	$search_terms = array();
	$checked_ids = array();
	$expanded_lists = array();
	$s_info = (!empty($_SESSION['s_info' . S_TOKEN])) ? $_SESSION['s_info' . S_TOKEN] : getProgramSelector($db,PROGRAM_SELECTOR);
	extract($s_info,EXTR_OVERWRITE);
	$rp_factor_domain_groups = getRiskProtectiveFactorGroups(); //risk, protective
	// init array to hold "constructed" vars to search on, such as the RP factors achieved
	// the $search_vars array is not altered; the saved searches will only include the original var, eg RP factors program focus
	$vars_extra = array();
	$k = 0;
	foreach ($vars as $var) {
		//var from post is checked for valid variable name before it gets passed to here
		//simple case, no save: $ea_vars = explode('a',ltrim($var,'a'));
		//$e = $ea_vars[0];
		//$a = $ea_vars[1];
		$search_vars[$k] = explode('a',ltrim($var,'a'));
		$e = $search_vars[$k][0];
		$a = $search_vars[$k][1];
		if (PROGRAM_FOCUS === $a) {
			$vars_extra[] = 'a' . $e . 'a' . ACHIEVED;
		}
		//test for empty elements or attributes. note e and a are tested to be ints before putting them into $vars but double-check here
		//This function does not check for inactive e or a (because getProgramSelector does).
		if (empty($e) || empty($a) || (is_string($e) && !ctype_digit($e)) || (!is_int((int)$e)) || (is_string($a) && !ctype_digit($a)) || (!is_int((int)$a))) {
			$ok = false;
			break;
		}
		else {
			foreach ($search_outcome_domains as $d_key=>$domain) {
				if (!empty($search_outcomes[$d_key][$e])) {
					$search_terms['outcomes'][] = $search_outcomes[$d_key][$e]['element_label'];  //no message
					$expanded_lists[$d_key] = $domain['label'];
				}
			}
			foreach ($selector_domains as $d_key=>$domain) {
				if (!empty($search_target_pop[$d_key][$e]['attributes'][$a])) {
					$a_label = $search_target_pop[$d_key][$e]['attributes'][$a]['attribute_label'];
					$message_type = $search_target_pop[$d_key][$e]['attributes'][$a]['message_type'];
					$message_content = $search_target_pop[$d_key][$e]['attributes'][$a]['message_content'];
					$label = getLabelFromMessage($a_label,$message_type,$message_content);
					$search_terms['target_pop'][] = $label;
					$expanded_lists[$e] = $search_target_pop[$d_key][$e]['element_label'];
				}
				elseif (!empty($search_specifics[$d_key][$e]['attributes'][$a])) {
					$a_label = $search_specifics[$d_key][$e]['attributes'][$a]['attribute_label'];
					$message_type = $search_specifics[$d_key][$e]['attributes'][$a]['message_type'];
					$message_content = $search_specifics[$d_key][$e]['attributes'][$a]['message_content'];
					$label = getLabelFromMessage($a_label,$message_type,$message_content);
					$search_terms['specifics'][] = $label;
					$expanded_lists[$e] = $search_specifics[$d_key][$e]['element_label'];
				}
				else {  //ea should be in search_rp_factors
					foreach ($search_rp_factors as $g_key=>$rp_factor_groups) {
						$group_label = $rp_factor_groups['label'];
						unset($rp_factor_groups['label']);
						foreach ($rp_factor_groups as $d_key=>$rp_factor_domain) {
							$term_key = (isset($rp_factor_domain_groups['risk'][$d_key])) ? 'risk' : 'protective';
							foreach ($rp_factor_domain as $rp_factors) {
								if ($e == $rp_factors['element_id']) {
									$label = getLabelFromMessage($rp_factors['element_label'],$rp_factors['message_type'],$rp_factors['message_content']);
									$search_terms[$term_key][] = $label;
									$expanded_lists[$g_key] = $group_label;
									break 4;  //break out of elements, domains, search_rp_factors, go to next var
								}
							}
						}
					}
				}
			}
			$checked_ids[$var] = 1;
			$k++;
		}
	}
	//save search vars in database if final search
	if (!empty($search_vars) && is_array($search_vars)) {
		if ($page==PROGRAM_SELECTOR && (empty($_SESSION['previous_search_items' . S_TOKEN]) || !is_array($_SESSION['previous_search_items' . S_TOKEN]) || (is_array($_SESSION['previous_search_items' . S_TOKEN]) && checkIdenticalArraysOrStrings($_SESSION['previous_search_items' . S_TOKEN],$search_vars)===false))) {
			//save new $search_vars in database
			$saved_search_id = saveSelectorSearch($db,$search_vars,$is_step_search);
		}
		$_SESSION['previous_search_items' . S_TOKEN] = $search_vars;
	}
	else {
		if (isset($_SESSION['previous_search_items' . S_TOKEN])) {
			$_SESSION['previous_search_items' . S_TOKEN] = array(); //error
		}
	}
	$temp_lists = array();
	if (!empty($expanded_lists)) {
		foreach($expanded_lists as $key=>$label) {
			$temp_lists[] = getListIdFromLabel($label);
		}
	}
	$expanded_lists = $temp_lists;
	if ($ok===true) {
		if (!empty($vars_extra) && is_array($vars_extra)) {
			$vars = array_merge($vars,$vars_extra);
		}
		$programs = getSelectorSearchResults($db,$vars);
	}
	$results = array($programs,$search_terms,$checked_ids,$expanded_lists);
	/*
	echo '<br clear="left"><pre>';
	echo 'vars ';print_r($vars);
	echo 'search_vars ';print_r($search_vars);
	echo 'results ';print_r($results);
	echo '</pre>';
	exit();
	*/
	return $results;
}

function mergeSelectorSearchTerms($search_terms)
{
	$temp_terms = array();
	$temp_terms['selector'] = array();
	if (!empty($search_terms['outcomes'])) { sort($search_terms['outcomes']);$temp_terms['selector'] = $search_terms['outcomes']; }
	if (!empty($search_terms['target_pop'])) { $temp_terms['selector'] = array_merge($temp_terms['selector'],$search_terms['target_pop']); }
	if (!empty($search_terms['specifics'])) { $temp_terms['selector'] = array_merge($temp_terms['selector'],$search_terms['specifics']); }
	if (!empty($search_terms['risk'])) { $temp_terms['risk'] = $search_terms['risk']; }
	if (!empty($search_terms['protective'])) { $temp_terms['protective'] = $search_terms['protective']; }
	if (empty($temp_terms['selector'])) { unset($temp_terms['selector']); }
	return $temp_terms;
}
