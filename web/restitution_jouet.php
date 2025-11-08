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

use classes\pages\RestitutionJouet;
use classes\config\Constants;

$page = new RestitutionJouet();
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
                if(document.getElementById("startPrint").value > 0){
                    await imprimer_page();
                    window.location.replace('<?php echo Constants::$PAGE_DEPOT."?clear_session&restitution" ?>');
                }
            }
    </script>
    
</head>
<body>
    <input type="hidden" id="startPrint" value="<?php echo $page->startPrint(); ?>"/>
    <?php $page->renderTemplateHeader(); ?>
    <?php $page->renderTemplateHeaderPrint(); ?>
    
    <div id="main_content"> <!-- main_content -->
            
        <div id="contact_area"> <!-- #contact_area -->
            
            <div class="container"> <!-- .container -->

            <div style="float: right;">
                <?php $page->showBillNumber(); ?>
            </div>
            <h2 id="contact" >RESTITUTION</h2>

            <form action="restitution_jouet.php" method="post" id="search_form">
                <fieldset>
                    <label for="ref_participant">Référence participant:</label>
                    <input type="text" name="ref_participant" id="ref_participant" />
                    <button type="submit" name="action" value="search_participant">Rechercher</button>
                </fieldset>
            </form>
            
            <div id="contact_info">                    
                    <fieldset>
                        
                        <?php $page->renderTemplateParticipantInfo(); ?>
                        <?php classes\template\TemplateManager::renderTemplate(\classes\template\TemplateList::$ALERT_MESSAGE, $page) ?>
                        
							
						</fieldset>
                <form action="#" method="post" id="contact_form">
						<fieldset> 
							<h2 id="jouet">LISTE DES JOUETS VENDU</h2>
							<table style="width: 100%; display: table;"> <!-- Tableau des jouets à ajouter 10 max -->
								<tr>
									<td id="entete_tableau" class="reference"> <label for="ref">R&Eacute;F&EacuteRENCE </label> </td>
									<td id="entete_tableau"> <label for="description">DESCRIPTION</label> </td>
									<td id="entete_tableau"  class="prix"> <label for="prix">PRIX (€)<label> </td>
								</tr>
                                                            <?php $page->renderTabSelled(); ?>
                                                            <tr>
                                                                <td>Total</td>
                                                                <td><?php echo $page->getNbItemSelled() ?> jouet(s)</td>
                                                                <td><?php echo $page->getTotalSelled() ?></td>
                                                            </tr>
							</table> <!-- Fin du Tableau des jouets à ajouter 10 max -->							
					</fieldset>
                                            <fieldset> 
							<h2 id="jouet">LISTE DES JOUETS INVENDU</h2>
							<table style="width: 100%; display: table;"> <!-- Tableau des jouets à ajouter 10 max -->
								<tr>
									<td id="entete_tableau"  class="reference"> <label for="ref">R&Eacute;F&EacuteRENCE </label> </td>
									<td id="entete_tableau"> <label for="description">DESCRIPTION</label> </td>
								</tr>
                                                            <?php $page->renderTabUnselled(); ?>
                                                            <tr>
                                                                <td>Total</td>
                                                                <td><?php echo $page->getNbItemUnselled(); ?> jouet(s)</td>
                                                            </tr>
							</table> <!-- Fin du Tableau des jouets à ajouter 10 max -->							
					</fieldset>
                                            <fieldset> 
							<h2 id="jouet">BILAN</h2>
                                                        <table style="width: 100%; text-align: left; display: table;">
                                                            <tr>
                                                                <td id="entete_tableau">Nombre de jouets restitués</td>
                                                                <td style="text-align: right;"><?php echo $page->getNbItemUnselled(); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td id="entete_tableau">Nombre de jouets vendus</td>
                                                                <td style="text-align: right;"><?php echo $page->getNbItemSelled(); ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td id="entete_tableau">Total des jouets vendus</td>
                                                                <td style="text-align: right;"><?php echo $page->getTotalSelled(); ?> €</td>
                                                            </tr>
                                                            <tr>
                                                                <td id="entete_tableau">Commission</td>
                                                                <?php if($page->isAdmin()){ ?>
                                                                    <td style="text-align: right;"><input type="text" name="tax" value="<?php echo $page->getTaxPercent(); ?>" style="text-align: right; width: 50px;"/> %</td>
                                                                    <td class="notPrintable"><button type="submit" name="action" value="tax">Modifier</button></td>
                                                                <?php }else{ ?>
                                                                    <td style="text-align: right;"><?php echo $page->getTaxPercent(); ?>  %</td>
                                                                <?php } ?>
                                                                
                                                            </tr>
                                                            <tr>
                                                                <td id="entete_tableau">Total</td>
                                                                <td style="text-align: right;"><?php echo $page->getTotalSelledMinusTax(); ?> €</td>
                                                            </tr>
                                                        </table>
                                                        
					</fieldset>
                    <div style="padding-top: 3em;">
                        <button name="action" type="submit" value="saveAndPrint" class="notPrintable">Imprimer</button>
                    </div>
				</form>
            </div> <!-- END .container -->  
        </div> <!-- END #contact_area -->  
    </div> <!-- END #main_content -->
    
    <?php $page->renderTemplateFooter(); ?>
    <?php $page->renderTemplateFooterPrint(); ?>
    
</body>
</html>
