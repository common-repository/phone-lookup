function plu_is_number_key(evt) {
    let e = evt || window.event;
    let charCode = e.which || e.keyCode;

    if (charCode > 31 && (charCode < 47 || charCode > 57)) {
        return false;
    }

    return !e.shiftKey;
}

function plu_insert_value_and_validate(selectorId, value) {

    if (value) {
        const input = jQuery('#' + selectorId);

        if (input) {
            input.val(value);
            input.trigger('validate');
        }
    }
}

function plu_parse_and_insert_data(data) {
    if (data && data.address) {
        let subAddress = data.address.floor + data.address.door;
        let address = data.address.roadName + ' ' + data.address.houseNumberNumericFrom + ' ' +
            data.address.houseNumberCharacterFrom + (subAddress ? ', ' + subAddress : '');

        plu_insert_value_and_validate('billing_first_name', data.firstName);
        plu_insert_value_and_validate('billing_last_name', data.lastName);
        plu_insert_value_and_validate('billing_company', data.companyName);
        plu_insert_value_and_validate('billing_address_1', address);
        plu_insert_value_and_validate('billing_postcode', data.address.postalCode);
        plu_insert_value_and_validate('billing_city', data.address.postalDistrict);
        plu_insert_value_and_validate('billing_phone', data.contactInfo.telephoneNumber);
        plu_insert_value_and_validate('billing_email', data.contactInfo.emailAddress);

        jQuery('#billing_country').trigger('validate');

        // If any required inputs which not are validated are left, scroll to that element, else scroll to order button.
        const nextInput = jQuery('form.checkout').find('.validate-required:not(.woocommerce-validated) input:visible').first();
        const orderButton = jQuery('#place_order:visible');

        let scrollToElement = null;
        if (nextInput && nextInput.offset() && nextInput.offset().top) {
            scrollToElement = nextInput;
        } else if (orderButton && orderButton.offset() && orderButton.offset().top) {
            scrollToElement = orderButton;
        }

        if (scrollToElement && scrollToElement.offset() && scrollToElement.offset().top) {
            jQuery('html, body').animate({
                scrollTop: scrollToElement.offset().top - 100
            }, 500);
            nextInput.focus();
        }
    }
}

function plu_look_up(adminUrl, nonce) {
    jQuery('.plu_status_message').addClass('plu_hidden');
    let phone = jQuery('#plu_phone').val();

    if (phone && phone.length === 8) {
        jQuery('#plu_button').addClass('disabled');
        jQuery('#plu_loader').removeClass('plu_hidden');

        let data = {
            'action': 'plu_look_up',
            'check': nonce,
            'phone': phone
        };

        jQuery.post(adminUrl, data)
            .done(function (response) {
                if (response.success && Array.isArray(response.data) && response.data[0]) {
                    plu_parse_and_insert_data(response.data[0]);
                    jQuery('#plu_success').removeClass('plu_hidden');
                } else {
                    jQuery('#plu_error_no_match').removeClass('plu_hidden');
                    plu_insert_value_and_validate('billing_phone', phone);
                }

            }).fail(function (response) {

            let status = response.status;

            if (status === 404) {
                jQuery('#plu_error_no_match').removeClass('plu_hidden');
                plu_insert_value_and_validate('billing_phone', phone);
            } else {
                jQuery('#plu_error_tech_code').text(response.responseJSON.data.code);
                jQuery('#plu_error_tech').removeClass('plu_hidden');
            }

        }).complete(function () {
            jQuery('#plu_button').removeClass('disabled');
            jQuery('#plu_loader').addClass('plu_hidden');
        });

    } else {
        jQuery('#plu_warning').removeClass('plu_hidden');
    }
}
