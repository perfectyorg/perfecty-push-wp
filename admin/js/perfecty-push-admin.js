(function( $ ) {
	'use strict';

	$(window).load(function(){
		$(".perfecty-push-confirm-action").click(function(e){
			var page = $(this).data("page");
			var action = $(this).data("action");
			var id = $(this).data("id");
			var wpnonce = $(this).data("nonce");
			if (confirm('Are you sure?')){
				var url="?page=" + page + "&action=" + action + "&id=" + id + "&_wpnonce=" + wpnonce;
				window.location.href = url;
			}
		});
	});
})( jQuery );
