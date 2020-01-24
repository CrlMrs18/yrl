<?php
/*******************************************************************
 * TEMPLATE
 * Use this page when you want to create a new page on the website.
 *******************************************************************/

require_once "init.php";
checkTopMenu();
generateHead(Config::getName());
checkConnection();
checkAdmin();
$admin_msg="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mailadmin=$_POST["mailadmin"];
    $mailsuper=$_POST["mailsuper"];
    if ($mailadmin) {
        $admin=setAdmin($mailadmin);
        if ($admin) {
            $admin_msg=$mailadmin._(" was successfully added to the administrators !");
        }
        else {
            $admin_msg=_("There was an error adding ").$mailadmin._(" to the administrators.");
        }
    }

    elseif ($mailsuper) {
        $super=setSuperUser($mailsuper);
        if ($super) {
            $super_msg=$mailsuper._(" was successfully added to the Super Users !");
        }
        else {
            $super_msg=_("There was an error adding ").$mailsuper._(" to the Super Users.");
        }
    }
}



?>

<body>
<?php generateTitle();?>

<table align="center" border="0" width="100%">
    <tr>
        <!-- Add an admin-->
        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
            <td align="center" valign="top" width="40%">
                <font size="+3">
                    <strong>
                        <U>
                            <?=_("Add an admin")?>
                        </U>
                    </strong>
                </font>
                <br>
                <br>
                <a align="center">
                    <?=_("Can access to this page. Is also a super user.")?>
                </a>
                <br>
                <br>
                <table align="center" border="0" width="100%">
                    <tr>
                        <td align="center" width="*"><strong><?= _("Mail")?> : </strong><input type="email" name="mailadmin" value="<?=$mailadmin?>" size="30" required="true"/></td>
                    </tr>
                </table>
                <br>
                <table align="center" border="0" width="100%">
                    <tr>
                        <td align="center" width="50%">
                            <strong><input type="submit" value=<?= _("Validate")?>></strong>
                        </td>
                    </tr>
                    <tr><td align="center" width="50%">
                            <br>
                            <?=$admin_msg?>
                        </td>
                    </tr>
                </table>
            </td>
        </form>

        <!-- Add a superUser -->
        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
            <td align="center" valign="top" width="40%">
                <font size="+3">
                    <strong>
                        <U>
                            <?=_("Add a Super User")?>
                        </U>
                    </strong>
                </font>
                <br>
                <br>
                <a align="center">
                    <?=_("Has an infinite number of slots.")?>
                </a>
                <br>
                <br>
                <table align="center" border="0" width="100%">
                    <tr>
                        <td align="center" width="*"><strong><?= _("Mail")?> : </strong><input type="email" name="mailsuper" value="<?=$mailsuper?>" size="30" required="true"/></td>
                    </tr>
                </table>
                <br>
                <table align="center" border="0" width="100%">
                    <tr>
                        <td align="center" width="50%">
                            <strong><input type="submit" value=<?= _("Validate")?>></strong>
                        </td>
                    </tr>
                    <tr><td align="center" width="50%">
                            <br>
                            <?=$super_msg?>
                        </td>
                    </tr>
                </table>
            </td>
        </form>
    <tr>
</table>



<footer class="bd-footer">
    <p class="mt-5 mb-3 text-muted text-center">PSC fentes d'Young &copy;2019</p>
</footer>
</body>

