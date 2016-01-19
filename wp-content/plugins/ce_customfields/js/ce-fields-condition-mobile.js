/**
 * Created by thuytien on 10/31/2014.
 */
(function ($) {
    var addedField = {};
    $(document).ready(function() {
    
    $("#ad_categories").change( function() {
        var cats = $(this).val();
        var $fields = getCatField();
        $.each($fields, function($field){
            var belongcats = $(this).data("cats");
            var  intersect = $.arrayIntersect(belongcats, cats);
            if(intersect.length > 0)
            {
                $(this).css('display','');
                var $outer = $(this).find("div.ui-disabled");
                if($outer.length > 0) {
                    $outer.addClass("ui-enable").removeClass("ui-disabled");
                    $outer.removeClass("mobile-textinput-disabled");
                    //$outer.removeClass("ui-state-disabled");
                    $(this).find("input, textarea").each(function () {
                        $(this).removeProp("disabled");
                    });
                }
            }
            else
            {
                var $outer = $(this).find("div.ui-enable");
                if($outer.length > 0)
                {
                    $(this).css('display','none');
                    $outer.addClass("ui-disabled").removeClass("ui-enable");
                    $(this).find("input, textarea").each(function(){
                        $(this).prop("disabled", "disabled");
                    });
                }
            }
        });
    });
    $( "#ad_categories" ).trigger( "change" );  
    });
    $.arrayIntersect = function(a, b) {
        return $.grep(a, function(i) {
            return $.inArray(i, b) > -1;
        });
    };
    function getCatField(catId) {
        return $('.hidden-custom-field');
    }
})(jQuery);