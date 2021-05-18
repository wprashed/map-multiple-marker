<?php

/**
 * Fired when the plugin is uninstalled.
 * @link       https://internetcss.com
 * @since      1.2
 *
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( get_option('eb_map_misc')['eb_uninstall_on_delete'] == 'on' ) {
	delete_option( 'eb_map_general_settings' );
	delete_option( 'eb_map_misc' );
}