<?php
require_once "init.php";
require_once "navbar.php";
checkTopMenu();
checkConnection();
update_schedule();
$id=$_SESSION['id'];
//test 1 2
//used for the reservation part
$date="";
$hour="";
$show_hour="style='display: none'";
$show_date="";
$error_message="";
$show_valider="";
$show_reset="";
$show_confirmation_booking="style='display: none'";
$confirmation_booking="";
$show_confirmation_unbooking="style='display: none'";
$confirmation_unbooking="";

/**************************************************************
* used to book a slot
**************************************************************/
if (number_booking($id)>=10 and !isSuperUser($id)) { //If user already has maximum number of reservations
    $error_message = _("You already booked your maximum amount of slots.");
    $show_date = "style='display: none'";
    $show_valider = "style='display: none'";
    $show_reset = "style='display: none'";
}
elseif($_SERVER["REQUEST_METHOD"] != "POST"){
    $cyril="<p>Formulaire non posté</p>";
}
elseif($_SERVER["REQUEST_METHOD"] == "POST") { //check if form has been submitted
    $date = $_POST["date"];
    $hour = $_POST["hour"];
    if ($date and !date_create_from_format("Y-m-d", $date)) {
        //if date has been submitted but is not in the required format (yyyy-mm-dd)
        //(date_create_from_format returns false if an error happens)
        $error_message = _("Please use the required date format.");
    }
    elseif ($date) { //if date has been submitted in the right format
        $show_hour = "";
        $show_date="style='display: none'";
        if (!isset($hour)) { //check if hour has not been chosen yet. Careful : $hour can be 0.
            $slots=list_slots($date);
            $options="";
            for ($i=0; $i<24; $i++) {
                if ($slots[$i]) {
                    $options.="<option value='".$i."'>".$i."h-".(($i+1)%24)."h</option>";
                }
            }
        }
        else { //date and hour have been submitted
            if (is_booked($date, $hour)) { //if someone else booked the slot during the choice of the user
                $error_message = _("Sorry, it appears that someone else just picked this slot.");
            }
            else {
                book($id, $date, $hour);
                if (!well_booked($date, $hour)) { //checks if one user has the slot
                    unbook($id, $date, $hour);
                    $error_message = _("There has been an issue with your reservation. Please try again.");
                }
                else { //Success
                    $show_confirmation_booking = "";
                    $confirmation_booking=booking_mail($id, $date, $hour);
                    $show_hour="style='display: none'";
                    $show_valider="style='display: none'";
                }
            }
        }

    }
}

/**************************************************************
 * used to show the slots already booked and/or to unbook them
 **************************************************************/
$style_slots_booked = "";
$slots_booked = list_booked($id);
foreach($slots_booked as $line_book) { //linebook is an array representing a line of the SQL table schedule
    $date_booked=(string)$line_book["date"];
    $hour_booked=$line_book["hour"];
    $timestamp = DateTime::createFromFormat("Y-m-d", $date_booked)->getTimestamp();

    //Unbooks a slot
    if ($_SERVER["REQUEST_METHOD"] == "POST" and $_POST[$date_booked.$hour_booked."_x"]) {
        //the form has 2 values : name_x and name_y, for the xy coordinates.
        unbook($id, $date_booked, $hour_booked);
        $show_confirmation_unbooking = "";
        $confirmation_unbooking=unbooking_mail($id, $date_booked, $hour_booked);

    }
    else {
        //Shows the slots and the unbooking button
        $style_slots_booked.= "<li>".date('l, F jS Y', $timestamp).", from ".$hour_booked."h to  ".(($hour_booked+1)%24)."h";
        $style_slots_booked.="<input type=\"image\" name=".$date_booked.$hour_booked." alt="._("Unbook")." src=\"images/cross.png\" height=\"15px\"/>";
        $style_slots_booked.="</li><hr>";
    }

}
if (!$style_slots_booked) {
    $style_slots_booked = _("You do not have reserved slots yet")."<hr>";
}

generateHead(Config::getName()." (booking)");
?>

<table align="center" border="0" width="100%">
    <tr>
        <!-- Reservation part (left column)-->
        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
            <td align="center" valign="top" width="45%">
                <font size="+3">
                    <strong>
                        <U>
                            <?=_("Booking")?>
                        </U>
                    </strong>
                </font>
                <br>
                <br>
                <br>
                <table align="center" border="0" width="100%">
                    <tr <?=$show_date?>>
                        <td align="right" width="50%"><strong><?= _("Date")?> : </strong></td>
                        <td width="1%"></td>
                        <td align="left" width="*"><input type="date" name="date" size="30" value="<?=$date?>" required="true"/></td>
                    </tr>
                    <tr <?=$show_hour?>>
                        <td align="right" width="50%"><strong><?= _("Hour")?> : </strong></td>
                        <td width="1%"></td>
                        <td align="left" width="*">
                            <select name="hour">
                                <?=$options?>
                            </select>
                        </td>
                    </tr>
                </table>
                <br>
                <div <?=$show_date?>>
                    <?=_("If you use an old navigator, you may have to enter text to chose a date.")?>
                    <br>
                    <?=_("Please use the following format : yyyy-mm-dd")?>
                </div>
                <hr>
                <?=_("You can book a slot through this form. Be careful, you can only have 10 slots at the same time.")?>
                <br>
                <?=_("Pick a date, and then an hour.")?>
                <br>
                <font color=#FF0000><?=$error_message?></font>
                <hr>
                <br>
                <table align="center" border="0" width="100%">
                    <tr>
                        <td align="right" width="50%">
                            <strong><input type="submit" <?=$show_valider?> value=<?= _("Validate")?>></strong>
                        </td>
                        <td align="left" width="50%">
                            <a href="booking.php"><input type="button" <?=$show_reset?> value=<?=_("Reset form")?>> </a>
                        </td>
                    </tr>
                </table>
                <br>
                <a <?=$show_confirmation_booking?>>
                    <?=$confirmation_booking?>
                </a><hr>
            </td>
        </form>

        <td align="center" valign ="top" width="5%">

        </td>
            <!--Unbooking part (right column) -->
        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
            <td align="center" valign="top" width="45%">
                <font size="+3">
                    <strong>
                        <U>
                            <?=_("My reservations")?>
                        </U>
                    </strong>
                </font>
                <div a align="left">
                    <br>
                    <?=_("Here is a list of your reservations :")?>
                </div>
                <br>
                <strong>
                    <div a align="left">
                        <u1>
                            <br>
                            <?=$style_slots_booked?>
                        </u1>
                    </div>
                </strong>
                <a <?=$show_confirmation_unbooking?>>
                    <?=$confirmation_unbooking?>
                    <hr>
                </a>
            </td>
        </form>
    </tr>
</table>

<?=Config::getNotice()?>
