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
		add_action( 'woocommerce_thankyou', 				array( $this, 'order_received_notification' ), 2 );

    	// Append notification on qualified customer Emails
    	add_action( 'woocommerce_email_after_order_table', 	array( $this, 'email_notification' ), 5, 3 );		
	}

	/**
	 * Print gift notification
	 * 
	 * @access private
	 * @param int 	order id
	 * @return void
	 */
	private function notification( $order_id, $mode = 'receipt' ){
		$product_id 			= get_post_meta( $order_id, $this->wc_checkout_gift->get_key( 'product_id' ), true );
		$minimum_purchase 		= get_post_meta( $order_id, $this->wc_checkout_gift->get_key( 'minimum_purchase' ), true );
		$notification_message 	= get_post_meta( $order_id, $this->wc_checkout_gift->get_key( 'notification_message' ), true );
		$style 					= apply_filters( "woocommerce_checkout_gift_notification_message_styling_{$mode}", 'border: 1px solid #317D24; padding: 10px; text-align: center; background: #52AD42; margin: 5px 0 20px; display: block; color: white;' );
		$product 				= new WC_Product( $product_id );

		if( $product_id && $minimum_purchase && $notification_message && $product->is_visible() ){
			$message = str_replace('%PRODUCT_NAME%', '<span class="product-name">' . $product->get_title() . '</span>', $notification_message );
			$message = str_replace( '%MINIMUM_PURCHASE%', wc_price( $minimum_purchase ), $message );

			echo '<div class="woocommerce-checkout-gift-notification" style="'. $style .'">';
			echo $message;
			echo '</div>';
		}
	}

	/**
	 * Print gift notification on qualified order-received page
	 * 
	 * @access public
	 * @param int 	order id
	 * @return void
	 */
	public function order_received_notification( $order_id ){
		$this->notification( $order_id, 'order_received' );
	}

	/**
	 * Print gift notification on qualified customer email
	 * 
	 * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     * @return void
	 */
	public function email_notification( $order, $sent_to_admin, $plain_text = false ){
    	if ( ! $sent_to_admin && 'on-hold' === $order->status ) {
    		$this->notification( $order->id, 'email' );
		}		
	}
}
new WC_Checkout_Gift_Notification;