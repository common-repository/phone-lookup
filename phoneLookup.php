<?php
/*
 * Plugin Name: Phone Lookup
 * Plugin URI: http://hexio.dk
 * Description: WooCommerce extension, adding the possibility to get address by phone lookup.
 * Version: 1.0.3
 * Author: Hexio IVS
 * Author URI: https://hexio.dk
 * License: GPL2
 * Text Domain: phoneLookup
 * Domain Path: /languages
 */

$plugin_path = plugin_dir_path(__FILE__) ;

// Include constants.
include $plugin_path . 'constants.php';

//Include options page.
include $plugin_path . 'admin/options.php';

//Link to options page.
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'plu_add_action_links');
function plu_add_action_links($links) {
    $link = array('<a href="' . admin_url('options-general.php?page=phonelookup') . '">'
        . _e('Settings', 'phoneLookup') . '</a>');
    return array_merge($links, $link);
}

//Load translations.
add_action('init', 'plu_load_textdomain');
function plu_load_textdomain() {
    load_plugin_textdomain('phoneLookup', false, basename(dirname(__FILE__)) . '/languages');
}

//Checks for WooCommerce and cancels install if not found.
add_action('admin_init', 'plu_requires_woocommerce');
function plu_requires_woocommerce() {
    if (is_admin() && current_user_can('activate_plugins') && !is_plugin_active('woocommerce/woocommerce.php')) {
        add_action('admin_notices', 'plu_requires_woocommerce_notice');

        deactivate_plugins(plugin_basename(__FILE__));

        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
}

//Wrap notice in action to avoid warnings.
function plu_requires_woocommerce_notice() {
    echo '<div class="error"><p>' . __('Phone Lookup requires WooCommerce to be installed and active.', 'phoneLookup') . '</p></div>';
}

//Adds search form to woocommerce before checkout hook.
add_action('woocommerce_before_checkout_billing_form', 'plu_action_woocommerce_before_checkout_billing_form');
function plu_action_woocommerce_before_checkout_billing_form() {
    if (get_option(PLU_OPTION_TOKEN)) {
        $plu_shown = true;

        // Only allow AB test for non admins.
        // TODO consider dynamic roles to be excluded. So the site owner could specify which roles should be excluded.
        if (!current_user_can('administrator')) {
            $plu_user_id = plu_get_cookie_value(PLU_COOKIE_VISITOR_ID);

            if ($plu_user_id === null) {
                $plu_user_id = plu_set_plu_user_id();
            }

            // Enqueue script on checkout page.
            wp_enqueue_script('plu_on_checkout', plugin_dir_url(__FILE__) . 'assets/onCheckout.js');
            wp_localize_script('plu_on_checkout', 'plu_on_checkout_data', [
                'nonce' => wp_create_nonce('plu_post_user_id_action'),
                'cookie_time_spent' => PLU_COOKIE_TIME_SPENT,
                'admin_url' => admin_url('admin-post.php'),
            ]);

            if (get_option(PLU_OPTION_AB)) {
                $plu_shown = $plu_user_id % 2 === 0;
            }
        }

        if ($plu_shown) {
            include('assets/searchForm.php');
            wp_enqueue_script('plu_search_form', plugin_dir_url(__FILE__) . 'assets/searchForm.js');
            wp_enqueue_style('plu_search_form', plugin_dir_url(__FILE__). 'assets/searchForm.css');
        }
    }
}

//Adds hook to post order number.
add_action('woocommerce_thankyou', 'plu_action_new_order', 99, 1);
function plu_action_new_order($order_id) {
    wp_enqueue_script('plu_post_order_info', plugin_dir_url(__FILE__) . 'assets/postOrderInfo.js');
    wp_localize_script('plu_post_order_info', 'plu_post_order_info_data', [
        'nonce' => wp_create_nonce('plu_post_order_info_action'),
        'admin_url' => admin_url('admin-post.php'),
        'order_id' => $order_id,
    ]);
}

function plu_set_plu_user_id() {
    $last_user_id = get_option(PLU_OPTION_LAST_USER_ID);

    if ($last_user_id === false) {
        add_option(PLU_OPTION_LAST_USER_ID, 0);
        $last_user_id = 0;
    }

    $plu_user_id = $last_user_id + 1;

    // Set last used to be current
    update_option(PLU_OPTION_LAST_USER_ID, $plu_user_id);

    // Set visitor id
    setcookie(PLU_COOKIE_VISITOR_ID, $plu_user_id, time() + 2592000, '/', '', false, true);
    return $plu_user_id;
}

function plu_get_cookie_value($key, $default = null) {
    $value = $default;

    if (isset($_COOKIE[$key])) {
        $value = $_COOKIE[$key];
    }

    return $value;
}

add_action('admin_post_plu_post_user_id', 'plu_post_user_id_action');
add_action('admin_post_nopriv_plu_post_user_id', 'plu_post_user_id_action');
function plu_post_user_id_action() {
    plu_check_request('plu_post_user_id_action');

    $data = [
        'visitorId' => plu_get_cookie_value(PLU_COOKIE_VISITOR_ID),
        'isSplitTesting' => (boolean)get_option(PLU_OPTION_AB),
    ];
    $body = plu_request_remote_service('stats/visitor', [], json_encode($data), 'post');

    if (!empty($body)) {
        wp_send_json_success();
    }
    wp_send_json_error(['error' => 'Internal request failed', 'code' => 'E05'], 500);
}

add_action('admin_post_plu_post_order_info', 'plu_post_order_info_action');
add_action('admin_post_nopriv_plu_post_order_info', 'plu_post_order_info_action');
function plu_post_order_info_action() {
    plu_check_request('plu_post_order_info_action');

    $safe_order_id = false;
    if (array_key_exists('order_id', $_POST)) {
        $safe_order_id = intval(filter_var($_POST['order_id'], FILTER_SANITIZE_NUMBER_INT));
    }

    if (!$safe_order_id) {
        wp_send_json_error(['error' => 'Invalid order id', 'code' => 'E09'], 422);
    }

    $order = wc_get_order($safe_order_id);
    if ($order === false) {
        wp_send_json_error(['error' => 'Order not found', 'code' => 'E10'], 404);
    }

    $body = [
        'orderId' => $safe_order_id,
        'phoneNumber' => wc_get_order($safe_order_id)->get_billing_phone(),
        'isSplitTesting' => (boolean)get_option(PLU_OPTION_AB),
        'visitorId' => plu_get_cookie_value(PLU_COOKIE_VISITOR_ID),
        'timeSpent' => plu_get_cookie_value(PLU_COOKIE_TIME_SPENT),
        'didLookup' => (boolean)plu_get_cookie_value(PLU_COOKIE_DID_LOOKUP, 0)
    ];

    if (get_option(PLU_OPTION_AB)) {
        $body['lookupWasShown'] = plu_get_cookie_value(PLU_COOKIE_VISITOR_ID) % 2 === 0;
    }

    $body = plu_request_remote_service('stats/order', [], json_encode($body), 'post');

    // Remove cookies, to allow new visit for same user.
    plu_remove_cookie(PLU_COOKIE_TIME_SPENT);
    plu_remove_cookie(PLU_COOKIE_DID_LOOKUP);
    plu_remove_cookie(PLU_COOKIE_VISITOR_ID);

    if (!empty($body)) {
        wp_send_json_success();
    }
    wp_send_json_error(['error' => 'Internal request failed', 'code' => 'E03'], 500);
}

//Internal phone lookup, available for both users with or without privileges.
add_action('admin_post_plu_look_up', 'plu_look_up_action');
add_action('admin_post_nopriv_plu_look_up', 'plu_look_up_action');
function plu_look_up_action() {
    plu_check_request('plu_look_up_action');

    if (isset($_POST['phone'])) {
        $params = ['number' => $_POST['phone']];
        $body = plu_request_remote_service('lookup', $params);

        if (!empty($body)) {
            setcookie(PLU_COOKIE_DID_LOOKUP, 1, time() + 2592000, '/', '', false, true);
            wp_send_json_success($body);
        }
        wp_send_json_error(['error' => 'No matches', 'code' => 'E04'], 404);
    }
    wp_send_json_error(['error' => 'Missing phone', 'code' => 'E05'], 400);
}

// Stats retrieval
add_action('admin_post_plu_stats', 'plu_stats_action');
function plu_stats_action() {
    plu_check_request('plu_stats_action', true);

    $from = plu_check_date_input('from');

    $to = plu_check_date_input('to');

    $params = [
        'from' => $from->format('Y-m-d\TH:i:s\Z'),
        'to' => $to->format('Y-m-d\TH:i:s\Z')
    ];

    $body = plu_request_remote_service('stats', $params);

    if (!empty($body)) {
        wp_send_json_success($body);
    }
    wp_send_json_error(['error' => 'Stats not found', 'code' => 'E08'], 404);
}

function plu_check_date_input($expectedKey) {
    $result = null;
    if (array_key_exists($expectedKey, $_POST) && $_POST[$expectedKey]) {
        try {
            $result = new DateTime($_POST[$expectedKey]);
        } catch (Exception $e) {
        }
    }

    if ($result === null) {
        wp_send_json_error(['error' => 'Invalid date format', 'code' => 'E07'], 422);
    }

    return $result;
}

/**
 * Checks the nonce and token
 *
 * @param $nonce_action_check
 * @param bool $admin_required
 */
function plu_check_request($nonce_action_check, $admin_required = false) {
    if (!$admin_required) {
        //Fixes verify nonce when not logged in.
        if (!is_user_logged_in()) {
            $wc_s_h = new WC_Session_Handler();
            $wc_s_h->init();
        }
    }

    if (isset($_REQUEST['check']) && wp_verify_nonce($_REQUEST['check'], $nonce_action_check)) {
        if ($admin_required && !is_user_logged_in() && !is_admin()) {
            wp_send_json_error(['error' => 'Unauthorized', 'code' => 'E06'], 403);
        }
        if (!get_option(PLU_OPTION_TOKEN)) {
            wp_send_json_error(['error' => 'No token', 'code' => 'E02'], 401);
        }
    } else {
        wp_send_json_error(['error' => 'Invalid check', 'code' => 'E01'], 401);
    }
}

/**
 * Request remote service.
 *
 * @param string $path
 * @param array $params
 * @param string $body
 * @param string $method
 *
 * @return array|WP_Error
 */
function plu_request_remote_service($path, $params, $body = null, $method = 'get') {
    $url = 'https://services.hexio.dk/phonelookup/';
    $token = get_option(PLU_OPTION_TOKEN);

    if ($token) {
        // Add path to url.
        if ($path && is_string($path)) {
            $url .= $path;
        }

        // Add params to url.
        $query = http_build_query($params);
        if ($query && is_string($query)) {
            $url .= "?" . $query;
        }

        // Get plugin version.
        $plugin_data = get_plugin_data(__FILE__);
        $version = isset($plugin_data['Version']) ? $plugin_data['Version'] : 'no version detected';

        $args = [
            'method' => $method,
            'headers' => [
                'x-api-key' => $token,
                'x-plugin-version' => $version
            ]
        ];

        if ($body && is_string($body)) {
            $args['body'] = $body;
            $args['data_format'] = 'body';
            $args['headers']['Content-Type'] = 'application/json; charset=utf-8';
        }

        $response = wp_safe_remote_request($url, $args);

        if (is_wp_error($response)) {
            wp_send_json_error(['error' => 'Internal request failed', 'code' => 'E03'], 500);
        } else {
            return json_decode(wp_remote_retrieve_body($response));
        }
    }

    wp_send_json_error(['error' => 'No token', 'code' => 'E02'], 500);
}

function plu_remove_cookie($key) {
    unset($_COOKIE[$key]);
    setcookie( $key, '', time() - ( 15 * 60 ), '/' , '', false, true);
}