(function($) {
	var methods = {
		/**
         * Initialize ezcashflow
         * 
         * @param {Object} options
         * @returns {Object[]}
         */
        init: function(options) {
            var settings = $.extend({}, this.ezcashflow.defaults, options);
            return this.each(function() {
                var $this = $(this);
            });
        },
    };
	
	$.fn.ezcashflow = function(method) {
        if (methods[method]) {
            return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method with name ' + method + ' does not exists for jQuery.ezcashflow');
        }
    };
    /**
     *  Plugin's default options
     */
    $.fn.ezcashflow.defaults = {
        name: 'EzCashFlow'
    };
})(jQuery);
