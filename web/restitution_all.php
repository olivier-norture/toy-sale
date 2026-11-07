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

use classes\config\ServerVarsConfig;

$page = new \classes\pages\RestitutionAll();
$page->process(false);
$summaries = $page->getSummaries();
$dateTime = \classes\utils\Date::getCurrentDateAndTime();
$redacteur = $page->getRedacteur()->getPnom();

function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Bourse aux Jouets Chambly</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/reset.css" />
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
    <link rel="stylesheet" type="text/css" href="stylesheets/style.css" />
    <style>
        /* Same rendering as the print of the restitution page (see imprimer_page() in js/script.js) */
        .onlyPrintable { display: block; }
        .restitution_page { overflow: hidden; }
        .restitution_page .container { margin-left: 0; margin-right: 0; }
        .restitution_page td.reference, .restitution_page td.prix { width: 100px; }
        .restitution_page { font-size: 12px; }
        @media print {
            .notPrintable { display: none !important; }
            .restitution_page { page-break-after: always; }
        }
    </style>
</head>
<body>
    <div class="notPrintable">
        <?php $page->renderTemplateHeader(); ?>
        <p><?php echo count($summaries); ?> participant(s) &mdash; une page par participant.</p>
        <button type="button" onclick="window.print()">Imprimer</button>
    </div>

    <div id="main_content">
    <?php foreach ($summaries as $s) { $p = $s['participant']; ?>
        <div class="restitution_page">
            <?php $page->renderTemplateHeaderPrint(); ?>

            <div id="contact_area">
                <div class="container">
                    <h2 id="contact">RESTITUTION</h2>

                    <div id="contact_info">
                        <fieldset>
                            <div style="display: block">
                                <div style="display: block">
                                    <label class="labelInfoTitle labelInfo">Date de la bourse au jouet : </label><label class="labelInfo"><?php echo h(ServerVarsConfig::getServerVar(ServerVarsConfig::$DATE_DEBUT) . ' - ' . ServerVarsConfig::getServerVar(ServerVarsConfig::$DATE_FIN)); ?></label>
                                    <label class="labelInfoTitle labelInfo">Date du jour : </label><label class="labelInfo"><?php echo h($dateTime); ?></label>
                                </div>
                                <div style="display: block">
                                    <label class="labelInfoTitle labelInfo">Nom : </label><label class="labelInfo"><?php echo h($p->getNom()); ?></label>
                                    <label class="labelInfoTitle labelInfo">Prénom : </label><label class="labelInfo"><?php echo h($p->getPnom()); ?></label>
                                    <label class="labelInfoTitle labelInfo">Référence : </label><label class="labelInfo"><?php echo h($p->getRef()); ?></label>
                                </div>
                                <div style="display: block">
                                    <label class="labelInfoTitle labelInfo">Rédacteur : </label><label class="labelInfo"><?php echo h($redacteur); ?></label>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset>
                            <h2 id="jouet">LISTE DES JOUETS VENDU</h2>
                            <table>
                                <tr>
                                    <td id="entete_tableau" class="reference"><label>RÉFÉRENCE</label></td>
                                    <td id="entete_tableau"><label>DESCRIPTION</label></td>
                                    <td id="entete_tableau" class="prix"><label>PRIX (€)</label></td>
                                </tr>
                                <?php foreach ($s['selled'] as $o) { ?>
                                <tr>
                                    <td><label><?php echo h($o->getRef()); ?></label></td>
                                    <td><label><?php echo h($o->getDescription()); ?></label></td>
                                    <td><label><?php echo h($o->getPrix()); ?></label></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td>Total</td>
                                    <td><?php echo count($s['selled']); ?> jouet(s)</td>
                                    <td><?php echo h($s['totalSelled']); ?></td>
                                </tr>
                            </table>
                        </fieldset>

                        <fieldset>
                            <h2 id="jouet">LISTE DES JOUETS INVENDU</h2>
                            <table>
                                <tr>
                                    <td id="entete_tableau" class="reference"><label>RÉFÉRENCE</label></td>
                                    <td id="entete_tableau"><label>DESCRIPTION</label></td>
                                </tr>
                                <?php foreach ($s['unselled'] as $o) { ?>
                                <tr>
                                    <td><label><?php echo h($o->getRef()); ?></label></td>
                                    <td><label><?php echo h($o->getDescription()); ?></label></td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td>Total</td>
                                    <td><?php echo count($s['unselled']); ?> jouet(s)</td>
                                </tr>
                            </table>
                        </fieldset>

                        <fieldset>
                            <h2 id="jouet">BILAN</h2>
                            <table style="width: 400px; text-align: left;">
                                <tr>
                                    <td id="entete_tableau">Nombre de jouets restitués</td>
                                    <td style="text-align: right;"><?php echo count($s['unselled']); ?></td>
                                </tr>
                                <tr>
                                    <td id="entete_tableau">Nombre de jouets vendus</td>
                                    <td style="text-align: right;"><?php echo count($s['selled']); ?></td>
                                </tr>
                                <tr>
                                    <td id="entete_tableau">Total des jouets vendus</td>
                                    <td style="text-align: right;"><?php echo h($s['totalSelled']); ?> €</td>
                                </tr>
                                <tr>
                                    <td id="entete_tableau">Commission</td>
                                    <td style="text-align: right;"><?php echo h($s['tax']); ?> %</td>
                                </tr>
                                <tr>
                                    <td id="entete_tableau">Total</td>
                                    <td style="text-align: right;"><?php echo h(round($s['totalMinusTax'], 2)); ?> €</td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                </div>
            </div>

            <?php $page->renderTemplateFooterPrint(); ?>
        </div>
    <?php } ?>
    </div>

    <div class="notPrintable"><?php $page->renderTemplateFooter(); ?></div>
</body>
</html>
