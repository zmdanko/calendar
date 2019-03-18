(function($){
	$.validDate = function(date, options){
		var defaults = {
			"pattern" : /^(\d{4}(-\d{2}){2} (\d{2})(:\d{2}){2})$/
		},

			opts = $.extend(defaults, options);

		return date.match(opts.pattern)!=null;
	};
})(jQuery);

// function validDate(date){
// 	var pattern = /^(\d{4}(-\d{2}){2} (\d{2})(:\d{2}){2})$/;
// 	return date.match(pattern)!=null;
// }