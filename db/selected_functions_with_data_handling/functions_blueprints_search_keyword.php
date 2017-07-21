<?php
/* Blueprint functions for conducting keyword searches */
$errorUrl = 'http://www.blueprintsprograms.com/error.php';
if (!defined('LIB_PATH') || !defined('CONTROLLER_START_TIME')) {header("Status: 200");header("Location: " .$errorUrl);exit();}

/***********************
function getKeywordSearch($db,$search_string,$limit='')
function getKeywordSearchElementAttributeLabels($db,$search_terms_output,$limit='')
function getParsedAndCleanedKeywords($search_string,$short_terms_as_acronyms=true)
function getParsedAndStemmedSelectorTerms($parsed_terms)
***********************/

/*******
terms for testing:
bullying "alcohol abuse" OR athletes training and learning to avoid steroids (atlas) OR it is not now
Lifeskills Training (lst) OR athletes training and learning to avoid steroids (atlas)
*******/
//limit is from optional pulldown menu
function getKeywordSearch($db,$search_string,$limit='')
{
	if (is_readable(PORTER_STEMMING)) {
		require PORTER_STEMMING;
	}
	$programs = array(); //for results
	$partial_matches = array(); //for partial matches to program names
	$search_terms_output = array();
	$parsed_strings = getParsedAndCleanedKeywords($search_string);
	$terms_count = count($parsed_strings['terms']); //if == 1, return match on exact program name, otherwise not. Should bail on == 0 too
	$exact_name = false; //flag to break on match to program name
	$parsetest['parsed_strings'] = $parsed_strings;

	//save search string in database
	if (!empty($parsed_strings['search_string']) && is_string($parsed_strings['search_string'])) {
		if (empty($_SESSION['previous_search_items' . S_TOKEN]) || !is_string($_SESSION['previous_search_items' . S_TOKEN]) || (is_string($_SESSION['previous_search_items' . S_TOKEN]) && checkIdenticalArraysOrStrings($_SESSION['previous_search_items' . S_TOKEN],$parsed_strings['search_string'])===false)) {
			//save new search text in database
			$saved_search_id = saveKeywordSearch($db,$parsed_strings['search_string'],$limit);//limit is from optional pulldown menu
		}
		$_SESSION['previous_search_items' . S_TOKEN] = $parsed_strings['search_string'];
	}
	else {
		if (isset($_SESSION['previous_search_items' . S_TOKEN])) {
			$_SESSION['previous_search_items' . S_TOKEN] = array(); //error
		}
	}
	
	//get the program names and ids
	$p_names = array();
	$all_mp_ids = array();
	if (!empty($_SESSION['programs_mp'])) {  //check for usable session
		$all_mp = $_SESSION['programs_mp'];
	}
	else {
		$all_mp = getProgramNames($db,ALL_PROGRAMS,false); //get all m/p
	}
	foreach ($all_mp as $p) {
		$all_mp_ids[] = $p['program_id'];
		$p_names[$p['program_id']] = getParsedAndCleanedKeywords($p['program_name'],false);  //false = no short words as acronyms
		$p_names[$p['program_id']] = $p_names[$p['program_id']]['terms'][0];
		$p_names[$p['program_id']]['names'] = array($p_names[$p['program_id']][0],$p_names[$p['program_id']][1]);
		unset($p_names[$p['program_id']][0],$p_names[$p['program_id']][1],$p_names[$p['program_id']]['search_terms']);
	}
	//$parsetest['p_names'] = $p_names;
	//get static search clauses
	$mp_clause = ' AND '.arrayToWhereClause($all_mp_ids,'program_id','i','OR','');
	$text_fields = array(CONTACT_PERSON,PROGRAM_GOALS,OTHER_RP_FACTORS,BRIEF_DESCRIPTION,THEORETICAL_RATIONALE,BRIEF_EVALUATION,OUTCOMES_BRIEF,OUTCOMES_BRIEF_BULLETED);
	$text_clause = ' AND '.arrayToWhereClause($text_fields,'element_id','s','OR','');
	//to extract sub-terms from the parsed search string
	$extract_keys = array('acronym','quotes','search_terms');
	//main loop of search terms and program names
	foreach ($parsed_strings['terms'] as $parsed_terms) {
		//grab and unset sub-terms so not to match eg, derived (parenthesized) acronym in the search string with words in a program name
		//this should leave only $parsed_strings['terms'][0] and $parsed_strings['terms'][1]
		foreach ($extract_keys as $key) {
			$$key = NULL;
			if (isset($parsed_terms[$key])) {
				$$key = $parsed_terms[$key]; // variable variable name
				unset($parsed_terms[$key]);
			}
		}
		$search_terms_count = (!empty($search_terms)) ? count($search_terms) : 0;
		//main loop of program names
		foreach ($p_names as $p_id=>$p_name) {
			$p_acronym = NULL;
			if (isset($p_name['acronym'])) {
				$p_acronym = $p_name['acronym'][0];
			}
			//return on program name ONLY IF there's only one set of terms (no "OR"), no quotes in the search term, at least 2 words in the search term, and it matches the full name with or without acronym
			if (count(array_intersect($p_name['names'],$parsed_terms))>0) {
				if (!isset($quotes) && $terms_count===1 && $search_terms_count>1 && $limit=='') {
					$parsetest[$p_id]['n'] = $programs[] = $p_id;
					$exact_name = true;
					break 2;
				}
				else {
					$parsetest[$p_id]['p'] = $partial_matches[$p_id] = 1;
				}
			}
			else {
				if (isset($quotes)) {
					foreach ($quotes as $quote) {
						if (strpos($p_name['names'][0],$quote)!==false) {
							$parsetest[$p_id]['p'] = $partial_matches[$p_id] = 1;
						}
					}
				}
				if (isset($p_acronym)) {
					if ((isset($acronym) && in_array($p_acronym,$acronym)) || $p_acronym == $parsed_terms[1]) {
						$partial_matches[$p_id] = 1;
						$parsetest[$p_id]['a'] = $p_acronym;
					}
				}
				//check for partial matches to the program name
				if (isset($search_terms)) {
					$hits = 0;
					foreach ($search_terms as $search_term) {
						if (strpos($p_name['names'][0],$search_term)!==false || strpos($p_name['names'][1],$search_term)!==false) {
							$hits++;
						}
					}
					$count_mark = $search_terms_count * .5;
					if ($hits >= $count_mark) { //relaxed "AND" search
						$parsetest[$p_id]['p'] = $partial_matches[$p_id] = 1;
					}
				}
			}
		}  //foreach p_names
		//if there was an exact name match, this remainder of the parsed terms loop will be bypassed by the break 2
		//include Porter Stemming Algorithm if possible
		$temp_terms = array();
		if (isset($search_terms)) {
			if (function_exists('PorterStem')) {
				foreach ($search_terms as $term) {
					//if (substr($term,-1)=="y" || substr($term,-2)=="ys") {  //why did they do this - it messes up "bully" etc.
						//$temp_terms[] = $term;
					//}
					//else {
						$temp_terms[] = PorterStem($term);
					//}
				}
			}
			else {
				foreach ($search_terms as $term) {
					$temp_terms[] = $term;
				}
			}
		}
		$search_terms = $temp_terms;
		if (isset($quotes)) {
			$search_terms = array_merge($search_terms,$quotes);
		}
		if (count($search_terms) > 0) {
			$search_terms_output[] = $search_terms;
		}
	}  //foreach parsed_terms
	//$parsetest['programs1'] = $programs;
	if ($exact_name !== true) {
		//search for programs if the search terms array was populated
		if (count($search_terms_output)>0) {
			$parsetest['search_terms_output'] = $search_terms_output;
			//limit should be either the empty string or an int constant
			if (!empty($limit) && ((is_string($limit) && ctype_digit($limit)) || is_int((int)$limit))) {
				//search only Outcomes or Program Types
				$programs = getKeywordSearchElementAttributeLabels($db,$search_terms_output,$limit);
			}
			else {
				//search db, outcomes, rp factors, and program types
				$programs = $partial_matches; //pick up partial matches to any program names/acronyms
				$search_terms = array();
				$datatype_params = '';
				$query_clause = array();
				foreach ($search_terms_output as $terms_output) {
					$search_clause = array();
					foreach ($terms_output as $term) {
						$search_terms[] = $term;
						$search_clause[] = 'text_content LIKE ?';
						$datatype_params .= 's';
					}
					$query_clause[] = '('.implode(' AND ',$search_clause).')';
				}
				//search outcomes, rp factors, and program types
				$element_attribute_matches = getKeywordSearchElementAttributeLabels($db,$search_terms_output,'');  //return array in format id=>1
				if (is_array($element_attribute_matches)) {
					foreach ($element_attribute_matches as $p_id=>$v) {
						$programs[$p_id] = 1;
					}
				}
				//search db
				$query_terms = array_map(function($v) {return "%".$v."%";},$search_terms);
				$search_clause = implode(' OR ',$query_clause);
				$query = "SELECT program_id FROM program_text WHERE (".$search_clause.')'.$mp_clause.$text_clause;
				$rows = mysqliQueryExecExtended($db,$query,$datatype_params,$query_terms);
				foreach ($rows as $row) {
					$programs[$row['program_id']] = 1;
				}
			}
		}
		if (count($programs) > 0) {
			$temp = array();
			foreach ($programs as $p_id=>$v) {
				$temp[] = $p_id;
			}
			$programs = $temp;
		}
	}
	//$parsetest['programs2'] = $programs;
	//echo '<pre>';print_r($parsetest);echo '</pre>';
	return $programs;
}

/*******
Search selected element or attribute labels for keyword search. Search includes outcomes, rp factors, and program types by default. 
Can be limited to outcomes and program types.
//search_terms_output is array of arrays that embed the AND/OR logic
*******/
function getKeywordSearchElementAttributeLabels($db,$search_terms_output,$limit='')
{
	if (!defined('OUTCOME_ACHIEVED')) { return false; }  //error
	$program_ids = array();
	$parsed_selector_terms = array();
	$search_result_vars = array();
	$delimiters = array(' ','/','-'); //for parsing
	//get $_SESSION['s_info' . S_TOKEN] and $_SESSION['selector_search_programs' . S_TOKEN]
	$selector_search_programs = (empty($_SESSION['selector_search_programs' . S_TOKEN])) ? getSelectorSearchPrograms($db) : $_SESSION['selector_search_programs' . S_TOKEN];
	$s_info = (empty($_SESSION['s_info' . S_TOKEN])) ? getProgramSelector($db) : $_SESSION['s_info' . S_TOKEN];
	//make sure limit is an allowed type or the empty string
	defined('KEYWORD_LIMIT_OUTCOMES') or define('KEYWORD_LIMIT_OUTCOMES','');
	defined('KEYWORD_LIMIT_PROGRAM_TYPE') or define('KEYWORD_LIMIT_PROGRAM_TYPE','');
	$limits_allowed = array(KEYWORD_LIMIT_OUTCOMES=>1,
		KEYWORD_LIMIT_PROGRAM_TYPE=>1,
	);
	$limit = (isset($limits_allowed[$limit])) ? $limit : '';
	if ($limit=='' || $limit==KEYWORD_LIMIT_OUTCOMES) {
		//$_SESSION['s_info' . S_TOKEN]['search_outcomes']
		if (!empty($_SESSION['parsed_selector_terms' . S_TOKEN]['outcomes'])) {
			$parsed_selector_terms['outcomes'] = $_SESSION['parsed_selector_terms' . S_TOKEN]['outcomes'];
		}
		else {
			$search_outcomes = (empty($_SESSION['s_info' . S_TOKEN]['search_outcomes'])) ? array() : $_SESSION['s_info' . S_TOKEN]['search_outcomes'];
			$parsed_terms = array();
			foreach ($search_outcomes as $outcomes_domain) {
				foreach ($outcomes_domain as $e_id=>$outcome) {
					$var = 'a'.$outcome['element_id'].'a'.OUTCOME_ACHIEVED;
					$label = strtolower($outcome['element_label']);
					$parsed_terms[$var]['label'] = $label;
					$parsed_terms[$var]['subs'] = array();
					$subs = explodeMultiDelimiters($delimiters,$label);
					if (count($subs) > 0) {  //if only 1, it's same as label<<<<<<<<<<<<<not true in some cases eg bullying
						$parsed_terms[$var]['subs'] = $subs;
					}
					$message = (!empty($outcome['message_content'])) ? strtolower($outcome['message_content']) : '';
					if ($message != '') {
						$parsed_terms[$var]['message'] = $message;
						$subs = explodeMultiDelimiters($delimiters,$message);
						if (count($subs) > 0) {
							$parsed_terms[$var]['subs'] = array_merge($parsed_terms[$var]['subs'],$subs);
						}
					}
				}
			}
			$parsed_terms = getParsedAndStemmedSelectorTerms($parsed_terms);
			$_SESSION['parsed_selector_terms' . S_TOKEN]['outcomes'] = $parsed_selector_terms['outcomes'] = $parsed_terms;
		}
	}
	if ($limit=='' || $limit==KEYWORD_LIMIT_PROGRAM_TYPE) {
		//$_SESSION['s_info' . S_TOKEN]['search_specifics'][PROGRAM_SPECIFICS][PROGRAM_TYPE]
		if (!empty($_SESSION['parsed_selector_terms' . S_TOKEN]['program_type'])) {
			$parsed_selector_terms['program_type'] = $_SESSION['parsed_selector_terms' . S_TOKEN]['program_type'];
		}
		else {
			$search_program_type = array();
			if (defined('PROGRAM_SPECIFICS') && defined('PROGRAM_TYPE') && !empty($_SESSION['s_info' . S_TOKEN]['search_specifics'][PROGRAM_SPECIFICS][PROGRAM_TYPE])) {
				$search_program_type = $_SESSION['s_info' . S_TOKEN]['search_specifics'][PROGRAM_SPECIFICS][PROGRAM_TYPE];
				$e_id = $search_program_type['element_id'];
			}
			$parsed_terms = array();
			foreach ($search_program_type['attributes'] as $attribute) {
				$var = 'a'.$e_id.'a'.$attribute['attribute_id'];
				$label = strtolower($attribute['attribute_label']);
				$parsed_terms[$var]['label'] = $label;
				$parsed_terms[$var]['subs'] = array();
				$subs = explodeMultiDelimiters($delimiters,$label);
				if (count($subs) > 0) {
					$parsed_terms[$var]['subs'] = $subs;
				}
				$message = (!empty($attribute['message_content'])) ? strtolower($attribute['message_content']) : '';
				if ($message != '') {
					$parsed_terms[$var]['message'] = $message;
					$subs = explodeMultiDelimiters($delimiters,$message);
					if (count($subs) > 0) {
						$parsed_terms[$var]['subs'] = array_merge($parsed_terms[$var]['subs'],$subs);
					}
				}
			}
			$parsed_terms = getParsedAndStemmedSelectorTerms($parsed_terms);
			$_SESSION['parsed_selector_terms' . S_TOKEN]['program_type'] = $parsed_selector_terms['program_type'] = $parsed_terms;
		}
	}
	if ($limit=='') {
		if (!empty($_SESSION['parsed_selector_terms' . S_TOKEN]['rp_factors'])) {
			$parsed_selector_terms['rp_factors'] = $_SESSION['parsed_selector_terms' . S_TOKEN]['rp_factors'];
		}
		else {
			$search_rp_factors = (empty($_SESSION['s_info' . S_TOKEN]['search_rp_factors'])) ? array() : $_SESSION['s_info' . S_TOKEN]['search_rp_factors'];
			$parsed_terms = array();
			foreach ($search_rp_factors as $rp_factor_groups) {
				unset($rp_factor_groups['label']);
				foreach ($rp_factor_groups as $rp_factor_domain) {
					foreach ($rp_factor_domain as $rp_factors) {
						$var = 'a' . $rp_factors['element_id'] . 'a' . PROGRAM_FOCUS;
						$label = strtolower($rp_factors['element_label']);
						$parsed_terms[$var]['label'] = $label;
						$parsed_terms[$var]['subs'] = array();
						$subs = explodeMultiDelimiters($delimiters,$label);
						if (count($subs) > 0) {
							$parsed_terms[$var]['subs'] = $subs;
						}
						$message = (!empty($rp_factors['message_content'])) ? strtolower($rp_factors['message_content']) : '';
						if ($message != '') {
							$parsed_terms[$var]['message'] = $message;
							$subs = explodeMultiDelimiters($delimiters,$message);
							if (count($subs) > 0) {
								$parsed_terms[$var]['subs'] = array_merge($parsed_terms[$var]['subs'],$subs);
							}
						}
					
					}
				}
			}
			$parsed_terms = getParsedAndStemmedSelectorTerms($parsed_terms);
			$_SESSION['parsed_selector_terms' . S_TOKEN]['rp_factors'] = $parsed_selector_terms['rp_factors'] = $parsed_terms;
		}
	}
	if (is_readable(STOP_WORDS)) {
		require STOP_WORDS;
	}
	foreach ($parsed_selector_terms as $selector_terms) {
		foreach ($selector_terms as $var=>$selector_term) {
			foreach ($search_terms_output as $search_terms) {
				$search_terms_count = count($search_terms);
				$hits = 0;
				foreach ($search_terms as $search_term) {
					if (isset($stop_words[$search_term])) {
						$search_terms_count--;
					}
					else {
						if (strpos($search_term,'violen')!==false) { $search_term = 'violen'; }  //hack for trouble with important word
						if (strpos($selector_term,$search_term)!==false) {
							$hits++;
						}
					}
				}
				//$count_mark = ($search_terms_count==2) ? $search_terms_count : $search_terms_count * .5;
				$count_mark = $search_terms_count * .8;
				if ($hits > $count_mark) { //slightly relaxed "AND" search
					$search_result_vars[$var] = 1;
				}
			}
		}
	}
	$results = array_intersect_key($selector_search_programs,$search_result_vars);
	foreach ($results as $programs) {
		foreach ($programs as $program) {
			$program_ids[$program] = 1;
		}
	}
	/*
	echo '<pre>';
	print_r($search_terms_output);
	print_r($_SESSION['parsed_selector_terms' . S_TOKEN]);
	print_r($_SESSION['s_info' . S_TOKEN]['search_outcomes']);
	print_r($_SESSION['s_info' . S_TOKEN]['search_specifics'][PROGRAM_SPECIFICS][PROGRAM_TYPE]);
	print_r($_SESSION['s_info' . S_TOKEN]['search_rp_factors']);
	print_r($_SESSION['selector_search_programs' . S_TOKEN]);
	print_r($program_ids);
	echo '</pre>';
	*/
	return $program_ids;
}

//parse input for keyword search
function getParsedAndCleanedKeywords($search_string,$short_terms_as_acronyms=true)
{
	$results = array();
	if (is_readable(STOP_WORDS)) {
		require STOP_WORDS;
	}
	//$parsed = strtolower(html2txt(trim($search_string),' '));
	$parsed_term = html2txt(trim($search_string),' ');
	$results['search_string'] = $parsed_term;
	$parsed_terms = explode(' or ',str_replace(' OR ',' or ',$parsed_term));
	foreach ($parsed_terms as $parsed) {
		$results_temp = array();
		$parsed = strtolower($parsed);
		$to_clean = array($parsed);
		//find double quoted terms
		$quote_count = substr_count($parsed,'"');
		if ($quote_count > 0 && !($quote_count & 1)) { //even number
			$pattern = '/"[^"]{3,}"/';
			preg_match_all($pattern,$parsed,$quotes);
			foreach ($quotes[0] as $quote) {
				if (strpos(trim($quote),' ')!==false || strlen($quote)<6) {
					$to_clean[] = $quote;
				}
			}
		}
		foreach ($to_clean as $k=>$cleaned) {
			$cleaned = preg_replace("/&#?[a-z0-9]{2,8};/", ' ',$cleaned); //already lower case
			$cleaned = preg_replace('/[^\da-z]/', ' ',$cleaned);
			$to_clean[$k] = trim(preg_replace('/[\s]{2,}/',' ',$cleaned));
		}
		$no_punct = $to_clean[0];
		array_push($results_temp,$parsed,$no_punct);
		//get acronym if any
		if (substr($parsed,-1)==')') {
			$substrings = explode('(',rtrim($parsed,')'));
			if (count($substrings)==2) {
				$acronym = trim($substrings[1]);
				$results_temp[1] = rtrim(preg_replace('/'.$acronym.'$/','',$no_punct));
				$results_temp['acronym'][] = $acronym;
			}
		}
		$terms = $results_temp[1];
		//get quoted search terms if any and remove duplicates from the search terms
		if (count($to_clean) > 1) {
			unset($to_clean[0]);
			foreach ($to_clean as $quote_term) {
				$pos = strpos($terms,$quote_term);
				if ($pos !== false) {
					$terms = substr_replace($terms,'',$pos,strlen($quote_term));
				}
			}
			$results_temp['quotes'] = array_keys(array_flip($to_clean));  //no dups
		}
		/*********
		if no quotes
		--if all words < 3 letters, put entire string in quotes, put == 3 letters in acronyms
		--if all words > 3 (or mix of < 3 and > 3), put == 3 in acronyms, > 3 in search terms
		if quotes
		--put quoted terms in quotes
		--for any words that are outside quotes, put == 3 in acronyms, > 3 in search terms
		*********/
		//get search terms (gets Porter Algorithm), quotes (no Porter), and 3-letter words as acronyms
		$terms = explode(' ',$terms);
		//if all search terms are < 3 chars and at least one == 3 chars, search on them as if quoted
		$min_length = (count($terms)>1) ? 5 : 2;
		if (empty($results_temp['quotes']) && strlen($results_temp[1])>$min_length && count($terms)===count(array_filter($terms,function($v) {return strlen(trim($v))<4;})) ) {
			$results_temp['quotes'] = array();
			$results_temp['quotes'][] = $results_temp[1];
		}
		$terms = array_keys(array_flip($terms));  //no dups.
		$temp_terms = array();
		foreach ($terms as $term) {
			$term = trim($term);
			$term_length = strlen($term);
			if (!isset($stop_words[$term])) {
				if ($term_length == 3 && $short_terms_as_acronyms===true) { $results_temp['acronym'][] = $term; }
				elseif ($term_length > 3) { $temp_terms[] = $term; }
			}
		}
		if (isset($results_temp['acronym'])) {
			$results_temp['acronym'] = array_keys(array_flip($results_temp['acronym']));  //no dups.
		}
		if (count($temp_terms) > 0) {
			$results_temp['search_terms'] = $temp_terms;
		}
		$results['terms'][] = $results_temp;
	}
	//echo '<pre>';print_r($results);echo '</pre>';
	//exit();
	return $results;
}

/*****
//for keyword search. subs can be empty.
Array ( [a119a103] => Array (
[label] => adult crime
[subs] => Array (
		[0] => adult
		[1] => crime
	)
) )
*****/
function getParsedAndStemmedSelectorTerms($parsed_terms)
{
	$results = array();
	if (is_array($parsed_terms) && count($parsed_terms) > 0) {
		$input_ok = true;
		foreach($parsed_terms as $var=>$parsed_term) {
			if (empty($parsed_term['label'])) {
				$input_ok = false;
				break;  //error
			}
			else {
				$results[$var] = $parsed_term['label'];
				if (!empty($parsed_term['message'])) {
					$results[$var] = $results[$var].' '.$parsed_term['message'];
				}
			}
		}
		if ($input_ok==true) {
			if (function_exists('PorterStem')) {
				foreach($parsed_terms as $var=>$parsed_term) {
					if (!empty($parsed_term['subs'])) {
						$temp_terms = array();
						foreach ($parsed_term['subs'] as $term) {
							$term = preg_replace("/&#?[a-z0-9]{2,8};/", ' ',$term); //already lower case
							$term = preg_replace('/[^\da-z]/', ' ',$term);
							$term = trim($term);
							//if (substr($term,-1) !="y" && substr($term,-2) != "ys") {  //Why? doesnt work for eg bully
								$temp_terms[] = PorterStem($term);
							//}
						}
						foreach ($temp_terms as $temp_term) {
							if (strpos($results[$var],$temp_term)===false) {
								$results[$var] = $results[$var].' '.$temp_term;
							}
						}
					
					}
				}
			}
		}
	}
	return $results;
}
