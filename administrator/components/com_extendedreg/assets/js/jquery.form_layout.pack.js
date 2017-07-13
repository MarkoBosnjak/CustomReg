/**
* This file is part of the ExtendedReg distribution. 
* Detailed copyright and licensing information can be found
* in the gpl-3.0.txt file which should be included in the distribution.
* 
* @version		2.11
* @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
* @link			http://jvitals.com
* @since			File available since 1.0.03
*/

(function ($) {
	
	realFixFormLayout = function (p) {
		var cols = p.children(".er-form-column,.er-form-col");
		if (cols.length) {
			var calcSpanNumber = Math.floor(12 / cols.length);
			p.children('.er-form-column,.er-form-col')
				.removeClass("span1").removeClass("span2")
				.removeClass("span3").removeClass("span4")
				.removeClass("span5").removeClass("span6")
				.removeClass("span7").removeClass("span8")
				.removeClass("span9").removeClass("span10")
				.removeClass("span11").removeClass("span12")
				.addClass("span" + calcSpanNumber);
		}
		
		p.children('.er-form-holder,.er-form-step,.er-form-row,.er-form-column,.er-form-col').each(function() {
			realFixFormLayout($(this));
		});
	};
	
	$.fn.fixFormLayout = function () {
		realFixFormLayout($(this));
    };
	
})(jQuery);
