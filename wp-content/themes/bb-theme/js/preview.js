/**
 * Logic for previewing a preset.
 */
(function($) {
	
	function spliceString(idx, rem, s) {
		return (this.slice(0, idx) + s + this.slice(idx + Math.abs(rem)));
	};
	
	$(function() {
		
		$('a').each(function() {
		
			var ele         = $(this),
				attrName    = 'href',
				attr        = ele.attr(attrName),
				q           = null,
				i           = 0;
		
			if(typeof attr != 'undefined' && attr.indexOf(window.location.hostname) > -1) {
			
				q = (attr.indexOf('?') > -1 ? '&' : '?') + 'fl-preview=' + preview.preset;
			
				if(attr.indexOf('#') > -1) {
					i    = attr.indexOf('#');
					attr = attr.substring(0, i) + q + attr.substring(i, attr.length);
				}
				else {
					attr += q;
				}
				
				ele.attr(attrName, attr);
			}
		});
	});
	
})(jQuery);