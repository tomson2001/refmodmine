<?php

class iTranslate_Response_Translation_Object {
    
    /**
     * Which provider was used
     * @var string
     */
    public $rid;
    
    /**
     * Array translated texts
     * @var array of translated strings 
     */
    public $text = array();
    
    /**
     * Error in format "<number> <description>"
     * @var type 
     */
    public $err;
}