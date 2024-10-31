<div class="plu_container">
    <p><? _e('Get address by phone.', 'phoneLookup') ?></p>
    <div class="plu_form">
        <input id="plu_phone" type='text' onkeypress="return plu_is_number_key(event)" maxlength="8">
        <button id="plu_button" type="button" class="button" onclick="plu_look_up(
                '<?= admin_url('admin-post.php'); ?>', '<?= wp_create_nonce('plu_look_up_action') ?>')">
            <? _e('Search', 'phoneLookup') ?>
            <img id="plu_loader" class="plu_hidden" src="<?= plugin_dir_url(__FILE__) . 'spinner.gif'; ?>">
        </button>
    </div>
    <i id="plu_success" class="plu_hidden plu_status_message">
        <? _e('Result found.', 'phoneLookup') ?>
    </i>
    <i id="plu_warning" class="plu_hidden plu_status_message">
        <? _e('Insert a valid phone number of 8 digits.', 'phoneLookup') ?>
    </i>
    <i id="plu_error_no_match" class="plu_hidden plu_status_message">
        <? _e('No matches found, try again.', 'phoneLookup') ?>
    </i>
    <i id="plu_error_tech" class="plu_hidden plu_status_message">
        <? _e('Sorry, a technical error occurred, try again later. ', 'phoneLookup') ?>
        <? _e('Error code', 'phoneLookup') ?>: <i id="plu_error_tech_code"></i>.
    </i>
    <hr class="plu_hr">
</div>
