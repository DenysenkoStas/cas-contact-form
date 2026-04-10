<?php
/**
 * Plugin Name: CAS Contact Form
 * Plugin URI:  https://github.com/DenysenkoStas/cas-contact-form
 * Description: A multi-step contact form via shortcode [cas_contact_form].
 * Version:     1.0.0
 * Author:      Stas Denysenko
 * Author URI:  https://github.com/DenysenkoStas
 * License:     GPL-2.0+
 * Text Domain: cas-contact-form
 */

defined( 'ABSPATH' ) || exit;

define( 'CAS_CF_VERSION', '1.0.0' );
define( 'CAS_CF_PATH', plugin_dir_path( __FILE__ ) );
define( 'CAS_CF_URL', plugin_dir_url( __FILE__ ) );
define( 'CAS_CF_TABLE', 'cas_contact_submissions' );

require_once CAS_CF_PATH . 'includes/class-database.php';
require_once CAS_CF_PATH . 'includes/class-shortcode.php';
require_once CAS_CF_PATH . 'includes/class-ajax-handler.php';
require_once CAS_CF_PATH . 'includes/class-email.php';
require_once CAS_CF_PATH . 'admin/class-admin-page.php';

register_activation_hook( __FILE__, [ 'CAS_CF_Database', 'create_table' ] );

new CAS_CF_Shortcode();
new CAS_CF_Ajax_Handler();
new CAS_CF_Admin_Page();