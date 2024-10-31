<?php

/* Phone Lookup Settings Page */

class Plu_Settings_Page
{
    public function __construct() {
        add_action('admin_menu', [$this, 'plu_create_settings']);
        add_action('admin_init', [$this, 'plu_setup_sections']);
        add_action('admin_init', [$this, 'plu_setup_fields']);
    }

    public function plu_create_settings() {
        $page_title = __('Phone Lookup settings', 'phoneLookup');
        $menu_title = __('Phone Lookup', 'phoneLookup');
        $capability = 'manage_options';
        $slug = 'phonelookup';
        $callback = [$this, 'plu_settings_content'];
        $icon = 'dashicons-phone';
        $position = 80;
        add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
    }

    public function plu_settings_content() { ?>
        <div class="wrap">
            <h1><? _e('Phone Lookup settings', 'phoneLookup') ?></h1>
            <p>
                <? _e('Get and fill out address by phone number, for WooCommerce.', 'phoneLookup') ?>
                <i><? _e('We only support Denmark at the moment.', 'phoneLookup') ?></i>
            </p>
            <p>
                <? _e('Contact', 'phoneLookup') ?>
                <a href="mailto:support@hexio.dk">support@hexio.dk</a>
                <? _e('for more information.', 'phoneLookup') ?>
            </p>
            <?php settings_errors(); ?>
            <form method="POST" action="options.php">
                <?php
                settings_fields('phonelookup');
                do_settings_sections('phonelookup');
                submit_button();
                ?>
            </form>

            <?php include('stats.php'); ?>
        </div> <?php
        wp_enqueue_script('plu_stats', plugin_dir_url(__FILE__) . 'stats.js');
        wp_enqueue_style('plu_stats', plugin_dir_url(__FILE__). 'stats.css');
    }

    public function plu_setup_sections() {
        add_settings_section('phonelookup_section', '', [], 'phonelookup');
    }

    public function plu_setup_fields() {
        $fields = [
            [
                'label' => __('Token', 'phoneLookup'),
                'id' => PLU_OPTION_TOKEN,
                'type' => 'text',
                'section' => 'phonelookup_section',
                'placeholder' => __('Insert token', 'phoneLookup'),
                'style' => 'width: 100%',
                'desc' => __('Contact', 'phoneLookup') . ' <a href="mailto:support@hexio.dk">support@hexio.dk</a> ' . __('to receive a token.', 'phoneLookup'),
            ],
            [
                'label' => __('A/B Test', 'phoneLookup'),
                'id' => PLU_OPTION_AB,
                'type' => 'checkbox',
                'section' => 'phonelookup_section',
                'placeholder' => '',
                'style' => '',
                'desc' => __('Check this to enable A/B Testing, to test whether this plugin makes any difference for your shop.', 'phoneLookup'),
            ],
        ];

        foreach ($fields as $field) {
            add_settings_field($field['id'], $field['label'], [$this, 'plu_field_callback'], 'phonelookup', $field['section'], $field);
            register_setting('phonelookup', $field['id']);
        }
    }

    public function plu_field_callback($field) {
        $value = get_option($field['id']);
        switch ($field['type']) {
            case 'checkbox':
                printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="1" %4$s style="%5$s" />',
                    $field['id'], $field['type'], $field['placeholder'], $value ? 'checked' : '', $field['style']);
                break;
            default:
                printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" style="%5$s" />',
                    $field['id'], $field['type'], $field['placeholder'], $value, $field['style']);
        }
        if ($desc = $field['desc']) {
            printf('<p class="description">%s </p>', $desc);
        }
    }
}

new Plu_Settings_Page();
