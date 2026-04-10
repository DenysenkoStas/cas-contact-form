<?php
defined( 'ABSPATH' ) || exit;

class CAS_CF_Email {

	public static function notify( array $data, int $id ) {
		$to      = get_option( 'admin_email' );
		$subject = sprintf(
			__( '[CAS Contact Form] New submission #%d', 'cas-contact-form' ),
			$id
		);

		$message = self::build_message( $data, $id );

		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_bloginfo( 'name' ) . ' <' . $to . '>',
		];

		wp_mail( $to, $subject, $message, $headers );
	}

	private static function build_message( array $data, int $id ): string {
		$site = esc_html( get_bloginfo( 'name' ) );
		$url  = esc_url( admin_url( 'admin.php?page=cas-contact-form' ) );
		$dob  = ! empty( $data['date_of_birth'] ) ? esc_html( $data['date_of_birth'] ) : '—';
		$nl   = ! empty( $data['newsletter'] )
			? __( 'Yes', 'cas-contact-form' )
			: __( 'No', 'cas-contact-form' );

		$rows = [
			__( 'Submission ID', 'cas-contact-form' ) => '#' . $id,
			__( 'First name', 'cas-contact-form' )    => esc_html( $data['first_name'] ),
			__( 'Last name', 'cas-contact-form' )     => esc_html( $data['last_name'] ),
			__( 'Email', 'cas-contact-form' )         => esc_html( $data['email'] ),
			__( 'Phone', 'cas-contact-form' )         => esc_html( $data['phone'] ),
			__( 'Date of birth', 'cas-contact-form' ) => $dob,
			__( 'Country', 'cas-contact-form' )       => esc_html( $data['country'] ),
			__( 'City', 'cas-contact-form' )          => esc_html( $data['city'] ),
			__( 'Street', 'cas-contact-form' )        => esc_html( $data['street'] ?? '—' ),
			__( 'ZIP', 'cas-contact-form' )           => esc_html( $data['zip'] ?? '—' ),
			__( 'Newsletter', 'cas-contact-form' )    => $nl,
		];

		$table_rows = '';
		foreach ( $rows as $label => $value ) {
			$table_rows .= "
                <tr>
                    <td style='padding:8px 12px;font-weight:600;background:#f9fafb;border:1px solid #e5e7eb;width:160px'>{$label}</td>
                    <td style='padding:8px 12px;border:1px solid #e5e7eb'>{$value}</td>
                </tr>";
		}

		return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto'>
            <h2 style='color:#0d6efd'>New form submission on {$site}</h2>
            <table style='border-collapse:collapse;width:100%'>
                {$table_rows}
            </table>
            <p style='margin-top:24px'>
                <a href='{$url}' style='background:#0d6efd;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none'>
                    View all submissions &rarr;
                </a>
            </p>
        </div>";
	}
}