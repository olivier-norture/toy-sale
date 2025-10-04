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

use classes\pages\PagePC;

$page = new PagePC();
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
        
        <script type="text/javascript">
            window.onload = function(){
                document.getElementById("nom").focus();
            }
        </script>
    </head>
    <body>

        <?php $page->renderTemplateHeader(); ?>
        

        <div id="main_content"> <!-- main_content -->

            <div id="contact_area"> <!-- #contact_area -->

                <div class="container"> <!-- .container -->

                    <h2 id="contact">PCs</h2>
                    <?php classes\template\TemplateManager::renderTemplate(\classes\template\TemplateList::$ALERT_MESSAGE, $page) ?>

                    <div id="pc-list">                    
                            <fieldset> 
                                <table> <!-- Tableau des jouets à ajouter 10 max -->
                                    <tr>
                                        <td id="entete_tableau">LETTRE</td>
                                        <td id="entete_tableau"><label>ADDRESSE IP</label> </td>
                                        <td id="entete_tableau">COMPTEUR</td>
                                        <td id="entete_tableau"></td>
                                        <td id="entete_tableau"></td>
                                    </tr>

                                    <!-- Display all objets -->
                                    <?php $page->tableRows(); ?>
                                    
                                    <!-- add a new pc -->
                                    <tr>
                                        <form action="#" method="post" id="createPc">
                                        <td><input type="text" name="letter" style="width: 100px;" value=""/></td>
                                        <td><input type="text" name="ip" value=""/></td>
                                        <td><input type="number" name="counter" value="0"/></td>
                                        <td></td>
                                        <td><button name="action" type="submit" value="create">Ajouter</button></td>
                                        </form>
                                    </tr>
                                </table>
                            </fieldset>
                    </div> <!-- END .container -->

                </div> <!-- END #contact_area -->

            </div> <!-- END #main_content -->

            <?php $page->renderTemplateFooter(); ?>

    </body>
</html>