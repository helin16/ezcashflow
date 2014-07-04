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
                $this.ezcashflow("addHead", settings);
                $this.ezcashflow("checkUser", settings);
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
        	var $this = $(this);
        	settings.deployd.users.me(function(user) {
    			if (user) {
    				$this.ezcashflow("showMainMenu", settings);
    				$('#me').html(user.username)
    			} else {
    				location.href = settings.homePage;
    			}
    		});
        }
        /**
         * Adding the html header
         */
        ,addHead: function (settings) {
        	$('head').append('<meta charset="utf-8">')
        	.append('<meta http-equiv="X-UA-Compatible" content="IE=edge">')
        	.append('<meta name="viewport" content="width=device-width, initial-scale=1">')
        	.append('<meta name="description" content="">')
        	.append('<meta name="author" content="">')
        	.append('<title>' + settings.appName + '</title>');
        }
        /**
         * showing the mainmenu
         */
        ,showMainMenu: function (settings) {
        	$(settings.html_selector.mainMenu)
        	.addClass('navbar navbar-inverse navbar-fixed-top')
        	.attr('role', 'navigation')
        	.append(
    			$('<div class="container"></div>')
    			.append(
					$('<div class="navbar-header"></div>')
					.append(
						$('<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"></buttom>')
						.append( $('<span class="sr-only">' + settings.appName + '</span>') )
						.append( $('<span class="icon-bar"></span>') )
						.append( $('<span class="icon-bar"></span>') )
						.append( $('<span class="icon-bar"></span>') )
					)
					.append($('<a class="navbar-brand" href="' + settings.urls.home + '"></a>').html(settings.appName) )
    			)
    			.append(
					$('<div class="collapse navbar-collapse"></div>')
					.append(
						$('<ul class="nav navbar-nav"></ul>')
						.append( $('<li ' + (settings.activateAppLink === 'home' ? 'class="active"': '') + '><a href="' + settings.urls.home + '" applink="home">Home</a></li>') )
						.append( $('<li ' + (settings.activateAppLink === 'accounts' ? 'class="active"': '') + '><a href="' + settings.urls.accounts + '" applink="accounts">Accounts</a></li>') )
						.append( $('<li ' + (settings.activateAppLink === 'transactions' ? 'class="active"': '') + '><a href="' + settings.urls.transactions + '" applink="transactions">Transactions</a></li>') )
						.append( $('<li ' + (settings.activateAppLink === 'properties' ? 'class="active"': '') + '><a href="' + settings.urls.properties + '" applink="properties">Properties</a></li>') )
	    			)
					.append(
						$('<ul class="nav navbar-nav navbar-right"></ul>')
						.append( $('<li><a applink="' + settings.urls.user_me + '">Welcome, <span id="me">Unknown User</span><span></a></li>') )
						.append(
							$('<li></li>')
							.append( $('<a href="void:javascript(0);" id="logout-btn">Logout</a>')
								.click(function() {
					        		settings.deployd.users.logout(function(res, err) {
					    				location.href = settings.urls.afterLogoutPage;
					    			});
					    		})
					    	)
					    )
					)
    			)
        	)
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
		,activateAppLink: 'home' //the active link of the main menu
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
			,mainMenu: '#mainMenu' //for all elements for the main menu
    	}
        ,deployd: dpd //the deployd
    };
})(jQuery);