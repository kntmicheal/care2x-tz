<?PHP
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
session_start();
//if ((isset($_SESSION['login']) && $_SESSION['login'] != '')) {
//	header ("Location: success.php");
//}
//set the session variable to 1, if the user signs up. That way, they can use the site straight away
//do you want to send the user a confirmation email?
//does the user need to validate an email address, before they can use the site?
//do you want to display a message for the user that a particular username is already taken?
//test to see if the u and p are long enough
//you might also want to test if the users is already logged in. That way, they can't sign up repeatedly without closing down the browser
//other login methods - set a cookie, and read that back for every page
//collect other information: date and time of login, ip address, etc
//don't store passwords without encrypting them

date_default_timezone_set('Africa/Dar_es_Salaam');

$uname = $idno = $pword = $pword2 = $firstname = $secondname = $lastname = $department = $userrole = $checkbox = "";
$unameErr = $idnoErr = $pwordErr = $pword2Err = $firstnameErr = $secondnameErr = $lastnameErr = $departmentErr = $userroleErr = $checkboxErr = "";
$permission = "";
$errorMessage = "";
$num_rows = 0;
$s_date = date('Y-m-d');
$s_time = date('h:m:s');

function quote_smart($value, $handle) {

    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }

    if (!is_numeric($value)) {
        $value = "'" . mysql_real_escape_string($value, $handle) . "'";
    }
    return $value;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //====================================================================
    //	GET THE CHOSEN USER DETAILS, AND CHECK IT FOR DANGEROUS CHARCTERS
    //====================================================================

    if (empty($_POST['username'])) {
        $unameErr = " * Username is required";
        $errorMessage = "Not all required fields are filled in. <BR>";
    } else {
        $uname = $_POST['username'];
    }

    if (empty($_POST['password'])) {
        $pwordErr = " * Password is required";
        $errorMessage = "Not all required fields are filled in. <BR>";
    } else {
        $pword = $_POST['password'];
    }

    if (empty($_POST['password2'])) {
        $pword2Err = " * Password must be between 5 and 16 characters";
        $errorMessage = "Not all required fields are filled in. <BR>";
    } elseif ($_POST['password2'] != $_POST['password']) {
        $pword2Err = "The passwords do no match, please try again.";
        $errorMessage = $errorMessage . "Passwords do not match. <BR>";
    } else {
        $pword2 = $_POST['password2'];
    }

    if (empty($_POST['firstname'])) {
        $firstnameErr = " * First name is required";
        $errorMessage = "Not all required fields are filled in. <BR>";
    } else {
        $firstname = $_POST['firstname'];
    }

    if (empty($_POST['secondname'])) {
        $secondnameErr = " * Second name is required";
        $errorMessage = "Not all required fields are filled in. <BR>";
    } else {
        $secondname = $_POST['secondname'];
    }

    if (empty($_POST['lastname'])) {
        $lastnameErr = " * First name is required";
        $errorMessage = "Not all required fields are filled in. <BR>";
    } else {
        $lastname = $_POST['lastname'];
    }

    if (empty($_POST['userrole'])) {
        $userroleErr = " * You must select your function from the dropdown list";
        $errorMessage = "Not all required fields are filled in correctly. <BR>";
    } else {
        $userrole = $_POST['userrole'];
    }

    if (empty($_POST['idno'])) {
        $idnoErr = " * You must enter your ID number, only numbers allowed";
        $errorMessage = "Not all required fields are filled in correctly. <BR>";
    } elseif (!is_numeric($_POST['idno'])) {
        $idnoErr = " * You must enter your ID number correctly, only numbers allowed";
        $errorMessage = "Not all required fields are filled in correctly. <BR>";
    } else {
        $idno = $_POST['idno'];
    }

    if (empty($_POST['department'])) {
        $departmentErr = " * Please fill in the department you are currently stationed";
        $errorMessage = "Not all required fields are filled in. <BR>";
    } else {
        $department = $_POST['department'];
    }

    if (empty($_POST['checkbox'])) {
        $checkboxErr = " * You must check the box to signify that you have filled in the form truthfully and completely";
        $errorMessage = "Not all required fields are filled in. <BR>";
    } else {
        $checkbox = $_POST['checkbox'];
    }

    $uname = htmlspecialchars($uname);
    $pword = htmlspecialchars($pword);
    $pword2 = htmlspecialchars($pword2);
    $firstname = htmlspecialchars($firstname);
    $secondname = htmlspecialchars($secondname);
    $lastname = htmlspecialchars($lastname);
    $userrole = htmlspecialchars($userrole);
    $idno = htmlspecialchars($idno);
    $department = htmlspecialchars($department);


    //====================================================================
    //	CHECK TO SEE IF U AND P ARE OF THE CORRECT LENGTH
    //	A MALICIOUS USER MIGHT TRY TO PASS A STRING THAT IS TOO LONG
    //	if no errors occur, then $errorMessage will be blank
    //====================================================================

    $uLength = strlen($uname);
    $pLength = strlen($pword);
    $fLength = strlen($firstname);
    $sLength = strlen($secondname);
    $lLength = strlen($lastname);

    if ($uLength < 5 || $uLength > 20) {
        $unameErr = " * Username must be between 5 and 20 characters";
    }

    if ($pLength < 5 || $pLength > 16) {
        $pwordErr = " * Password must be between 5 and 16 characters";
    }

    if ($fLength < 3 || $fLength > 30) {
        $firstnameErr = " * First name must be between 3 and 30 characters";
    }

    if ($sLength < 3 || $sLength > 30) {
        $secondnameErr = " * Second name must be between 3 and 30 characters";
    }

    if ($lLength < 3 || $lLength > 30) {
        $lastnameErr = " * Last name must be between 3 and 30 characters";
    }

//test to see if $errorMessage is blank
//if it is, then we can go ahead with the rest of the code
//if it's not, we can display the error
    //====================================================================
    //	Write to the database
    //====================================================================
    if ($errorMessage == "") {

        include('./include/dbase_connect.php');


        $db_handle = mysql_connect($server, $user_name, $pass_word);
        $db_found = mysql_select_db($database, $db_handle);

        if ($db_found) {

//Get permissions from function by lookung up in the database
            $SQL = "SELECT * FROM userrole_permission WHERE userrole = '$userrole'";
            $result = mysql_query($SQL);
            $num_rows = mysql_num_rows($result);

            if ($num_rows > 0) {
                while ($db_field = mysql_fetch_assoc($result)) {
                    $permission = $db_field['permission'];
                }
            }

            $unameSmart = quote_smart($uname, $db_handle);
            $pwordSmart = quote_smart($pword, $db_handle);
            $userroleSmart = quote_smart($userrole, $db_handle);
            $idnoSmart = quote_smart($idno, $db_handle);
            $fullname = $firstname . ' ' . $secondname . ' ' . $lastname;
            $fullnameSmart = quote_smart($fullname, $db_handle);
            $permissionSmart = quote_smart($permission, $db_handle);
            $departmentSmart = quote_smart($department, $db_handle);
            $s_dateSmart = quote_smart($s_date, $db_handle);
            $s_timeSmart = quote_smart($s_time, $db_handle);


            //====================================================================
            //	CHECK THAT THE USERNAME IS NOT TAKEN
            //====================================================================

            $SQL = "SELECT * FROM care_users WHERE login_id = '$uname'";
            $result = mysql_query($SQL);
            $num_rows = mysql_num_rows($result);

            if ($num_rows > 0) {
                $errorMessage = "Username already taken, please choose a different user name";
            } else {

                $SQL = "INSERT INTO care_users (name, login_id, password, personell_nr, permission, s_date, s_time, history, create_id, status, userrole, department) VALUES ($fullnameSmart, $unameSmart, md5($pwordSmart), $idnoSmart, $permissionSmart, $s_dateSmart, $s_timeSmart, '', 'Registration Form', 'normal', $userroleSmart, $departmentSmart)";

                $result = mysql_query($SQL);

                mysql_close($db_handle);

                //====================================================
                //	EMAIL THE FORM
                //====================================================

                if ($result) {
                    $to = 'jonas.rosenstok@haydom.co.tz';
                    $subject = 'New user registered - ' . $uname . ' - ' . $fullname;
                    $message = $fullname . ' has registered as follows:' . "\r\n\r\n" .
                            'Username:	' . $uname . "\r\n" .
                            'ID no:	' . $idno . "\r\n" .
                            'Role:	' . $userrole . "\r\n" .
                            'Department:	' . $department . "\r\n\r\n";
                    $headers = 'From: care2x@haydom.co.tz' . "\r\n" . 'Reply-To: care2x@haydom.co.tz' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

                    mail($to, $subject, $message, $headers);
                }
                //=================================================================================
                //	IF ALL WENT WELL START THE SESSION AND PUT SOMETHING INTO THE SESSION VARIABLE CALLED login
                //	SEND USER TO A DIFFERENT PAGE AFTER SIGN UP
                //=================================================================================

                if ($result) {

                    $_SESSION['login'] = "1";
                    $_SESSION['uname'] = $uname;
                    $_SESSION['fullname'] = $fullname;

                    header("Location: success.php");
                } else {
                    $errorMessage = "Error writing to databse";
                }
            }
        } else {
            $errorMessage = "Database Not Found";
        }
    }
}
?>

<html>
    <head>
        <title>Care2x - register new user</title>


    </head>
    <body>
        <br><br><br>
        <table width=400 border=0 cellspacing=0 cellpadding=0>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
            <center><img src="../gui/img/common/default/haydom.png"></center><br><br>
            <p><b>Use this form to register a new user for Care2x. </b></p>
            <br>
            <p><i>All fields must be filled correctly.</i></p>
            <p><font color=red><?PHP print $errorMessage; ?></font></p>
            <FORM NAME ="form1" METHOD ="POST" ACTION ="index.php">
                <table border=0 cellspacing=0 cellpadding=0>
                    <tr>
                        <td>Staff ID no:</td>
                        <td><INPUT TYPE = 'TEXT' Name ='idno'  value="<?PHP print $idno; ?>" maxlength="5"><font color=red><?PHP print $idnoErr; ?></font></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>First name:</td>
                        <td><INPUT TYPE = 'TEXT' Name ='firstname'  value="<?PHP print $firstname; ?>" maxlength="30"><font color=red><?PHP print $firstnameErr; ?></font></td>
                    </tr>
                    <tr>
                        <td>Second Name:</td>
                        <td><INPUT TYPE = 'TEXT' Name ='secondname'  value="<?PHP print $secondname; ?>" maxlength="30"><font color=red><?PHP print $secondnameErr; ?></font></td>
                    </tr>
                    <tr>
                        <td>Last Name:</td>
                        <td><INPUT TYPE = 'TEXT' Name ='lastname'  value="<?PHP print $lastname; ?>" maxlength="30"><font color=red><?PHP print $lastnameErr; ?></font></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>Function:</td>
                        <td><select Name ='userrole'>
                                <option value="">Please select...</option>
                                <option <?php if ($userrole == 'Doctor') {
    echo("selected");
} ?> value="Doctor">Doctor</option>
                                <option <?php if ($userrole == 'Nurse') {
    echo("selected");
} ?> value="Nurse">Nurse</option>
                                <option <?php if ($userrole == 'MedAtt') {
    echo("selected");
} ?> value="MedAtt">Medical Attendant</option>
                                <option <?php if ($userrole == 'C2xClerk') {
    echo("selected");
} ?> value="C2xClerk">Care2x clerk</option>
                                <option <?php if ($userrole == 'Cashier') {
    echo("selected");
} ?> value="Cashier">Cashier</option>
                                <option <?php if ($userrole == 'Lab') {
    echo("selected");
} ?> value="Lab">Laboratory</option>
                                <option <?php if ($userrole == 'Radio') {
    echo("selected");
} ?> value="Radio">Radiology</option>
                                <option <?php if ($userrole == 'Pharmacy') {
    echo("selected");
} ?> value="Pharmacy">Pharmacy</option>
                            </select><font color=red><?PHP print $userroleErr; ?></font>
                        </td>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>Current Department:</td>
                        <td><select Name ='department'>
                                <option value="">Please select...</option>
                                <option <?php if ($department == 'Reception') {
    echo("selected");
} ?> value="Reception">Reception</option>
                                <option <?php if ($department == 'General Ward') {
    echo("selected");
} ?> value="General Ward">General Ward</option>
                                <option <?php if ($department == 'Surgical Ward II') {
    echo("selected");
} ?> value="Surgical Ward II">Surgical Ward II</option>
                                <option <?php if ($department == 'ICU') {
    echo("selected");
} ?> value="ICU">ICU</option>
                                <option <?php if ($department == 'Theatre') {
    echo("selected");
} ?> value="Theatre">Theatre</option>
                                <option <?php if ($department == 'Old Ward') {
    echo("selected");
} ?> value="Old Ward">Old Ward</option>
                                <option <?php if ($department == 'TB Ward') {
    echo("selected");
} ?> value="TB Ward">TB Ward</option>
                                <option <?php if ($department == 'Maternity Ward') {
    echo("selected");
} ?> value="Maternity Ward">Maternity Ward</option>
                                <option <?php if ($department == 'Lena Ward') {
    echo("selected");
} ?> value="Lena Ward">Lena Ward</option>
                                <option <?php if ($department == 'Amani Ward') {
    echo("selected");
} ?> value="Amani Ward">Amani Ward</option>
                                <option <?php if ($department == 'Mortuary') {
    echo("selected");
} ?> value="Mortuary">Mortuary</option>
                                <option <?php if ($department == 'Laboratory') {
    echo("selected");
} ?> value="Laboratory">Laboratory</option>
                                <option <?php if ($department == 'Pharmacy') {
    echo("selected");
} ?> value="Pharmacy">Pharmacy</option>
                                <option <?php if ($department == 'Radiology') {
    echo("selected");
} ?> value="Radiology">Radiology</option>
                                <option <?php if ($department == 'OPD') {
    echo("selected");
} ?> value="OPD">OPD</option>
                                <option <?php if ($department == 'RCHS') {
    echo("selected");
} ?> value="RCHS">RCHS</option>
                                <option <?php if ($department == 'Dental') {
    echo("selected");
} ?> value="Dental">Dental</option>
                                <option <?php if ($department == 'Eye Clinic') {
    echo("selected");
} ?> value="Eye Clinic">Eye Clinic</option>
                                <option <?php if ($department == 'Diabetic Clinic') {
    echo("selected");
} ?> value="Diabetic Clinic">Diabetic Clinic</option>
                                <option <?php if ($department == 'Physiotherapy') {
    echo("selected");
} ?> value="Physiotherapy">Physiotherapy</option>
                                <option <?php if ($department == 'Billing') {
    echo("selected");
} ?> value="Billing">Billing</option>
                                <option <?php if ($department == 'Administration') {
    echo("selected");
} ?> value="Administration">Administration</option>
                                <option <?php if ($department == 'Other') {
    echo("selected");
} ?> value="Other">Other</option>
                            </select><font color=red><?PHP print $departmentErr; ?></font>
                        </td>
                    </tr>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td width=200>Care2x Username:</td>
                        <td><INPUT TYPE = 'TEXT' Name ='username' value="<?PHP print $uname; ?>" maxlength="20"><font color=red><?PHP print $unameErr; ?></font></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>Password: </td>
                        <td><INPUT TYPE = 'password' Name ='password' maxlength="16"><font color=red><?PHP print $pwordErr; ?></font></td>
                    </tr>
                    <tr>
                        <td>Password (again): </td>
                        <td><INPUT TYPE = 'password' Name ='password2' maxlength="16"><font color=red><?PHP print $pword2Err; ?></font></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>Tick the box to signify that you have filled the form truthfully and completely.</td>
                        <td><INPUT type="checkbox" name='checkbox' value='checked'><font color=red><?PHP print $checkboxErr; ?></font></td>
                    </tr>
                </table>

                <P align=right>
                    <INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Register">
                </p>
            </FORM>
        </td>
    </tr>
</table>
</body>
</html>