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