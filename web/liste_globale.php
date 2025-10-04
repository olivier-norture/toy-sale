<?php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/../'));
spl_autoload_register(function ($class) {
    $file_as_is = str_replace('\\', '/', $class) . '.php';
    $file_lowercase = strtolower($file_as_is);

    if (stream_resolve_include_path($file_as_is)) {
        require_once $file_as_is;
    } else if (stream_resolve_include_path($file_lowercase)) {
        require_once $file_lowercase;
    }
});

$page = new \classes\pages\ListeGlobale();
$page->process(false);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Bourse aux Jouets Chambly</title>
        <link rel="stylesheet" type="text/css" href="stylesheets/reset.css" />
        <link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
        <link rel="stylesheet" type="text/css" href="stylesheets/style.css" />

        <script type="text/javascript" src="js/script.js"></script>
        </script>

    </head>
    <body>
        <?php $page->renderTemplateHeader(); ?>

        <div id="main_content"> <!-- main_content -->

            <div id="contact_area"> <!-- #contact_area -->

                <div class="container"> <!-- .container -->

                    <h2 id="contact" >LISTE DES JOUETS</h2>

                    <div id="contact_info">                    
                        <form action="#" method="post" id="contact_form">
                            <fieldset> 
                                <table> <!-- Tableau des jouets à ajouter 10 max -->
                                    <tr>
                                        <td id="entete_tableau" class="reference"> <label for="ref">R&Eacute;F&EacuteRENCE</label> </td>
                                        <td id="entete_tableau"><label>DESCRIPTION</label> </td>
                                        <td id="entete_tableau" class="prix"> <label>PRIX</label> </td>
                                        <td id="entete_tableau"> <label>ETAT</label> </td>
                                        <td id="entete_tableau">D&Eacute;POT</td>
                                        <td id="entete_tableau">VENTE</td>
                                        <td id="entete_tableau">RESTITUTION</td>
                                    </tr>

                                    <!-- Display all objets -->
                                    <?php $page->renderTab(); ?>

                                </table>
                            </fieldset>

                    </div> <!-- END .container -->
                    </form>
             </div> <!-- END #contact_area -->

            </div> <!-- END #main_content -->

            <?php $page->renderTemplateFooter() ?>
        </div>
    </body>
</html>