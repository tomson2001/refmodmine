<?php

require_once 'iTranslate/Abstract.php';

/**
 * Class responsible to handling the communication with the iTranslate4.eu servers 
 */
class iTranslate extends iTranslate_Abstract {

    /**
     * This function provides information about available source and target languages. 
     * These languages are valid options as src and trg parameters in Translate function.
     * @return iTranslate_Response_Languages 
     */
    public function getLanguages() {
        $fnc = iTranslate_RemoteFunctions::FNC_GETLANGUAGES;
        $response = json_decode($this->DoRequest($fnc));
        
        $langs = new iTranslate_Response_Languages();
        foreach ($response as $k => $v) {
            $langs->{$k} = $v;
        }
        return $langs;
    }

    /**
     * This function provides information about the available alternative translations returning 
     * the translation route list for a given language pair. This list consists of route IDs 
     * which are valid options as rid parameter in Translate function. 
     * @param string $src
     * @param string $trg
     * @return array Array of available routes 
     */
    public function getRoutes($src, $trg) {

        $this->AddParam("src", $src);
        $this->AddParam("trg", $trg);

        $fnc = iTranslate_RemoteFunctions::FNC_GETROUTES;
        $response = json_decode($this->DoRequest($fnc), false);

        return $response;
    }

    /**
     * This function tells the full name of the different machine translation providers (companies) 
     * which take part in the iTranslate4 service. 
     * @return array containing provider ID - provider name pairs as assoc array
     */
    public function getProviders() {
        $fnc = iTranslate_RemoteFunctions::FNC_GETPROVIDERS;

        $response = json_decode($this->DoRequest($fnc), true);

        return $response;
    }

    /**
     * Translate text segments from source to target language
     * @param string source language
     * @param string target language
     * @param string[] text segemtns to translate     
     * @param int Optional: minimum alternative translations (default=1)
     * @param int Optional: maximum alternative translations (default=1)
     * @param int Optional: timeout in seconds (default=20)
     * @param ITranslate_Translate_Domain Optional: domain of text (default=ITranslate_Translate_Domain::DOM_GENERAL)      
     * @param array Optional: routes to use for translation (Retrieved with getRoutes method)
     * @return iTranslate_Response_Translations translation object     
     */
    public function translate($src, $trg, $dat, $min = 1, $max = 1, $timeout = 20, $dom = iTranslate_Translate_Domain::DOM_GENERAL, $rid = null) {
        $fnc = iTranslate_RemoteFunctions::FNC_TRANSLATE;

        $this->AddParam("src", $src)->
                AddParam("trg", $trg)->
                AddParam("min", $min)->
                AddParam("max", $max)->
                AddParam("timeout", $timeout)->
                AddParam("dom", $dom);        

        if ($rid !== null) {
            if (is_array($rid))
                foreach ($rid as $r)
                    $this->AddParam('rid', $r);
            elseif (is_string($rid) && !empty($rid)) {
                $this->AddParam("rid", $rid);
            }
        }

        if (is_array($dat))
            foreach ($dat as $d)
                $this->AddParam("dat", urlencode($d));
        else
            $this->AddParam("dat", urlencode($dat));

        $arrResponse = json_decode($this->DoRequest($fnc), true);

        $response = new iTranslate_Response_Translations();

        $arrTransO = array();
        foreach ($arrResponse["dat"] as $tr) {
            $tmp = new iTranslate_Response_Translation_Object();
            foreach ($tr as $k => $v) {
                $tmp->{$k} = $v;
            }
            $arrTransO[] = $tmp;
        }
        $response->dat = $arrTransO;
        return $response;
    }

}