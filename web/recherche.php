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

$page = new \classes\pages\Recherche();
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

</head>

<body>
    <?php $page->renderTemplateHeader(); ?>

    <div id="main_content">
        <!-- main_content -->

        <div id="contact_area">
            <!-- #contact_area -->

            <div class="container">
                <!-- .container -->

                <h2 id="contact">RECHERCHE</h2>

                <form action="#" method="post" id="contact_form1">
                    <div id="contact_info">
                        <label>Référence de l'objet : </label>
                        <input autofocus type="text" name="ref" value="<?php echo $page->searchRef ?>">
                        <label>Description : </label>
                        <input type="text" name="description" value="<?php echo $page->searchDescription ?>">
                        <br />
                        <br />
                        <br />
                        <button type="submit" name="action" value="search">Rechercher</button>
                    </div> <!-- END .container -->
                </form>

                <?php if ($page->objectList != null) { ?>
                <br /><br />
                <div id="contact_info">
                    <table>
                        <tr>
                            <td id="entete_tableau">R&Eacute;FERENCE</td>
                            <td id="entete_tableau">D&Eacute;SIGNATION</td>
                            <td id="entete_tableau">PRIX</td>
                            <td id="entete_tableau">STATUS</td>
                            <td id="entete_tableau">NOM</td>
                            <td id="entete_tableau">PR&Eacute;NOM</td>
                            <td id="entete_tableau"></td>
                        </tr>

                        <?php echo $page->renderSearchResults() ?>
                    </table>
                </div> <!-- END .container -->
                <?php } ?>



            </div> <!-- END #contact_area -->

        </div> <!-- END #main_content -->

        <?php $page->renderTemplateFooter() ?>
    </div>
</body>

</html>