jQuery(document).ready(function($) { 
	$('._woocommerce_checkout_gift_product_id').ajaxChosen({
		type : 'GET',
		url : 'admin-ajax.php?action=woocommerce_checkout_gift_get_products',
		dataType : 'json'
	}, function( data ){

	    return data;		
	});
});