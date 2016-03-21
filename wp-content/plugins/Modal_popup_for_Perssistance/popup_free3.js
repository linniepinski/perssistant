jQuery(document).ready(function () {
    if ( jQuery.cookie('popup_free') !== "1" && (window.location.pathname == '/' || window.location.pathname == '/de/')){
        setTimeout("popup_in()",5000);
    }
    jQuery('#popup_free_3').on('hidden.bs.modal', function (e) {

            jQuery.cookie('popup_free', '1', { expires: 1, path: '/' });
    })
});
function popup_in (){
    jQuery('#popup_free_3').modal('show');
    centerModals(jQuery("#popup_free_3"));
}

function centerModals($element) {
    var $modals;
    if ($element.length) {
        $modals = $element;
    } else {
        $modals = jQuery('.modal-vcenter:visible');
    }
    $modals.each( function(i) {
        var $clone = jQuery(this).clone().css('display', 'block').appendTo('body');
        var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
        top = top > 0 ? top : 0;
        $clone.remove();
        jQuery(this).find('.modal-content').css("margin-top", top);
    });
}
jQuery('.modal-vcenter').on('show.bs.modal', function(e) {
    centerModals(jQuery(this));
});
jQuery(window).on('resize', centerModals);