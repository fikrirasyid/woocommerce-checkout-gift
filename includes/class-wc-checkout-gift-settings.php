<?php
/**
 * Plugin settings
 */
class WC_Checkout_Gift_Settings{

	var $wc_checkout_gift;

	/**
	 * Construct the class
	 */
	public function __construct(){
		$this->wc_checkout_gift = new WC_Checkout_Gift;

		// Enqueueing scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Adding settings to Dashboard > WooCommerce > Settings > Checkout
		add_filter( 'woocommerce_payment_gateways_settings', 			array( $this, 'settings' ) );

		// Providing endpoint for product autocomplete
		add_action( 'wp_ajax_woocommerce_checkout_gift_get_products', 	array( $this, 'get_products_endpoint' ) );
	}

	/**
	 * Register and enqueue script on dashboard
	 * 
	 * @access public
	 * @return void
	 */
	public function admin_scripts(){
		if( ! function_exists( 'get_current_screen' ) )
			return;

		// Get current screen estate
		$screen = get_current_screen();

		// Only enqueue the script on bulk sale screen
		if( 'woocommerce_page_wc-settings' == $screen->id ){
			wp_enqueue_script( 'woocommerce-checkout-gift', $this->wc_checkout_gift->plugin_url . '/js/woocommerce-checkout-gift-admin.js', array( 'jquery', 'ajax-chosen' ), '0.1' );
		}
	}

	/**
	 * Adding checkout settings on Dashboard > WooCommerce > Settings > Checkout tab
	 * 
	 * @access public
	 * @param array  	settings
	 * @return array 	modified settings
	 */
	public function settings( $settings ){	

		$recent_products = $this->get_products( false, 'init' );

		$settings[] = array( 
			'title' 				=> __( 'Checkout Gift', 'woocommerce-checkout-gift' ), 
			'type' 					=> 'title', 
			'desc' 					=> __( 'Grant your user a gift if his/her purchase amout passes the minimum limit defined below', 'woocommerce-checkout-gift' ), 
			'id' 					=> 'checkout_gift_options' 
		);

		$settings[] = array(
			'title'   				=> __( 'Purchase Limit', 'woocommerce-checkout-gift' ),
			'desc'     				=> __( 'Grant user a gift if his/her amout of purchase passes this limit. To disable gift, set the value to 0', 'woocommerce-checkout-gift' ),
			'id'       				=> $this->wc_checkout_gift->get_key( 'minimum_purchase' ),
			'type'     				=> 'number',
			'default'  				=> 0,
			'desc_tip' 				=> true,
		);

		$settings[] = array(
			'title'             	=> __( 'Select Product as Gift', 'woocommerce-checkout-gift' ),
			'type'              	=> 'select',
			'id'					=> $this->wc_checkout_gift->get_key( 'product_id' ),
			'class'					=> $this->wc_checkout_gift->get_key( 'product_id' ),
			'default'           	=> __( 'Select Gift' ),
			'desc'      			=> __( 'Choose product to be given', 'woocommerce-checkout-gift' ),
			'options'           	=> $recent_products,
			'desc_tip'          	=> true,
			'custom_attributes'	 	=> array(
				'data-placeholder' 	=> __( 'Select Gift', 'woocommerce-checkout-gift' )
			)
		);	

		$settings[] = array(
			'title'    				=> __( 'Gift Notification Message', 'woocommerce-checkout-gift' ),
			'desc'     				=> __( 'This message will appear in qualified order page and emails.', 'woocommerce-checkout-gift' ),
			'id'       				=> $this->wc_checkout_gift->get_key( 'notification_message' ),
			'css'      				=> 'width:100%; height: 75px;',
			'type'     				=> 'textarea',
			'default'  				=> __( "Congratulation! Your amout of purchase is more than %MINIMUM_PURCHASE% so you are eligible to get %PRODUCT_NAME% for free!", 'woocommerce-checkout-gift' ),
		);

		$settings[] = array( 
			'type' => 'sectionend', 
			'id' => 'checkout_gift_options' 
		);			

		return $settings;
	}

	/**
	 * Get products
	 * 
	 * @access private
	 * @param string 	search term
	 * @return array
	 */
	private function get_products( $term = false, $mode = false ){
		$args = array(
			'post_status' 	=> 'publish',
			'post_type'		=> 'product',
			'posts_per_page'=> 10
		);

		if( $term ){
			$args['s'] = sanitize_text_field( $term );
		}

		$products = get_posts( $args );

		if( 'init' == $mode ){
			$default = $this->wc_checkout_gift->get_option( 'product_id' );

			if( $default && '' != $default ){
				$post 							= get_post( $default );
				$default_product 				= new stdClass();
				$default_product->ID 			= $default;
				$default_product->post_title 	= $post->post_title;

				$products[] = $default_product;
			}
		}			

		return $this->prepare_products( $products );
	}

	/**
	 * Prepare products object to be displayed as key => value
	 * 
	 * @access private
	 * @param obj
	 * @param array
	 */
	private function prepare_products( $posts = array(), $mode = 'init' ){
	
		$products = array();

		if( 'init' == $mode ){
			$products[''] = __( 'Select product as gift', 'wooocommerce-checkout-gift' );
		}

		if( ! empty( $posts ) ){

			foreach ($posts as $post ) {

				switch ( $mode ) {
					case 'ajax':
						$products[] = array( 'value' => $post->ID, 'text' => $post->post_title );
						break;
					
					default:
						$products[$post->ID] = $post->post_title;
						break;
				}

			}

		}

		return $products;
	}

	/**
	 * Get products endpoint for AJAX powered select product dropdown
	 * 
	 * @access public
	 * @return void
	 */
	public function get_products_endpoint(){

		/**
		 * Get term
		 */
		if( isset( $_GET['term'] ) ){
			$term = $_GET['term'];
		} else {
			$term = false;
		}

		/**
		 * Get product list
		 */
		$products = $this->get_products( $term, 'ajax' );

		/**
		 * Output product as json
		 */
		echo json_encode( $products );

		die();
	}
}
new WC_Checkout_Gift_Settings;