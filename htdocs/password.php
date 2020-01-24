<?php
require_once "init.php";

$pw1 = "";
$pw2 = "";
$pw1_style = "";
$pw2_style = "";
$mail = "";
$key_error = true;

// Check top menu action
$topMenuActivated = checkTopMenu();

// Check if key is valid
if (!isset($_GET['key'])) {
    $error_msg = _("No key provided for resetting password.");
} else {
    $key = $_GET['key'];
    $id = getIdFromPasswordKey($key, 0, true);
    if ($id == 0) {
        $id = getIdFromPasswordKey($key, 1, false);
        if ($id) {
            $mail = getMailFromId($id);
            $error_msg = _("This password key has already been used. ");
        } else {
            $id = getIdFromPasswordKey($key, 0, false);
            if ($id) {
                $error_msg = _("This password key is too old.");
                $mail = getMailFromId($id);
            } else {
                $error_msg = _("This password key is not valid.");
            }
        }
        if (strlen($mail)>0)
            $param = "?mail=$mail";
        else
            $param = "";
        $error_msg .= " ".sprintf(_("Please %s request a new key%s"),"<a href=signup.php$param>","</a>.");
    } else {
        $mail = getMailFromId($id);
        if ($mail==null) {
            $error_msg = _("An error has occurred ...");
            $mail = "";
        } else // Key is OK
            $key_error = false;
    }
}

// Check if form has been submitted
if((!$topMenuActivated) && (!$key_error) && ($_SERVER["REQUEST_METHOD"] == "POST")){ 
    $pw1 = $_POST["password1"];
    $pw2 = $_POST["password2"];
    $error_msg = checkPasswordValid($pw1);
    if (strlen($error_msg)!=0) // Password entry not valid
        $pw1_style = "style=\"border-color:red\"";
    else if ($pw1!=$pw2) { // Password entries not identical
        $pw2_style = "style=\"border-color:red\"";
        $error_msg = _("The two password entries must be identical");
    } else { // Password OK
        setPassword($id,$pw1);
        markKeyAsUsed($id, $key);
        connect($id,$mail); // Connect and go to welcome page
        exit;
    }
}

generateHead("Young remote lab (password)");

?>
<body>
    <?php
         generateTitle(Config::getName(),_("Password form"));
         if ($key_error) {
             echo "</body>"; // End of page
             exit;
         }
    ?>
    <?=_("Please enter a password of at least 8 characters containing at least one letter and one digit.")?>
    <center>
        <form action="<?= $_SERVER["PHP_SELF"]."?key=".$key; ?>" method="post" autocomplete="off">
            <table align="center" border="0" width="100%">
                <tr>
                    <td align="right" width="40%"><strong><?=_("E-mail address")?> :</strong></td>
                    <td align="left" width="*"><input type="text" name="username" size="30" disabled value="<?=$mail?>"/></td>
                </tr>
                <tr>
                    <td align="right" width="30%"><strong><?=_("New password")?> :</strong></td>
                    <td align="left" width="*"><input type="password" name="password1" size="30" value="<?=$pw1?>" <?=$pw1_style?> autocomplete="off"/></td>
                </tr>
                <tr>
                    <td align="right" width="30%"><strong><?=_("Check password")?> :</strong></td>
                    <td align="left" width="*"><input type="password" name="password2" size="30" value="<?=$pw2?>" <?=$pw2_style?> autocomplete="off"/></td>
                </tr>
            </table>
            <br>
            <input type="submit" value="OK">
        </form>
     </center>
<?=Config::getNotice()?>
</body>