jQuery(document).ready(function($) {
	//alert(wp_db_js_object.listing_page_url);
});
	
function wp_db_op_trach(id){
	
		var data = {
			'action': 'wp_db_op_delete_row',
			'id': id
		};

		jQuery.post(ajaxurl, data, function(response) {
			if(response){
				var url = wp_db_js_object.listing_page_url+'&status='+response.status+'&message='+response.message;
				window.location = url;
			} 
		});
}
