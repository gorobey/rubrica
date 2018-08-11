//jquery.background.js
(function($){
	$.fn.setBackground = function(config_user) {
		var config_default = {
			'background' : 'silver'
		}
		config_default = $.extend(config_default, config_user);
		this.each(function(){
			$(this).focus(function(){
				$(this).css(config_default)
			})
			$(this).blur(function(){
				$(this).css({'background':'white','color':'black'})
			})
		})
	}
})(jQuery);