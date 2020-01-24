<?php
require_once "init.php";
checkTopMenu();
checkConnection();
generateHead(Config::getName()." (welcome)");

//This page is where the user is redirected after logging in.
//This page is called by "connect($id)", in "init.php".
//You can also directly change the adress of redirection in "config($id)".

//Redirection :
header('Location: index.php');
//currently the user is redirected to the website where he can control the experience,
//but he'll be soon redirected into the reservation system
?>
<body>
    <?php 
        generateTitle(Config::getName());
    ?>
    <div lang='en'>
        This page can be used to direct the user to the remote lab if available, or to make a reservation for future use.
    </div>
    <div lang='fr'>
        Cette page permet de rediriger l'utilisateur vers le remote lab si celui-ci est disponible, ou encore d'effectuer une réservation pour un usage ultérieur.
    </div>

<?=Config::getNotice()?>

</body>