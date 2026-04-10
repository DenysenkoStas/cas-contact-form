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
				'bootstrap',
				'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
				[],
				'5.3.0'
			);
			wp_enqueue_style(
				'cas-cf-style',
				CAS_CF_URL . 'assets/css/form.css',
				[ 'bootstrap' ],
				CAS_CF_VERSION
			);
			wp_enqueue_script(
				'bootstrap',
				'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
				[],
				'5.3.0',
				true
			);
			wp_enqueue_script(
				'cas-cf-script',
				CAS_CF_URL . 'assets/js/form.js',
				[ 'bootstrap' ],
				CAS_CF_VERSION,
				true
			);
			wp_localize_script( 'cas-cf-script', 'casCF', [
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'cas_cf_submit' ),
			] );
		}
	}

	public function render( $atts ): string {
		ob_start();
		?>
		<div class="cas-cf-wrapper" id="cas-cf-wrapper">

			<!-- Step indicator -->
			<div class="cas-cf-steps mb-4">
				<?php for ( $i = 1; $i <= 3; $i ++ ) : ?>
					<div class="cas-cf-step <?php echo $i === 1 ? 'active' : ''; ?>" data-step="<?php echo $i; ?>">
						<span><?php echo $i; ?></span>
					</div>
					<?php if ( $i < 3 ) : ?>
						<div class="cas-cf-step-line <?php echo $i === 1 ? 'active' : ''; ?>"
						     data-line="<?php echo $i; ?>"></div>
					<?php endif; ?>
				<?php endfor; ?>
			</div>

			<!-- Alerts -->
			<div class="alert alert-success" id="cas-cf-success" style="display:none;">
				🎉 <?php esc_html_e( 'Thank you! Your submission has been received.', 'cas-contact-form' ); ?>
			</div>
			<div class="alert alert-danger" id="cas-cf-server-error" style="display:none;"></div>

			<form id="cas-contact-form" novalidate>
				<?php wp_nonce_field( 'cas_cf_submit', 'cas_cf_nonce' ); ?>

				<!-- STEP 1 -->
				<div class="cas-cf-panel active" data-panel="1">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title"><?php esc_html_e( 'Personal info', 'cas-contact-form' ); ?></h5>
							<p class="text-muted small"><?php esc_html_e( 'Step 1 of 3', 'cas-contact-form' ); ?></p>

							<div class="row g-3">
								<div class="col-md-6">
									<label for="cas_first_name"
									       class="form-label"><?php esc_html_e( 'First name', 'cas-contact-form' ); ?>
										<span
											class="text-danger">*</span></label>
									<input type="text" class="form-control" id="cas_first_name" name="first_name"
									       placeholder="John"
									       required>
									<div class="invalid-feedback" id="err-first_name"></div>
								</div>
								<div class="col-md-6">
									<label for="cas_last_name"
									       class="form-label"><?php esc_html_e( 'Last name', 'cas-contact-form' ); ?>
										<span
											class="text-danger">*</span></label>
									<input type="text" class="form-control" id="cas_last_name" name="last_name"
									       placeholder="Doe"
									       required>
									<div class="invalid-feedback" id="err-last_name"></div>
								</div>
							</div>

							<div class="mt-3">
								<label for="cas_email"
								       class="form-label"><?php esc_html_e( 'Email', 'cas-contact-form' ); ?> <span
										class="text-danger">*</span></label>
								<input type="email" class="form-control" id="cas_email" name="email"
								       placeholder="john@example.com"
								       required>
								<div class="invalid-feedback" id="err-email"></div>
							</div>

							<div class="mt-3">
								<label for="cas_phone"
								       class="form-label"><?php esc_html_e( 'Phone', 'cas-contact-form' ); ?> <span
										class="text-danger">*</span></label>
								<input type="tel" class="form-control" id="cas_phone" name="phone"
								       placeholder="+380 (XX) XXX-XX-XX"
								       required>
								<div class="invalid-feedback" id="err-phone"></div>
							</div>

							<div class="mt-3">
								<label for="cas_dob"
								       class="form-label"><?php esc_html_e( 'Date of birth', 'cas-contact-form' ); ?></label>
								<input type="date" class="form-control" id="cas_dob" name="date_of_birth">
							</div>

							<div class="d-flex justify-content-end mt-4">
								<button type="button" class="btn btn-primary" data-next="2">
									<?php esc_html_e( 'Next', 'cas-contact-form' ); ?> &rarr;
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- STEP 2 -->
				<div class="cas-cf-panel" data-panel="2">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title"><?php esc_html_e( 'Address', 'cas-contact-form' ); ?></h5>
							<p class="text-muted small"><?php esc_html_e( 'Step 2 of 3', 'cas-contact-form' ); ?></p>

							<div>
								<label for="cas_country"
								       class="form-label"><?php esc_html_e( 'Country', 'cas-contact-form' ); ?>
									<span class="text-danger">*</span></label>
								<select class="form-select" id="cas_country" name="country" required>
									<option
										value=""><?php esc_html_e( 'Select country...', 'cas-contact-form' ); ?></option>
									<?php foreach ( self::countries() as $code => $name ) : ?>
										<option
											value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $name ); ?></option>
									<?php endforeach; ?>
								</select>
								<div class="invalid-feedback" id="err-country"></div>
							</div>

							<div class="mt-3">
								<label for="cas_city"
								       class="form-label"><?php esc_html_e( 'City', 'cas-contact-form' ); ?> <span
										class="text-danger">*</span></label>
								<input type="text" class="form-control" id="cas_city" name="city" placeholder="Odessa"
								       required>
								<div class="invalid-feedback" id="err-city"></div>
							</div>

							<div class="mt-3">
								<label for="cas_street"
								       class="form-label"><?php esc_html_e( 'Street address', 'cas-contact-form' ); ?></label>
								<input type="text" class="form-control" id="cas_street" name="street"
								       placeholder="123 Main St, Apt 4">
							</div>

							<div class="mt-3">
								<label for="cas_zip"
								       class="form-label"><?php esc_html_e( 'ZIP / Postal code', 'cas-contact-form' ); ?></label>
								<input type="text" class="form-control" id="cas_zip" name="zip" placeholder="65000">
							</div>

							<div class="d-flex justify-content-between mt-4">
								<button type="button" class="btn btn-outline-secondary" data-back="1">
									&larr; <?php esc_html_e( 'Back', 'cas-contact-form' ); ?>
								</button>
								<button type="button" class="btn btn-primary" data-next="3">
									<?php esc_html_e( 'Next', 'cas-contact-form' ); ?> &rarr;
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- STEP 3 -->
				<div class="cas-cf-panel" data-panel="3">
					<div class="card">
						<div class="card-body">
							<h5 class="card-title"><?php esc_html_e( 'Confirmation', 'cas-contact-form' ); ?></h5>
							<p class="text-muted small"><?php esc_html_e( 'Step 3 of 3', 'cas-contact-form' ); ?></p>

							<div class="form-check">
								<input class="form-check-input" type="checkbox" name="terms" id="cas_terms" required>
								<label class="form-check-label" for="cas_terms">
									<?php esc_html_e( 'I agree to the ', 'cas-contact-form' ); ?>
									<a href="#"
									   target="_blank"><?php esc_html_e( 'Terms and Conditions', 'cas-contact-form' ); ?></a>
									<span class="text-danger">*</span>
								</label>
								<div class="invalid-feedback" id="err-terms"></div>
							</div>

							<div class="form-check mt-2">
								<input class="form-check-input" type="checkbox" name="newsletter" id="cas_newsletter">
								<label class="form-check-label" for="cas_newsletter">
									<?php esc_html_e( 'Subscribe to newsletter', 'cas-contact-form' ); ?>
								</label>
							</div>

							<div class="d-flex justify-content-between mt-4">
								<button type="button" class="btn btn-outline-secondary" data-back="2">
									&larr; <?php esc_html_e( 'Back', 'cas-contact-form' ); ?>
								</button>
								<button type="submit" class="btn btn-success" id="cas-cf-submit">
									<?php esc_html_e( 'Submit', 'cas-contact-form' ); ?> &#10003;
								</button>
							</div>
						</div>
					</div>
				</div>

			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	public static function countries(): array {
		return [
			'UA' => 'Ukraine',
			'US' => 'United States',
			'GB' => 'United Kingdom',
			'DE' => 'Germany',
			'FR' => 'France',
			'PL' => 'Poland',
			'CA' => 'Canada',
			'AU' => 'Australia',
			'NL' => 'Netherlands',
			'IT' => 'Italy',
			'ES' => 'Spain',
			'SE' => 'Sweden',
			'NO' => 'Norway',
			'CH' => 'Switzerland',
			'AT' => 'Austria',
		];
	}
}