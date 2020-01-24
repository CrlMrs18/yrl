<?php
$id=$_SESSION['id'];
$admin="";
$connected="style='display: none'";
$not_connected="";

if (isAdmin($id)) {
    $admin="";
}

if ($_SESSION['id']) {
    $connected="";
    $not_connected="style='display: none'";
}

?>

<head>
    <link rel="stylesheet" href="css/navbar.css" id="main-stylesheet">

    <script type="text/javascript">
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })
        (window,document,'script','//www.google-analytics.com/analytics.js','ga');
    </script>
</head>

<body class="ng-scope">
<script src="https://www.google.com/recaptcha/api.js?render=6LfawooUAAAAACUFQPCs3LgBMRgD8NK-2ti2q1sD"></script>
<nav class="navbar navbar-default navbar-main">
    <div class="container-fluid">
        <div class="navbar-header">
            <button class="navbar-toggle active" ng-init="navCollapsed = true" ng-click="navCollapsed = !navCollapsed" ng-class="{active: !navCollapsed}" aria-label="Toggle Navigation">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
            <table>
                <tr>
                    <td width="80px">
                        <a class="navbar-brand" href="index.php"><img src="images/logo.png" width="70px"></a>
                    </td>
                    <td>
                        <div class="site-footer-content hidden-print">
                            <div class="row">
                                <ul class="col-md-9">
                                    <li class="dropdown subdued" dropdown="">
                                        <a class="dropdown-toggle" href="#" dropdown-toggle="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Select Language" tooltip="Language">
                                            <img src="images/flag_<?=$_SESSION['lang']?>.png" width="50px">
                                        </a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li class="dropdown-header"><?=_("Language")?>
                                            <form action="<?=$_SERVER["PHP_SELF"]?>" method="post">
                                            <?php
                                                global $supported_languages;
                                                $s=$_SERVER["PHP_SELF"];
                                                foreach ($supported_languages as $lang) {
                                                    echo "
                                                        <li class=\"lngOption\">
                                                            <a class=\"menu-indent\">
                                                                <input type=\"image\" name=\"lang_$lang\" alt=\"$lang\" src=\"images/flag_$lang.png\" height=\"25px\">
                                                            </a>
                                                        </li>";
                                                }
                                                ?>
                                                <!--
                                                <li class="lngOption" onclick="">
                                                    <a class="menu-indent">
                                                        <img src="images/flag_fr.png" width="25px">
                                                        French
                                                    </a>
                                                </li>
                                                -->
                                            </form>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="navbar-collapse collapse" collapse="navCollapsed" style="height: 0px;">
            <ul class="nav navbar-nav navbar-right">
                <li class="subdued">
                    <a class="subdued" href="experiment.php"><?=_("Experiment")?></a>
                </li>
                <li class="subdued">
                    <a class="subdued" href="spectator.php"><?=("Spectator")?></a>
                </li>
                <li class="subdued">
                    <a class="subdued" href="theory.php"><?=("Theory")?></a>
                </li>
                <li <?=$admin?> class="subdued">
                    <a class="subdued" href="admin.php"><?=_("Administration")?></a>
                </li>
                <!--
                <li class="dropdown subdued" dropdown="">
                    <a class="dropdown-toggle" href="" dropdown-toggle="" aria-haspopup="true" aria-expanded="false">
                        <?=_("Help")?>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/learn"><?=_("Documentation")?></a>
                        </li>
                        <li>
                            <a ng-controller="ContactModal" ,="" ng-click="contactUsModal()" href="" class="ng-scope">Contact Us</a>
                        </li>
                    </ul>
                </li>
                -->
                <li <?=$connected?>>
                    <a href="booking.php"><?=_("Reservation")?></a>
                </li>
                <li <?=$connected?> class="dropdown" dropdown="">
                    <a class="dropdown-toggle" href="" dropdown-toggle="" aria-haspopup="true" aria-expanded="false">
                        Account
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="subdued" ng-non-bindable="">
                                <?php
                                echo getMailFromId($id)
                                ?>
                            </div>
                        </li>
                        <li class="divider hidden-xs hidden-sm"></li>
                        <!--
                        <li>
                            <a href="settings" >
                                <?=_("Account Settings")?>
                            </a>
                        </li>
                        -->
                        <li class="divider hidden-xs hidden-sm"></li>
                        <li>
                            <form method="POST" action="logout.php" class="ng-pristine ng-valid">
                                <input name="_csrf" type="hidden" value="HMlXe6Dc-LPQxzWmh6WMUH34_cNHMPzNAdyI" autocomplete="off">
                                <button class="btn-link text-left dropdown-menu-button"><?=_("Log Out")?></button>
                            </form>
                        </li>
                    </ul>
                </li>
                <li <?=$not_connected?>>
                    <a href="signup.php"><?=_("Register")?></a>
                </li>
                <li <?=$not_connected?>>
                    <a href="login.php"><?=_("Log in")?></a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<main class="content content-alt project-list-page ng-scope" ng-controller="ProjectPageController" role="main">
    <script src="js/libraries-b8efef92df5ad597deac.js"></script>
    <script src="js/main-0080bc231b5f39734ab0.js"></script>


    <meta http-equiv="Content-Type" content="text/html; charset=utf-8\">
    <!--<link rel="stylesheet" href="style.css" type="text/css" media="screen"/>-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">-->
    <br>
