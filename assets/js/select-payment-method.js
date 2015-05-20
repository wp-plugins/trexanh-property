jQuery( function ($) {
    var changePaymentMethodHandler = function (value) {
        $('[id$=_payment_description_box]').slideUp();
        var form = $('#' + value + '_payment_description_box');
            form.slideDown();
    };
    
    var paymentMethods = $('[name=payment_method]');
    paymentMethods.change(function() {
        changePaymentMethodHandler($(this).val());
    });
    changePaymentMethodHandler(paymentMethods.filter('[checked]').val());
});