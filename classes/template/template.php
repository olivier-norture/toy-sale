<?php
namespace classes\template;

interface Template {
    /**
     * 
     * @param Page $page
     */
    function render($page);
}
