<?php

define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'fentes_young_photon_exp');
define('DB_PASSWORD', '=N8[wqS724!U+cgV');
define('DB_NAME', 'fentes_young_photon_exp');


class Config {
    // Home address of web site
    public static function getHome() { return "https://fentes-young-photon-exp.binets.fr/"; }
    //https://www.enseignement.polytechnique.fr/profs/physique/Manuel.Joffre/psc19/
    // Name of web site
    public static function getName() { return "Young remote lab"; }
    // E-mail address that will be used with sendmail
    public static function getErrorMail() { return "fentes.young@gmail.com";}
    // E-mail address that will be displayed on web site in case of various errors
    public static function getWebmasterMail() { return "fentes.young@gmail.com";}
    // Message displayed at the end of each page
    public static function getNotice() {return "
        <footer class=\"bd-footer\">
           <p class=\"mt-5 mb-3 text-muted text-center\">PSC fentes d'Young &copy;2019
           </p>
        </footer>
        </main>";}
}
/* code Elia*/
$host="127.0.0.1";
$user="fentes_young_photon_exp";
$pass="=N8[wqS724!U+cgV";
$db="fentes_young_photon_exp";


?>