<?php
namespace classes\template;

use classes\template\TemplateList;

class TemplateManager {
    
    /**
     * 
     * @param TemplateList $nom
     * @return Template
     */
    private static function getTemplate($nom){
        foreach(TemplateList::getAll() as $template){
            if($nom == $template){
                $tmp = "\\classes\\template\\".$template;
                return new $tmp;
            }
        }
    }
    
    /**
     * Get the template and call the render method
     * @param TemplateList $nom The template name
     * @param Page $page The page which call the template
     */
    public static function renderTemplate($nom, $page){
        TemplateManager::getTemplate($nom)->render($page);
    }
}
