<?php
require_once "init.php";
checkConnection();
checkBooking();
if(!($_GET['message'])){exit("You have no business here mate troll");}
$file="task.txt";
$msg=$_GET['message'];
if($msg=="5"){
    file_put_contents($file,$msg);
    $param_1=$_GET['param_1'];
    $param_2=$_GET['param_2'];
    $param_3=$_GET['param_3'];
    $param_4=$_GET['param_4'];
    $param_5='1';

    //Restrictions on the parameters of an acquisition
    global $maxAcquisition;
    global $maxExposure;
    global $maxThreshold;
    global $minThreshold;
    if ($param_1>$maxAcquisition) {
        $param_1 = $maxAcquisition;
    }
    if ($param_4>$maxThreshold) {
        $param_3=$maxThreshold;
    }
    if ($param_4<$minThreshold) {
        $param_3 = $minThreshold;
    }
    if ($param_3>$maxExposure) {
        $param_3=$maxExposure;
    }

    $param=$param_1."\n".$param_2."\n".$param_3."\n".$param_4."\n".$param_5;
    $file="param.txt";
    file_put_contents($file,$param);
    $file=fopen("uploads/coordonnees.txt",'w');
    fwrite($file,'');
    fclose($file);
    echo "task 5";
}elseif($msg=="7"){
    file_put_contents($file,$msg);
    $param_1=$_GET['param_1'];
    $param_2=$_GET['param_2'];
    $param_3=$_GET['param_3'];
    $param_4=$_GET['param_4'];
    $param_5='0';
    $param=$param_1."\n".$param_2."\n".$param_3."\n".$param_4."\n".$param_5;
    $file="param.txt";
    file_put_contents($file,$param);
    echo "task 0"; //Stops the experiment through the "param.txt" file
}else{
    file_put_contents($file,$msg);
    echo "the rest";
}
?>