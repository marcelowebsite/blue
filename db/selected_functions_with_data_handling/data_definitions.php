<?php
/* Defined constants for attributes, elements, domains, and sections in the db */
$errorUrl = 'http://www.blueprintsprograms.com/error.php';
if (!defined('LIB_PATH') || !defined('CONTROLLER_START_TIME')) {header("Status: 200");header("Location: " .$errorUrl);exit();}

//*************************************
// Model/Promising program constants

//Blueprints ratings (elements, attributes)
defined('BLUEPRINTS_RATING') or define('BLUEPRINTS_RATING','196');
defined('MODEL_PLUS_PROGRAM') or define('MODEL_PLUS_PROGRAM','618');
defined('MODEL_PROGRAM') or define('MODEL_PROGRAM','282');
defined('PROMISING_PROGRAM') or define('PROMISING_PROGRAM','283');

//***************************
// Database items

//attributes
defined('CONTENT_PRIMARY_FOCUS') or define('CONTENT_PRIMARY_FOCUS','101');
defined('CONTENT_SECONDARY_FOCUS') or define('CONTENT_SECONDARY_FOCUS','102');
defined('OUTCOME_ACHIEVED') or define('OUTCOME_ACHIEVED','103');
defined('TREATMENT') or define('TREATMENT','155');
defined('MAINTENANCE') or define('MAINTENANCE','156');
defined('PROGRAM_FOCUS') or define('PROGRAM_FOCUS','287');
defined('ACHIEVED') or define('ACHIEVED','288');
defined('THERAPEUTIC') or define('THERAPEUTIC','603');
defined('SCHOOL_PROGRAMS') or define('SCHOOL_PROGRAMS','604');
defined('IS_INTERNATIONAL') or define('IS_INTERNATIONAL','619');
defined('PROGRAM IS POLICY') or define('PROGRAM IS POLICY','625');

//elements
defined('PROGRAM_DESIGNER_CONTACT') or define('PROGRAM_DESIGNER_CONTACT','101');
defined('CONTACT_PERSON') or define('CONTACT_PERSON','102');
defined('POSITION') or define('POSITION','103');
defined('ORGANIZATION') or define('ORGANIZATION','104');
defined('ADDRESS') or define('ADDRESS','105');
defined('ADDR2') or define('ADDR2','106');
defined('ADDR3') or define('ADDR3','107');
defined('CITY') or define('CITY','108');
defined('STATE') or define('STATE','109');
defined('COUNTRY') or define('COUNTRY','110');
defined('ZIP_CODE') or define('ZIP_CODE','111');
defined('PHONE') or define('PHONE','112');
defined('ALT_PHONE') or define('ALT_PHONE','113');
defined('FAX') or define('FAX','114');
defined('EMAIL') or define('EMAIL','115');
defined('ALT_EMAIL') or define('ALT_EMAIL','116');
defined('WEBSITE') or define('WEBSITE','117');
defined('PROGRAM_TYPE') or define('PROGRAM_TYPE','157');
defined('PROGRAM_SETTING') or define('PROGRAM_SETTING','158');
defined('CONTINUUM_INTERVENTION') or define('CONTINUUM_INTERVENTION','159');
defined('PROGRAM_GOALS') or define('PROGRAM_GOALS','160');
defined('DEMOGRAPHICS') or define('DEMOGRAPHICS','161');
defined('AGE') or define('AGE','162');
defined('GENDER') or define('GENDER','163');
defined('RACE_ETHNICITY') or define('RACE_ETHNICITY','165');
defined('RACE_GENDER_DETAILS') or define('RACE_GENDER_DETAILS','167');
defined('PROGRAM_RECIPIENTS') or define('PROGRAM_RECIPIENTS','168');
defined('OTHER_RP_FACTORS') or define('OTHER_RP_FACTORS','169');
defined('BRIEF_DESCRIPTION') or define('BRIEF_DESCRIPTION','181');
defined('DESCRIPTION') or define('DESCRIPTION','182');
defined('THEORETICAL_RATIONALE') or define('THEORETICAL_RATIONALE','183');
defined('BRIEF_EVALUATION') or define('BRIEF_EVALUATION','185');
defined('OUTCOMES_BRIEF') or define('OUTCOMES_BRIEF','186');
defined('OUTCOMES_BRIEF_BULLETED') or define('OUTCOMES_BRIEF_BULLETED','187');
defined('MEDIATING_EFFECTS') or define('MEDIATING_EFFECTS','188');
defined('EFFECT_SIZE') or define('EFFECT_SIZE','189');
defined('GENERALIZABILITY') or define('GENERALIZABILITY','190');
defined('LIMITATIONS') or define('LIMITATIONS','191');
defined('NOTES') or define('NOTES','192');
defined('SAMHSA') or define('SAMHSA','198');
defined('REFERENCES') or define('REFERENCES','199');
defined('PROGRAM_INFORMATION_CONTACT') or define('PROGRAM_INFORMATION_CONTACT','200');
defined('STUDY') or define('STUDY','201');
defined('PROGRAM_BENEFITS') or define('PROGRAM_BENEFITS','202');
defined('PROGRAM_COSTS') or define('PROGRAM_COSTS','203');
defined('NET_PRESENT_VALUE') or define('NET_PRESENT_VALUE','204');
defined('MEASURED_RISK') or define('MEASURED_RISK','205');
defined('COST_DATA_SOURCE') or define('COST_DATA_SOURCE','206');
defined('UNIT_COSTS') or define('UNIT_COSTS','207');
defined('INITIAL_TRAINING') or define('INITIAL_TRAINING','208');
defined('CURRICULUM') or define('CURRICULUM','209');
defined('LICENSING') or define('LICENSING','210');
defined('OTHER_STARTUP_COSTS') or define('OTHER_STARTUP_COSTS','211');
defined('ONGOING_CURRICULUM') or define('ONGOING_CURRICULUM','212');
defined('STAFFING') or define('STAFFING','213');
defined('OTHER_IMPLEMENTATION_COSTS') or define('OTHER_IMPLEMENTATION_COSTS','214');
defined('ONGOING_TRAINING') or define('ONGOING_TRAINING','215');
defined('FIDELITY_MONITORING') or define('FIDELITY_MONITORING','216');
defined('ONGOING_LICENSE_FEES') or define('ONGOING_LICENSE_FEES','217');
defined('IMPLEMENTATION_SUPPORT') or define('IMPLEMENTATION_SUPPORT','218');
defined('OTHER_COST') or define('OTHER_COST','219');
defined('INTRODUCTORY_STATEMENT') or define('INTRODUCTORY_STATEMENT','220');
defined('YEAR_ONE_COST_ITEM') or define('YEAR_ONE_COST_ITEM','221');
defined('YEAR_ONE_COST_AMOUNT') or define('YEAR_ONE_COST_AMOUNT','222');
defined('YEAR_ONE_COST_TOTAL') or define('YEAR_ONE_COST_TOTAL','223');
defined('SUMMARY_STATEMENT') or define('SUMMARY_STATEMENT','224');
defined('FUNDING_OVERVIEW') or define('FUNDING_OVERVIEW','225');
defined('FUNDING_BRIEF_PDF') or define('FUNDING_BRIEF_PDF','226');
defined('IMPROVING_EXISTING_PUBLIC_FUNDS') or define('IMPROVING_EXISTING_PUBLIC_FUNDS','227');
defined('ALLOCATING_STATE_LOCAL_FUNDS') or define('ALLOCATING_STATE_LOCAL_FUNDS','228');
defined('MAXIMIZING_FEDERAL_FUNDS') or define('MAXIMIZING_FEDERAL_FUNDS','229');
defined('FOUNDATION_GRANTS') or define('FOUNDATION_GRANTS','230');
defined('DEBT_FINANCING') or define('DEBT_FINANCING','231');
defined('GENERATING_NEW_REVENUE') or define('GENERATING_NEW_REVENUE','232');
defined('DATA_SOURCES') or define('DATA_SOURCES','233');
defined('LOGIC_MODEL_FILE') or define('LOGIC_MODEL_FILE','234');
defined('TRAINING_TECHNICAL_ASSISTANCE') or define('TRAINING_TECHNICAL_ASSISTANCE','235');
defined('TRAINING_CERTIFICATION_PROCESS') or define('TRAINING_CERTIFICATION_PROCESS','236');
defined('VIDEO_URL') or define('VIDEO_URL','237');
defined('ENDORSEMENT_STATUS') or define('ENDORSEMENT_STATUS','323');

//domains
defined('OUTCOME_BEHAVIOR') or define('OUTCOME_BEHAVIOR','101');
defined('OUTCOME_EDUCATION') or define('OUTCOME_EDUCATION','102');
defined('OUTCOME_EMOTIONAL') or define('OUTCOME_EMOTIONAL','103');
defined('OUTCOME_PHYSICAL') or define('OUTCOME_PHYSICAL','104');
defined('OUTCOME_RELATIONSHIPS') or define('OUTCOME_RELATIONSHIPS','105');
defined('PROGRAM_SPECIFICS') or define('PROGRAM_SPECIFICS','106');
defined('TARGET_POPULATION') or define('TARGET_POPULATION','107');
defined('BENEFITS_COSTS') or define('BENEFITS_COSTS','110');
defined('INDIVIDUAL_RISK_FACTOR') or define('INDIVIDUAL_RISK_FACTOR','111');
defined('PEER_RISK_FACTOR') or define('PEER_RISK_FACTOR','112');
defined('FAMILY_RISK_FACTOR') or define('FAMILY_RISK_FACTOR','113');
defined('SCHOOL_RISK_FACTOR') or define('SCHOOL_RISK_FACTOR','114');
defined('NEIGHBORHOOD_RISK_FACTOR') or define('NEIGHBORHOOD_RISK_FACTOR','115');
defined('INDIVIDUAL_PROTECTIVE_FACTOR') or define('INDIVIDUAL_PROTECTIVE_FACTOR','116');
defined('PEER_PROTECTIVE_FACTOR') or define('PEER_PROTECTIVE_FACTOR','117');
defined('FAMILY_PROTECTIVE_FACTOR') or define('FAMILY_PROTECTIVE_FACTOR','118');
defined('SCHOOL_PROTECTIVE_FACTOR') or define('SCHOOL_PROTECTIVE_FACTOR','119');
defined('NEIGHBORHOOD_PROTECTIVE_FACTOR') or define('NEIGHBORHOOD_PROTECTIVE_FACTOR','120');
defined('PEER_IMPLEMENTATION_SITES') or define('PEER_IMPLEMENTATION_SITES','324');
defined('STATUS_AS_POLICY') or define('STATUS_AS_POLICY','328');

//sections
defined('PROGRAM_DESIGNER_CONTACT') or define('PROGRAM_DESIGNER_CONTACT','101');
defined('OUTCOMES') or define('OUTCOMES','102');
defined('RISK_PROTECTIVE_FACTORS') or define('RISK_PROTECTIVE_FACTORS','103');
defined('ENDORSEMENTS') or define('ENDORSEMENTS','104');
defined('START_UP_COSTS') or define('START_UP_COSTS','105');
defined('IMPLEMENTATION_COSTS') or define('IMPLEMENTATION_COSTS','106');
defined('SUPPORT_MONITORING_COSTS') or define('SUPPORT_MONITORING_COSTS','107');
defined('YEAR_ONE_COST_EXAMPLE') or define('YEAR_ONE_COST_EXAMPLE','108');
defined('FINANCING_STRATEGIES') or define('FINANCING_STRATEGIES','109');
defined('PROGRAM_SPECIFICS_FULL') or define('PROGRAM_SPECIFICS_FULL','110');
defined('TARGET_POPULATION_FULL') or define('TARGET_POPULATION_FULL','111');
defined('TRAINING_ASSISTANCE') or define('TRAINING_ASSISTANCE','112');

//messages
defined('COST_DATA_INFO') or define('COST_DATA_INFO','109');

//***************************
/* Key to message types and targets (additional or replacement labels for various items)
-- `target_type`
-- 1 = attribute
-- 2 = element
-- 3 = domain
-- 4 = section
-- `message_type`
-- 1 = edit page comment (added to end of target label)
-- 2 = edit page alias  (complete substitute on editing page only)
-- 3 = public comment (added to end of target label)
-- 4 = public alias (complete substitute, displays on blueprintsprograms.com)
-- 5 = short block of text
*/
defined('EDIT_PAGE') or define('EDIT_PAGE',1);
defined('PUBLIC_PAGE') or define('PUBLIC_PAGE',2);
defined('TARGET_ATTRIBUTE') or define('TARGET_ATTRIBUTE',1);
defined('TARGET_ELEMENT') or define('TARGET_ELEMENT',2);
defined('TARGET_DOMAIN') or define('TARGET_DOMAIN',3);
defined('TARGET_SECTION') or define('TARGET_SECTION',4);
defined('MESSAGE_EDIT_COMMENT') or define('MESSAGE_EDIT_COMMENT',1);
defined('MESSAGE_EDIT_ALIAS') or define('MESSAGE_EDIT_ALIAS',2);
defined('MESSAGE_PUBLIC_COMMENT') or define('MESSAGE_PUBLIC_COMMENT',3);
defined('MESSAGE_PUBLIC_ALIAS') or define('MESSAGE_PUBLIC_ALIAS',4);
defined('MESSAGE_PUBLIC_TEXT') or define('MESSAGE_PUBLIC_TEXT',5);


