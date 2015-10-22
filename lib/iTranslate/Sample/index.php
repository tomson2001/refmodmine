<?php

/*
 * Welcome to the PHP Sample for iTranslate4.eu
 * In this minimalist sample it will be demonstrated how a translation process
 * works with PHP and what are the requirements.
 * 
 * Some CSS and Javascript code is also included in the sample for a prettier
 * interface.
 * 
 * There are 4 main resources for this sample in this sample:
 *  - sample_api_settings.php - the settings which needs to be configured
 *  - index.php - the business logic for the sample
 *  - html_code.php - the markup that is shown in the browser
 *  - resources folder - contains the css, js, other resources for the sample
 * 
 * The iTranslate4.eu servers act similar to the Model part of an MVC pattern  
 */

//////////////////////////////
/// FLOW CONTROL OF SAMPLE ///
//////////////////////////////

// Step 1: include the iTranslate4.eu API
require_once '../iTranslate.php';
// Step 2: import settings
require_once 'api_settings.php';

// Step 3: Initiate the API instance
$api = iTranslate::getInstance("../");

// Step 4: Set the API key from the settings file
$api->setApiKey($apiSettings['apiKey']);


// variables that are used in this file as well as in html_code.php
$translationText = null;
$languages = null;

// Step 5: determine if we have something to translate and if so, translate it
if (!empty($_POST)) {
    // store the translated text in this variable which is also used in html_code.php
    $translationText = weTranslateFinally();
}

// Step 6: Get available languages to show in the markup (html_code.php)
$languages = retrieveLanguages();

// display the html code
require_once 'html_code.php';

//////////////////////////////
//// API RELATED FUNCTIONS ///
//////////////////////////////

/**
 * Translate text
 * @global type $apiSettings
 * @global type $api
 * @return null|array null if translation is not possible
 * @throws Exception 
 */
function weTranslateFinally() {
    global $apiSettings, $api;
    // some checks
    if (empty($_POST['srcLangId']) || empty($_POST['trgLangId']) || empty($_POST['srcText'])) {
        return null;
    }        

    // Step 1: retrieve postback data
    $sourceLanguageCode = $_POST['srcLangId'];
    $targetLanguageCode = $_POST['trgLangId'];
    $textToTranslate = $_POST['srcText'];

    // Step 2: check if it is possible to translate from source to target
    //           if not throw exception
    // Note: the iTranslate::getRoutes method is cacheable. This code sample
    // includes a simple file based cache    
    if (($availableRoutes = getFromCache('croutes')) == null) {
        $availableRoutes = $api->getRoutes($sourceLanguageCode, $targetLanguageCode);
        setCache("croutes", $availableRoutes);
    }
    // do we have any routes that we can use?
    if (empty($availableRoutes)) {
        throw new Exception("No translation routes have been found! Aborting translation");
    }
    
    // Step 3: do the translation vie the translate api call
    $tr = $api->translate($sourceLanguageCode, $targetLanguageCode, array($textToTranslate)//, // text segments to translate
//            1, // Optional: minimum translation alternatives
//            count($availableRoutes), // Optional: maximum translation alternatives
//            20, // Optional: timeout for translation
//            iTranslate_Translate_Domain::DOM_GENERAL, // Optional: Domain of the text to be translated
//            $availableRoutes                          // Optional: routes to be used for translation
    );

    // Step 4: return the the translated text or throw exception
    if ($tr->dat[0]->err == null) {
        return $tr->dat[0]->text[0];
    } else {
        throw new Exception("Remote server responded with an error: " . $tr->dat[0]->err);
    }
}

/**
 * Communicates with the API and the cache and retrieves the available source
 * and target languages
 * @return array languages 
 */
function retrieveLanguages() {
    global $apiSettings;
    $languages = null;
    // Check if it has already been cached and if not, get the languages from the iTranslate4.eu server
    if (($languages = getFromCache('clanguages')) == null) {
        $api = iTranslate::getInstance('../');
        $api->setApiKey($apiSettings['apiKey']);
        $languages = $api->getLanguages();
        setCache('clanguages', $languages);
    }
    return $languages;
}

//////////////////////////////
////// HELPER FUNCTIONS //////
//////////////////////////////

/**
 * Retrieves data from a cache file based on cacheID or null if no match was found
 * or cache is expired (24 hours)
 * @param string $cacheID
 * @return array data from cache
 */
function getFromCache($cacheID) {
    global $apiSettings;
    $cacheFolder = rtrim($apiSettings['cacheFolder'], " /");
    if (is_dir($cacheFolder)) {
        $cachFile = $cacheFolder . '/' . $cacheID . '.cache';
        if (!file_exists($cachFile))
            return null;
        else {
            $dayInSeconds = 86400;
            $thisTimeYesterday = time() - $dayInSeconds;
            if (filemtime($cachFile) < $thisTimeYesterday)
                return null;
            $contents = file_get_contents($cachFile);
            return unserialize($contents);
        }
    }else {
        throw new Exception("Configured cache folder does not exists!");
    }
}

/**
 * Store data in a cache file with cache ID 
 * @param string $cacheID
 * @param array $data 
 * @throws Exception 
 */
function setCache($cacheID, $data) {
    global $apiSettings;
    $cacheFolder = rtrim($apiSettings['cacheFolder'], " /");
    if (is_dir($cacheFolder)) {
        $cachFile = $cacheFolder . '/' . $cacheID . '.cache';
        file_put_contents($cachFile, serialize($data));
    } else {
        throw new Exception("Configured cache folder does not exists!");
    }
}

