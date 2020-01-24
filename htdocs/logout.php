<?php
require_once("init.php");
echo "You're being logged out and redirected";
if(isset($_SESSION['id'])){
	global $connect;
    $id=$_SESSION['id'];
    date_default_timezone_set('Europe/Paris');
    $date=date('Y-m-d');
    $hour=date('H');
    if (isCurrentlyBooked()) {//checks that the user is connected and able to use the lab
        resetExperiment();
    }
}
if($_GET["case"]="logout"){
    unset($_SESSION['id']);
    unset($_SESSION['login']);
    header('Location: index.php');
}
?>
