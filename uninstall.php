<?php
/**
 * Runs automatically when the plugin is deleted from the WordPress admin.
 * Drops the custom database table and removes plugin options.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( ! defined( 'CAS_CF_TABLE' ) ) {
	define( 'CAS_CF_TABLE', 'cas_contact_submissions' );
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-database.php';

CAS_CF_Database::drop_table();