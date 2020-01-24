<?php
require 'init.php';
if(isset($_GET["id"])){
    checkConnection();
    checkBooking();
    //The request comes from experiment.php, which wants to upload the figure
    $coords=file_get_contents('uploads/coordonnees.txt');

    echo $coords;
    }
elseif(isset($_POST['password'])) {
    if($_POST['password']=="ViveLesRequetesHTTP") {
        $coord=$_POST['coords'];
        file_put_contents("uploads/coordonnees.txt", $coord);
        echo "true";
        echo $coord;
    }
    else {
        echo "false";
    }

}elseif($_FILES['coords']){
    //The request comes from the lab server which wants to upload the data on the web server

    //verifier lab server
    $uploaddir = 'uploads/';
    $uploadfile = $uploaddir . basename($_FILES['coords']['name']);

    move_uploaded_file($_FILES['coords']['tmp_name'], $uploadfile);
}else{
    exit("you have no business here");
}


?>
