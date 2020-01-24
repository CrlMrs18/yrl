<?php
require_once "init.php";
generateHead(Config::getName()." (welcome)");
checkTopMenu();

$show_connect="style='display: none'";
$show_exp="";
if (!$_SESSION['id']) {
    $show_connect="";
    $show_exp="style='display: none'";
}

?>

<body>
<?php generateTitle(Config::getName());?>
<div class="container" style="margin-top:30px">
    <div class="col-sm-12 row align-items-start">
        <center><h1 style="color:black; text-shadow: 2px 2px 2px white;"><b><?=_("Quantum physics remote laboratory")?></b></h1>
            <br>
            <p style="color:black; text-shadow: 2px 2px 2px white;"><?=_("This site offers you the opportunity to perform a real remote quantum physics experiment: the Young's slit experiment in a unique photon regime. Discover the first remote lab that allows you to manipulate one of the most beautiful experience of physics, live !")?></p>
            <br>
            <table border="0" width="100%">
                <tr>
                    <td align="right">
                    <a <?=$show_connect?> display="inline" href="login.php" class="btn btn-success" style="margin : 5pt;"><?=_("Connect")?></a>
                    <a <?=$show_exp?> href="experiment.php" class="btn btn-success" style="margin : 5pt;"><?=_("Experiment")?></a>
                    </td>

                    <td align="left">
                    <a <?=$show_connect?> href="signup.php" class="btn btn-success" style="margin : 5pt;"><?=_("Register")?></a>
                    <a <?=$show_exp?> href="booking.php" class="btn btn-success" style="margin : 5pt;"><?=_("Booking")?></a>
                    </td>
                </tr>
            </table>
            <p color="black" text-shadow="2px 2px 2px white">
                <br>
                <?=_("You can manipulate the experiment by logging in an booking a slot,")?>
                <br>
                <?=_("or starting by watching the recorded results just below.")?>
            </p>
            <video width="100%" height="600" controls autoplay>
                <source src="frangesPSC.mp4" type="video/mp4">
                <?=_("Your browser does not support the video tag.")?>
            </video>
        </center>
        <div class="row align-items-start" style="width:100%;">
            <div class="col-sm-1 align-items-start" style="width:2%;"></div>
            <div class="col-sm-10 align-items-start" style="width:96%;">
                <p><?=_("This site allows you to remotely control an experimental set-up of Young's slits (see photo above), in a single photon regime. A conventional and monochromatic light source (a diode) emits light whose intensity is greatly reduced thanks to an optical density placed in the opaque PVC tube. This attenuated light then passes through a Young's double slit and reaches the EMCCD camera, and you can observe the measurement and analysis of the received light signal live.")?></p>
                <p><?=_("You can choose the wavelength of the light source, and set the following parameters:")?></p>
                <ul>
                    <li><?=_("Threshold: threshold of light intensity at which the received light signal is not noise.")?></li>
                    <li><?=_("Camera exposure time: the higher it is, the further away from the single photon regime you are.")?></li>
                </ul>
                <p><?=_("Click on the \"Blue\" or \"Red\" button to remotely turn on the LED, and perform live \"the most beautiful physics experiment\"!")?></p>
                <p><i><?=_("Do not update the page to avoid losing the information already acquired!")?></i></p>
            </div>
            <div class="col-sm-1 align-items-start" style="width:2%;"></div>
        </div>
        <center>
            <!-- OLD PICTURE DISPLAY
            <div class="container" style="margin-top:30px">
                <div class="col-sm-12 row align-items-start">
                    <canvas id="canvas"></canvas>
                </div>
            </div>
            -->
            <img src="image_accueil.png" width="100%">
            <br>
            <p style="color:black; text-shadow: 2px 2px 2px white;"><?=_("This website was built during a collective scientific project at the Ecole Polytechnique")?></p>
        </center>
        <br>
    </div>

<script>
    /*OLD PICTURE  DISPLAY
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');

    const image = new Image(512, 148); // Using optional size for image
    image.onload = drawImageActualSize; // Draw when image has loaded

    // Load an image of intrinsic size 300x227 in CSS pixels
    image.src = 'image_accueil.png';

    function drawImageActualSize() {
        // Use the intrinsic size of image in CSS pixels for the canvas element
        canvas.width = 1100;
        canvas.height = 400;

        // Will draw the image as 300x227, ignoring the custom size of 60x45
        // given in the constructor
        ctx.drawImage(this, 0, 0);

        // To use the custom size we'll have to specify the scale parameters
        // using the element's width and height properties - lets draw one
        // on top in the corner:
        ctx.drawImage(this, 0, 0,1100, 400);
    }
    */
</script>
</div>
<?=Config::getNotice()?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
</body>
</html>