<?php
/**
 * Runs on Uninstall of Phone Lookup
 *
 * @package   Phone Lookup
 * @author    Mikkel
 * @license   GPL-2.0+
 * @link      https://hexio.dk
 */

// Check that we should be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit; // Exit if accessed directly
}

include plugin_dir_path(__FILE__) . 'constants.php';

// Delete options.
delete_option( PLU_OPTION_AB );
delete_option( PLU_OPTION_TOKEN );
delete_option( PLU_OPTION_LAST_USER_ID );
