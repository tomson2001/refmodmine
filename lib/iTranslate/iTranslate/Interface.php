<?php

interface iTranslate_Interface {

    public function getLanguages();

    public function getRoutes($src, $trg);

    public function getProviders();
    
    public function translate($src, $trg, $dat, $min = 1, $max = 1, $timeout = 20, $dom = iTranslate_Translate_Domain::DOM_GENERAL, $rid = array());
}
