jQuery(document).ready(function($) { 
	$('.woocommerce-checkout-gift-product').ajaxChosen({
		type : 'GET',
		url : 'admin-ajax.php?action=woocommerce_checkout_gift_get_products',
		dataType : 'json'
	}, function( data ){

	    return data;		
	});
});