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

use classes\pages\DepotJouet;
use classes\config\Constants;

$page = new DepotJouet();
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

        <script type="text/javascript" src="js/script.js"></script>
        <script type="text/javascript">
            window.onload = async function () {
                initTextArea();
                if (document.getElementById("startPrint").value > 0) {
                    console.log("start print");
                    await imprimer_page();
                    window.location.replace('<?php echo Constants::$PAGE_DEPOT . "?clear_session&depot" ?>');
                }
            }

            function printLabel(objetId) {
                window.open('print_label.php?objet_id=' + objetId, '_blank', 'width=62mm,height=30.48mm,resizable=yes,scrollbars=yes,status=yes');
            }
        </script>

    </head>
    <body>
        <input type="hidden" id="startPrint" value="<?php echo $page->startPrint(); ?>"/>
        <?php classes\template\ParticipantInfo::$date = $page->getBillDate();
                $page->renderTemplateHeader(); ?>
        <?php $page->renderTemplateHeaderPrint(); ?>

        <div id="main_content"> <!-- main_content -->

            <div id="contact_area"> <!-- #contact_area -->

                <div class="container"> <!-- .container -->

                    <div style="float: right;">
                        <?php $page->showBillNumber(); ?>
                    </div>
                    <h2 id="contact">D&Eacute;POT</h2>

                    <div id="contact_info">             
                        <fieldset>
                            <?php classes\template\ParticipantInfo::$date = $page->getBillDate();
                                    $page->renderTemplateParticipantInfo(); ?>
                            <?php classes\template\TemplateManager::renderTemplate(\classes\template\TemplateList::$ALERT_MESSAGE, $page) ?>
                        </fieldset>
                        <form action="#" method="post" id="contact_form">
                            <fieldset> 
                                <h2 id="jouet" class="notPrintable">LISTE DES JOUETS A D&Eacute;POSER</h2>
                                <div class="print-content-group">
                                    <table class="print-table-section"> <!-- Tableau des jouets à ajouter 10 max -->
                                        <thead class="print-table-header">
                                            <tr>
                                            <td id="entete_tableau"> <label class="reference">R&Eacute;F&Eacute;RENCE </label> </td>
                                            <td id="entete_tableau" style="width: 100%;"> <label>DESCRIPTION</label> </td>
                                            <td id="entete_tableau"> <label class="prix">PRIX (€)</label> </td>
                                            <td id="entete_tableau" class="notPrintable"> <label for="edit"> </label> </td>
                                            <td id="entete_tableau" class="notPrintable"> <label for="print"> </label> </td>
                                            <td id="entete_tableau" class="notPrintable"> <label for="save"> </label> </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr  class="notPrintable">
                                                <td><label><?php echo classes\db\object\objetUtils::getNextRef($page->getAcheteur(), $page->getLetter()); ?></label></td>
                                                <td><textarea autofocus name="description" id="description" class="textarea" onkeydown="setTextareaHeight(this);"></textarea>
                                                    <td><input type="text" name="prix" value="" style="height: 100%; width: 100%;"/></td>
                                                    <td class="notPrintable"><button class="add" name="action" type="submit" value="add">Enregistrer</button></td>
                                                    <td class="notPrintable"></td>
                                                    <td class="notPrintable"></td>
                                            </tr>

                                            <!-- Display all objets -->
                                            <?php $page->renderTab(); ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="print-total-row">
                                                <td id="entete_tableau">Total</td>
                                                <td><label><?php echo $page->getTotalSize() ?> jouet(s)</label></td>
                                                <td></td>
                                                <td class="notPrintable"></td>
                                                <td class="notPrintable"></td>
                                                <td class="notPrintable"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </fieldset>


                        <div style="padding-top: 2em;" class="notPrintable">
                            <button name="action" type="submit" value="saveAndPrint">Imprimer</button>
                        </div>
                    </form>
                </div> <!-- END #contact_area -->

            </div> <!-- END #main_content -->

            <?php $page->renderTemplateFooter() ?>
            <?php $page->renderTemplateFooterPrint() ?>
        </div>
        </div>
    </body>
</html>
