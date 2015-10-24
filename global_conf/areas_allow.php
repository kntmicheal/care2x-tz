<?php

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR),
### The following arrays are the "role" levels each containing an access point or groups of access points

$all = '_a_0_all';
$sysadmin = 'System_Admin';

$allow_area = array(
    'admit_all' => array('_a_1_admissionwrite', '_a_2_admissionread', '_a_2_archiveread', '_a_1_medocswrite'),
    'admit_write' => array('_a_1_admissionwrite'),
    'admit_read' => array('_a_2_admissionread'),
    'admit_archive' => array('_a_2_archiveread'),
    'aar' => array('_a_1_aarreadwrite'),
    'report_all' => array('_a_1_allreportingread', '_a_2_reportingread', '_a_2_clinicreportingread', '_a_2_financialreportingread', '_a_2_systemreportingread'),
    'report' => array('_a_2_reportingread'),
    'report_clinical' => array('_a_2_clinicreportingread'),
    'report_financial' => array('_a_2_financialreportingread'),
    'report_system' => array('_a_2_systemreportingread'),
    'bill' => array('_a_1_billallwrite', '_a_2_billallread', '_a_2_billquotations', '_a_2_billreports'),
    'bill_r' => array('_a_2_billallread'),
    'bill_q' => array('_a_2_billquotations'),
    'bill_rep' => array('_a_2_billreports'),
//    'bill_ins' => array('_a_2_billinsread', '_a_3_billallwrite'),
    'cafe' => array('_a_1_newsallwrite', '_a_1_newscafewrite'),
    'medocs' => array('_a_1_medocswrite'),
    'phonedir' => array('$all, $sysadmin'),
    'doctors' => array('_a_1_opdoctorallwrite', '_a_1_doctorsdutyplanwrite'),
    'wards' => array('_a_1_doctorsdutyplanwrite', '_a_1_opdoctorallwrite', '_a_1_nursingstationallwrite', $all, $sysadmin),
    'op_room' => array('_a_1_opdoctorallwrite', '_a_1_opnursedutyplanwrite', '_a_2_opnurseallwrite'),
    'tech' => array('_a_1_techreception'),
    'lab_all' => array('_a_1_labresultsreadwrite', '_a_2_labresultswrite', '_a_3_labresultsread', '_a_2_labparametersedit'),
    'lab_request' => array('_a_2_labrequest'),
    'lab_r' => array('_a_3_labresultsread'),
    'lab_w' => array('_a_2_labresultswrite'),
    'lab_bl_w' => array('_a_2_labbloodwrite'),
    'lab_bl_r' => array('_a_3_labbloodread'),
//    'lab_p' => array('_a_2_labparametersedit'),
    'radio' => array('_a_1_radiowrite', '_a_1_opdoctorallwrite', '_a_2_opnurseallwrite'),
    'pharma_db' => array('_a_1_pharmadbadmin'),
    'pharma_receive' => array('_a_1_pharmadbadmin', '_a_2_pharmareception'),
    'pharma' => array('_a_1_pharmadbadmin', '_a_2_pharmareception', '_a_3_pharmaorder'),
    'depot_db' => array('_a_1_meddepotdbadmin'),
    'depot_receive' => array('_a_1_meddepotdbadmin', '_a_2_meddepotreception'),
    'depot' => array('_a_1_meddepotdbadmin', '_a_2_meddepotreception', '_a_3_meddepotorder'),
    'edp' => array('no_allow_type_all',),
    'news' => array('_a_1_newsallwrite'),
    'cafenews' => array('_a_1_newsallwrite', '_a_2_newscafewrite'),
    'op_docs' => array('_a_1_opdoctorallwrite'),
    'duty_op' => array('_a_1_opnursedutyplanwrite'),
    'fotolab' => array('_a_1_photowrite'),
    'test_diagnose' => array('_a_1_diagnosticsresultwrite', '_a_2_labresultswrite'),
    'test_receive' => array('_a_1_diagnosticsresultwrite', '_a_2_labresultswrite', '_a_2_diagnosticsreceptionwrite'),
    'test_order' => array('_a_1_diagnosticsresultwrite', '_a_2_labresultswrite', '_a_2_diagnosticsreceptionwrite', '_a_3_diagnosticsrequest')
);

/*
 * Include a file for child areas for overriding the child permissions when
 * assigned a superior permission i.e: parent aquires all the child permissions
 * By: Mark Pat 24th Oct 2015
 */

include($root_path . 'global_conf/areas_children.php');
?>
