//this is the page generator
var BKAppPage = function(wrapperId) {
	
	//getting the wrapper
	this.getWrapper = function() {
		return $('#' + wrapperId);
	};
	
	//setting the appJs
	this.setAppJs = function(appJs) {
		this.appJs = appJs;
	}
	
	//private function: getting the header div
	getHeader = function(title, leftbtn, rightbtn) {
		var tmp = {};
		tmp.div = $('<div />').attr('data-role', 'header').addClass('appPageHeader');
		if(leftbtn)
			tmp.div.append(leftbtn);
		if(title)
			tmp.div.append($('<h1 />').append(title));
		if(rightbtn)
			tmp.div.append(rightbtn);
		return tmp.div;
	};
	
	//private function: getting the page div element
	getPage = function(id, header, content, footer, theme, classname) {
		var tmp = {};
		tmp.pageDiv = $('<div />').attr('data-role', 'page').addClass('appPage').attr('id', id);
		if(theme)
			tmp.pageDiv.attr('data-theme', theme);
		if(classname)
			tmp.pageDiv.removeClass(classname).addClass(classname);
		if(header)
			tmp.pageDiv.append(header);
		if(content)
			tmp.pageDiv.append($('<div data-role="content" />').append(content));
		if(footer)
			tmp.pageDiv.append(footer);
		return tmp.pageDiv;
	};
	
	//private function: get field container
	getFieldContainer =  function() {
		return $('<div />').attr('data-role', 'fieldcontain');
	};
	
	//private function: getting the list view ul
	getListView = function() {
		return $('<ul />').attr('data-role', 'listview');
	};
	
	//showin the loading icon
	this.showLoading = function(options) {
		$.mobile.loading("show", options);
	};
	
	//hide the loading icon
	this.hideLoading = function(options) {
		$.mobile.loading('hide');
	};
	
	//changeTo a new page
	this.changePage = function(newPage) {
		var tmp = {};
		tmp.oldPage = $.mobile.activePage;
		$.mobile.changePage(newPage);
		return this;
	};
	
	//getting the login page
	this.getLoginPage = function() {
		var tmp = {};
		tmp.me = this;
		tmp.header = getHeader('BSuite App');
		
		tmp.usernamebox = $('<input />').attr('id', 'username').attr('placeholder', 'username').attr('type', 'text');
		tmp.passwordbox = $('<input />').attr('id', 'password').attr('placeholder', 'password').attr('type', 'password');
		tmp.content = $('<div />')
		.append(getFieldContainer()
			.append($('<label />').attr('for', 'username').html('Username: '))
			.append(tmp.usernamebox)
			.append($('<label />').attr('for', 'password').html('Password: '))
			.append(tmp.passwordbox)
			.append($('<button  />').attr('data-theme', 'b').html('Login').click(function() {
					tmp.me.appJs.login(tmp.usernamebox.val(), tmp.passwordbox.val());
				})
			)
		)
		return getPage('loginPage', tmp.header, tmp.content).appendTo(this.getWrapper());
	};
	
	//getting the mainmenu item
	getMainMenuItem = function(title, imgPath, desc, aside, href) {
		var tmp = {};
		tmp.a = $('<a />');
		if(href)
			tmp.a.attr('href', href);
		if(imgPath)
			tmp.a.append($('<img />').attr('src', imgPath));
		if(title)
			tmp.a.append($('<h2 />').html(title));
		if(desc)
			tmp.a.append($('<p />').html(desc));
		if(aside)
			tmp.a.append($('<p />').addClass('ui-li-aside').html(aside));
		return $('<li />').append(tmp.a);
	};
	
	//getting the main menu page
	this.getMainMenuPage = function(user) {
		var tmp = {};
		tmp.me = this;
		tmp.header = getHeader('Welcome, ' + user.person);
		tmp.content = $('<div />').append(
			$('<ul data-role="listview" data-inset="true" />').append(
				getMainMenuItem('Record Trans', 'http://view.jquerymobile.com/master/demos/_assets/img/apple.png', 'Recording Transactions')
			)
			.append(
				getMainMenuItem('menu 2', 'http://view.jquerymobile.com/master/demos/_assets/img/apple.png', 'description', 'aside')
			)
			.append(
				getMainMenuItem('menu 3', 'http://view.jquerymobile.com/master/demos/_assets/img/apple.png', 'description', 'aside')
			)
			.append(
				getMainMenuItem('menu 4', 'http://view.jquerymobile.com/master/demos/_assets/img/apple.png', 'description', 'aside')
			)
		);
		return getPage('mainMenuPage', tmp.header, tmp.content).appendTo(this.getWrapper());
	};
};