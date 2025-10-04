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

use classes\pages\VendreJouet;
use classes\config\Constants;

$page = new VendreJouet();
$page->process();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Bourse aux Jouets Chambly</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/reset.css" />
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
    <link rel='stylesheet' type='text/css' href="stylesheets/style.css" />
    
    <script type="text/javascript" src="js/script.js"></script>
    <script type="text/javascript">
        window.onload = async function () {
                document.getElementById("ref").focus();
                modifyPay();
                if(document.getElementById("startPrint").value > 0){
                    await imprimer_page();
                    window.location.replace('<?php echo Constants::$PAGE_VENTE."?clear_session&vente" ?>');
                }
            }
    </script>
    
</head>
<body>
    <input type="hidden" id="startPrint" value="<?php echo $page->startPrint(); ?>"/>
    <?php $page->renderTemplateHeader(); ?>
    
    <div id="main_content"> <!-- main_content -->
        <div id="contact_area"> <!-- #contact_area -->
            <div class="container"> <!-- .container -->
                
    <?php $page->renderTemplateHeaderPrint(); ?>
            <div style="float: right;">
                <?php $page->showBillNumber(); ?>
            </div>
            <h2 id="contact" >VENTE</h2>
            <div id="contact_info">                    
                
                    <fieldset>                            
                        <?php $page->renderTemplateParticipantInfo(); ?>
                        <?php classes\template\TemplateManager::renderTemplate(\classes\template\TemplateList::$ALERT_MESSAGE, $page) ?>
                    </fieldset>
                
                        <form action="#" method="post" id="contact_form">
                            <fieldset> 
                                <h2 id="jouet" class="notPrintable">LISTE DES JOUETS A VENDRE</h2>
                                <table> <!-- Tableau des jouets à ajouter 10 max -->
                                    <tr>
                                        <td id="entete_tableau"> <label class="reference">R&Eacute;F&EacuteRENCE </label> </td>
                                        <td id="entete_tableau" style="width: 100%;"> <label>DESCRIPTION</label> </td>
                                        <td id="entete_tableau"> <label class="prix">PRIX (€)</label> </td>
                                        
                                        <td id="entete_tableau" class="notPrintable"> <label for="save"> <label> </td>
                                    </tr>

                                    <tr class="notPrintable">
                                        <td><input type="text" name="ref" id="ref" style="width: 100px;" value=""/></td>
                                        <td></td>
                                        <td></td>
                                        <td><button name="action" type="submit" value="add">Ajouter</button></td>
                                    </tr>
                
                                    <?php $page->renderTab(); ?>

                                    <tr>
                                        <td id="entete_tableau">Total</td>
                                        <td><?php echo $page->getTotalSize() ?> jouet(s)</td>
                                        <td><input type="hidden" name="pay_basket_sum" value="<?php echo $page->getTotalPrice() ?>"/><?php echo $page->getTotalPrice() ?></td>
                                    </tr>
                                    <tr>
                                        <td id="entete_tableau">Espèces</td>
                                        <td></td>
                                        <td><input type="text" name="pay_cash" onkeyup="modifyPay()" value="<?php echo $page->getPayCash(); ?>"/></td>
                                    </tr>
                                    <tr>
                                        <td id="entete_tableau">Chèque</td>
                                        <td></td>
                                        <td><input type="text" name="pay_check" onkeyup="modifyPay()" value="<?php echo $page->getPayCheck(); ?>"/></td>
                                    </tr>
                                    <tr>
                                        <td id="entete_tableau">Carte Bleu</td>
                                        <td></td>
                                        <td><input type="text" name="pay_credit_card" onkeyup="modifyPay()" value="<?php echo $page->getPayCreditCard(); ?>"/></td>
                                    </tr>
                                    <tr>
                                        <td id="entete_tableau">Reste à payer</td>
                                        <td></td>
                                        <td><input type="text" name="pay_left_to" disabled/></td>
                                    </tr>
                                    <tr>
                                        <td id="entete_tableau">Reste à rendre</td>
                                        <td></td>
                                        <td><input type="text" name="pay_give_back" disabled/></td>
                                    </tr>
                                    <tr>
                                        <td id="entete_tableau">Observations</td>
                                        <td><textarea name="observations" class="textarea" style="width: 100%;"><?php echo $page->getObservations(); ?></textarea></td>
                                        <td></td>
                                    </tr>
                                </table> <!-- Fin du Tableau des jouets à ajouter 10 max -->							
    <?php $page->renderTemplateFooterPrint(); ?>
                                
                                
                            </fieldset>
                            <div style="padding-top: 2em;" class="notPrintable">
                                <button name="action" type="submit" value="saveAndPrint">Imprimer</button>
                            </div>

                </form>
            </div> <!-- END .container -->  
        </div> <!-- END #contact_area -->  
    </div> <!-- END #main_content -->
    
    <?php $page->renderTemplateFooter(); ?>
    
</body>
</html>
