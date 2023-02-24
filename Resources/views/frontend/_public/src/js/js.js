/**
 * JS Files to get the value from checkout form
 */

console.log("js is loaded");

$(document).ready(function() {
    $('#nackform').change(function() {
        console.log("on change called");

        url = $('#nackform').attr('data-ajaxUrl');
        var deliverySlot = $(this).find("input:checked").attr('value');

        $.post( url, { deliverySlot: deliverySlot }, function( data ) {
            $.loadingIndicator.close();
        });
    });
});
