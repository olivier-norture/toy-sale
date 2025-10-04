<?php
namespace classes\template;

use classes\template\Template;

class footer implements Template{
    public function render($page) {
        echo'
            <div id="footer" class="notPrintable">
                    <div id="footer_contact" class="footer_info">
                        <p>
                            <span class="bold_text">Bourse aux Jouets Chambly</span>
                            <br />
                            <span class = "italic_text">Association loi 1901</span>
                            <br />
                            60230 Chambly
                        </p>
                    </div>
                    <a href="index.php" id="footer_logo">Accueil</a>
            </div>
        ';
    }
}
