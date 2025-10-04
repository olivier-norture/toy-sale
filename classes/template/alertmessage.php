<?php
namespace classes\template;

/**
 * Description of alertmessage
 *
 * @author olivier
 */
class AlertMessage implements Template{
    public function render($page) {
        $display = $page->getErrorMessage() != null ? "block" : "none";

        echo'<div class="errorMessage" style="display: '. $display .';">
                        <img src="images/alert.png" style="float: left;"/>
                        <div><label>' . $page->getErrorMessage() . '</label></div>
                        <img src="images/alert.png" style="float: right;"/>
                    </div>';
    }
}
