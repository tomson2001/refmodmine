<?php

require 'Interface.php';

abstract class iTranslate_Abstract implements iTranslate_Interface {

    const API_URL = "http://itranslate4.eu/api/";

    /**
     * @var iTranslate_Abstract 
     */
    private static $_instance;
    protected $_apiKey = null;
    protected $_parameters = array();
    private $_lastCall = null;
    private $_lastResponseJson = null;
    private $_basePath = null;

    /**
     * Get the instance of the translation API - Singleton Pattern
     * @return iTranslate 
     */
    public static function getInstance($base = null) {
        if (!(self::$_instance instanceof iTranslate_Abstract)) {
            $c = get_called_class();
            self::$_instance = new $c($base);
        }
        return self::$_instance;
    }

    private final function __construct($basePath) {
        // register autoloader
        require_once 'Loader.php';
        $basePath = rtrim($basePath, " /");
        $this->_basePath = realpath($basePath);
        spl_autoload_register("iTranslate_Loader::loadClass");
    }

    public function getBasePath() {
        return $this->_basePath;
    }

    public function setApiKey($key) {
        if (empty($key))
            throw new Exception("Key must not be empty");

        $this->_apiKey = $key;
        return $this;
    }

    protected function AddParam($key, $value = null) {

        if ($key instanceof iTranslate_Parameter_Abstract)
            $this->_parameters[] = $key;
        elseif (is_string($key) && strpos($key, "=") !== false) {
            list($key, $value) = explode("=", $key, 1);
            $this->_parameters[] = new iTranslate_Parameter($key, $value);
        } else
            $this->_parameters[] = new iTranslate_Parameter($key, $value);

        return $this;
    }

    protected function GetParam($key) {
        if (isset($this->_parameters[$key]))
            return $this->_parameters[$key];
        else
            return null;
    }

    protected function DoRequest($function) {

        array_unshift($this->_parameters, new iTranslate_Parameter("auth", $this->_apiKey));

        $requestUrl = rtrim(self::API_URL, '/') . "/" . $function . "?" . $this->AssembleParameters();
        $this->_lastCall = $requestUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = trim(curl_exec($ch));        
        curl_close($ch);
        
        $chrFirst = substr($response, 0, 1);
        if($chrFirst != "{" && $chrFirst != '[') {           
            throw new Exception("The iTranslate.eu API encountered an error: ".$response, (int)$response);            
        }
                
        $this->_lastResponseJson = $response;
        return $response;
    }

    private function AssembleParameters() {
        $strReturn = "";
        foreach ($this->_parameters as $param) {
            $strReturn .= $param->__toString() . "&";
        }
        return rtrim($strReturn, '&');
    }

    public function getLastCall() {
        return $this->_lastCall;
    }

    public function getLastJson() {
        return $this->_lastResponseJson;
    }

}