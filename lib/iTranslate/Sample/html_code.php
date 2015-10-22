<!DOCTYPE html>
<html>
    <head>
        <title>iTranslate4.eu Sample Application</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">                
        <link rel="stylesheet" type="text/css" media="all" href="resources/boxstyle.css" />
        <script type="text/javascript" src="resources/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="resources/jquery-ui-1.8.13.custom.min.js"></script>        
        <script type="text/javascript">
            var trgLangCodes = new Array(<?php $trg = null; foreach($languages->trg as $lng): $trg .= "\"{$lng}\", "; endforeach; echo trim($trg, ", "); ?>);
            var srcLangCodes = new Array(<?php $src = null; foreach($languages->src as $lng): $src .= "\"{$lng}\", "; endforeach; echo trim($src, ", "); ?>);
        </script>
        <script type="text/javascript" src="resources/translatebox.js" ></script>
    </head>
    <body>        
        <form action="#" method="post" id="translateForm" class="size234x60">
            <div class="gradientBackground">
                <div class="logoLink">&nbsp;</div>
                <div class="boxContainer">
                    <div class="langSelectors"> 
                        <div class="chooselanguage" id="srcLanguageSelectorTrigger">
                            <?php echo isset($_POST['srcLangId']) ? $_POST['srcLangId'] : 'en'; ?>
                        </div>
                        <div class="languageDropDown" id="srcLanguageDropDown" >
                            <?php
                            foreach ($languages->src as $lang):
                                echo "<div class=\"languageItems\" id=\"src_{$lang}\">{$lang}</div>";
                            endforeach;
                            ?>    
                        </div>
                        <input type="hidden" name="srcLangId" id="srcLangId" value="<?php echo isset($_POST['srcLangId']) ? $_POST['srcLangId'] : 'en'; ?>" />
                        <div class="arrowtwo"></div>
                        <div class="chooselanguage" id="trgLanguageSelectorTrigger">
                            <?php
                            echo isset($_POST['trgLangId']) ? $_POST['trgLangId'] : 'hu';
                            ?>
                        </div>
                        <div class="languageDropDown" id="trgLanguageDropDown" >
                            <?php
                            foreach ($languages->trg as $lang):
                                echo "<div class=\"languageItems\" id=\"trg_{$lang}\">{$lang}</div>";
                            endforeach;
                            ?>                            
                        </div>
                        <input type="hidden" name="trgLangId" id="trgLangId" value="<?php echo isset($_POST['trgLangId']) ? $_POST['trgLangId'] : 'hu' ?>" />
                    </div>
                    <div class="transText"><textarea id="textToTranslate" name="srcText" class="transText" rows="8" cols="200"><?php if(isset($_POST['srcText'])): echo $_POST['srcText']; endif; ?></textarea></div>
                    <div class="translateButton">Translate</div>                    
                </div>                
                <?php
                if (isset($translationText) && !empty($translationText)):
                    echo '<textarea class="transTrans" style="">'.$translationText.'</textarea></p>';
                endif;
                ?>
            </div>
        </form>        
    </body>
</html>