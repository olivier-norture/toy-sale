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

use classes\pages\AjouterUtilisateur;

$page = new AjouterUtilisateur();
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
    </head>
    <body>

        <?php $page->renderTemplateHeader(); ?>

        <div id="main_content"> <!-- main_content -->

            <div id="contact_area"> <!-- #contact_area -->

                <div class="container"> <!-- .container -->

                    <h2 id="contact">AJOUTER UN UTILISATEUR</h2>

                    <div id="ajout_vendeur_add">                    
                        <form action="#" method="post" id="contact_form1">

                            <fieldset>                            
                                <ol>
                                    <li>
                                        <label for="nom">Prenom</label> 
                                        <input type="text" name="pnom"id="pnom" value="<?php echo $page->getParticipant()->getPnom(); ?>" />
                                    </li>
                                    <li>
                                        <label for="password">Nom</label>
                                        <input type="text" name="nom" id="nom" value="<?php echo $page->getParticipant()->getNom(); ?>"/>
                                    </li>
                                    <li>
                                        <label for="nom">Login</label> 
                                        <input type="text" name="login"id="login" value="<?php echo $page->getEditUser()->getLogin(); ?>" />
                                    </li>
                                    <li>
                                        <label for="password">Mot de passe</label>
                                        <input type="text" name="password" id="password" value="<?php echo $page->getEditUser()->getPassword(); ?>"/>
                                    </li>
                                    <li>
                                        <label for="isAdmin">Administrateur</label>
                                        <input name="isAdmin" id="isAdmin" type="checkbox" value="TRUE" <?php echo $page->getEditUser()->getIsAdmin()?"checked":"";?>/>
                                    </li>
                                    <li>
                                        <label for="isDepot">D&eacute;p&ocirc;t</label>
                                        <input name="isDepot" id="isDepot" type="checkbox" value="TRUE" <?php echo $page->getEditUser()->getIsDepot()?"checked":"";?>/>
                                    </li>
                                    <li>
                                        <label for="isVente">Vente</label>
                                        <input name="isVente" id="isVente" type="checkbox" value="TRUE" <?php echo $page->getEditUser()->getIsVente()?"checked":"";?>/>
                                    </li>
                                    <li>
                                        <label for="isRestitution">Restitution</label>
                                        <input name="isRestitution" id="isRestitution" type="checkbox" value="TRUE" <?php echo $page->getEditUser()->getIsRestitution()?"checked":"";?>/>
                                    </li>
                                    <li>
                                        <div style="display: block;">
                                            <div style="display: inline-block;">
                                                <button name="action" type="submit" value="update">Enregistrer</button>
                                            </div>
                                        </div>
                                    </li>
                                </ol>
                            </fieldset>
                        </form>
                    </div> <!-- END .container -->

                </div> <!-- END #contact_area -->

            </div> <!-- END #main_content -->

            <?php $page->renderTemplateFooter(); ?>

    </body>
</html>