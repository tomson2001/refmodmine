<?php
require 'autoloader.php';

$tagger = new StanfordPOSTagger(Config::STANDFORD_POS_TAGGER_PATH);
// print_r($tagger->array_tag("The cow jumped over the moon and the dish ran away with the spoon."));
print_r($tagger->array_tag("There is a cult of ignorance in the United States."));
?>