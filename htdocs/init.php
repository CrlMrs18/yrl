<?php
// Management of a user database and internationalization with php/mysqli/gettext
// (c) 2019 Manuel Joffre & PSCX2018/2017

// Start session for storing connection data
session_start(); 

// database and website configuration
require_once "config.php";

// Internationalization in the php code is achieved using the gettext function
// Translated files must be updated using poedit in each appropriate folder
// In html, the text is repeated in different div sections with lang set to the appropriate language
$supported_languages = array('en','fr');

//Restrictions in the parameters of an acquisition
$maxAcquisition=2000;
$maxExposure=100;
$minThreshold=100;
$maxThreshold=200;
detectDefaultLanguage(); // Detect language from browser if not in session variable
initializeLocale();

// Attempt connection to database
$connect = new PDO("mysql:host=".DB_SERVER."; dbname=".DB_NAME,DB_USERNAME, DB_PASSWORD);
 
// Check connection
if($connect == ''){
    echo "Unable to connect to database...<br>";
    echo "ERROR message : " . $connect->errorInfo();
    die("Connection error: " . $connect->errorInfo());
}
$connect-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Global variables
$error_msg = ""; // Error message to be displayed on top of page when needed

/******************************************************************************
 *                       General-purpose functions                            *
 ******************************************************************************/
// Generate a random string of specified length consisting of a mixture of
// capital letters and numerical digits
function generateRandomString($length) {
  // Mix things up
  $n = mt_rand(50,200);
  for ($i=0; $i<$n; $i++)
    mt_rand(0,100000);
  // Define char set
  $charset = str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
  // Generate random string from char set
  $s = "";
  for ($i=0; $i<$length; $i++)
    $s .= $charset[(mt_rand(0,(strlen($charset)-1)))];
  return $s;
}

// Check if password is robust enough
// Return empty string if OK, error message otherwise
function checkPasswordValid($pwd) {
    $error_msg = "";
    if (strlen($pwd) < 8)
        $error_msg = _("The password should contain at least 8 characters.");
    else if (!preg_match("@[0-9]@", $pwd))
        $error_msg = _("The password should include at least one number.");
    else if (!preg_match("@[a-zA-Z]@", $pwd))
        $error_msg = _("The password should include at least one letter.");
    return $error_msg;
}

// Convert date from php to sql
function getSqlDate($php_date) {
  return date('Y',$php_date)."-".date('m',$php_date)."-".date('d',$php_date)." "
        .date('H',$php_date).":".date('i',$php_date).":".date('s',$php_date);
}

/******************************************************************************
 *                    Internationalization functions                          *
 ******************************************************************************/
// Detect default language
function detectDefaultLanguage() {
    global $supported_languages;
    if (isset($_GET['lang'])) {
        $key = array_search($_GET['lang'],$supported_languages);
        if ($key) {
            $_SESSION['lang'] = $supported_languages[$key];
        }
        else {
            $_SESSION['lang'] = 'en';
        }
    }
    if (!isset($_SESSION['lang'])) {
        $_SESSION['lang']='en'; // Defaults to English
        // Detect browser default language
        $s = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        if (strlen($s)>2) {
            $key = array_search(substr($s,0,2),$supported_languages);
            if ($key)
                $_SESSION['lang']=$supported_languages[$key];
        }  
    }
}

// Initialize locale if language is not English
function initializeLocale() {
    if ($_SESSION['lang']!='en') {
        //CAREFUL : DOES NOT WORK WITH OTHER LOCALNAMES THAN THOSE INSTALLED ON THE OSTIZAN SERVER

        //Access to the list of installed localnames with "locale -a" on ostizan

        //Install a new package with "sudo apt-get install language-pack-[2 first language characters like fr or en]-base" on the ostizan server
        //(needs root rights)
        $localeName = $_SESSION['lang']."_".strtoupper($_SESSION['lang']).".UTF-8";
        //$localeName = "fr_FR.utf8";
        setlocale(LC_ALL, $localeName);
        bindtextdomain("yrl", "./locale");
        textdomain("yrl");
        bind_textdomain_codeset($domain, 'UTF-8');
        //echo "éàèç";
    } else {
        setlocale(LC_ALL,NULL);    
        $s = _("Test");
    }
}

// Check if the form has been submitted in relation with the top menu
// If so return true, in which case no further form processing is needed
function checkTopMenu() {
    global $supported_languages;
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST["logout"])) {
            //user logged out
            //actually bypassed by logout.php
            unset($_SESSION['id']);
            unset($_SESSION['login']);
            header('Location: index.php');
            exit();
        }
        foreach ($supported_languages as $lang) {
            if (isset($_POST["lang_".$lang."_x"])) {
                $_SESSION['lang']=$lang;
                initializeLocale();
                return true;
            }
        }
    }
    return false;
}

/******************************************************************************
 *                  General-purpose database functions                        *
 *  Not injection safe ! Never use directly with user inputs !                *
 ******************************************************************************/
// Query a single item from database
function query($s) {
    global $connect;
    $result = $connect->query($s);
    if ($result && $result->rowCount()>0) {
        $row = $result->fetch();
        return $row[0];
    }
    else
        return null;
}


// Return the number of rows associated with specified sql query
function numRows($sql) {
    global $connect;
    $result = $connect->query($sql);
    if ($result)
        return $result->rowCount();
    else
        return 0;
}

/******************************************************************************
 *                 Database functions using prepared statements               *
 *                 Can be safely used directly with user inputs               *
 ******************************************************************************/
// Get id from mail
// Return 0 if mail not found
function getIdFromMail($mail) {
    global $connect;
    // make a prepared statement to avoid sql injection
    $stmt = $connect->prepare("SELECT id FROM `users` WHERE mail = ?");
    $stmt->execute(
        array(
            $mail
        )
    );
    $result = $stmt->fetchColumn();
    if ($result) {
        return $result;
    }
    else
        return 0;
}
// Get id from password key
// If $used is 0, return only unused keys
// If $checkDate is true, return only recent keys 
// Return 0 if not found
function getIdFromPasswordKey($key, $used, $checkDate) {
    global $connect;
    // make a prepared statement to avoid sql injection
    if ($checkDate) {
        $sql_time_24 = getSqlDate(time()-86400);
        $stmt = $connect->prepare("SELECT id FROM password_codes WHERE key_code = ? AND used = ? AND date>?");
        $stmt->execute(array($key, $used, $sql_time_24));
    } else {
        $stmt = $connect->prepare("SELECT id FROM password_codes WHERE key_code = ? AND used = ?");
        $stmt->execute(array($key, $used));
    }
    $result = $stmt->fetchColumn();
    if ($result) {
        return $result;
    }
    else
        return 0;
}

// Mark specified key as used
function markKeyAsUsed($id, $key) {
    global $connect;
    $stmt = $connect->prepare("UPDATE password_codes SET used='1' WHERE key_code = ? AND id = ?");
    $stmt->execute(array($key, $id));
}

// Insert new user in database
function insertNewUser($mail, $lastname, $firstname) {
    global $connect;
    // make a prepared statement to avoid sql injection
    $stmt = $connect->prepare("INSERT INTO `users` (mail,lastname,firstname) VALUES (?,?,?)");
    $stmt->execute(array($mail, $lastname, $firstname));
}

// Update user data
function updateUserData($id, $lastname, $firstname) {
    global $connect;
    // make a prepared statement to avoid sql injection UPDATE users SET password='$hashed_password' WHERE id='$id'")
    $stmt = $connect->prepare("UPDATE `users` SET lastname = ?, firstname = ? WHERE id = ?");
    $stmt->execute(array($lastname, $firstname, $id));
}

// Set password to specified string
// Store only hashed version of password to avoid hacking of raw password
// Use random salt to avoid brute force attacks with dictionaries
function setPassword($id, $password) {
    global $connect;
    $hashed_password = hash('sha256',getUserSalt($id).$password); 
    // make a prepared statement (not really needed here as user input is hashed)
    $stmt = $connect->prepare("UPDATE `users` SET password = ? WHERE id = ?");
    $stmt->execute(array($hashed_password,$id));
}


/******************************************************************************
 *                     Specific database  login functions                     *
 ******************************************************************************/
// get user salt, i.e. user-specific random string set once and for all
// If salt not set, generate a new random string and store in database
function getUserSalt($id) {
    global $connect;
    $salt = $connect->query("SELECT salt FROM `users` WHERE id = '$id'")->fetchColumn();
    if (!$salt) {
        $salt = generateRandomString(6);
        $connect->query("UPDATE `users` SET salt='$salt' WHERE id='$id'");
    }
    return $salt;
}

// Check if password is correct
function checkPassword($id, $password) {
    global $connect;
    $hashed_password = hash('sha256',getUserSalt($id).$password);
    $stored_password = $connect->query("SELECT password FROM `users` WHERE id='$id'")->fetchColumn();
    return ($stored_password and $hashed_password==$stored_password);
}

// Send an e-mail to provided address with a link for resetting password
// Return empty string if OK, error message otherwise
function sendPasswordMail($id) {
    global $connect;
    $key_code = generateRandomString(20);
    $sql_time = getSqlDate(time());
    $sql_time_24 = getSqlDate(time()-86400);
    $link = Config::getHome()."password.php?lang=".$_SESSION['lang']."&key=".$key_code;
    $msg = _("To define a new password, please click on the following link:")."<br />"
    ."<a href=\"$link\">$link</a><br /><br />"
    ._("For safety reasons, the above link is valid only for 24h and can be used only once.")."<br />";
    $mail = getMailFromId($id);
    $n = $connect->query("SELECT COUNT(*) FROM password_codes WHERE id = '$id' AND date > '$sql_time_24';")->fetchColumn();
    //Old version. Useless I guess
    //$n = numRows("SELECT key_code FROM password_codes WHERE id = '$id' AND date>'$sql_time_24';");
    if ($n>2)
        return _("Three e-mails have already been sent to this address in the last 24h. For safety reasons we cannot send you any additional e-mail before the expiration of this 24-hour delay.");
    else if ($n>1) {
        $msg.="<br /><br />";
        $msg.=_("Please note that for safety reasons, this is the last e-mail we can send you before the expiration of a 24-hour delay");
    }
    $connect->query("INSERT INTO password_codes (id,key_code) VALUES ('$id','$key_code');");

    // Set up headers according to information provided by DSI
    //what appears in the mail
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: ".Config::getName()." <".Config::getWebmasterMail().">\r\n";
    $headers .= "Reply-To: <".Config::getWebmasterMail().">\r\n";

    //e-mail used to send the mail
    $param = '-f'.Config::getErrorMail();
    $subject = Config::getName()." - "._("Password initialization");
    if (mail($mail, '=?utf-8?B?'.base64_encode($subject).'?=', $msg, $headers, $param) === true) {
        return "";
    } else {
        return _("Error while sending password mail.");
    }
    return "";
}

// Return mail from id
function getMailFromId($id) {
    global $connect;
    return $connect->query("SELECT mail FROM `users` WHERE id = '$id'")->fetchColumn();
}

// Check if user is connected
// If not, redirect to login page
function checkConnection() {
    if (!$_SESSION['id']) {
        header('Location: login.php');
        exit;
    }
}

//Check if user is on login/signup page but is already connected
function already_connected() {
    if ($_SESSION['id']) {
        header('Location: redirect_after_login.php');
        exit;
    }
}

// Establish connection using session variables
function connect($id) {
    global $connect;
    $_SESSION['id'] = $id;
    $_SESSION['login'] = $connect->query("SELECT mail FROM `users` WHERE id = '$id'")->fetchColumn();
    header('Location: redirect_after_login.php');
    exit;
}

/******************************************************************************
 *                        Html generation functions                           *
 ******************************************************************************/

// Generate head section of html page
function generateHead($title) {
    global $supported_languages;

    /*
     * DEPRECATED CSS FILE
    echo "<head>
            <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
            <link rel=\"stylesheet\" href=\"style.css\" type=\"text/css\" media=\"screen\"/>\r\n
        <meta charset=\"utf-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css\">";
    */

    echo "<style>\r\n";
    foreach ($supported_languages as $lang) {
        //displays only the "div lang=[lang chosen]"
        //does not affect the _() function
        if ($lang == $_SESSION['lang'])
            echo "div:lang($lang) {display:block;}";
        else
            echo "div:lang($lang) {display:none;}";
    }
    echo "</style>\r\n";
    echo "<title>$title</title>
         </head>";
}

// Generate centered title of html page
function generateTitle($title="", $subTitle="") {
    global $error_msg;
    include("navbar.php");

    /*
     * OLD NAVBAR
    echo "<nav class=\"navbar navbar-expand-sm bg-dark navbar-dark\">
    <a class=\"navbar-brand\" href=\"index.php\">"._("Home")."</a>
    <a class=\"navbar-brand\" href=\"booking.php\">"._("Reservation")."</a>
    <a class=\"navbar-brand\" href=\"experiment.php\">"._("Experiment")."</a>
    <a class=\"navbar-brand\" href=\"spectator.php\">"._("Spectator")."</a>
    <a class=\"navbar-brand\" href=\"theory.php\">"._("Theory")."</a>";
    $id=$_SESSION['id'];
    if (isAdmin($id)) {
        echo "<a class=\"navbar-brand\" href=\"admin.php\">"._("Administration")."</a>";
    }
    echo "<div class=\"collapse navbar-collapse\" id=\"collapsibleNavbar\">
            <ul class=\"nav navbar-nav ml-auto\">";
            global $supported_languages;
            if (sizeof($supported_languages)>1) { // Show language menu
                echo "<li class=\"nav-item\">";
                echo "<form action=\"".$_SERVER["PHP_SELF"]."\" method = \"post\" > ";
                echo "<table id=\"topmenu\"><tr>";
                foreach ($supported_languages as $lang) {
                    if ($lang == $_SESSION['lang']) {
                        echo "<td style=\"border: 1px solid black\">";
                        echo "<img alt=\"$lang\" src=\"images/flag_$lang.png\" height=\"25px\"></u1>";
                    } else {
                        echo "<td style=\"width: 40px;\">";
                        echo "<input type=\"image\" name=\"lang_$lang\" alt=\"$lang\" src=\"images/flag_$lang.png\" height=\"25px\"/></u1>";
                    }
                }
                echo "</td></tr></table></form></li>";
            }

            echo "<li class=\"nav-item\">";
            if ($_SESSION['login']) {
                echo "<a class=\"nav-link\" href=\"logout.php?case=logout\">"._("Logout")."</a>";
            }
            else {
                echo "<a class=\"nav-link\" href=\"login.php\">"._("Log in")."</a>";
                echo "<li class=\"nav-item\">";
                echo "</li>";
                echo "<a class=\"nav-link\" href=\"signup.php\">"._("Register")."</a>";
            }
            echo "</li>";
            echo "</li></ul></div></nav><br>";
    */

    if (strlen($title>0)) {
        echo "<center>
            <h1><font color=#0040C0>$title</font></h1>";
    }
    if (strlen($subTitle)>0)
        echo "<h2>$subTitle</h2>";
    echo "</center>";
    if (strlen($error_msg)>0)
        echo "<font color=#FF0000>$error_msg</font><hr>";

}

// Generate link to webmaster e-mail with specified subject
function getWebmasterMailUrl($subject) {
    return "<a href=\"MAILTO:".Config::getWebmasterMail()."?subject=".Config::getName()." - $subject"
          ."\">".Config::getWebmasterMail()."</a>.";
}

/******************************************************************************
 *                              SUPERUSER PART                                *
 ******************************************************************************/

function isSuperUser($id) {
    global $connect;
    return $connect->query("SELECT superuser FROM users WHERE id='$id'")->fetchColumn();
}

function setSuperUser($mail) {
    global $connect;
    //made a prepared statement to avoid sql injections
    $stm=$connect->prepare("UPDATE users SET superuser=1 WHERE mail= ? ");
    $stm->execute(array($mail));
    return isSuperUser(getIdFromMail($mail));
}

/******************************************************************************
 *                                ADMIN PART                                  *
 ******************************************************************************/

function isAdmin($id) {
    global $connect;
    return $connect->query("SELECT admin FROM users WHERE id='$id'")->fetchColumn();
}

function checkAdmin() {
    $id=$_SESSION['id'];
    if (!isAdmin($id)) {
        header('Location: welcome.php');
    }
}

function setAdmin($mail) {
    global $connect;
    //made a prepared statement to avoid sql injections
    $stm=$connect->prepare("UPDATE users SET superuser=1, admin=1 WHERE mail = ?");
    $stm->execute(array($mail));
    return isAdmin(getIdFromMail($mail));
}

/******************************************************************************
 *                              BOOKING SYSTEM                                *
 ******************************************************************************/

//Checks the actual number of bookings of the user
function number_booking($id){
    global $connect;
    return $connect->query("SELECT nbr_booking FROM users WHERE id = $id")->fetchColumn();
}

//Returns an array containing the list of slots already booked by the user
function list_booked($id) {
    global $connect;
    $slots = $connect->query("SELECT date, hour FROM schedule WHERE id = '$id'")->fetchAll();
    //Now we want to sort this array by time
    function compare($arr1, $arr2) { //Returns  boolean (time2-time1 > 0)
        if (DateTime::createFromFormat('Y-m-d', $arr2['date'])>DateTime::createFromFormat('Y-m-d', $arr1['date'])) {
            return true;
        }
        elseif ($arr2['date']==$arr1['date']) { //Same day
            return ($arr2['hour']>$arr1['hour']);
        }
        else {
            return false;
        }
    }
    function insertSort($sorted_array) {
        for ($i=1; $i<count($sorted_array); $i+=1) {
            $v=$sorted_array[$i];
            $j=$i;
            while ($j>0 and compare($v, $sorted_array[$j-1])) {
                $sorted_array[$j] = $sorted_array[$j-1];
                $j-=1;
            }
            $sorted_array[$j]=$v;
        }
    return $sorted_array;
    }
    return insertSort($slots);
}



//For a specific day, returns an array with 24 entries, where the index is the hour and the value is:
// - 1 when the hour is not booked
// - 0 when the slot is already booked
function list_slots($date) {
    global $connect;
    // make a prepared statement to avoid sql injection
    $booked = $connect->prepare("SELECT hour FROM schedule WHERE date = ?");
    $booked->execute(array($date));
    $booked = $booked->fetchAll(PDO::FETCH_COLUMN, 0);
    $list_slots = array();
    for ($i=0; $i < 24; $i++) {
        $list_slots[$i] = 1;
    }
    foreach($booked as $value) {
        $list_slots[$value]=0;
    }
    return $list_slots;
}

//Checks if a slot is booked
function is_booked($date, $hour) {
    global $connect;
    // make a prepared statement to avoid sql injection
    $booked = $connect->prepare("SELECT id FROM schedule WHERE date = ? AND hour = ?");
    $booked->execute(array($date, $hour));
    return (bool) $booked->fetchColumn();
}

//Makes a reservation
function book($id, $date, $hour) {
    //we have to check if 0<=$hour<24 before
    global $connect;
    // make a prepared statement to avoid sql injection
    $update = $connect->prepare("INSERT INTO schedule (id,date,hour) VALUES (?,?,?)");
    $update -> execute(array($id, $date, $hour));
    $nbBooking = number_booking($id)+1;
    $connect->query("UPDATE users SET nbr_booking='$nbBooking' WHERE id = $id");
}

//Unbooks a reservation
function unbook($id, $date, $hour) {
    global $connect;
    //no need to avoid sql injection
    $connect->query("DELETE FROM schedule WHERE id = '$id' AND date = '$date' AND hour = '$hour'");
    $nbBooking = number_booking($id)-1;
    $connect->query("UPDATE users SET nbr_booking='$nbBooking' WHERE id = $id");
}

//Table shedule needs a daily update in order to remove old reservations
function update_schedule() {
    global $connect;
    date_default_timezone_set('Europe/Paris');
    $actual_date = substr(getSqlDate(time()), 0, 10);
    //Removes the hour part of the date
    //otherwise, it does not compares well with the sql 'date'
    //$update = $connect->query("DELETE FROM schedule WHERE '$actual_date' >= date");
    $update = $connect->query("SELECT id, date, hour FROM schedule WHERE '$actual_date' > date")->fetchAll();
    foreach ($update as $line) {
        unbook($line['id'], $line['date'], $line['hour']);
    }
}

//Checks if 2 ids have the same slot just after making a reservation
function well_booked($date, $hour) {
    global $connect;
    $stmt = $connect -> prepare("SELECT COUNT(*) FROM schedule WHERE date=? AND hour=?");
    $stmt ->execute(array($date, $hour));
    $n = $stmt->fetchColumn();
    return $n==1;
}

//Sends a confirmation e-mail when booking a slot
function booking_mail($id, $date, $hour) {
    if (isSuperUser($id)) {
        //no email is sent for the superusers
        return _("Your slot has been successfully registered !");
    }
    $mail = getMailFromId($id);
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: ".Config::getName()." <".Config::getWebmasterMail().">\r\n";
    $headers .= "Reply-To: <".Config::getWebmasterMail().">\r\n";

    //e-mail used to send the mail
    $param = '-f'.Config::getErrorMail();
    $subject = Config::getName()." - "._("Reservation confirmed");
    $sdate=(string) $date;

    $msg = "Your reservation of the Young Remote Lab on ".$sdate.", ".(string) $hour."h-".(string) (($hour+1)%24)."h is registered.<br/><br/>Thanks for using our lab !";
    if (mail($mail, '=?utf-8?B?'.base64_encode($subject).'?=', $msg, $headers, $param) === true) {
        return _("Your slot has been successfuly registered ! A confirmation has been sent to your e-mail.");
    }
    else {
        return _("<p style='color:red'>Error while sending reservation confirmation mail.</p>");
    }
}

//(Sends a confirmation e-mail when unbooking a slot)
//just sends a message on the website to avoid spam
function unbooking_mail($id, $date, $hour) {
    return _("Your slot has been successfully unbooked.");
    /*
    if (isSuperUser($id)) {
        //no email is sent for the superusers
        return _("Your slot has been successfully unbooked.");
    }
    $mail = getMailFromId($id);
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: ".Config::getName()." <".Config::getWebmasterMail().">\r\n";
    $headers .= "Reply-To: <".Config::getWebmasterMail().">\r\n";

    //e-mail used to send the mail
    $param = '-f'.Config::getErrorMail();
    $subject = Config::getName()." - "._("Unbooking confirmed");
    $sdate=(string) $date;

    $msg = "Your reservation of the Young Remote Lab on ".$sdate.", ".(string) $hour."h-".(string) (($hour+1)%24)."h is cancelled.<br/><br/>See you soon !";
    if (mail($mail, '=?utf-8?B?'.base64_encode($subject).'?=', $msg, $headers, $param) === true) {
        return _("Your slot has been successfuly unbooked. A confirmation has been sent to your e-mail.");
    } else {
        return _("<p style='color:red'>Error while sending unbooking confirmation mail.</p>");
    }
    */
}
//Checks if the user is connected for his slot
function checkBooking() {
    global $connect;
    $id=$_SESSION['id'];
    date_default_timezone_set('Europe/Paris');
    $date=date('Y-m-d');
    $hour=date('H');
    if (!($connect->query("SELECT id FROM schedule WHERE id='$id' AND date='$date' AND hour='$hour'")->fetchColumn())) {
        header('Location: booking.php');
        exit();
    }
}

function isCurrentlyBooked() {
    global $connect;
    $id=$_SESSION['id'];
    date_default_timezone_set('Europe/Paris');
    $date=date('Y-m-d');
    $hour=date('H');
    return is_booked($date, $hour);
}

//checks if the next slot is booked by the current user
function is_booked_after() {
    date_default_timezone_set('Europe/Paris');
    $date=date('Y-m-d');
    $hour=date('H')+1;
    return is_booked($date, $hour);
}

//If someone calls init.php and wants to execute is_booked (by using GET method),
//it returns the result of the function is_booked
if($_GET["is_booked_after"]) {
    if(is_booked_after()) {
        echo "true";
    }
    else {
        echo "false";
    }
}

/******************************************************************************
 *                                EXPERIMENT                                  *
 ******************************************************************************/

//returns an array containing led color, exposure time
function getParameters() {

}

function resetExperiment() {
    $file=fopen("uploads/coordonnees.txt",'w');
    fwrite($file,'');
    fclose($file);
    $file=fopen("task.txt",'w');//orders the lab server to switch off the LEDs
    fwrite($file,'0');
    fclose($file);
}
?>