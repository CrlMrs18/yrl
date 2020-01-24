<?php
require_once "init.php";
already_connected();

$lastname = "";
$firstname = "";
$mail = "";
$mail_style = "";
$name_style = "";
$show_name = true;
$msg = "";
$actionFile = $_SERVER["PHP_SELF"];

// Check if mail was specified in url
if (isset($_GET['mail'])) {
    $name_style = "style='display: none'";
    $show_name = false;
    $mail = $_GET['mail'];
    $actionFile .= "?mail";
    if (strlen($mail)>0)
        $actionFile .= "=$mail";
}

if (checkTopMenu()) { // Check top menu action
    // No further processing needed
}
else if($_SERVER["REQUEST_METHOD"] == "POST"){ // Check if form has been submitted
    $lastname = trim($_POST["lastname"]);
    $firstname = trim($_POST["firstname"]);
    $mail = $_POST["mail"];
    if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $id = getIdFromMail($mail);
        if (($id==0)&&(!$show_name)) { // Cannot reset password if not registered yet
            $error_msg = sprintf(_("User %s is not yet registered in database. Please sign up."),$mail);
            // Display full form
            $show_name = true;
            $name_style = "";
            $actionFile = $_SERVER["PHP_SELF"];
        } else {
            if ($id>0) { // Old user. Update data.
                if ($show_name) {
                  // Let the user know that we know he/she was already registered in database despite signing up again
                  $msg = _("This e-mail address was already registered in")." ".Config::getName().". ";
                  // Update lastname and firstname
                  updateUserData($id, $lastname, $firstname);
                }
            } else { // New user. Insert in database
                insertNewUser($mail, $lastname, $firstname);
                $id = getIdFromMail($mail);
                if ($id>0)
                    $msg = _("Thank you for signing up to")." ".Config::getName().".";
                else {
                    $error_msg = _("An error occured. Please contact")
                                .getWebmasterMailUrl(_("Unexpected error"));
                }
            }
            $error_msg = sendPasswordMail($id);
            if (strlen($error_msg)==0) {
                $msg .= _("The relevant information for initializing your new password has been sent to the e-mail address below :")
                        ."<br /><br /><CENTER><B>$mail</B></CENTER><br />"
                        ._("If you don't get this e-mail, please contact ")
                        .getWebmasterMailUrl(_("e-mail for new password not received"))
                        ."<hr>\n";
            }
        }
    } else {
        $mail_style = "style=\"border-color:red\"";
        $error_msg = _("Please enter a valid e-mail address");
    }
}

generateHead(Config::getName()." (sign up)");
?>

<body>
<?php

if ($show_name)
    generateTitle(Config::getName(),_("Sign up form"));
else
    generateTitle(Config::getName(),_("Password reset form"));
if (strlen($msg)>0) {
    echo $msg;
    exit;
}

?>
    <center>
        <form action="<?=$actionFile?>" method="post">
            <table align="center" border="0" >
                <tr <?=$name_style?>>
                    <td align="right" width="50%"><strong><?=_("Last name")?> :</strong></td>
                    <td align="left" width="*"><input type="text" name="lastname" size="30" value="<?=$lastname?>"/></td>
                </tr>
                <tr <?=$name_style?>>
                    <td align="right" width="50%"><strong><?=_("First name")?> :</strong></td>
                    <td align="left" width="*"><input type="text" name="firstname" size="30" value="<?=$firstname?>"/></td>
                </tr>
                <tr>
                    <td align="right" width="50%"><strong><?=_("E-mail address")?> :</strong></td>
                    <td align="left" width="*"><input type="text" name="mail" size="30" value="<?=$mail?>" <?=$mail_style?>/></td>
                </tr>
            </table>
            <br>
            <input type="submit" class="btn btn-primary" value="OK">
        </form>
     </center>
<?=Config::getNotice()?>
</body>