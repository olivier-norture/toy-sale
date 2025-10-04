<?php
namespace classes\template;

use classes\template\Template;

class headerPrint implements Template{
    public function render($page) {
        echo '
            <div class="onlyPrintable">
                <div style="float left; display: inline-block">
                    <img src="images/logo.png"/>
                </div>
                <div style="float: right; display: "inline-block;">
                    <p>
                        <span class="bold_text">Bourse aux Jouets Chambly</span>
                        <br />
                        <span class = "italic_text">Association loi 1901</span>
                        <br />
                        60230 Chambly
                    </p>
                </div>
            </div>
        ';
    }

}
