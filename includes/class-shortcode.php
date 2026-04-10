<?php
defined( 'ABSPATH' ) || exit;

class CAS_CF_Shortcode {

	public function __construct() {
		add_shortcode( 'cas_contact_form', [ $this, 'render' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function enqueue_assets() {
		global $post;

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'cas_contact_form' ) ) {
			wp_enqueue_style(
				'cas-cf-style',
				CAS_CF_URL . 'assets/css/form.css',
				[],
				CAS_CF_VERSION
			);
			wp_enqueue_script(
				'cas-cf-script',
				CAS_CF_URL . 'assets/js/form.js',
				[ 'jquery' ],
				CAS_CF_VERSION,
				true
			);
			wp_localize_script( 'cas-cf-script', 'casCF', [
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'cas_cf_submit' ),
			] );
		}
	}
}