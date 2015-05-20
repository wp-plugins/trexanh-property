jQuery(document).ready(function () {
    var paymentForm = jQuery('#payment_property_form');
    var submitBtn = paymentForm.find('input[type=submit]');

    var handler = StripeCheckout.configure({
        key: StripeParams.key,
        token: function(token) {
            paymentForm.append('<input type="hidden" name="stripeToken" value="' + token.id + '">');
            paymentForm.submit();
      }
    });

    submitBtn.on('click', function(e) {
      if (jQuery('[name=payment_method]:checked').val() != StripeParams.payment_method_id) {
          return true;
      }
      e.preventDefault();
      // Open Checkout with further options
      handler.open({
        name: StripeParams.sitename,
        amount: StripeParams.amount
      });
    });

    // Close Checkout on page navigation
    jQuery(window).on('popstate', function() {
      handler.close();
    });
});