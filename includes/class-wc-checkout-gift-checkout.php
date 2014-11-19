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

			WC()->cart->add_to_cart( $this->wc_checkout_gift->get_option( 'product_id' ) );

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
		add_filter( 'woocommerce_get_price', array( $this, 'gift_price' ), 10, 2 );
	}

	/**
	 * Set gift price to zero upon checkout
	 * 
	 * @access public
	 * @param int 	 	price
	 * @param obj 		product object
	 * @return int|bool
	 */
	public function gift_price( $price, $product ){
		if( $this->wc_checkout_gift->get_option( 'product_id' ) == $product->id && defined('WOOCOMMERCE_CHECKOUT') ){
			return 0;
		} else {
			return $price;
		}
	}		
}
new WC_Checkout_Gift_Checkout;