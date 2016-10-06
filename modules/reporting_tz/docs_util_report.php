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




if (!isset($_POST['amount_per_person']) || $_POST['amount_per_person'] == '') {
    $amount_per_person = 0;
} else {
    $amount_per_person = $_POST['amount_per_person'];
}

if ($printout) {
    $startdate = $_GET['start'];
    $enddate = $_GET['end'];
//    $start_timeframe = $start;
//    $end_timeframe = $end;
//    $startdate = date("y.m.d ", $start_timeframe);
//    $enddate = date("y.m.d", $end_timeframe);
    $amount_per_person = $_GET['amount_per_person'];
} else {
    $startdate = @formatDate2STD($_POST['date_from'], "dd/mm/yyyy");
    (!isset($_POST['date_to']) || $_POST['date_to'] == '') ? $enddate = @formatDate2STD(date('Y-m-d'), "yyyy-mm-dd") : $enddate = @formatDate2STD($_POST['date_to'], "dd/mm/yyyy");
}

$debug = FALSE;
($debug) ? $db->debug = TRUE : $db->debug = FALSE;


//Get the doctors list and the patients they have attended.

$docs_patients = "SELECT name, doctor, date, cd.name_formal, patients 
    FROM care_tz_hospital_doctor_history dh, care_users cu, care_department cd 
    WHERE dh.doctor = cu.login_id AND dh.dept=cd.nr 
    AND date >= '$startdate' AND date <= '$enddate' 
    ORDER BY doctor asc";
$db_docs_patients = $db->Execute($docs_patients);

//Get patients attended on a specific date

$data = $data1 = array();

$i = 0;
$tabler = '';
$gtotal = 0;
while ($row = $db_docs_patients->FetchRow()) {
    //Get the first name on the list
    if ($i == 0) {
        $doc = $row['doctor'];
        $name = $row['name'];
        $count_t = 0;  //Initialize total count to zero which holds no of patients per doctor
    }

    /* Get doctor's name for the rest of the rows, if new, filter data to return distinct values
     * and assign new value to $doctor
     */

    if ($row['doctor'] != $doc) {
//        $data['patients_list'][$doctor] = array_unique($data['patients_list'][$doctor]);
        //Get totals
        $amount_doc_total = number_format($amount_per_person * $count_t);
        $tabler .= '<tr>';
        $tabler.='<td colspan=3 align=right><b>Sub Total<b></td>';
//        $tabler.='<td></td>';
        $tabler.='<td align=center>' . $count_t . '</td>';
        $tabler.='<td></td>';
        $tabler.='<td align="center">' . number_format($amount_per_person, 2) . '</td>';
        $tabler.='<td align="center">' . $amount_doc_total . '</td>';
        $tabler.="<td><a href=\"javascript:patientsList('$doc');\">Open List</a></td>";
        $tabler .= '</tr>';

        $count_t = 0;       //Reset total count to zero which holds no of patients per doctor
        //Assign new values
        $doc = $row['doctor'];
        $name = $row['name'];
    }

    //Get the list of patients separated by | into array, extract and assign to $data
    $arr_patients = explode('|', $row['patients']);

    //Get the date of attendance
    $date = $row['date'];

    //Get dept
    $dept = $row['name_formal'];

    foreach ($arr_patients as $key => $value) {
        $data['patients_list'][$doc][$date][] = $value;
        $data['date'][$doc][] = $date;
    }

//}
//
////Check patients with consultation etc per doctor on a given date
//
    foreach ($data['patients_list'] as $doc => $list) {

//        print_r($data['patients_list']);

        foreach ($list as $date => $patients) {
            //Iterate through doctor and get patients on a date
//        foreach ($patients as $date => $encounter) {
            $count = array();
            //Check diagnosis
            $sql_diag = "SELECT COUNT(DISTINCT encounter_nr) AS count FROM  care_tz_diagnosis "
                    . " WHERE encounter_nr IN(" . implode(',', $patients) . ")
                        AND FROM_UNIXTIME(timestamp) like '%$date%' "
                    . " AND doctor_name ='$doc'";
            $db_docs_diag = $db->Execute($sql_diag);
            $row_diag = $db_docs_diag->FetchRow();
            array_push($count, $row_diag[0]);

            //Check notes
            $sql_notes = "SELECT COUNT(DISTINCT cen.encounter_nr) AS count
                        FROM  care_encounter_notes cen
                        WHERE cen.encounter_nr IN(" . implode(',', $patients) . ")
                        AND date like '%$date%'  "
                    . " AND personell_name='$doc'";
            $db_docs_notes = $db->Execute($sql_notes);
            $row_notes = $db_docs_notes->FetchRow();
            array_push($count, $row_notes[0]);


            //Check prescriptions
            $sql_presc = "SELECT COUNT(DISTINCT encounter_nr) AS count "
                    . "FROM  care_encounter_prescription "
                    . " WHERE encounter_nr IN(" . implode(',', $patients) . ")
                        AND prescribe_date like '%$date%' "
                    . " AND prescriber ='$doc'";
            $db_docs_presc = $db->Execute($sql_presc);
            $row_presc = $db_docs_presc->FetchRow();
            array_push($count, $row_presc[0]);
        }
    }

    unset($data);

    //Get the largest in array
    $count_r = max($count);

    //Omit rows with zero no of patients
    if ($count_r > 0) {
        $count_t+=$count_r;
        $gtotal+=$count_r;

//    $row['count'] = $count_r;
//    echo $name . ' - ' . $count_t . ' - ' . $date;
//    $data1['docs_list'][] = $row;

        $tabler .= '<tr>';
        $serial = $i + 1;

        $tabler.='<td align="center">' . $serial . '</td>';
        $tabler.='<td align=center>' . @formatDate2Local($date, "dd/mm/yyyy") . '</td>';
        $tabler.="<td align=center>$name</td>";
        $tabler.='<td align=center>' . $count_r . '</td>';
        $tabler.='<td align=center>' . $dept . '</td>';
        $tabler.='<td></td>';
        $tabler.='<td></td>';
        $tabler.='</tr>';
        $i++;
    }
}
//Get totals on exiting loop
$amount_doc_total = number_format($amount_per_person * $count_t);
$tabler .= '<tr>';
$tabler.='<td colspan=3 align=right><b>Sub Total<b></td>';
//        $tabler.='<td></td>';
$tabler.='<td align=center>' . $count_t . '</td>';
$tabler.='<td></td>';
$tabler.='<td align="center">' . number_format($amount_per_person, 2) . '</td>';
$tabler.='<td align="center">' . $amount_doc_total . '</td>';
$tabler.="<td><a href=\"javascript:patientsList('$doc');\">Open List</a></td>";
$tabler .= '</tr>';



//print_r($db_docs_patients);
//print_r($data['patients_list']);

require_once('gui/gui_docs_util.php');
