<?php
/**
 * Displaying notification
 */
class WC_Checkout_Gift_Notification{

	var $wc_checkout_gift;

	/**
	 * Init the method
	 */
	function __construct(){
		$this->wc_checkout_gift = new WC_Checkout_Gift;

		// Print notification on qualified order receipt and email
		add_action( 'woocommerce_thankyou', 	array( $this, 'notification' ), 2 );
	}

	/**
	 * Print gift notification on qualified order-received page
	 * 
	 * @access public
	 * @param int 	order id
	 * @return void
	 */
	public function notification( $order_id ){
		$product_id 			= get_post_meta( $order_id, $this->wc_checkout_gift->get_key( 'product_id' ), true );
		$minimum_purchase 		= get_post_meta( $order_id, $this->wc_checkout_gift->get_key( 'minimum_purchase' ), true );
		$notification_message 	= get_post_meta( $order_id, $this->wc_checkout_gift->get_key( 'notification_message' ), true );
		$style 					= apply_filters( 'woocommerce_checkout_notification_box_styling', 'border: 1px solid #65A871; padding: 10px; text-align: center; background: #99F2A9; margin: 5px 0 20px; float: left; width: 100%;' );
		$product 				= new WC_Product( $product_id );

		if( $product_id && $minimum_purchase && $notification_message && $product->is_visible() ){
			$message = str_replace('%PRODUCT_NAME%', '<span class="product-name">' . $product->get_title() . '</span>', $notification_message );
			$message = str_replace( '%MINIMUM_PURCHASE%', wc_price( $minimum_purchase ), $message );

			echo '<div class="woocommerce-checkout-gift-notification" style="'. $style .'">';
			echo $message;
			echo '</div>';
		}
	}
}
new WC_Checkout_Gift_Notification;