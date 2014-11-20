<?php
/*
    Plugin Name: WooCommerce Checkout Gift
    Version: 0.1
    Description: Granting gift to customer who's purchase passes particular amount limit
    Author: Fikri Rasyid
    Author URI: http://fikrirasyid.com
*/
/*
    Copyright 2014 Fikri Rasyid
    Developed by Fikri Rasyid (fikrirasyid@gmail.com)
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    /**
     * If the plugin is called before woocommerce, we need to include it first
     */
    if( !class_exists( 'Woocommerce' ) ){
    	include_once(plugin_dir_path( dirname( __FILE__ ) ) . "/woocommerce/woocommerce.php"); 	
    }

	/**
	 * Variables and values 
	 */
	class WC_Checkout_Gift{
		var $prefix;
		var $plugin_url;

		/**
		 * Construct the class
		 */
		public function __construct(){
			$this->prefix 		= '_woocommerce_checkout_gift_';
			$this->plugin_url 	= untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get option key, basically prefix + key. Useful for option
		 * 
		 * @access public
		 * @param string 	key
		 * @return string 	key
		 */
		public function get_key( $key ){

			return "{$this->prefix}{$key}";
		}

		/**
		 * Get product ID which is set as gift
		 * 
		 * @access public
		 * @param string 	product_id|minimum_purchase|notification_message
		 * @param bool 		is intended value is integer?
		 * @return int|bool product ID
		 */
		public function get_option( $key, $is_int = false ){
			// Define key
			$key = $this->get_key( $key );

			// Get default value
			switch ( $key ) {
				case 'notification_message':
					$default = __( "Congratulation! Your amout of purchase is more than %MINIMUM_PURCHASE% so you are eligible to get %PRODUCT_NAME% for free!", 'woocommerce-checkout-gift' );
					break;
				
				default:
					$default = 0;
					break;
			}

			// Get value
			$value = get_option( $key, $default );

			// Return value
			if( $is_int ){
				return intval( $value );
			} else {
				return $value;
			}
		}		
	}
	
	/**
	 * Requiring external files
	 */
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wc-checkout-gift-settings.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wc-checkout-gift-checkout.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wc-checkout-gift-notification.php' );
}