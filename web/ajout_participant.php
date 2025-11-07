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

use classes\pages\AjouterParticipant;

$page = new AjouterParticipant();
$page->process();
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

                    <h2 id="contact">RECHERCHER UNE PERSONNE</h2>
                    <?php classes\template\TemplateManager::renderTemplate(\classes\template\TemplateList::$ALERT_MESSAGE, $page) ?>

                    <div id="ajout_vendeur_add">                    
                        <form action="" method="post" id="contact_form1" >

                            <fieldset>                            
                                <ol>
                                    <li>
                                        <label for="ref">Référence</label> 
                                        <input type="text" name="ref" id="ref" value="<?php echo $page->getVendeur()->getRef(); ?>" />
                                    </li>
                                    <li>
                                        <label for="nom">Nom</label> 
                                        <input type="text" name="nom"id="nom" value="<?php echo $page->getVendeur()->getNom(); ?>" />
                                    </li>
                                    <li>
                                        <label for="prenom">Pr&eacute;nom</label>
                                        <input type="text" name="prenom" id="prenom" value="<?php echo $page->getVendeur()->getPnom(); ?>"/>
                                    </li>
                                    <li>
                                        <button name="action" type="submit" value="search">Rechercher</button>
                                                                        <li>
                                    <?php if($page->getErrorMessage() != null){ ?>
                                    <li>
                                        <div style="float: left;">
                                            <label for="tel">Num&eacute;ro de t&eacute;l&eacute;phone</label>
                                            <input type="text" name="tel" id="tel" value="<?php echo $page->getVendeur()->getTel(); ?>"/>
                                        </div>
                                        <div style="float: right; padding-top: 30px;">
                                            <button name="action" type="submit" value="add">Créer cette personne</button>
                                        </div>
                                    </li>
                                        <?php }?>

                                    <?php
                                        if(isset($_GET['restitution'])){
                                            echo "
                                            <button onclick=\"location.href='restitution_envelope.php'\" type=\"button\">Publipostage restitution</button>
                                            ";
                                        }
                                    ?>

                                        <?php if($page->getParticipantFind()){ ?>
                                    <div style="width: 350px; display:inline-block;">
                                            <label for="tel">Num&eacute;ro de t&eacute;l&eacute;phone</label>
                                            <input type="text" name="tel" id="tel" value="<?php echo $page->getVendeur()->getTel(); ?>"/>
                                    </div>
                                    <li>
                                        <label for="adresse">Adresse</label>
                                        <input type="text" name="adresse" id="adresse" style="width: 653px;" value="<?php echo $page->getVendeur()->getAdresse(); ?>"/>
                                    </li>
                                    <li>
                                        <div style="width: 350px; display:inline-block;">
                                            <label for="code_postal">Code postal</label>
                                            <input type="text" name="code_postal" id="code_postal" value="<?php echo $page->getVendeur()->getCp(); ?>"/>
                                        </div>
                                        <div style="width: 300px; display:inline-block;">
                                            <label for="ville">Ville</label>
                                            <input type="text" name="ville" id="ville" value="<?php echo $page->getVendeur()->getVille(); ?>"/>
                                        </div>
                                    </li>
                                    <li>
                                        <div style="width: 300px; display:inline-block;">
                                            <label for="email">E-mail</label>
                                            <input type="text" name="email" id="email" value="<?php echo $page->getVendeur()->getEmail(); ?>"/>
                                        </div>
                                    </li>
                                    </br>
                                    <li>
                                        <div style="display: block;">
                                            <div style="display: inline-block;">
                                                <button name="action" type="submit" value="update">Enregistrer</button>
                                            </div>
                                            <div style="display: inline-block; float: right; ">
                                                <?php $page->renderButton(); ?>
                                            </div>
                                        </div>
                                    </li>
                                    <?php }?>
                                </ol>
                            </fieldset>
                            
                        
                        </div>
                            </form>
                    </div> <!-- END .container -->

                </div> <!-- END #contact_area -->

            </div> <!-- END #main_content -->

            <?php $page->renderTemplateFooter(); ?>

    </body>
</html>