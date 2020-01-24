<?php
require_once "init.php";
checkTopMenu();
checkConnection();
generateHead(Config::getName()." (meme)");
?>

<body>
<?php generateTitle();?>
<div class id="meme">
    <h1> Quelques Memes sympas </h1>
    <table border="0" width="100%" align="center">
        <tr width="100%" align="center">
            <td width="50%" align="center"><img src="images/download.jpeg" width="60%" align="center"></td>
            <td width="50%" align="center"><img src="images/download2.jpeg" width="60%" align="center"></td>
        </tr>
        <br>
        <tr width="100%" align="center">
            <td width="50%" align="center"><img src="images/patrick.jpg" width="60%" align="center"></td>
            <td width="50%" align="center"><img src="images/mess.png" width="60%" align="center"></td>
        </tr>
        <br>
        <tr width="100%" align="center">
            <td width="50%" align="center"><img src="images/observer.jpeg" width="60%" align="center"></td>
            <td width="50%" align="center"><img src="" width="60%" align="center"></td>
        </tr>
    </table>
</div>
<?=Config::getNotice()?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
</body>
</html>