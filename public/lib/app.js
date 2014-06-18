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
                $this.ezcashflow('setContainer', $(this));
                $this.ezcashflow('setSettings', settings);
                $this.ezcashflow("checkUser", settings);
                $this.ezcashflow("showAppName", settings);
                $this.ezcashflow("bindLinks", settings);
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
            if (!$(this).ezcashflow('getContainer')) {
                return null;
            }
            return $(this).ezcashflow('getContainer').data('settings')[name];
        }
        /**
         * Add new settings
         * 
         * @param {Object} settings
         */
        ,setSettings: function(settings) {
            $(this).ezcashflow('getContainer').data('settings', settings);
        }
        /**
         * Checking whether the current user is valid
         */
        ,checkUser: function (settings) {
        	settings.deployd.users.me(function(user) {
    			if (user) {
    				$(settings.html_selector.user_me).text(user.username)
    					.attr("href", settings.urls.user_me);
    			} else {
    				location.href = settings.homePage;
    			}
    		});
        	
        	$(settings.html_selector.logout_btn).click(function() {
        		settings.deployd.users.logout(function(res, err) {
    				location.href = settings.urls.afterLogoutPage;
    			});
    		});
        }
        /**
         * showing the application name
         */
        ,showAppName: function (settings) {
        	$(settings.html_selector.appName).text(settings.appName);
        }
        /**
         * bind all element with attribute: applink to a url
         */
        ,bindLinks: function (settings) {
        	$.each($('[applink]'), function(index, element) {
        		$(element).attr("href", settings.urls[$(element).attr('applink')]);
        	});
        }
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
		appName: 'EzCashFlow'
        ,urls: {
        	afterLogoutPage: '/'
        	,home: '/home.html'
        	,user_me: '/me.html' //the page to go to the current user edit page
    		,accounts: '/accounts.html' //the page to go to the current user edit page
        }
    	,html_selector: {
			user_me: '#me' //the html id of the user: ME
			,logout_btn: '#logout-btn' //the html id of the logout btn
			,homeLink: '.homeLink' //for all links that needs to go to home
			,appName: '.appName' //for all elements that needs the app's name
    	}
        ,deployd: dpd //the deployd
    };
})(jQuery);
