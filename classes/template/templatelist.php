<?php
namespace classes\template;

/**
 * Provides list of all Template
 */
class TemplateList {
    /**
     * @var string The template whith participant's informations
     */
    public static $PARTICIPANT_INFO = "ParticipantInfo";
    
    /**
     * @var string The template with header menu
     */
    public static $HEADER = "Header";
    
    /**
     * @var string The template with the header for printing web page
     */
    public static $HEADER_PRINT = "HeaderPrint";
    
    /**
     * @var string The template with the footer
     */
    public static $FOOTER = "Footer";
    
    /**
     * @var string The template with the footer for printing web page 
     */
    public static $FOOTER_PRINT = "FooterPrint";
    
    /**
     * @var type The template for render alerting message
     */
    public static $ALERT_MESSAGE = "AlertMessage";
    
    /**
     * 
     * @return string[] Array of all existing template
     */
    public static function getAll(){
        return array(
            TemplateList::$PARTICIPANT_INFO,
            TemplateList::$HEADER,
            TemplateList::$HEADER_PRINT,
            TemplateList::$FOOTER,
            TemplateList::$FOOTER_PRINT,
            TemplateList::$ALERT_MESSAGE
            );
    }
}
