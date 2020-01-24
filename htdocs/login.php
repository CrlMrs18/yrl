<?php
require_once "init.php";
already_connected();

$mail = "";
$password = "";

if (checkTopMenu()) { // Check top menu action
    // No further processing needed
}
else if($_SERVER["REQUEST_METHOD"] == "POST"){ // Check if form has been submitted
    $mail = $_POST["mail"];
    $password = $_POST["password"];
    $id = getIdFromMail($_POST["mail"]);
    if ($id==0) { // Not found in database
        if ((strlen($mail)==0)||(strlen($password)==0))
            $error_msg = _("Please enter your e-mail address and passord in the form below.");
        else
            $error_msg = sprintf(_("User %s not found in database. Please sign up."),$mail);
    }
    else if (checkPassword($id,$password)) {
        connect($id);
    } else
        $error_msg =_("Invalid password");
}

generateHead("Young remote lab");
?>
<body>
    <?php generateTitle(Config::getName());?>
    <center>
        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
            <table border="6" bgcolor="#D0D0D0">
                <tr>
                    <td align="left">
                        <table align="center" border="0" width="100%">
                            <tr>
                                <td align="right" width="40%"><strong><?= _("Login")?> :</strong></td>
                                <td align="left" width="*"><input type="text" name="mail" size="30" value="<?=$mail?>"/></td>
                            </tr>
                            <tr>
                                <td align="right" width="40%"><strong><?= _("Password") ?> :</strong></td>
                                <td align="left" width="*"><input type="password" name="password" size="30" value="<?=$password?>"/></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <br>
            <input type="submit" value="<?= _("Log in")?>">
        </form>
     </center>
<hr>
<div lang="en">
If you do not have an account already, please <a href="signup.php">register</a>.<br/>
If you already have an account but forgot your password, please <a href="signup.php?mail">click here</a> to generate a new password.
</div>
<div lang="fr">
Si vous ne disposez pas déjà d'un compte, veuillez <a href="signup.php">vous inscrire</a>.<br/>
Si vous avez déjà un compte mais que vous avez oublié votre mot de passe, veuiller <a href="signup.php?mail">cliquer ici</a> pour définir un nouveau mot de passe.
</div>
<?=Config::getNotice()?>
</body>