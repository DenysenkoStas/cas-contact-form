<?php
defined( 'ABSPATH' ) || exit;

class CAS_CF_Database {

	public static function create_table() {
		global $wpdb;

		$table   = $wpdb->prefix . CAS_CF_TABLE;
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            first_name    VARCHAR(100)    NOT NULL DEFAULT '',
            last_name     VARCHAR(100)    NOT NULL DEFAULT '',
            email         VARCHAR(255)    NOT NULL DEFAULT '',
            phone         VARCHAR(50)     NOT NULL DEFAULT '',
            date_of_birth DATE                     DEFAULT NULL,
            country       VARCHAR(100)    NOT NULL DEFAULT '',
            city          VARCHAR(100)    NOT NULL DEFAULT '',
            street        VARCHAR(255)             DEFAULT '',
            zip           VARCHAR(20)              DEFAULT '',
            newsletter    TINYINT(1)      NOT NULL DEFAULT 0,
            submitted_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}