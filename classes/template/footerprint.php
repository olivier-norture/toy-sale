<?php
namespace classes\template;

use classes\template\Template;

class footerPrint implements Template{
    public function render($page) {
        $date = \classes\utils\Date::getCurrentDate();
        echo '
            <div class="onlyPrintable">
            <hr/>
                <div class="signature" style="float: left;">
                    <br />
                    <br />
                    <label>Tout retour d\'article ou demande de rembousement doit être effectué avant 17h aujourd\'hui.
                    Passé ce délai aucune réclamation ne pourra être effectuée.
                    Nous vous remercions de votre compréhension.</label>

                    <br/>
                    <br/>
                    <label>Le : ' . $date . '</label>

                    <br/>
                    <br/>
                    <label>A : Chambly</label>

                    <br/>
                    <br/>
                    <br/>
                    <label>Signature :</label>
                </div>
                <div style="float: right; padding-top: 30px;">
                    <img src="images/logo.png"/>
                </div>
                <hr/>
            </div>
        ';
    }
}
