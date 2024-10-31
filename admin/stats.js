let nonce = null;
let adminUrl = null;
let abEnabled = null;


jQuery(document).ready(function () {
  const statsContainer = jQuery('.plu_stats_container');
  nonce = statsContainer.data('nonce');
  adminUrl = statsContainer.data('admin-url');
  abEnabled = jQuery('#plu_ab_enabled').length > 0;

  // All time stats

  plu_update_stat(jQuery('#plu_lookups_total'), new Date(2015, 1));

  // Monthly stats

  let date = new Date();
  date = new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(),
    date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds());

  // First day of month in UTC
  const firstDay = new Date(date.getUTCFullYear(), date.getUTCMonth(), 1,
    date.getUTCHours());

  // Last day of Month, 1 millis to midnight, in UTC
  const lastDay = new Date(date.getUTCFullYear(), date.getUTCMonth() + 1, 0,
    date.getUTCHours() + 23, 59, 59, 999);

  plu_update_stat(jQuery('#plu_lookups_month'), firstDay, lastDay);
  const from = jQuery('#plu_stats_from_date');
  const to = jQuery('#plu_stats_to_date');

  const tomorrow = new Date(date.getFullYear(), date.getMonth(), date.getDate() + 1);
  const lastMonth = new Date(date.getFullYear(), date.getMonth() - 1, date.getDate());

  from.on('change', () => plu_update_order_stats());
  to.on('change', () => plu_update_order_stats());

  from.val(plu_format_date_for_input(lastMonth));
  to.val(plu_format_date_for_input(tomorrow));
  plu_update_order_stats()
});

function plu_format_date_for_input(date) {
  return date.getFullYear() + '-' + ("0" + (date.getMonth() + 1)).slice(-2) + '-' + ("0" + date.getDate()).slice(-2);
}

function plu_update_stat(element, from, to = null) {
  plu_get_stats_by_period(from, to)
    .done((response) => {
      element.html(response.data.lookupCount);
    })
    .fail((error) => {
      const response = error.responseJSON;
      element.html(response.data.code);
    });
}

function plu_get_stats_by_period(from, to = null) {
  if (to === null) {
    to = new Date();
  }

  let data = {
    'action': 'plu_stats',
    'check': nonce,
    'from': from.toISOString(),
    'to': to.toISOString(),
  };

  return jQuery.post(adminUrl, data);
}

function plu_update_order_stats() {
  let from = new Date(jQuery('#plu_stats_from_date').val());
  let to = new Date(jQuery('#plu_stats_to_date').val());

  if (isNaN(from)) {
    from = new Date();
  }

  if (isNaN(to)) {
    to = new Date();
  }

  plu_get_stats_by_period(from, to)
    .done((response) => {
      const data = response.data;
      const withPhoneLookup = plu_get_conversion_rate(data.numberOfOrdersWithLookup, data.numberOfVisitors, abEnabled);

      jQuery('#plu_enabled_rate').html(withPhoneLookup + '%');
      jQuery('#plu_enabled_value').html(data.numberOfOrdersWithLookup);
      jQuery('#plu_stats_visitor_count').html(data.numberOfVisitors);
      const didLookUpTime = ((data.timeSpentDidLookup - data.timeSpentDidNotLookup) / 1000).toFixed(1);
      jQuery('#plu_did_lookup_time').html((didLookUpTime > 0 ? '+' : '') + didLookUpTime + 's');

      if (abEnabled) {
        const withoutPhoneLookup = plu_get_conversion_rate(data.numberOfOrdersWithoutLookup, data.numberOfVisitors, abEnabled);
        jQuery('#plu_disabled_rate').html(withoutPhoneLookup + '%');
        jQuery('#plu_disabled_value').html(data.numberOfOrdersWithoutLookup);


        const diffRate = plu_round(withPhoneLookup - withoutPhoneLookup);
        const diffValue = data.numberOfOrdersWithLookup - data.numberOfOrdersWithoutLookup;
        jQuery('#plu_enabled_rate_difference').html((diffRate > 0 ? '+' : '') + diffRate + '%');
        jQuery('#plu_enabled_value_difference').html((diffValue > 0 ? '+' : '') + diffValue);

        const timeWith = (data.timeSpentWithLookup / 1000).toFixed(1);
        const timeWithOut = (data.timeSpentWithoutLookup / 1000).toFixed(1);
        const timeDiff = timeWithOut - timeWith;
        jQuery('#plu_disabled_time').html('' + timeWith + 's');
        jQuery('#plu_enabled_time').html('' + timeWithOut + 's');
        jQuery('#plu_difference_time').html((timeDiff > 0 ? '+' : '') + timeDiff + 's');

      }
    })
    .fail((error) => {
      const response = error.responseJSON;

      jQuery('#plu_enabled_rate').html(response.data.code);
      jQuery('#plu_enabled_value').html(response.data.code);

      if (abEnabled) {
        jQuery('#plu_disabled_rate').html(response.data.code);
        jQuery('#plu_disabled_value').html(response.data.code);

        jQuery('#plu_enabled_rate_difference').html(response.data.code);
        jQuery('#plu_enabled_value_difference').html(response.data.code);

        jQuery('#plu_disabled_time').html(response.data.code);
        jQuery('#plu_enabled_time').html(response.data.code);
        jQuery('#plu_difference_time').html(response.data.code);
        jQuery('#plu_did_lookup_time').html(response.data.code);
      }
    });
}

function plu_get_conversion_rate(numberOfOrders, numberOfVisitors, abEnabled) {
  let rate = 0;

  if (abEnabled) {
    numberOfVisitors = numberOfVisitors / 2;
  }

  if (numberOfVisitors > 0) {
    rate = plu_round((numberOfOrders / numberOfVisitors) * 100);
  }

  return rate;
}

function plu_round(toBeRounded) {
  return Math.round(toBeRounded * 100) / 100;
}
