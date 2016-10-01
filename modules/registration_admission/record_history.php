<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
define('LANG_FILE', 'aufnahme.php');
$local_user = 'aufnahme_user';
require_once($root_path . 'include/inc_front_chain_lang.php');
require_once($root_path . 'include/inc_date_format_functions.php');

//$thisfile=basename($_SERVER['PHP_SELF']);


if (!isset($db) || !$db)
    include_once($root_path . 'include/inc_db_makelink.php');

switch ($table) {
    case 'care_person': $sql = "SELECT name_last AS \"LastName\", name_first AS \"FirstName\", history FROM care_person WHERE pid=$pid";
        break;
    case 'care_encounter': $sql = $sql = "SELECT p.name_last AS  \"LastName\", p.name_first AS  \"FirstName\", e.history 
	                                                    FROM care_person AS p, care_encounter AS e WHERE p.pid=e.pid AND e.encounter_nr=$pid";
        break;
}
//
$result = $db->Execute($sql);
$history = $result->FetchRow();
?><?php html_rtl($lang); ?>
<!-- Generated by AceHTML Freeware http://freeware.acehtml.com -->
<head>
    <?php echo setCharSet(); ?>
    <title><?php echo $LDRecordsHistory; ?></title>


</head>
<body onBlur="window.close()"><font face=arial>

    <font size=3 color="#000099"><b><?php echo $history['LastName']; ?>, <?php echo $history['FirstName']; ?></b></font>


    <table border=0 cellpadding=0 cellspacing=0 bgcolor="#efefef">
        <tr>
            <td>

                <table border=0 cellspacing=1>


                    <tr>
                        <td background="../../gui/img/common/default/tableHeaderbg.gif">
                            <font face=arial color="#efefef" size=3><b><?php echo $LDRecordsHistory ?> </b>
                        </td>
                    </tr>

                    <?php
                    //echo $mode;
                    if (!empty($history['history'])) {
                        ?> 
                        <tr bgcolor="#ffffff">
                            <td ><font face=arial size=2>
                                <?php
                                $buffer = nl2br($history['history']);
                                //$str='</td></tr><tr bgcolor="#ffffff" background="'.$root_path.'gui/img/common/default/tableHeaderbg3.gif"><td>';
                                $str = '</td></tr><tr bgcolor="#ffffff"><td><font face=arial size=2>';

                                $toggle = !$toggle;

                                $buffer = str_replace('<br>', $str, $buffer);
                                $buffer = str_replace(',', '</td><td>', $buffer);
                                echo $buffer;
                                ?>
                                &nbsp;
                            </td>
                        </tr>
                        <?php
                    }
                    ?>		 </table>

            </td>
        </tr>
    </table>




    </font>
</body>
</html>
