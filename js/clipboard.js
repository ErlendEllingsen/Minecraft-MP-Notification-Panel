$(document).ready(function(){
    $('a#copyactlink').zclip({
        path:'js/clip/ZeroClipboard.swf',
        copy:function(){return $('input#actlink').val();}
    });

});