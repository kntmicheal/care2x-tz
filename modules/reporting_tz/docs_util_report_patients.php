<?php

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
//require($root_path . 'include/inc_front_chain_lang.php');
//require($root_path . 'language/en/lang_en_reporting.php');
require($root_path . 'language/en/lang_en_date_time.php');
require($root_path . 'include/inc_date_format_functions.php');

#Load and create paginator object
require_once($root_path . 'include/care_api_classes/class_tz_reporting.php');
/**
 * getting summary of OPD...
 */
$rep_obj = new selianreport();

$lang_tables[] = 'date_time.php';
$lang_tables[] = 'reporting.php';
require($root_path . 'include/inc_front_chain_lang.php');
require_once('include/inc_timeframe.php');
$month = array_search(1, $ARR_SELECT_MONTH);
$year = array_search(1, $ARR_SELECT_YEAR);

if ($printout) {
    $startdate = $_GET['start'];
    $enddate = $_GET['end'];
} else {
    $startdate = $_GET['start'];
    $enddate = $_GET['end'];

//    $startdate = @formatDate2STD($_POST['date_from'], "dd/mm/yyyy");
//    (!isset($_POST['date_to']) || $_POST['date_to'] == '') ? $enddate = @formatDate2STD(date('Y-m-d'), "yyyy-mm-dd") : $enddate = @formatDate2STD($_POST['date_to'], "dd/mm/yyyy");
}

$debug = FALSE;
($debug) ? $db->debug = TRUE : $db->debug = FALSE;

//Get Doctors Name
$doctor = $_GET['doctor'];
$doc_name_qry = "SELECT name FROM care_users
                 WHERE login_id = '$doctor'";
$doc_name_res = $db->Execute($doc_name_qry)->FetchRow();
$doc_name = $doc_name_res[0];


//Get the patients attended by doctor, check that patient in consultation, diagnosis, prescriptions etc

$doc_patients_list = "SELECT name, doctor, date, cd.name_formal, patients 
            FROM care_tz_hospital_doctor_history dh, care_users cu, care_department cd 
                WHERE dh.doctor = cu.login_id AND dh.dept=cd.nr 
                  AND dh.doctor = '$doctor'
                  AND date >= '$startdate' AND date <= '$enddate' 
                  ORDER BY date asc";
$db_doc_patients_list = $db->Execute($doc_patients_list);


$data = array();

while ($row_patients_list = $db_doc_patients_list->FetchRow()) {
    //Get the list of patients separated by | into array, extract and assign to $data
    $arr_patients = explode('|', $row_patients_list['patients']);
    //Set the date of attendance
    $date = $row_patients_list['date'];

    foreach ($arr_patients as $key => $value) {
        $data['patients_list'][$doctor][$date][] = $value;
        $data['date'][] = $date;
        $data['all'][] = $value;
    }
//    print_r($data['patients_list']);
}


//Check patients with consultation etc per doctor on a given date
//
foreach ($data['patients_list'] as $doc => $list) {

//        print_r($data['patients_list']);

    foreach ($list as $date => $patients) {
        //Iterate through each encounter and get patient details if count is not zero
//        $count = array();
        //add data to array encounters for a given date
        foreach ($patients as $encounter) {
//            print $date . ' - ' . $encounter . 'III';
            $data['encounters'][$date][] = $encounter;
        }
    }
}
//
//print_r($data['encounters']);
//
//
////$data['patients_list'] = array_unique($data['patients_list']);
////Get the patient details using the encounter number
//$patients_qry = "SELECT care_person.pid, care_person.selian_pid, care_person.name_first, care_person.name_2, care_person.name_last, 
//                 care_person.sex, care_encounter.encounter_nr, care_encounter.encounter_date
//                 FROM care_person , care_encounter
//                 WHERE care_person.pid = care_encounter.pid
//                 AND care_encounter.encounter_nr IN(" . implode(',', $data['all']) . ") 
//                 ORDER BY care_encounter.pid asc";
//$db_patients_qry = $db->Execute($patients_qry);



require_once('gui/gui_docs_util_patients.php');
?>
