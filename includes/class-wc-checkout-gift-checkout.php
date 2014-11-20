<?php
/**
 * Processing on checkout
 */
class WC_Checkout_Gift_Checkout{

	var $wc_checkout_gift;

	/**
	 * Constructing the class
	 */
	public function __construct(){

		$this->wc_checkout_gift = new WC_Checkout_Gift;

		// Adding gift to cart
		add_action( 'woocommerce_checkout_process', 					array( $this, 'add_gift_to_cart' ) );

		// Adding metadata to the newly created order
		add_action( 'woocommerce_checkout_order_processed', 			array( $this, 'add_gift_metadata_to_order' ) );

		// Set price as zero price for gift
		add_action( 'woocommerce_calculate_totals', 					array( $this, 'set_gift_price' ) );

	}

	/**
	 * Get minimum purchase value
	 * 
	 * @access private
	 * @return int
	 */
	private function minimum_purchase(){
		return $this->wc_checkout_gift->get_option( 'minimum_purchase' );
	}

	/**
	 * Conditional method for checking current cart's status for gift
	 * 
	 * @access private
	 * @return bool
	 */
	private function is_qualified_for_gift(){

		if( 0 != $this->minimum_purchase() && WC()->cart->subtotal_ex_tax > $this->minimum_purchase() ){

			return true;

		} else {

			return false;

		}

	}

	/**
	 * Adding gift to cart during checkout process if the amount of purchase is qualified for the gift
	 * 
	 * @access public
	 * @return void
	 */
	public function add_gift_to_cart(){

		if( $this->is_qualified_for_gift() ){

			WC()->cart->add_to_cart( $this->wc_checkout_gift->get_option( 'product_id' ), 1, '', '', array( 'is_woocommerce_checkout_gift' => true ) );

		}
	}

	/**
	 * Adding metadata to newly created order so we can display notification for user
	 * 
	 * @access public
	 * @param int 	order id
	 * @return obj 	posted form
	 */
	public function add_gift_metadata_to_order( $order_id, $posted ){

		if( $this->is_qualified_for_gift() ){

			$keys = array( 'product_id', 'minimum_purchase', 'notification_message' );

			foreach ( $keys as $key ) {
				update_post_meta( $order_id, $this->wc_checkout_gift->get_key( $key ), $this->wc_checkout_gift->get_option( $key ) );
			}				
		}
	}

	/**
	 * Change the gift price to free
	 * 
	 * @access public
	 * @return void
	 */
	public function set_gift_price( $cart ){

		// Perform this on checkout only
		if( ! defined('WOOCOMMERCE_CHECKOUT') )
			return;

		// Check if there's cart contents
		if( isset( $cart->cart_contents) && ! empty( $cart->cart_contents ) ){

			// Loop the cart's content
			foreach ( $cart->cart_contents as $cart_key => $cart_item ) {

				// Set the price to zero if this is the automatically added gift
				if( isset( $cart_item['is_woocommerce_checkout_gift'] ) && $cart_item['is_woocommerce_checkout_gift'] ){

					// Set price to zero
					$cart_item['data']->price = 0;

				}
			}
		}
	}	
}
new WC_Checkout_Gift_Checkout;