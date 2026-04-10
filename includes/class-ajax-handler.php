<?php
defined( 'ABSPATH' ) || exit;

class CAS_CF_Ajax_Handler {

	public function __construct() {
		add_action( 'wp_ajax_cas_cf_submit', [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_cas_cf_submit', [ $this, 'handle' ] );
	}

	public function handle() {
		if ( ! check_ajax_referer( 'cas_cf_submit', 'nonce', false ) ) {
			wp_send_json_error(
				[ 'message' => __( 'Security check failed. Please refresh and try again.', 'cas-contact-form' ) ],
				403
			);
		}

		$data   = $this->get_data();
		$errors = $this->validate( $data );

		if ( ! empty( $errors ) ) {
			wp_send_json_error(
				[
					'message' => __( 'Please correct the errors below.', 'cas-contact-form' ),
					'fields'  => $errors,
				],
				422
			);
		}

		$id = CAS_CF_Database::insert( $data );

		if ( ! $id ) {
			wp_send_json_error(
				[ 'message' => __( 'Could not save your submission. Please try again.', 'cas-contact-form' ) ],
				500
			);
		}

		CAS_CF_Email::notify( $data, $id );

		wp_send_json_success(
			[ 'message' => __( 'Thank you! Your submission has been received.', 'cas-contact-form' ) ]
		);
	}

	private function get_data(): array {
		return [
			'first_name'    => isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '',
			'last_name'     => isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '',
			'email'         => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
			'phone'         => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
			'date_of_birth' => isset( $_POST['date_of_birth'] ) ? sanitize_text_field( wp_unslash( $_POST['date_of_birth'] ) ) : '',
			'country'       => isset( $_POST['country'] )
				? ( CAS_CF_Shortcode::countries()[ sanitize_text_field( wp_unslash( $_POST['country'] ) ) ] ?? sanitize_text_field( wp_unslash( $_POST['country'] ) ) )
				: '',
			'city'          => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
			'street'        => isset( $_POST['street'] ) ? sanitize_text_field( wp_unslash( $_POST['street'] ) ) : '',
			'zip'           => isset( $_POST['zip'] ) ? sanitize_text_field( wp_unslash( $_POST['zip'] ) ) : '',
			'newsletter'    => ! empty( $_POST['newsletter'] ) ? 1 : 0,
			'terms'         => ! empty( $_POST['terms'] ),
		];
	}

	private function validate( array $data ): array {
		$errors = [];

		if ( empty( $data['first_name'] ) ) {
			$errors['first_name'] = __( 'First name is required.', 'cas-contact-form' );
		}
		if ( empty( $data['last_name'] ) ) {
			$errors['last_name'] = __( 'Last name is required.', 'cas-contact-form' );
		}
		if ( empty( $data['email'] ) || ! is_email( $data['email'] ) ) {
			$errors['email'] = __( 'A valid email address is required.', 'cas-contact-form' );
		}
		if ( empty( $data['phone'] ) ) {
			$errors['phone'] = __( 'Phone number is required.', 'cas-contact-form' );
		}
		if ( empty( $data['country'] ) ) {
			$errors['country'] = __( 'Please select a country.', 'cas-contact-form' );
		}
		if ( empty( $data['city'] ) ) {
			$errors['city'] = __( 'City is required.', 'cas-contact-form' );
		}
		if ( empty( $data['terms'] ) ) {
			$errors['terms'] = __( 'You must agree to the Terms and Conditions.', 'cas-contact-form' );
		}

		return $errors;
	}
}