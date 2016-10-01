<?PHP
session_start();
if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
    header("Location: index.php");
}

date_default_timezone_set('Africa/Dar_es_Salaam');
$uname = $_SESSION['uname'];
$fullname = $_SESSION['fullname'];
?>

<html>
    <head>
        <title>Registered for Care2x</title>
    </head>
    <body>
        <br><br><br>
        <table width=400 border=0 cellspacing=0 cellpadding=0>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>
            <center><img src="../gui/img/common/default/haydom.png"></center><br><br>
            <p><b>CARE2X USER CREATED</b><br></p>
            <br>
            You have successfully created the user <b>"<?PHP print $uname; ?>"</b> for <b><?PHP print $fullname; ?></b> to use Care2x. You can now log-in to Care2x by <a href=http://care2x.haydom.co.tz>clicking here</a>.<br>
            <br>
            <b>If you have any questions:</b> Please turn to the IT department.<br>
            <br>
            <i><a href="./index.php">Click here to register another user.</a>
                </td></tr>
        </table>
    </body>
</html>