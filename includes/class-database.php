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

	public static function insert( array $data ) {
		global $wpdb;
		$table = $wpdb->prefix . CAS_CF_TABLE;

		$result = $wpdb->insert(
			$table,
			[
				'first_name'    => sanitize_text_field( $data['first_name'] ),
				'last_name'     => sanitize_text_field( $data['last_name'] ),
				'email'         => sanitize_email( $data['email'] ),
				'phone'         => sanitize_text_field( $data['phone'] ),
				'date_of_birth' => ! empty( $data['date_of_birth'] ) ? sanitize_text_field( $data['date_of_birth'] ) : null,
				'country'       => sanitize_text_field( $data['country'] ),
				'city'          => sanitize_text_field( $data['city'] ),
				'street'        => sanitize_text_field( $data['street'] ?? '' ),
				'zip'           => sanitize_text_field( $data['zip'] ?? '' ),
				'newsletter'    => ! empty( $data['newsletter'] ) ? 1 : 0,
			],
			[ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d' ]
		);

		return $result ? $wpdb->insert_id : false;
	}

	public static function get_all( int $limit = 50, int $offset = 0 ): array {
		global $wpdb;
		$table = $wpdb->prefix . CAS_CF_TABLE;

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} ORDER BY submitted_at DESC LIMIT %d OFFSET %d",
				$limit,
				$offset
			),
			ARRAY_A
		);
	}

	public static function count(): int {
		global $wpdb;
		$table = $wpdb->prefix . CAS_CF_TABLE;

		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
	}
}