(function($){
	$.fn.dateZoom = function(options){
		var opts = $.extend($.fn.dateZoom.defaults, options);
		return this.each(function(){
			var originalSize = $(this).css("font-size");
			$(this).hover(function(){
				$.fn.dateZoom.zoom(this, opts.fontsize, opts);
			},
			function(){
				$.fn.dateZoom.zoom(this, originalSize, opts);
			});
		});
	};
	$.fn.dateZoom.defaults = {
		"fontsize" : "110%",
		"easing" : "swing",
		"duration" : "600",
		"callback" : null
	};

	$.fn.dateZoom.zoom = function(element, size, opts){
		$(element).animate({
			"font-size" : size
		},{
			"duration" : opts.duration,
			"easing" : opts.easing,
			"complete" : opts.callback
		})
		.dequeue()
		.clearQueue();
	};
})(jQuery);