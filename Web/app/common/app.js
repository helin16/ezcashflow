(function($) {
	//this is the main app
	var BKApp = function(settings, pageGenerator) {
		this.accounts = settings.getData('accounts');
		this.user = settings.getData('user');
		pageGenerator.setAppJs(this);
		
		this.getSettings = function() {
			return settings;
		};
		this.getPageGenerator = function() {
			return pageGenerator;
		};
		
		postMe = function(url, data, successFunc) {
			return $.ajax({
				type: "POST",
				dataType: "json",
				url: url,
				data: data,
				success: successFunc
			});
		};
		
		//format the account into associative array
		formatAccounts = function(accounts) {
			var tmp = {};
			$.each(accounts, function(index, item) {
				if(!tmp[item.root.id])
					tmp[item.root.id] = {};
				tmp[item.root.id][item.id] = item;
			});
			return tmp;
		};
		
		this.login = function(username, password) {
			var tmp = {};
			tmp.me = this;
			tmp.requestData = {'method': 'User.getUser', 'user': {'username': username, 'password': password}}
			pageGenerator.showLoading();
			postMe(tmp.me.getSettings().getServerUrl(), tmp.requestData,
				function(data) {
					try {
						if(data.errors && data.errors.length >0)
							throw 'Error found: ' + data.errors.join('; ');
						if(!data.resultData.accounts)
							throw 'System Error: accounts found!';
						//record the accounts
						tmp.me.accounts = formatAccounts(data.resultData.accounts);
						settings.addToStorage('accounts', tmp.me.accounts);
						//record the user
						tmp.requestData.user.id = data.resultData.id;
						tmp.requestData.user.person = data.resultData.person;
						tmp.me.user = tmp.requestData.user;
						tmp.me.getSettings().addToStorage('user', tmp.me.user);
						
						pageGenerator.hideLoading();
						pageGenerator.changePage(pageGenerator.getRecordTransPage(tmp.me.user, tmp.me.accounts));
					} catch (e) {
						pageGenerator.hideLoading();
						alert(e);
					}
				}
			);
		};
	};

	//getting the interface
	$.fn.bkApp = function(settings) {
		var tmp = {};
		tmp.url = (settings.url || 'app.php');
		tmp.totalWrapperId = (settings.totalWrapperId || "pageWrapper");
		
		$.mobile.page.prototype.options.domCache = true;
		
		// Return early if this element already has a plugin instance
		tmp.bkApp = $(document).data('bkApp');
		if(tmp.bkApp)
			return tmp.bkApp;
		
		tmp.bkApp = new BKApp(new BKAppSettings('bkApp', tmp.url), new BKAppPage(tmp.totalWrapperId));
		tmp.bkApp.user = tmp.bkApp.getSettings().getData('user');
		if(tmp.bkApp.user.id === undefined) {
			tmp.bkApp.getPageGenerator().getLoginPage();
		} else {
			tmp.bkApp.getPageGenerator().getRecordTransPage(tmp.bkApp.user, tmp.bkApp.accounts);
		}
		try {
			// check to support local storage
			if ((!('localStorage' in window)) || window['localStorage'] === null)
				throw 'Local Storage NOT supported!';

			// check to support JSON
			if ((!('JSON' in window)) || window['JSON'] === null)
				throw 'JSON NOT supported!';
		} catch (e) {
			alert(e);
			return;
		}
		// Store plugin object in this element's data
		$(document).data('bkApp', tmp.bkApp);
		return tmp.bkApp;
	};
})(jQuery);