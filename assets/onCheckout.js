jQuery(() => {
  jQuery.post(plu_on_checkout_data.admin_url, {
    'action': 'plu_post_user_id',
    'check': plu_on_checkout_data.nonce,
  });
});

jQuery(document).ready(function() {
    // Set cookie on checkout.
    let plu_time_spent_start = performance.now();
    jQuery("form.woocommerce-checkout").on('submit', function() {
        let plu_time_spent = Math.floor(performance.now() - plu_time_spent_start);
        document.cookie = plu_on_checkout_data.cookie_time_spent + "=" + (plu_time_spent || "") + "; path=/";
    });
});
