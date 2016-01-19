(function($) {
    $(document).ready(function() {

        if ($('#et_users').length > 0) {
            var sellers = JSON.parse(jQuery('#et_users').html());

            $('#et_expired_date').datepicker({
                dateFormat: edit_ad.dateFormat,
                defaultDate: new Date(jQuery('#et_date').val())
            });

            // $('#et_expired_date').datepicker({
            // 	dateFormat : edit_ad.dateFormat
            // });

            $('#address').blur(function(event) {
                var address = $(this).val();
                //gmaps = new GMaps
                GMaps.geocode({
                    address: address,
                    callback: function(results, status) {
                        if (status == 'OK') {
                            var latlng = results[0].geometry.location;
                            $('#et_location_lat').val(latlng.lat());
                            $('#et_location_lng').val(latlng.lng());
                        }
                    }
                });
            });

            $('#seller').autocomplete({
                source: sellers,
                focus: function(event, ui) {
                    $('#seller').val(ui.item.label);
                    //console.log(ui.item.label);
                    return false;
                },
                select: function(event, ui) {
                    $('#seller').val(ui.item.label);
                    $('input[id=et_author]').val(ui.item.value);
                    $('#post_author_override').val(ui.item.value).change();
                    return false;
                }
            });

            $('#et_price').keyup(function() {
                var price = $('#et_price').val();
                while (/(\d+)(\d{3})/.test(price.toString())) {
                    price = price.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
                }
                $('.price span').html(price);
            });
        }

    });
})(jQuery);