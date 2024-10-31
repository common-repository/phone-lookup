jQuery(() => {
  jQuery.post(plu_post_order_info_data.admin_url, {
    action: 'plu_post_order_info',
    check: plu_post_order_info_data.nonce,
    order_id: plu_post_order_info_data.order_id,
  });
});
