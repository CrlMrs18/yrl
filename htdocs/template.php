<?php
/*******************************************************************
 * TEMPLATE
 * Use this page when you want to create a new page on the website.
 *******************************************************************/

require_once "init.php";
checkTopMenu();
generateHead(Config::getName());
//checkConnection();
//checkBooking();
?>

<body>
<?php generateTitle();?>

<footer class="bd-footer">
    <p class="mt-5 mb-3 text-muted text-center">PSC fentes d'Young &copy;2019</p>
</footer>
</body>

