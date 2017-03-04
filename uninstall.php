<?php
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
global $wpdb;

delete_option("i9-idx-key");

$wpdb->query("DELETE FROM `{$wpdb->prefix}options` WHERE option_name LIKE '_transient_idx_%' OR option_name LIKE '_transient_timeout_idx_%'");
