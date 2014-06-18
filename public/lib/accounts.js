/**
 * 
 */
(function($) {
	var methods = {
		/**
         * Initialize ezcashflowAccounts
         * 
         * @param {Object} options
         * @returns {Object[]}
         */
        init: function(options) {
            var settings = $.extend({}, this.ezcashflowAccounts.defaults, options);
            return this.each(function() {
                var $this = $(this);
                $this.ezcashflowAccounts('setContainer', $(this));
                $this.ezcashflowAccounts('setSettings', settings);
                $this.ezcashflowAccounts('showAccounts', settings);
            });
        }
        /**
         * Return app container
         * 
         * @returns {HtmlElement}
         */
        ,getContainer: function() {
            return $(this).data('container');
        }
        /**
         * Set app container
         * 
         * @param {HtmlE;ement} container
         */
        ,setContainer: function(container) {
            return $(this).data('container', container);
        }
        
        /**
         * Method return setting by name
         * 
         * @param {type} name
         * @returns {unresolved}
         */
        ,getSetting: function(name) {
            if (!$(this).ezcashflowAccounts('getContainer')) {
                return null;
            }
            return $(this).ezcashflowAccounts('getContainer').data('settings')[name];
        }
        /**
         * Add new settings
         * 
         * @param {Object} settings
         */
        ,setSettings: function(settings) {
            $(this).ezcashflowAccounts('getContainer').data('settings', settings);
        }
        
        ,_getAccRow:  function(tag, data) {
        	return $('<tr></tr>')
				.data('data', data)
				.append($('<' + tag + '/>').append($('<strong>' + data.name + '</strong>')).append($('<em style="display: block;font-size: 9px;">' + data.comments + '</em>')))
				.append($('<' + tag + '/>').text(data.value))
				.append($('<' + tag + '/>').append(data.btn));
        }
        
        ,_showAccList: function(settings, listDiv, accounttype) {
    		listDiv.html('');
    		settings.deployd.accounts.get({'typeId': accounttype.id}, function (res) {
				$.each(res, function(i, account){
					listDiv.append($(this).ezcashflowAccounts('_getAccRow', 'th', {'name': account.name, 'value': account.values.sum, 'comments': account.comments}))
				})
			});
			listDiv.find('#treelist').treegrid({
				expanderExpandedClass : 'glyphicon glyphicon-minus-sign'
				,expanderCollapsedClass : 'glyphicon glyphicon-plus-sign'
			});
			return listDiv;
    	}
        
        ,showAccounts: function (settings) {
        	var tmp = {};
        	tmp.container = $(this).ezcashflowAccounts('getContainer');
        	tmp.dpd = settings.deployd;
        	tmp.container.html('');
        	
        	tmp.listHeader = $('<ul class="nav nav-tabs"></ul>').appendTo(
    			$('<div class="panel-heading"></div>').appendTo(tmp.container)
    		);
        	tmp.listBody = $('<tbody></tbody>').appendTo(
    			$('<table class="table table-striped" id="treelist"></table>')
    				.append($('<thead></thead>').append($(this).ezcashflowAccounts('_getAccRow', 'th', {'name': 'Account Name', 'value': 'Value', 'btn': '', 'comments': ''})))
	    			.appendTo(
						$('<div class="panel-body table-responsive"></div>').appendTo(tmp.container)
	    			)
        	);
        	tmp.dpd.accounttype.get({}, function(result) {
    			$.each(result, function(index, value) {
    				$('<a href="#" data-toggle="tab">' + value.name + '</a>')
    					.appendTo(
    						$('<li' + (index == 0 ? ' class="active"' : '') + '></li>').appendTo(tmp.listHeader)
    					)
    					.data('data', value)
    					.click(function(){
    						if($(this).parent().hasClass('active'))
    							return false;
    						$(this).ezcashflowAccounts('_showAccList', settings, tmp.listBody, $(this).data('data'));
    					})
    				;
    			});
    			if(result.length > 0)
    				$(this).ezcashflowAccounts('_showAccList', settings, tmp.listBody, result[0]);
    		});
        	return $(this);
        }
    };
	
	$.fn.ezcashflowAccounts = function(method) {
        if (methods[method]) {
            return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method with name ' + method + ' does not exists for jQuery.ezcashflowAccounts');
        }
    };
    /**
     *  Plugin's default options
     */
    $.fn.ezcashflowAccounts.defaults = {
        deployd: dpd //the deployd
    };
})(jQuery);