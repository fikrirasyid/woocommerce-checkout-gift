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
        include_once( ABSPATH . 'wp-content/plugins/woocommerce/woocommerce.php' );    	
    }
	
	/**
	 * Requiring external files
	 */
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wc-checkout-gift.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wc-checkout-gift-settings.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wc-checkout-gift-checkout.php' );
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-wc-checkout-gift-notification.php' );
}