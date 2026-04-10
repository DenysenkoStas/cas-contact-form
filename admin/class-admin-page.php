<?php
defined( 'ABSPATH' ) || exit;

class CAS_CF_Admin_Page {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function register_menu() {
		add_menu_page(
			__( 'CAS Contact Form', 'cas-contact-form' ),
			__( 'CAS Form', 'cas-contact-form' ),
			'manage_options',
			'cas-contact-form',
			[ $this, 'render_page' ],
			'dashicons-email-alt',
			30
		);
	}

	public function enqueue_assets( string $hook ) {
		if ( 'toplevel_page_cas-contact-form' !== $hook ) {
			return;
		}
		wp_enqueue_style(
			'cas-cf-admin',
			CAS_CF_URL . 'assets/css/admin.css',
			[],
			CAS_CF_VERSION
		);
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'cas-contact-form' ) );
		}

		$per_page    = 20;
		$current     = max( 1, (int) ( $_GET['paged'] ?? 1 ) );
		$offset      = ( $current - 1 ) * $per_page;
		$total       = CAS_CF_Database::count();
		$submissions = CAS_CF_Database::get_all( $per_page, $offset );
		$total_pages = ceil( $total / $per_page );
		?>

      <div class="wrap">
        <h1>
          <span class="dashicons dashicons-email-alt"></span>
			<?php esc_html_e( 'CAS Contact Form — Submissions', 'cas-contact-form' ); ?>
        </h1>

        <p class="description">
			<?php printf(
				__( 'Total submissions: %d', 'cas-contact-form' ),
				(int) $total
			); ?>
        </p>

		  <?php if ( empty( $submissions ) ) : ?>

            <p><?php esc_html_e( 'No submissions yet.', 'cas-contact-form' ); ?></p>

		  <?php else : ?>

            <table class="widefat striped">
              <thead>
              <tr>
                <th><?php esc_html_e( '#', 'cas-contact-form' ); ?></th>
                <th><?php esc_html_e( 'Date', 'cas-contact-form' ); ?></th>
                <th><?php esc_html_e( 'Name', 'cas-contact-form' ); ?></th>
                <th><?php esc_html_e( 'Email', 'cas-contact-form' ); ?></th>
                <th><?php esc_html_e( 'Phone', 'cas-contact-form' ); ?></th>
                <th><?php esc_html_e( 'Country', 'cas-contact-form' ); ?></th>
                <th><?php esc_html_e( 'City', 'cas-contact-form' ); ?></th>
                <th><?php esc_html_e( 'Newsletter', 'cas-contact-form' ); ?></th>
              </tr>
              </thead>
              <tbody>
			  <?php foreach ( $submissions as $row ) : ?>
                <tr>
                  <td><?php echo esc_html( $row['id'] ); ?></td>
                  <td><?php echo esc_html( wp_date(
						  get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
						  strtotime( $row['submitted_at'] )
					  ) ); ?></td>
                  <td><?php echo esc_html( $row['first_name'] . ' ' . $row['last_name'] ); ?></td>
                  <td>
                    <a href="mailto:<?php echo esc_attr( $row['email'] ); ?>">
						<?php echo esc_html( $row['email'] ); ?>
                    </a>
                  </td>
                  <td><?php echo esc_html( $row['phone'] ); ?></td>
                  <td><?php echo esc_html( $row['country'] ); ?></td>
                  <td><?php echo esc_html( $row['city'] ); ?></td>
                  <td>
					  <?php echo $row['newsletter']
						  ? '<span class="cas-badge-yes">Yes</span>'
						  : '<span class="cas-badge-no">No</span>';
					  ?>
                  </td>
                </tr>
			  <?php endforeach; ?>
              </tbody>
            </table>

			  <?php if ( $total_pages > 1 ) : ?>
              <div class="tablenav bottom">
				  <?php echo paginate_links( [
					  'base'      => add_query_arg( 'paged', '%#%' ),
					  'format'    => '',
					  'prev_text' => '&laquo;',
					  'next_text' => '&raquo;',
					  'total'     => $total_pages,
					  'current'   => $current,
				  ] ); ?>
              </div>
			  <?php endif; ?>

		  <?php endif; ?>
      </div>

		<?php
	}
}