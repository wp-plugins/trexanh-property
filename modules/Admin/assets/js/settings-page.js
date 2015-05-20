
(function($) {
    var pageLoadingCompleted = false;
    $(window).load(function() {
        pageLoadingCompleted = true;
    });

    var requirePayment = $("#require_payment_to_submit");
    var submitFee = $("#submit_fee");
    
    var requirePaymentClickHandler = function() {
        if (requirePayment.prop("checked")) {
            submitFee.closest("tr").show();
            // focus on submit fee if require payment checkbox is checked but not do this on page load
            if (pageLoadingCompleted) {
                submitFee.focus();
            }
        } else {
            submitFee.closest("tr").hide();
        }
    };
    requirePayment.click(requirePaymentClickHandler);
    requirePaymentClickHandler();
    
    var enableSubmission = $('#enable_property_submission');
    var requireAdminApprove = $('[name="trexanhproperty_general_settings[require_admin_to_approve]"]');
    
    var enableSubmissionClickHandler = function () {
        if (enableSubmission.prop('checked')) {
            requireAdminApprove.closest('tr').show();
            requirePayment.closest('tr').show();
            requirePaymentClickHandler();
        } else {
            requireAdminApprove.closest('tr').hide();
            requirePayment.closest('tr').hide();
            submitFee.closest('tr').hide();
        }
        
    };
    
    enableSubmission.click(enableSubmissionClickHandler);
    enableSubmissionClickHandler();
})(jQuery);