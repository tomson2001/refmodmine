<?php

/**
 * Description of MatchingVisualizer
 *
 * @author Simon
 */
class MatchingVisualizerMultiple {

    private $visualizers;
    private $epcs;
    private $matchingFile;
    private $jsCode = "";

    public function __construct($visualizers, $matchingFile, $epcs) {
        $this->visualizers = $visualizers;
        $this->matchingFile = $matchingFile;
        $this->epcs = $epcs;
    }

    // source: https://gist.github.com/Pushplaybang/5432844
    private function rgb2hex($rgb) {
        return '#' . sprintf('%02x', $rgb[0]) . sprintf('%02x', $rgb[1]) . sprintf('%02x', $rgb[2]);
    }

    public function generateVisJSCode() {

        $this->jsCode .= '
<script type="text/javascript">
function drawMatching() {
var visualizers = [];';

//        foreach ($this->visualizers as $visualizer) {
//            $this->jsCode .= $visualizer->generateVisJSCodeInMatchVizMultiple();
//        }

        if ($this->matchingFile != null) {
            $this->jsCode .= '; mappings = ';
            $this->jsCode .= $this->matchingFile->getMatchesJSON();
        } else {
            $this->jsCode .= '; mappings = ';
            $this->jsCode .= "undefined";
        }

        $this->jsCode .= ';';

        $this->jsCode .= '
// create a MatchViz
matchViz = new MatchVizMultiple(visualizers, mappings);';

//        $counter = 1;
//        foreach ($this->visualizers as $visualizer) {
//            $this->jsCode .= 'left_epcViz' . $counter . '.setMatchViz(matchViz);';
//            $this->jsCode .= 'right_epcViz' . $counter . '.setMatchViz(matchViz);';
//            $counter++;
//        }

        $this->jsCode .= '}
</script>';

        return $this->jsCode;
    }

}

?>