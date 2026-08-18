"use strict";
$(document).ready(function () {
    $("#shopID").select2();
});

$(document).on('change', '#shopID', function () {
    var url = $(this).data('url');
    $.ajax({
        type: 'POST',
        url: url,
        data: { 'id': $(this).val(), 'shop': false },
        dataType: "html",
        success: function (data) {
            var shop = JSON.parse(data);
            $('#merchant_id').val(shop.merchant_id);
            $('#pickup_phone').val(shop.contact_no);
            $('#pickup_address').val(shop.address);
            $('#pickup_lat').val(shop.merchant_lat);
            $('#pickup_long').val(shop.merchant_long);
        }
    });
});

$(document).on('keyup change', '#cash_collection', function () {
    var cash_collection = parseFloat($(this).val());
    if (isNaN(cash_collection) || cash_collection === '') {
        cash_collection = 0;
    }
    $('#totalCashCollection').text(cash_collection.toFixed(2));
    $('#currentPayable').text(cash_collection.toFixed(2));
});
