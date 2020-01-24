<?php
require_once "init.php";
checkTopMenu();
checkConnection();
checkBooking();
resetExperiment();

global $maxAcquisition;

/****************
 *     Timer
 ****************/
//timer end
$heures = 0;  // les heures < 24
$minutes = 60 - date("i") - 1;   // les minutes  < 60
$secondes = 60 - date("s") - 1;  // les secondes  < 60

$annee = date("Y");  // par defaut cette année
$mois = date("m");  // par defaut ce mois
$jour = date("d");  // par defaut aujourd'hui

// quand le compteur arrive à 0
// -> redirection
$redirection = 'index.php';

//calcul des secondes
$secondes = mktime(date("H") + $heures,
        date("i") + $minutes,
        date("s") + $secondes,
        $mois,
        $jour,
        $annee
    ) - time();
generateHead(Config::getName());
?>
<head>
    <style>
        .fakeimg {
            height: 200px;
            background: #aaa;
        }

        fieldset {
            border: 1px solid #ddd !important;
            margin: 0;
            xmin-width: 0;
            padding: 10px;
            position: relative;
            border-radius: 4px;
            background-color: #f5f5f5;
            padding-left: 10px !important;
        }

        legend {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 0px;
            width: 35%;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px 5px 5px 10px;
            background-color: #ffffff;
        }
    </style>

    <!-- JSC TIMER -->
    <script type="text/javascript">
        var temps = <?php echo $secondes;?>;
        var timer = setInterval('CompteaRebour()', 1000);

        //Part used to check if the user is connected to the next slot
        //we prefer to check this in jsc and not by using a php code which could determine the number of next slots,
        //because if the user books another slot during the hour, it will be detected (without refresh) only by a jsc code
        function createRequestObjectParSlot() {
            var ro;
            var browser = navigator.appName;
            if(browser == "Microsoft Internet Explorer"){
                ro = new ActiveXObject("Microsoft.XMLHTTP");
            }else{
                ro = new XMLHttpRequest();
            }
            return ro;
        }
        var http = createRequestObjectParSlot();
        function sendRequestSlot() {
            http.open('get', 'init.php?is_booked_after=ok');
            http.onreadystatechange = handleResponseParSlot;
            http.send(null);
        }
        function handleResponseParSlot() { //what happens when sendRequestSlot is used, after we get the result of the GET method
            if(http.readyState == 4){
                var response = http.responseText; //type : text
                if (response=="true") {//next slot also booked by the user
                    temps += 3600;
                }
                else if(response=="false") { //next slot not booked by the user
                    clearInterval(timer);
                    url = "<?php echo $redirection;?>";
                    Redirection(url);
                }

            }
        }
        //End of the "next slot is also booked" part
        //use "sendRequestSlot()" to obtain the result of this part (returns a string "false" or "true")

        function CompteaRebour() {
            temps--;
            j = parseInt(temps);
            h = parseInt(temps / 3600);
            m = parseInt((temps % 3600) / 60);
            s = parseInt((temps % 3600) % 60);
            document.getElementById('minutes').innerHTML = (h < 10 ? "0" + h : h) + '  h :  ' +
                (m < 10 ? "0" + m : m) + ' mn : ' +
                (s < 10 ? "0" + s : s) + ' s ';

            //uncomment this line to test the "next slot is also booked" part every second
            //sendRequestSlot();

            if ((s <= 0 && m<=0 && h <= 0)) {
                sendRequestSlot()
            }
        }

        function Redirection(url) {
            setTimeout("window.location=url", 500)
        }
    </script>
</head>
<body>
<?php generateTitle(); ?>
<div class="container" style="margin-top:30px" style="width:100%;">
    <div class="col-sm-12 row align-items-start" style="width:100%;">
        <div class="row align-items-start" style="width:100%;">

            <div class="col-sm-2" style="width:25%;">
                <fieldset class="form-group" style="background-color:transparent;">
                    <legend>LED</legend>
                    <div class="row align-items-start" style="width:100%;">
                        <div class="col-sm-4" style="width:20%;"></div>
                        <div class="col-sm-4" style="width:40%;">
                            <button id="2" type="button" class="btn btn-info param" value="2" onclick="wt(this.value)" align="center">
                                B
                            </button>
                        </div>
                        <div class="col-sm-4" style="width:40%;">
                            <button id="4" type="button" class="btn btn-danger param" value="4" onclick="wt(this.value)" align="center">
                                R
                            </button>
                        </div>
                    </div>
                    <br>
                </fieldset>
            </div>
            <div class="col-sm-6" style="width:50%;">
                <fieldset class="form-group" style="background-color:transparent;">
                    <legend><?= _("Parameters") ?> </legend>
                    <div class="row align-items-start" style="width:100%;">
                        <div class="col-sm-1" style="width:15%;"><p></p></div>
                        <div class="col-sm-4" style="width:40%;">
                            <center><label><?= _("Exposure (ms)") ?></label>
                                <center>
                                    <br>
                                    <center><input class="param" type="range" name="param_3" id="param_3" min="1"
                                                   max="100"></center>
                                    <!-- Exposure time. Capped at 40ms because of a bug during the transfert of data.-->

                                    <center><span id="range_1"></span>
                                        <center>
                        </div>
                        <div class="col-sm-2" style="width:15%;"><p></p></div>
                        <div class="col-sm-4" style="width:30%;">
                            <center><label><?= _("Threshold") ?> </label></center>
                            <br>
                                <center><input class="param" type="range" name="param_4" id="param_4" min="125" max="135"
                                           step="5"></center>
                            <center><span id="range_2">130</span>
                                <center>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-sm-4" style="width:25%;">
                <fieldset class="form-group" style="background-color:transparent;">
                    <legend><?= _("Time left") ?></legend>
                    <div class="col-sm-12" style="width:100%;">
                        <div class="row align-items-start" style="width:100%;" onload="timer">
                            <?php
                            // la condition est que le nombre de seconde soit etre superieur a 24 heures
                            if ($secondes <= 3600 * 24) {
                                ?>
                                <div id="minutes" style="font-size: 18px;"></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <br>
    <br>
    <div class="col-sm-12 row align-items-start" style="width:100%;">
        <div class="row justify-content-center" style="width:100%;">
            <div class="col-sm-6" style="width:50%;">
                <center><p><?= _("Image obtained on the camera screen") ?></p></center>
                <br>
                <canvas id="viewportF" width="512" height="400"></canvas>
                <br>
                <br>
                <center><p><font size="5"><b><?= _("Number of photons :") ?></b><label
                                    id="labelPhotonCount">0</label></font></p><br></center>
            </div>
            <div class="col-sm-6" style="width:50%;">
                <center><p><?= _("Impact histogram") ?></p></center>
                <br>
                <canvas id="viewportHisto" width="512" height="400"></canvas>
                <br>
                <p style="float:left;">0</p>
                <p style="float:right;">512</p>
                <br>
                <center><p><font size="5"><b><?= _("Pixel absciss") ?></b></font></p></center>
            </div>
            <table border="0" width="100%" align="center">
                <tr align="center">
                    <td align="right" width="47.5%">
                        <button id="5" type="button" class="btn btn-success" value="5"
                                onclick="wt(this.value)"><?= _("Acquire") ?></button>
                    </td>
                    <td align="center" width="5%"></td>
                    <td align="left" width="47.5%">
                        <button id="7" type="button" class="btn btn-danger" value="7"
                                onclick="wt(this.value)"><?= _("Stop") ?></button>
                    </td>
                </tr>
            </table>

            <div style="display: flex; justify-content: center; width: 100%;">
                <div>
                    <br>
                    <?= _("You can't modify the parameters during an acquisition.") ?>
                </div>
            </div>
        </div>
        <br>
        <br>
    </div>
</div>

<?= Config::getNotice() ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
<script>
    var slider = document.getElementById("param_3");
    var output = document.getElementById("range_1");
    output.innerHTML = slider.value;

    slider.oninput = function () {
        output.innerHTML = this.value;
    }
</script>
<script>
    var slider2 = document.getElementById("param_4");
    var output2 = document.getElementById("range_2");
    output.innerHTML = slider.value;

    slider2.oninput = function () {
        output2.innerHTML = this.value;
    }
</script>
<script>
    var width;    // Largeur de l'image
    var height;   // Hauteur de l'image
    var heightHisto; // Hauteur de l'histogramme
    var photonArray; // Tableau contenant le cumul du nombre d'impacts de photons
    var histoArray;  // Tableau contenant le cumul du nombre d'impacts dans chaque colonne
    var context; // Contexte graphique de l'image
    var contextHisto; // Contexte graphique de l'histogramme
    var imageData; // Image qui sera affichée sur la page web
    var photonCount = 0; // Nombre total de photons
    var maxCount = 1; // Nombre de coups maximum sur l'image
    var maxHisto = 10; // Maximum de l'échelle de l'histogramme
    var clearHisto = 0; // 1 signifie qu'il faut effacer l'histogramme
    var responsePending = 0; // 1 signifie que l'on est en attente d'un appel à handleResponse()
    var verif = 0; //variable qui nous permettra de verifier si les coordonnees present dans la page php ont ete modifies
    var verif2 = 0; //variable qui nous permettra de verifier si les coordonnees present dans la page php ont ete modifies
    var color = "red";

    // Construction de l'image à partir du nombre d'impacts
    function updateImage() {
        // Facteur de saturation
        // Quand il y a peu de coups, le pixel est saturé dès qu'il y a un photon
        // A l'inverse, quand il y a beaucoup de coups, la brillance est
        // proportionnelle au nombre de photons par pixel
        //saturationFactor = Math.max(4*Math.exp(-maxCount/100),1);

        // Boucle sur tous les pixels
        for (var x = 0; x < width; x++) {
            for (var y = 0; y < height; y++) {
                var index = (y * width + x);
                var imageIndex = 4 * index;

                // On divise par maxCount pour pouvoir visualiser plus d'un photon par pixel
                value = Math.floor(photonArray[index] * 255)//.*saturationFactor/maxCount);
                // value = Math.floor(photonArray[index]*255);
                value = 255 - Math.min(255, value);
                if (color == "red") {
                    imageData.data[imageIndex] = 255;    // Rouge
                    imageData.data[imageIndex + 2] = value;  // Bleu
                } else {
                    imageData.data[imageIndex] = value;    // Rouge
                    imageData.data[imageIndex + 2] = 255;  // Bleu
                }
                imageData.data[imageIndex + 1] = value;  // Vert
                imageData.data[imageIndex + 3] = 255;    // Alpha
            }
        }
    }

    // Mise à jour du tableau des impacts de photons
    function updatePhotonArray() {
        for (var i = 0; i < shots.length; i++) {
            index = shots[i];
            photonArray[index] = photonArray[index] + 1;
            maxCount = Math.max(maxCount, photonArray[index]);
            x = (index % width);
            histoArray[x] = histoArray[x] + 1;
            if (histoArray[x] > maxHisto) {
                maxHisto = maxHisto * 2;
                clearHisto = 1;
            }
        }
    }

    // Représentation graphique de l'histogramme
    function drawHisto(ctx) {
        if (clearHisto) {
            ctx.clearRect(0, 0, width, heightHisto);
            clearHisto = 0;
        }
        for (var i = 0; i < width; i++) {
            ctx.strokeStyle = "#C0C0C0";
            ctx.strokeRect(0, 0, width, heightHisto);
            ctx.strokeStyle = color;
            ctx.beginPath();
            ctx.moveTo(i, heightHisto);
            ctx.lineTo(i, heightHisto - heightHisto * histoArray[i] / maxHisto * .9);
            ctx.stroke();
        }
    }

    // Partie du code liée aux communications javascript-http
    function createRequestObject() {
        var ro;
        var browser = navigator.appName;
        if (browser == "Microsoft Internet Explorer") {
            ro = new ActiveXObject("Microsoft.XMLHTTP");
        } else {
            ro = new XMLHttpRequest();
        }
        return ro;
    }

    var http = createRequestObject();

    function sendRequest() {
        http.open('get', 'upload.php?id=ok');
        http.onreadystatechange = handleResponse;
        http.send(null);
    }

    // Fonction callback appelée quand les impacts sont disponibles
    function handleResponse() {
        if (http.readyState == 4) {
            var response = http.responseText;
            lineArray = response.split('\n'); // Tableau contenant (2*nShots+1) ligne (la dernière ligne étant vide)
            nShots = Math.floor(lineArray.length / 2);

            //on verifie que le tableau ne contient pas les memes valeurs que precedemment
            if ((verif != parseInt(lineArray[0]) || verif2 != parseInt(lineArray[1])) && nShots != 0) {

                photonCount = photonCount + nShots;
                document.getElementById("labelPhotonCount").innerText = photonCount;

                shots = Array(nShots);
                for (var iShot = 0; iShot < nShots; iShot++)
                    shots[iShot] = (parseInt(lineArray[2 * iShot]) - 100) * width + parseInt(lineArray[2 * iShot + 1]);

                // Enregistre les nouveaux impacts
                updatePhotonArray();

                // Mise à jour de l'image
                updateImage();

                // Dessin de l'image dans le canvas
                context.putImageData(imageData, 0, 0);

                // Représentation de l'histogramme
                drawHisto(contextHisto);

                //On actualise la valuer de la variable verif
                verif = parseInt(lineArray[0]);
                verif2 = parseInt(lineArray[1]);

            }
            responsePending = 0;
        }
    }

    window.onload = function () {
        // Récupérer le canvas et le context
        var canvas = document.getElementById("viewportF");
        context = canvas.getContext("2d");

        // Récupérer les dimensions de l'image
        width = canvas.width;
        height = canvas.height;

        // Création d'un objet image associé
        imageData = context.createImageData(width, height);

        // Récupération du contexte pour l'histogramme
        var canvasHisto = document.getElementById("viewportHisto");
        contextHisto = canvasHisto.getContext("2d");
        heightHisto = canvasHisto.height;

        // Initialisation du tableau contenant le cumul du nombre d'impacts de photons
        photonArray = Array(width * height).fill(0);
        histoArray = Array(width).fill(0);

        // Appel de la boucle principale
        setInterval(function () {
            if (responsePending == 0) {
                responsePending = 1;
                sendRequest();
            }
        }, 100);
    };
</script>
<script type="text/javascript">
    /******************
     *     BUTTONS
     ******************/
    //Function that changes the style of the led buttons
    function buttonStyle(val) {
        var elmt = document.getElementById(val);
        var elmt2;
        if (val == "2") { //Blue
            elmt2 = document.getElementById("4");
            elmt.style.border = "3px solid black";
            elmt2.style.border = "";
        } else if (val == "4") { //Red
            elmt2 = document.getElementById("2");
            elmt.style.border = "3px solid black";
            elmt2.style.border = "";

        } else if (val == "5") { //Acquire
            document.getElementById("5").style.border = "3px solid black";
            //puts a border on the acquire button
            for (let elt of document.getElementsByClassName("param")) {
                elt.disabled = 'disabled';
            }
            //disables the parameters
        } else if (val == "7") { //Stop
            document.getElementById("5").style.border = "";
            //removes the border of the acquire button when click on stop

            // Timer in order to avoid glitch.
            // Otherwise, if the user is too fast and rapidly clicks on a led and on "acquire",
            // the Python code of the lab server may still be sending data and will not see the change of color.
            //OBSOLETE : we updated the lab server code and there is no more delay when stopping an acquisition
            var time = 0;
            if (time != 0) {
                document.getElementById("5").disabled = "disabled";
                document.getElementById("7").style.border = "3px solid black";
            }
            setTimeout(unlock, time);
        }
    }

    function unlock() {
        //unlocks the parameters
        for (let elt of document.getElementsByClassName("param")) {
            elt.disabled = '';
        }
        document.getElementById("5").disabled = "";
        document.getElementById("7").style.border = "";
    }

    //function that handles what happens if the user clicks on a button
    function wt(val) {
        $.ajax({
            type: 'GET',
            url: "ajax.php",
            data: {
                message: val,
                param_1: <?php echo $maxAcquisition?>,
                param_2: '20',
                param_3: $('#param_3').val(),
                param_4: $('#param_4').val()
            },
            success: function (result) {
                console.log(result);
            }
        });
        if (val == "5") { //Acquire data
            //on efface le contenu des canvas
            var canvas_i = document.getElementById("viewportF");
            var ctx_i = canvas_i.getContext("2d");
            ctx_i.fillStyle = "white";
            ctx_i.fillRect(0, 0, width, height);
            var canvas_ih = document.getElementById("viewportHisto");
            var ctx_ih = canvas_ih.getContext("2d");
            ctx_ih.fillStyle = "white";
            ctx_ih.fillRect(0, 0, width, heightHisto);
            photonCount = 0;
            document.getElementById("labelPhotonCount").innerText = 0;
            photonArray = Array(width * height).fill(0);
            histoArray = Array(width).fill(0);
            maxHisto = 10;
        } else if (val == "2") {
            color = "blue";
        } else if (val == "4") {
            color = "red";
        }
        buttonStyle(val);
    }
</script>
</body>
</html>
