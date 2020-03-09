(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */


	 $(document).on('click', '#btn_dl_all_gal_id' , function(e){
	 	var array_url = $('#btn_dl_all_gal_form_id').val();
	 	var data =  {
	 		'action': 'btn_click_dl_all',
	 		'url_arr' : array_url
	 	};


	 	$.post(ajaxmedia_download_all.url, data, function () {
            // Response div goes here.
            //console.log(resp.zip);

        }).success( function(resp) {
        	console.log(resp);

        	alert("action performed successfully");

        	window.location = resp;


        	/*
        	var obj = JSON.parse(resp);

        	var full_path_url = obj.zip;

        	var parts = full_path_url.split('/');

			var zip_name = parts.pop() || parts.pop();  // handle potential trailing slash

			var zip_path = '/wp-admin/'+zip_name;

			e.preventDefault();

			window.location.href = zip_path;



			var data2 =  {
				'action' : 'btn_click_delete_all',
				'zip_del_path' : zip_name
			};


			$.post(ajaxurl, data2, function(response){
				//console.log(response);
			});
			*/

		}, 'json');
    });


})( jQuery );
