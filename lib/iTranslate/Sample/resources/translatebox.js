$('document').ready(function(){     
    $('div.logoLink').click(function(){
        window.location = "http://itranslate4.eu";
    });
    $('.languageItems').click(function(e){        
        var id=e.target.id.replace(/^src_|^trg_/ig,"");
        var trg=e.target.id.replace("_"+id,"");
        $('#'+trg+'LangId').val(id);
        $('#'+trg+'LanguageSelectorTrigger').html(e.target.firstChild.nodeValue)
        $('#'+trg+'LanguageDropDown').fadeOut('fast');
        if($('#srcLangId').val()==$('#trgLangId').val()){
            $('#srcLanguageSelectorTrigger')
            .animate({
                color:"#f00"
            },300)
            .animate({
                color:"#800"
            },150);
            $('#trgLanguageSelectorTrigger')
            .animate({
                color:"#f00"
            },300)
            .animate({
                color:"#800"
            },150);
        }else{
            $('#srcLanguageSelectorTrigger')
            .animate({
                color:"#000"
            },150);
            $('#trgLanguageSelectorTrigger')
            .animate({
                color:"#000"
            },150);
        }
    });
    $('.chooselanguage').click(function(e){
        if($('#srcLanguageDropDown').css('display')!="none")
            $('#srcLanguageDropDown').css('display',"none");
        if($('#trgLanguageDropDown').css('display')!="none")
            $('#trgLanguageDropDown').css('display',"none");
        if(e.target.id=="srcLanguageSelectorTrigger"){
            $('#srcLanguageDropDown').fadeIn();
        }else{
            $('#trgLanguageDropDown').fadeIn();
        }
    });
    $('.arrowtwo').click(function(e){   
        if($.inArray($('#srcLangId').val(),trgLangCodes)!=-1 && $.inArray($('#trgLangId').val(),srcLangCodes)!=-1){
            var templang=$('#srcLanguageSelectorTrigger').html();
            var tempid=$('#srcLangId').val();
            $('#srcLanguageSelectorTrigger').html($('#trgLanguageSelectorTrigger').html());
            $('#srcLangId').val($('#trgLangId').val());
            $('#trgLanguageSelectorTrigger').html(templang);
            $('#trgLangId').val(tempid);
        }else{
            $('#srcLanguageSelectorTrigger')
            .animate({
                color:"#f00"
            },300)
            .animate({
                color:"#000"
            },150);
            $('#trgLanguageSelectorTrigger')
            .animate({
                color:"#f00"
            },300)
            .animate({
                color:"#000"
            },150);
        }
    });
    $('.translateButton').click(function(e){
        if ($('#srcLangId').val()!="" && $('#trgLangId').val()!="" && $('#textToTranslate').val()!="" && $('#srcLangId').val()!=$('#trgLangId').val())
        {
            // submit form
            $('#translateForm').get(0).submit();
        }
    });
    
});
