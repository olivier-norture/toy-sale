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

use classes\pages\Index;

$page = new Index();
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
        window.onload = function () {
            document.getElementById("login").focus();
        }
    </script>
    
</head>
<body>
    
    <?php $page->renderTemplateHeader(); ?>
    
    <div id="main_content"> <!-- main_content -->

            <div id="contact_area"> <!-- #contact_area -->

                <div class="container"> <!-- .container -->
                    
                    <?php classes\template\TemplateManager::renderTemplate(classes\template\TemplateList::$ALERT_MESSAGE, $page); ?>
                    
                    <h2 id="contact">IDENTIFIANT PC</h2>
                    
                    <?php if($page->getUser()->getPk() != null){
                        echo "<label>Bienvenu(e) \"<b> " . $page->getUser()->getLogin() . " \"</b>";
                        echo "<br /><br />";
                    } ?>
                    
                    
                    <label>Ce PC est identifié avec la lettre : " <b><?php echo $page->getLetter() ?></b> "</label>
                    <br />
                    <br />
                    <label>L'adresse IP de ce PC est : " <b><?php echo array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']; ?></b> "</label>
                    <br />
                    <br />
                    <br />

                        
<?php if($page->getUser()->getPk() == null){ ?>

                    <h2 id="contact">CONNEXION</h2>

                    <div id="ajout_vendeur_add">              
            
            <form action="#" method="post" id="contact_form1">
                <label>Nom d'utilisateur : </label> <input type="text" name="login" id="login"/>
                <label>Mot de passe : </label> <input type="password" name="password"/>
                <br />
                <br />
                <br />
                <button type="submit" name="connect">Connexion</button>
                
                </form>
                </div>
                        
<?php } else { ?>

    <!--
    if admin
-->
    <div>
        <ul>
            <li>Mettre à jour l'addresse IP du serveur <a href="server_vars.php">dans la configuration</a></li>
            <li>Changer date de début et date de fin <a href="server_vars.php">dans la configuration</a></li>
        </ul>
    </div>
                        
<?php } ?>
            
<!---->        </div> <!-- END #slideshow_area -->
        
    </div> <!-- END #main_content -->
    </div> <!-- END #main_content -->
    
    <?php $page->renderTemplateFooter(); ?>
    
</body>
</html>
