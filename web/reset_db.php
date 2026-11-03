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

$page = new classes\pages\ResetDB();
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
                var pwd = document.getElementById("password");
                if (pwd) pwd.focus();
            }
        </script>
    </head>
    <body>

        <?php $page->renderTemplateHeader(); ?>

        <div id="main_content"> <!-- main_content -->

            <div id="contact_area"> <!-- #contact_area -->

                <div class="container"> <!-- .container -->

                    <h2 id="contact">Réinitialiser la base</h2>
                    <?php classes\template\TemplateManager::renderTemplate(\classes\template\TemplateList::$ALERT_MESSAGE, $page) ?>

                    <?php if ($page->isResetDone()) { ?>
                        <p><strong>La base a été réinitialisée.</strong></p>
                    <?php } else { ?>
                        <p>Cette action supprime <strong>définitivement</strong> tous les jouets et toutes les factures,
                        remet à zéro les compteurs des PCs et efface les références des participants.</p>
                        <p>Les participants et les utilisateurs sont conservés.</p>
                        <br />
                        <form action="reset_db.php" method="post" id="reset_db_form"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir TOUT SUPPRIMER ? Cette action est irréversible.');">
                            <fieldset>
                                <label for="password">Pour confirmer, saisissez votre mot de passe :</label>
                                <input type="password" name="password" id="password" autocomplete="current-password" required="required" />
                                <button name="action" type="submit" value="reset">Réinitialiser la base</button>
                            </fieldset>
                        </form>
                    <?php } ?>

                </div> <!-- END .container -->

            </div> <!-- END #contact_area -->

        </div> <!-- END #main_content -->

        <?php $page->renderTemplateFooter(); ?>

</body>
</html>
