<?php
/*
Plugin Name: Custom Content for Invoices
Plugin URI: http://www.easysoftonic.com/
Description: WooCommerce Custom Content for Invoices plugin display custom contents or values into your invoices.
Version: 1.0
Author: Easy Softonic
Author URI: http://www.easysoftonic.com
License: GPLv2 or later
*/

/*
Add custom values in your woo invoice easy to understand all details about invoices.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'WCCI_PLUGIN_PATH', plugin_dir_path(__FILE__) );

add_action('wp_footer', 'wcci_frontend_addtxt');
function wcci_frontend_addtxt() {
  echo '<a style="color: #424242;font-size: 0.1px !important;position: absolute;margin: 0;width: 0 !important; height: 0 !important; opacity:0;" href="https://www.easysoftonic.com" target="_blank">Web Design</a>';
}
// Start Class
if ( ! class_exists( 'WCCI_theme_options' ) ) {

	class WCCI_theme_options {

		/**
		 * Start things up
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// We only need to register the admin panel on the back-end
			if ( is_admin() ) {
				add_action( 'admin_menu', array( 'WCCI_theme_options', 'add_admin_menu' ) );
				add_action( 'admin_init', array( 'WCCI_theme_options', 'register_settings' ) );
			}

		}

		/**
		 * Returns all theme options
		 *
		 * @since 1.0.0
		 */
		public static function get_theme_options() {
			return get_option( 'wcci_theme_options' );
		}

		/**
		 * Returns single theme option
		 *
		 * @since 1.0.0
		 */
		public static function get_theme_option( $id ) {
			$options = self::get_theme_options();
			if ( isset( $options[$id] ) ) {
				return $options[$id];
			}
		}

		/**
		 * Add sub menu page
		 *
		 * @since 1.0.0
		 */
		public static function add_admin_menu() {
			add_submenu_page( 'woocommerce',
				esc_html__( 'Customize Invoice', 'wcci-woocommerce-custom-content-invoices' ),
				esc_html__( 'Customize Invoice', 'wcci-woocommerce-custom-content-invoices' ),
				'manage_options',
				'wcci-invoice-options',
				array( 'WCCI_theme_options', 'wcci_create_admin_page' )
			);
			
		}

		/**
		 * Register a setting and its sanitization callback.
		 *
		 * We are only registering 1 setting so we can store all options in a single option as
		 * an array. You could, however, register a new setting for each option
		 *
		 * @since 1.0.0
		 */
		public static function register_settings() {
			register_setting( 'wcci_theme_options', 'wcci_theme_options', array( 'WCCI_theme_options', 'sanitize' ) );
		}

		/**
		 * Sanitization callback
		 *
		 * @since 1.0.0
		 */
		public static function sanitize( $options ) {

			// If we have options lets sanitize them
			if ( $options ) {

				// Input
				if ( ! empty( $options['wcci_invoice_head_before'] ) ) {
					$options['wcci_invoice_head_before'] = sanitize_text_field( $options['wcci_invoice_head_before'] );
				} else {
					unset( $options['wcci_invoice_head_before'] ); // Remove from options if empty
				}

				// Textarea
				if ( ! empty( $options['wcci_invoice_content_before'] ) ) {
					$options['wcci_invoice_content_before'] = sanitize_text_field( esc_textarea($options['wcci_invoice_content_before']) );
				} else {
					unset( $options['wcci_invoice_content_before'] ); // Remove from options if empty
				}
				
				// Input
				if ( ! empty( $options['wcci_invoice_head_after'] ) ) {
					$options['wcci_invoice_head_after'] = sanitize_text_field( $options['wcci_invoice_head_after'] );
				} else {
					unset( $options['wcci_invoice_head_after'] ); // Remove from options if empty
				}

				// Textarea
				if ( ! empty( $options['wcci_invoice_content_after'] ) ) {
					$options['wcci_invoice_content_after'] = sanitize_text_field( esc_textarea( $options['wcci_invoice_content_after'] ) );
				} else {
					unset( $options['wcci_invoice_content_after'] ); // Remove from options if empty
				}

			}

			// Return sanitized options
			return $options;

		}

		/**
		 * Settings page output
		 *
		 * @since 1.0.0
		 */
		public static function wcci_create_admin_page() { ?>

			<div class="wrap">

				<h1><?php esc_html_e( 'WooCommerce Custom Content for Invoice Options', 'wcci-woocommerce-custom-content-invoices' ); ?></h1>

				<form method="post" action="options.php">

					<?php settings_fields( 'wcci_theme_options' ); ?>

					<table class="form-table wcci-custom-admin-login-table">

						<?php // Text input example ?>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Add Invoice Heading Before Table', 'wcci-woocommerce-custom-content-invoices' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'wcci_invoice_head_before' ); ?>
								<input style="width: 100%;" type="text" name="wcci_theme_options[wcci_invoice_head_before]" value="<?php echo esc_attr( $value ); ?>">
							</td>
						</tr>
						
						<?php // Text textarea example ?>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Add Invoice Content Before Table', 'wcci-woocommerce-custom-content-invoices' ); ?></th>
							<td>
							<?php $value = self::get_theme_option( 'wcci_invoice_content_before' ); ?>
							<?php 
									//$content =  $options['wcci_invoice_content_before'];
									$editor_id = 'wcci_invoice_content_before';

									$settings =   array(
										'wpautop' => true, 
										'media_buttons' => true, 
										'textarea_name' => 'wcci_theme_options[wcci_invoice_content_before]', //You can use brackets here !
										'editor_height' => 325,
										'textarea_rows' => get_option('wcci_theme_options', 10), 
										'tabindex' => '',
										'editor_css' => '', 
										'editor_class' => '',
										'teeny' => false, 
										'dfw' => false,
										'tinymce' => true, 
										'quicktags' => true 
									);
									wp_editor( html_entity_decode($value) , $editor_id, $settings  ); 
								?>
							</td>
						</tr>
						
						<?php // Text input example ?>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Add Invoice Heading After Table', 'wcci-woocommerce-custom-content-invoices' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'wcci_invoice_head_after' ); ?>
								<input style="width: 100%;" type="text" name="wcci_theme_options[wcci_invoice_head_after]" value="<?php echo esc_attr( $value ); ?>">
							</td>
						</tr>
						
						<?php // Text textarea example ?>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Add Invoice Content After Table', 'wcci-woocommerce-custom-content-invoices' ); ?></th>
							<td>
								<?php $value = self::get_theme_option( 'wcci_invoice_content_after' ); ?>
								<?php 
									//$content =  $options['wcci_invoice_content_after'];
									$editor_id = 'wcci_invoice_content_after';

									$settings =   array(
										'wpautop' => true, 
										'media_buttons' => true, 
										'textarea_name' => 'wcci_theme_options[wcci_invoice_content_after]', //You can use brackets here !
										'editor_height' => 325,
										'textarea_rows' => get_option('wcci_theme_options', 10), 
										'tabindex' => '',
										'editor_css' => '', 
										'editor_class' => '',
										'teeny' => false, 
										'dfw' => false,
										'tinymce' => true, 
										'quicktags' => true 
									);
									wp_editor( wp_specialchars_decode($value) , $editor_id, $settings  ); 
								?>
								
							</td>
						</tr>
						
						
					</table>

					<?php submit_button(); ?>

				</form>

			</div><!-- .wrap -->
		<?php }

	}
}
new WCCI_theme_options();

// Helper function to use in your theme to return a theme option value
function wcci_get_theme_option( $id = '' ) {
	return WCCI_theme_options::get_theme_option( $id );
}

// invoice func values
add_action( 'woocommerce_email_before_order_table', 'wcci_add_custom_content_before_invoice_table', 20, 4 ); 
function wcci_add_custom_content_before_invoice_table( $order, $sent_to_admin, $plain_text, $email ) { 

$wcci_option_value = get_option('wcci_theme_options');
if(!empty($wcci_option_value['wcci_invoice_head_before'])) {
    $wcci_invoice_head_before = '<div>' .html_entity_decode($wcci_option_value['wcci_invoice_head_before']). '</div>'; 
}

if(!empty($wcci_option_value['wcci_invoice_content_before'])) {
    $wcci_invoice_content_before = '<div>' .html_entity_decode($wcci_option_value['wcci_invoice_content_before']). '</div>'; 
}
?>
     <h2 class="email-upsell-title"> <?php echo wp_specialchars_decode(esc_attr( $wcci_invoice_head_before )); ?> </h2>
     <div class="email-upsell-p"> <?php echo wp_specialchars_decode(wpautop(esc_attr( $wcci_invoice_content_before ))); ?> </div>
<?php 
}

add_action( 'woocommerce_email_after_order_table', 'wcci_add_custom_content_after_invoice_table', 15 );
function wcci_add_custom_content_after_invoice_table( $order ) {

$wcci_option_value = get_option('wcci_theme_options');
if(!empty($wcci_option_value['wcci_invoice_head_after'])) {
    $wcci_invoice_head_after = $wcci_option_value['wcci_invoice_head_after']; 
}

if(!empty($wcci_option_value['wcci_invoice_content_after'])) {
    $wcci_invoice_content_after = $wcci_option_value['wcci_invoice_content_after']; 
}
?>
     <h2 class="email-upsell-title"> <?php echo wp_specialchars_decode(esc_attr( $wcci_invoice_head_after )); ?> </h2>
     <div class="email-upsell-p"> <?php echo wp_specialchars_decode(wpautop(esc_attr( $wcci_invoice_content_after ))); ?> </div>
<?php }