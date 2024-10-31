<div>
    <h1><? _e('Statistics', 'phoneLookup') ?></h1>
    <div class="plu_stats_container" data-admin-url="<?= admin_url('admin-post.php') ?>"
         data-nonce="<?= wp_create_nonce('plu_stats_action') ?>">
        <div class="card plu_stats_card">
            <div class="plu_stats_header">
                <? _e('Total lookups', 'phoneLookup') ?>
            </div>
            <div class="plu_stats_content" id="plu_lookups_total"><? _e('Loading', 'phoneLookup') ?></div>
        </div>
        <div class="card plu_stats_card">
            <div class="plu_stats_header"><? _e('Lookups this month', 'phoneLookup') ?></div>
            <div class="plu_stats_content" id="plu_lookups_month"><? _e('Loading', 'phoneLookup') ?></div>
        </div>
        <div class="card plu_stats_card">
            <div class="plu_stats_header"><? _e('Conversion rate', 'phoneLookup') ?></div>
            <div class="plu_stats_card_container">
                <div>
                    <? _e('From', 'phoneLookup') ?>
                    <input type="date" name="from_date" id="plu_stats_from_date">
                </div>
                <div>
                    <? _e('To', 'phoneLookup') ?>
                    <input type="date" name="to_date" id="plu_stats_to_date">
                </div>
            </div>
            <div class="plu_stats_sub_stat">
                <? _e('Visitors', 'phoneLookup') ?>:
                <span id="plu_stats_visitor_count"><? _e('Loading', 'phoneLookup') ?></span>
            </div>
            <div class="plu_stats_card_container">
                <div class="plu_stats_conv">
                    <?php if (get_option(PLU_OPTION_AB)) : ?>
                        <div class="plu_stats_header">
                            <? _e('With PhoneLookup', 'phoneLookup') ?>
                        </div>
                    <?php endif; ?>
                    <div class="plu_stats_content">
                        <span id="plu_enabled_rate"><? _e('Loading', 'phoneLookup') ?></span>
                    </div>
                    <div class="plu_stats_sub_stat">
                        <? _e('Orders', 'phoneLookup') ?>:
                        <span id="plu_enabled_value"><? _e('Loading', 'phoneLookup') ?></span>
                    </div>
                    <?php if (get_option(PLU_OPTION_AB)) : ?>
                        <div class="plu_stats_sub_stat">
                            <? _e('Avg time', 'phoneLookup') ?>:
                            <span id="plu_enabled_time"><? _e('Loading', 'phoneLookup') ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (get_option(PLU_OPTION_AB)) : ?>
                    <div id="plu_ab_enabled" style="display: none;"></div>
                    <div class="plu_stats_divider"></div>
                    <div class="plu_stats_conv">
                        <div class="plu_stats_header">
                            <? _e('Difference', 'phoneLookup') ?>
                        </div>
                        <div class="plu_stats_content">
                            <span id="plu_enabled_rate_difference"><? _e('Loading', 'phoneLookup') ?></span>
                        </div>
                        <div class="plu_stats_sub_stat">
                            <? _e('Orders','phoneLookup') ?>:
                            <span id="plu_enabled_value_difference"><? _e('Loading', 'phoneLookup') ?></span>
                        </div>
                        <div class="plu_stats_sub_stat">
                            <? _e('Avg time', 'phoneLookup') ?>:
                            <span id="plu_difference_time"><? _e('Loading', 'phoneLookup') ?></span>
                        </div>
                    </div>
                    <div class="plu_stats_divider"></div>
                    <div class="plu_stats_conv">
                        <div class="plu_stats_header">
                            <? _e('Without PhoneLookup', 'phoneLookup') ?>
                        </div>
                        <div class="plu_stats_content">
                            <span id="plu_disabled_rate"><? _e('Loading', 'phoneLookup') ?></span>
                        </div>
                        <div class="plu_stats_sub_stat">
                            <? _e('Orders', 'phoneLookup') ?>:
                            <span id="plu_disabled_value"><? _e('Loading', 'phoneLookup') ?></span>
                        </div>
                        <div class="plu_stats_sub_stat">
                            <? _e('Avg time', 'phoneLookup') ?>:
                            <span id="plu_disabled_time"><? _e('Loading', 'phoneLookup') ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="plu_stats_footer">
                    <? _e('Avg time spent, when customer did lookup:', 'phoneLookup') ?>
                    <b id="plu_did_lookup_time"><? _e('Loading', 'phoneLookup') ?></b>
                    <br>
                    <i><? _e('Avg time is measured at checkout page', 'phoneLookup') ?></i>
                </div>
            </div>
        </div>
    </div>
</div>
