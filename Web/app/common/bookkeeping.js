(function($) {
	var Bookkeeping = function() {
		var settings = {
			'serverPath' : '/app/app.php',
			'id_dialogPage' : 'dialogPage'
		}, 
		myLocalStorage = {
			id : 'bookkeeping',
			// get local storage
			getStorage : function(id) {
				var tmp = {};
				if (localStorage[myLocalStorage.id] === undefined
						|| localStorage[myLocalStorage.id] === null)
					return tmp;

				tmp.current = jQuery.parseJSON(localStorage[myLocalStorage.id]);
				tmp.data = (typeof tmp.current[id] != "undefined") ? tmp.current[id] : {};
				return tmp.data;
			},
			// Checking storage
			hasInStorage : function(id) {
				return (myLocalStorage.getStorage(id) !== {});
			},

			// save storage
			saveStorage : function(id, newData) {
				var tmp = {};
				if (localStorage[myLocalStorage.id] === undefined
						|| localStorage[myLocalStorage.id] === null)
					localStorage[myLocalStorage.id] = JSON.stringify(tmp);

				tmp.current = jQuery.parseJSON(localStorage[myLocalStorage.id]);
				tmp.current[id] = newData;
				localStorage[myLocalStorage.id] = JSON.stringify(tmp.current);
			},

			// removing from localStorage
			removeFromStorage : function(id, label) {
				if (myLocalStorage.hasInStorage(id)) {
					var data = myLocalStorage.getStorage(id);
					delete data[id];
					myLocalStorage.saveStorage(id, data);
				}
			},

			// dangerous, this is clear the whole Bsuite Local storage
			clearStorage : function() {
				if (localStorage[myLocalStorage.id] === undefined
						|| localStorage[myLocalStorage.id] === null)
					return tmp;

				localStorage[myLocalStorage.id] = JSON.stringify({});
			}
		}, user = {};

		this.setServerPath = function(path) {
			settings.serverPath = path;
		};
		
		this.date_YMD = function(cDate) {
		    return cDate.getFullYear()
		           + '-' + ("00" + (cDate.getMonth()+1)).slice(-2)
		           + '-' + ("00" + cDate.getDate()).slice(-2);
		};
		
		this.date_YMDHIS = function(cDate) {
		    return date_YMD(cDate)
		       + ' ' + ("00" + (cDate.getHours()+1)).slice(-2)
	           + '-' + ("00" + (cDate.getMinutes()+1)).slice(-2)
	           + '-' + ("00" + cDate.getSeconds()).slice(-2);
		};
		
		this.getNow = function()
		{
			return new Date();
		};

		var parseResponse = function(data, throwError) {
			if (data.errors.length > 0) {
				if (throwError !== undefined && throwError === true)
					throw data.errors.join('; ');

				pageJs.showDialog('Error!', '<div class="errorMsg">'
						+ data.errors.join('<br />') + '</div>', '', 'e');
				return {};
			}

			return data.resultData;
		};

		this.showDialog = function(title, content, footer, dataTheme) {
			var tmp = {};

			if ($("div#" + settings.id_dialogPage).length > 0)
				$("div#" + settings.id_dialogPage).remove();

			tmp.dataTheme = (dataTheme !== undefined ? 'data-theme="'
					+ dataTheme + '"' : '');
			tmp.title = (title !== undefined ? title : 'confirm?');
			tmp.content = (content !== undefined ? content : '');
			tmp.footer = ((footer !== undefined && footer !== '') ? '<div data-role="footer" data-position="inline" '
					+ tmp.dataTheme + '>' + footer + '</div>'
					: '');

			$('<div />', {
				'data-role' : "page",
				'id' : settings.id_dialogPage
			}).html(
					'<div data-role="header" data-position="inline" '
							+ tmp.dataTheme + '>' + '<h3 id="title">'
							+ tmp.title + '</h3>' + '</div>'
							+ '<div data-role="content"' + tmp.dataTheme + '>'
							+ tmp.content + '</div>' + tmp.footer).appendTo(
					$.mobile.pageContainer);

			$.mobile.changePage("#" + settings.id_dialogPage, {
				role : 'dialog',
				transition : 'slidedown'
			});
		};

		this.login = function(usernameBox, passwordBox) {
			var tmp = {};
			tmp.username = $(usernameBox).val();
			tmp.password = $.sha1($(passwordBox).val());
			this.postMe(settings.serverPath, {
				'method' : 'user.getUser',
				'user' : {
					'username' : tmp.username,
					'password' : tmp.password
				}
			}).success(function(data, textStatus, jqXHR) {
				data = parseResponse(data);
				if (Object.keys(data).length === 0)
					return;

				user.id = data.id;
				user.name = data.person;
				user.username = tmp.username;
				user.password = tmp.password;
				
				myLocalStorage.saveStorage('accounts', data.accounts);
				
				$(usernameBox).val('');
				$(passwordBox).val('');
				pageJs.changeActionType('Spend');
				$.mobile.changePage('#mainMenuPage');
			});
		};

		this.logout = function() {
			if(!confirm('Are you sure you want to logout?'))
				return;
			user = {};
			$.mobile.changePage($('div#loginPage:jqmData(role=page)'));
		};

		this.postMe = function(url, data, loadingMsg) {
			var tmp = {};
			tmp.loadingMsg = (loadingMsg === undefined ? 'Loading...' : loadingMsg);
			$.mobile.showPageLoadingMsg("b", tmp.loadingMsg, true);
			return $.post(url, data, function(data, textStatus, jqXHR){}, 'json')
			.complete(function() {
				$.mobile.hidePageLoadingMsg();
			})
			.error(function(jqXHR, textStatus, errorThrown) {
				console.error(jqXHR.responseText);
			});
		};
		
		//change the top nav bar on main menu to do: spend, income and transfer
		this.changeActionType = function(newValue){
			var tmp = {};
			tmp.value = newValue;
			tmp.fromRootIds = tmp.toRootIds = [];
			if(newValue === 'Spend')
			{
				tmp.fromRootIds = [1,2];
				tmp.toRootIds = [4];
			}
			else if(newValue === 'Income')
			{
				tmp.fromRootIds = [3];
				tmp.toRootIds = [1];
			}
			else if(newValue === 'Transfer')
			{
				tmp.fromRootIds = [1,2];
				tmp.toRootIds = [1,2];
			}
			getTransEditList('actionrecordpanel', newValue, tmp.fromRootIds, tmp.toRootIds);
		};
		
		getTransEditList = function(listId, actiontype, fromRootIds, toRootIds, trans, backToPageId){
			var tmp = {};
			tmp.transId = (trans !== undefined ? trans.id : '');
			tmp.fromId = (trans !== undefined  && trans.from !== null ? trans.from.id : '');
			tmp.fromName = (trans !== undefined  && trans.from !== null  ? trans.from.name : '');
			tmp.fromBreadCrubms = (trans !== undefined  && trans.from !== null  ? trans.from.breadCrumbs : 'Click Here to Select Account');
			tmp.fromAmount = (trans !== undefined  && trans.from !== null  ? trans.from.amount : '');
			tmp.toId = (trans !== undefined  && trans.to !== null ? trans.to.id : '');
			tmp.toName = (trans !== undefined  && trans.to !== null ? trans.to.name : '');
			tmp.toBreadCrubms = (trans !== undefined  && trans.to !== null ? trans.to.breadCrumbs : 'Click Here to Select Account');
			tmp.toAmount = (trans !== undefined  && trans.to !== null ? trans.to.amount : '');
			tmp.value = (trans !== undefined ? trans.value : '');
			tmp.comments = (trans !== undefined ? trans.comments : '');
			
			tmp.editPanelList = $('#actionrecordpanel:jqmData(role=listview)');
			tmp.fromAccountList = $('#fromAccountList:jqmData(role=listview)');
			tmp.toAccountList = $('#toAccountList:jqmData(role=listview)');
			tmp.editPanelList.find('#actionBtn').val(actiontype).prev('span').find('span.ui-btn-text').text(actiontype);
			tmp.editPanelList.find('#transId').val(tmp.transId);
			tmp.editPanelList.find('#fromAccount #accountId').val(tmp.fromId);
			tmp.editPanelList.find('#fromAccount #title').html(tmp.fromName);
			tmp.editPanelList.find('#fromAccount #breadCrumbs').html(tmp.fromBreadCrubms);
			tmp.editPanelList.find('#fromAccount .ui-li-count').remove();
			if(tmp.fromAmount !== '')
				tmp.editPanelList.find('#fromAccount a').append('<span class="ui-li-count">' + tmp.fromAmount + '</span>');
			tmp.editPanelList.find('#fromAccount #rootIds').val(fromRootIds.join(','));
			
			tmp.editPanelList.find('#toAccount #accountId').val(tmp.toId);
			tmp.editPanelList.find('#toAccount #title').html(tmp.toName);
			tmp.editPanelList.find('#toAccount #breadCrumbs').html(tmp.toBreadCrubms);
			tmp.editPanelList.find('#toAccount .ui-li-count').remove();
			if(tmp.toAmount !== '')
				tmp.editPanelList.find('#toAccount a').append('<span class="ui-li-count">' + tmp.toAmount + '</span>');
			tmp.editPanelList.find('#toAccount #rootIds').val(toRootIds.join(','));
			
			tmp.editPanelList.find('#amount').val(tmp.value);
			tmp.editPanelList.find('#comments').val(tmp.comments);
			tmp.editPanelList.find('#backToPageId').val(backToPageId);
		};
		
		getListOptions = function(data, clickFunc){
			var tmp = {};
			tmp.html = '';
			$.each(data, function(index, account){
				if(account.isLeaf === true)
				{
					tmp.html += '<li value="' + account.id + '">';
						tmp.html += "<a href='#' data-rel='back' data-icon='arrow-l' onclick=\"" + clickFunc + "\">";
							tmp.html += '<h4>' + account.name + '</h4>';
							tmp.html += '<p class="ui-li-desc">' + account.breadCrumbs + '</p>';
							tmp.html += '<span class="ui-li-count">$' + account.amount + '</span>';
							tmp.html += '<span style="display: none;" id="data">' + JSON.stringify(account) + '</span>';
						tmp.html += '</a>';
					tmp.html += '</li>';
				}
			});
			return tmp.html;
		};
		
		this.showAccounts = function(listId, listItemId, rootIds){
			var tmp = {};
			tmp.accounts = myLocalStorage.getStorage('accounts');
			tmp.html = '<ul data-role="listview" data-inset="true" data-filter="true" data-autodividers="true">';
			$.each(rootIds.split(','), function(index, id){
				tmp.html += getListOptions(tmp.accounts[id], "pageJs.selectAccount('" + listId + "', '" + listItemId + "', $.parseJSON($(this).find('#data').html()));");
			});
			tmp.html += '</ul>';
			this.showDialog('select a account', tmp.html);
		};
		
		this.selectAccount = function(listId, listItemId, data) {
			var tmp = {};
			tmp.listItem = $('#' + listId + ':jqmData(role=listview)').find('li#' + listItemId);
			tmp.listItem.find('h3 #title').html(data.name);
			tmp.listItem.find('p.ui-li-desc').html(data.breadCrumbs);
			tmp.listItem.find('.ui-li-count').remove();
			tmp.listItem.append('<span class="ui-li-count">$' + data.amount + '</span>');
			tmp.listItem.find('#accountId').val(data.id);
			$('#' + listId + ':jqmData(role=listview)').listview('refresh');
		};
		
		this.recordTrans = function(clickedBtn)
		{
			var tmp = {};
			tmp.transdata = {};
			tmp.container = $.mobile.activePage;
			tmp.transdata.action = $(clickedBtn).val();
			
			tmp.backToPageIdHolder = tmp.container.find("#backToPageId");
			tmp.backToPageId = $.trim(tmp.backToPageIdHolder.val());
			
			tmp.idHolder = tmp.container.find("#transId");
			tmp.transdata.id = $.trim(tmp.idHolder.val());
			tmp.fromAccountBox = tmp.container.find('#fromAccount #accountId');
			tmp.transdata.fromAccountId = $.trim(tmp.fromAccountBox.val());
			if(tmp.transdata.fromAccountId === '')
				return alert('Invalid From Account!');
			
			tmp.toAccountBox = tmp.container.find('#toAccount #accountId');
			tmp.transdata.toAccountId = $.trim(tmp.toAccountBox.val());
			if(tmp.transdata.toAccountId === '')
				return alert('Invalid To Account!');
			
			tmp.amountBox = tmp.container.find('input#amount');
			tmp.transdata.amount = tmp.amountBox.val().replace(" ", "").replace('$', '').replace(',', '');
			if(!tmp.transdata.amount.match(/^\d+(\.\d{0,2})?$/))
				return alert('Invalid Amount(=' + tmp.transdata.amount + ')!\nCorrect Format:\n 123.23');
			
			tmp.commentsBox = tmp.container.find('input#comments');
			tmp.transdata.comments = $.trim(tmp.commentsBox.val());
			
			this.postMe(settings.serverPath, {'method' : 'trans.recordTrans', 'user' : user, 'trans': tmp.transdata}, 'Recording Trans ...')
			.success(function(data, textStatus, jqXHR) {
					data = parseResponse(data);
					if (Object.keys(data).length === 0)
						return;
					
					updateAccounts(data);
					tmp.fromAccountBox.val('');
					tmp.toAccountBox.val('');
					tmp.amountBox.val('');
					tmp.commentsBox.val('');
					tmp.idHolder.val('');
					tmp.backToPageIdHolder.val('');
					alert( tmp.transdata.action + ' Recorded Successfully!');
					if(tmp.backToPageId !== '')
						$.mobile.changePage('#' + tmp.backToPageId);
					else
						pageJs.changeActionType(tmp.transdata.action, true);
			});
		};
		
		updateAccounts = function(accounts)
		{
			var tmp = {};
			tmp.accounts = myLocalStorage.getStorage('accounts');
			$.each(accounts, function(index, account){
				if(account.active == 0)
					delete tmp.accounts[account.rootId][account.accountNumber];
				else
					tmp.accounts[account.rootId][account.accountNumber] = account; 
			});
			myLocalStorage.saveStorage('accounts',tmp.accounts);
			tmp.accounts = myLocalStorage.getStorage('accounts');
		};
		
		this.getTrans = function(fromDate, toDate, pageNo){
			var tmp = {};
			tmp.transPage = $("#transPage:jqmData(role='page')");
			tmp.transPage.find('#transListTitle').html('From Date: ' + fromDate + '<br />To Date:' + toDate);
			tmp.searchInfo = {'fromDate': fromDate, 'toDate': toDate, 'pageNo': pageNo, 'pageSize': 10};
			this.postMe(settings.serverPath, {'method' : 'trans.getTrans', 'user' : user, 'searchInfo': tmp.searchInfo}, 'Getting Transactions ...')
			.success(function(data, textStatus, jqXHR) {
					data = parseResponse(data);
					if (Object.keys(data).length === 0)
						return;
					
					tmp.listContainer = tmp.transPage.find('#transList::jqmData(role=listview)');
					if(pageNo === 1)
						tmp.listContainer.html('');
					
					tmp.total = data.total;
					tmp.html = '';
					$.each(data.tans, function(index, item){
						
						tmp.html += '<li data-role="list-divider" role="heading" class="transItem_' + item.id + '">';
							tmp.html += item.created;
							tmp.html += '<span class="ui-li-count">$' + item.value + '</span>';
						tmp.html += '</li>';
						tmp.html += '<li class="transItem_' + item.id + '">';
							tmp.html += "<a onclick=\"pageJs.loadEditTrans($.parseJSON($(this).find('#data').html()));\">";
								tmp.html += '<span id="data" style="display: none;">' + JSON.stringify(item) + "</span>";
								tmp.html += '<p class="ui-li-desc">';
									tmp.html += 'From: <strong>' + (item.from !== null ? item.from.name : '') + '</strong> ';
									tmp.html += '<span style="padding-left: 10px; font-size: 10px;">' + (item.from !== null ? item.from.breadCrumbs : '') + "</span>";
								tmp.html += '</p>';
								tmp.html += '<p class="ui-li-desc">';
								tmp.html += 'To: <strong>' + (item.to !== null ? item.to.name : '') + '</strong> ';
								tmp.html += '<span style="padding-left: 10px; font-size: 10px;">' + (item.to !== null ? item.to.breadCrumbs : '') + "</span>";
								tmp.html += '</p>';
								tmp.html += '<p class="ui-li-desc">';
									tmp.html += item.comments;
								tmp.html += '</p>';
							tmp.html += '</a>';
							tmp.html += '<a onclick="pageJs.deleteTrans(' + item.id + ');">';
							tmp.html += '</a>';
						tmp.html += '</li>';
					});
					if((tmp.listContainer.find('li').length/2) + data.tans.length < tmp.total)
					{
						tmp.html += "<li data-theme='e' onclick=\"$(this).remove();pageJs.getTrans('" + fromDate + "', '" + toDate + "', " + (pageNo * 1 + 1) + ");\">";
							tmp.html += 'Get more results';
						tmp.html += '</li>';
					}
					tmp.listContainer.append(tmp.html).listview('refresh');
			});
		};
		
		this.loadEditTrans = function(item)
		{
			var tmp = {};
			tmp.backToPageId = $.trim($.mobile.activePage.attr('id'));
			getTransEditList('actionrecordpanel', 'Transfter', [1,2,3,4], [1,2,3,4], item, tmp.backToPageId);
			$('#actionrecordpanel:jqmData(role=listview)').listview('refresh');
			$.mobile.changePage('#mainMenuPage');
		};
		
		this.deleteTrans = function(transId){
			if(!confirm('Are you sure you want to delete this transaction?'))
				return;
			
			this.postMe(settings.serverPath, {'method' : 'trans.deleteTrans', 'user' : user, 'transIds': [transId]}, 'Delete Trans ...')
			.success(function(data, textStatus, jqXHR) {
					data = parseResponse(data);
					if (Object.keys(data).length === 0)
						return;
					
					updateAccounts(data);
					$.mobile.activePage.find('li.transItem_' + transId).remove();
					$.mobile.activePage.find('#transList::jqmData(role=listview)').listview('refresh');
			});
		};
		
		this.getTodayTrans = function(){
			var tmp = {};
			tmp.today = pageJs.getNow();
			pageJs.getTrans(pageJs.date_YMD(tmp.today) + ' 00:00:00', pageJs.date_YMD(tmp.today) + ' 23:59:59', 1);
		};
		
		this.getMTDTrans = function(){
			var tmp = {};
			tmp.today = pageJs.getNow();
			tmp.month = tmp.today.getFullYear() + '-' + (('00' + (tmp.today.getMonth()+1)).slice(-2)); 
			pageJs.getTrans(tmp.month + '-01 00:00:00', tmp.month + '-31 23:59:59', 1);
		};
		
		this.getWTDTrans = function(){
			var tmp = {};
			tmp.today = pageJs.getNow();
			tmp.day = tmp.today.getDay();
			
			if(tmp.day >= 4)
			{
				tmp.lastestThurday = new Date(tmp.today.getTime() - (tmp.day - 4)*24*60*60*1000);
				tmp.nearestWed = new Date(tmp.today.getTime() + (7 - tmp.day + 3)*24*60*60*1000);
			}
			else
			{
				tmp.lastestThurday = new Date(tmp.today.getTime() - (tmp.day + 7 - 4)*24*60*60*1000);
				tmp.nearestWed = new Date(tmp.today.getTime() + (3- tmp.day)*24*60*60*1000);
			}
			pageJs.getTrans(pageJs.date_YMD(tmp.lastestThurday) + ' 00:00:00', pageJs.date_YMD(tmp.nearestWed) + ' 23:59:59', 1);
		};
		
		this.loadAccounts = function(parentId){
			var tmp = {};
			tmp.page = $('#accountsPage:jqmData(role=page)');
			
			tmp.accounts = myLocalStorage.getStorage('accounts');
			tmp.parentAccount = null;
			tmp.html = '';
			tmp.maxAccountNumber = '';
			$.each(Object.keys(tmp.accounts), function(index, rootId){
				$.each(tmp.accounts[rootId], function(index, account){
					if(account.id == parentId)
						tmp.parentAccount = account;
					if(account.parentId == parentId) 
					{
						tmp.html += getAccountListItem(account, "pageJs.loadAccounts(" + account.id + ");");
						if(tmp.maxAccountNumber < account.accountNumber)
							tmp.maxAccountNumber = account.accountNumber;
					}
				});
			});
			tmp.maxAccountNumber = (tmp.maxAccountNumber === '' ? (tmp.parentAccount.accountNumber + '0001') : ((tmp.maxAccountNumber * 1) + 1));
			
			tmp.page.find(':jqmData(role=header) h3').html(tmp.parentAccount.name);
			tmp.page.find('.accountTitle').html(tmp.parentAccount.name);
			tmp.editPane = tmp.page.find('#editPane:jqmData(role="collapsible")');
			
			tmp.showEditPaneBtn = tmp.page.find('#showEditPaneBtn:jqmData(role=button)');
			tmp.showEditPaneBtn.hide();
			if($.inArray(parentId, [1,2,3,4]) === -1)
				tmp.showEditPaneBtn.show();
			tmp.editPane.find('#accountId').val(tmp.parentAccount.id);
			tmp.editPane.find('#accountName').val(tmp.parentAccount.name);
			tmp.editPane.find('#accountNumber').val(tmp.parentAccount.accountNumber);
			tmp.editPane.find('#comments').val(tmp.parentAccount.comments);
			tmp.editPane.find('#accountValue').val(tmp.parentAccount.value);
			tmp.editPane.find('#accountBudget').val(tmp.parentAccount.budget);
			
			tmp.newPane = tmp.page.find('#newPane:jqmData(role="collapsible")');
			tmp.newPane.find('#accountNumber').val(tmp.maxAccountNumber);
			tmp.newPane.find('#parentId').val(tmp.parentAccount.id);
			tmp.newPane.find('#accountName').val('');
			tmp.newPane.find('#comments').val('');
			tmp.newPane.find('#accountValue').val('');
			tmp.editPane.find('#accountBudget').val('');
			
			if(tmp.parentAccount.parentId !== null)
				tmp.html = '<li data-role="list-divider" data-icon="arrow-l" data-iconpos="left"><a onclick="pageJs.loadAccounts(' + tmp.parentAccount.parentId + ');">Back To Its Parent</a></li>' + tmp.html;
			tmp.page.find('#accountsList:jqmData(role=listview)').html(tmp.html);
			try{tmp.page.find('#accountsList:jqmData(role=listview)').listview('refresh');}catch(e){};
			
			this.showAccountListPane('listPan');
		};
		
		getAccountListItem = function(account, clickFunc) {
			var tmp = {};
			tmp.html = '';
			tmp.html += '<li class="accountId_' + account.id + '" data-role="list-divider" role="heading">';
				tmp.html += account.name;
				tmp.html += (account.isLeaf === true ? '<span class="ui-li-count">$' + account.amount + '</span>' : '');
			tmp.html += '</li>';
			tmp.html += '<li class="accountId_' + account.id + '" >';
				tmp.html += "<a onclick=\"" + clickFunc + "\">";
					tmp.html += '<p class="ui-li-desc"><b>Acc. No.:</b>' + account.accountNumber + '</p>';
					tmp.html += '<p class="ui-li-desc"><b>Comments:</b> ' + account.comments + '</p>';
					tmp.html += '<p class="ui-li-desc">' + account.breadCrumbs + '</p>';
					tmp.html += '<span style="display: none;" id="data">' + JSON.stringify(account) + '</span>';
				tmp.html += '</a>';
				if(account.isLeaf === true)
					tmp.html += "<a onclick=\"pageJs.deleteAccount(" + account.id + ");\"></a>";
			tmp.html += '</li>';
			return tmp.html;
		};
		
		this.deleteAccount = function(accountId){
			if(!confirm('Are you sure you want to delete this account?'))
				return;
			
			this.postMe(settings.serverPath, {'method' : 'account.deleteAccounts', 'user' : user, 'accountIds': [accountId]}, 'Delete Accounts ...')
			.success(function(data, textStatus, jqXHR) {
					data = parseResponse(data);
					if (Object.keys(data).length === 0)
						return;
					
					updateAccounts(data);
					$.mobile.activePage.find('li.accountId_' + accountId).remove();
					$.mobile.activePage.find('#accountsList::jqmData(role=listview)').listview('refresh');
			});
		};
		
		this.saveAccount = function(clickedBtn, accountId, parentId)
		{
			var tmp = {};
			tmp.editPane = $(clickedBtn).parents('div[data-role="collapsible"]');
			tmp.accountInfo = {};
			tmp.accountInfo.accountId = accountId;
			tmp.accountInfo.parentId = parentId;
			tmp.accountInfo.comments = tmp.editPane.find('#comments').val();
			tmp.accountInfo.accountNumber = tmp.editPane.find('#accountNumber').val().replace(" ", "");
			if(tmp.accountInfo.accountNumber === '')
				return alert('Invalid account number(=' + tmp.accountInfo.accountNumber + ')!');
			
			tmp.accountInfo.accountName = $.trim(tmp.editPane.find('#accountName').val());
			if(tmp.accountInfo.accountName === '')
				return alert('Invalid account Name(=' + tmp.accountInfo.accountName + ')!');
			
			tmp.accountInfo.value = tmp.editPane.find('#accountValue').val().replace(" ", "").replace('$', '').replace(',', '');
			if(tmp.accountInfo.value !== '' && !tmp.accountInfo.value.match(/^\d+(\.\d{0,2})?$/))
				return alert('Invalid Value(=' + tmp.accountInfo.value + ')!\nCorrect Format:\n 123.23');
			
			tmp.accountInfo.budget = tmp.editPane.find('#accountBudget').val().replace(" ", "").replace('$', '').replace(',', '');
			if(tmp.accountInfo.budget !== '' && !tmp.accountInfo.budget.match(/^\d+(\.\d{0,2})?$/))
				return alert('Invalid Budget(=' + tmp.accountInfo.budget + ')!\nCorrect Format:\n 123.23');
			
			this.postMe(settings.serverPath, {'method' : 'account.saveAccount', 'user' : user, 'accountInfo': tmp.accountInfo}, 'Saving Accounts ...')
			.success(function(data, textStatus, jqXHR) {
					data = parseResponse(data);
					if (Object.keys(data).length === 0)
						return;
					
					updateAccounts(data);
					tmp.resultAccountIds = Object.keys(data);
					pageJs.loadAccounts(data[tmp.resultAccountIds[0]].parentId);
			});
		};
		
		this.showAccountListPane = function(showDivId){
			$.mobile.activePage.find('div.accountListPane').hide();
			$.mobile.activePage.find('div#' + showDivId).show();
		};

		this.bindEvents = function() {
			var tmp = {};

			// initial
			$(document).bind("mobileinit", function() {
				$.mobile.defaultDialogTransition = 'none';
				$.mobile.defaultPageTransition = 'none';
				$.mobile.allowCrossDomainPages = $.support.cors = true;
			});

			// check authed pages
			$("div.authedPage:jqmData(role='page')").live('pagebeforeshow',	function() {
				if ((user.id === undefined || user.id === '')) {
					$.mobile.changePage($('div#loginPage:jqmData(role=page)'));
				}
			});
			
			//events for mainMenuPage
			$("#mainMenuPage:jqmData(role='page')").live("pagebeforeshow", function(){
				$("div#mainMenuPage:jqmData(role='page')").find('#user').html(user.name);
			});
			
			//events for transPage
			$("#transPage:jqmData(role='page')").live("pagebeforeshow", function(){
				pageJs.getTodayTrans();
			});
		}
	};

	$.fn.bookeeping = function(options) {
		var tmp = {}
		tmp.appBookkeeping = $(document).data('appBookkeeping');
		// Return early if this element already has a plugin instance
		if (tmp.appBookkeeping === undefined) {
			tmp.appBookkeeping = new Bookkeeping();
			try {
				// check to support local storage
				if ((!('localStorage' in window))
						|| window['localStorage'] === null)
					throw 'Local Storage NOT supported!';

				// check to support JSON
				if ((!('JSON' in window)) || window['JSON'] === null)
					throw 'JSON NOT supported!';
			} catch (e) {
				$(document).bind("pagebeforecreate",function() {
					$("div:jqmData(role='page')").html('HTML5 or JSON is NOT supported by your browser: ' + e);
				});
				return;
			}
			tmp.appBookkeeping.bindEvents();

			// Store plugin object in this element's data
			$(document).data('appBookkeeping', tmp.appBookkeeping);
		}
		return tmp.appBookkeeping;
	};
})(jQuery);
var pageJs = $(document).bookeeping();
