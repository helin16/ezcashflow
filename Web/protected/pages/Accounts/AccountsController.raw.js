//this is the source file for the AccountList page
var AccountsJs = new Class.create();
AccountsJs.prototype = {
	rootId: 1
	,divIds: {
		list: ''
	}
	,callBackIds: {
		saveAccount: '',
		deleteAccounts: '',
		moveAccounts: ''
	}
	
	//constructor
	,initialize: function (accountListId, getAccCallBackId, saveAccCallBackId, delAccCallBackId, moveAccCallBackId) {
		this.divIds.list = accountListId;
		this.callBackIds.getAccounts = getAccCallBackId;
		this.callBackIds.saveAccount = saveAccCallBackId;
		this.callBackIds.deleteAccounts = delAccCallBackId;
		this.callBackIds.moveAccounts = moveAccCallBackId;
	}
	
	/**
	 * click event for the table in the .box-title
	 */
	,_selectAccountType: function (btn) {
        var tmp = {};
        tmp.clickedBtn = $(btn);
        tmp.clickedBtn.up('ul').getElementsBySelector('li').each(function(item){
            item.down('a').removeAttribute('selected');
        });
        tmp.clickedBtn.writeAttribute('selected');
        return $(btn).readAttribute('rootId');
    }
	
    /**
     * selecting a root type
     */
    ,showAccounts: function(btn) {
    	var tmp = {};
    	tmp.me = this;
    	tmp.accounts = appJs.getPageData('accounts');
    	tmp.me.rootId = tmp.me._selectAccountType(btn);
		appJs.postAjax(this.callBackIds.getAccounts, {'rootId': tmp.me.rootId}, {
    		'onLoading': function(sender, param){
    			$(tmp.me.divIds.list).update('<img src="/contents/images/loading.gif" />');
    		},
	    	'onComplete': function(sender, param){
	    		try {
	    			tmp.accounts[tmp.me.rootId] = appJs.getResp(param, false, true);
	    			appJs.setPageData('accounts', tmp.accounts);
	    			$(tmp.me.divIds.list).update('');
	    			tmp.me._showAccList(tmp.accounts[tmp.me.rootId], tmp.me.rootId, 0);
	    		} catch (e) {
	    			alert(e);
	    		}
	    	}
    	});
    	return false;
    }
    
    //showing the result
    ,_showAccList: function (accounts, startAccId, rowNo) {
    	var tmp = {};
    	tmp.me = this;
    	tmp.account = accounts[startAccId];
		tmp.rowNo = rowNo;
		$(tmp.me.divIds.list).insert({'bottom': tmp.me._formatAccountRow(tmp.account)
			.addClassName(tmp.rowNo % 2 === 0 ? 'even' : 'odd')
			.store('account', tmp.account)
		});
		tmp.account.children.map(function(k) { return accounts[k]; }).sortBy(function(acc) {
    		return acc.name;
    	}).each(function(acc){
    		tmp.rowNo = tmp.me._showAccList(accounts, acc.id, tmp.rowNo + 1);
    	});
		return tmp.rowNo;
    }
    
    /**
     * getting the accounts
     */
    ,_formatAccountRow: function(account) {
    	var tmp = {};
    	tmp.me = this;
    	tmp.leftMargin = account.level * 4 ;
    	tmp.newRow = new Element('div', {'class': 'row accountrow', 'accountid': account.id, 'accountno': account.accountNumber, 'parentid': account.parent.id})
	    	.insert({'bottom': new Element('div', {'class': 'space rowDivd', 'style': 'width: ' + tmp.leftMargin + '%'}).update(
	    		account.children.size() === 0 ? '&nbsp;' :
	    		new Element('span', {'class': 'expendBtn'}).update('-').observe('click', function() {
	    			tmp.account = $(this).up('.accountrow').retrieve('account');
	    			if($(this).innerHTML.strip() === '-') {
	    				$(tmp.me.divIds.list).getElementsBySelector('.accountrow').filter(function(item) {
		    				return item.readAttribute('accountno').startsWith(tmp.account.accountNumber) && item.readAttribute('accountid') !== tmp.account.id;
		    			}).each(function(item) {
		    				if($(item).retrieve('account').children.size() > 0)
		    					$(item).down('.expendBtn').update('+');
		    				item.hide();
		    			});
	    				$(this).update('+');
	    			} else {
	    				$(tmp.me.divIds.list).getElementsBySelector('.accountrow[parentid=' + tmp.account.id + ']').each(function(item) {
		    				item.show();
		    			});
	    				$(this).update('-');
	    			}
	    		})
	    	)})
	    	.insert({'bottom': new Element('div', {'class': 'content rowDivd', 'style': 'width: ' + (100 - tmp.leftMargin - 5) + '%'})
	    		.insert({'bottom': new Element('div', {'class': 'accountname'}).update( account.name ) })
	    		.insert({'bottom': new Element('div', {'class': 'value'}).update( account.sum === 0 ? '' : '$' + account.sum ) })
	    		.insert({'bottom': new Element('div', {'class': 'accountno'}).update( account.accountNumber ) })
	    		.insert({'bottom': new Element('div', {'class': 'budget'}).update( (account.budget === 0 || account.budget.blank()) ? '' : '$' + account.budget ) })
	    		.insert({'bottom': new Element('div', {'class': 'comments'}).update( account.comments ) })
	    	})
	    	.insert({'bottom': new Element('div', {'class': 'btns rowDivd', 'style': 'width: 5%'})
	    		.update(new Element('img', {'class': 'dropdownmenu', 'src': '/contents/images/arrow-down.gif'})
	    			.observe('click', function(){
	    				tmp.dropdownmenu = $(this).up('.row').down('.btnListDiv');
	    				if(tmp.dropdownmenu)
	    					tmp.dropdownmenu.remove();
	    				else
	    					$(this).insert({'after': tmp.me.showBtnsDiv(account) });
	    			})
	    		)
	    	});
    	return tmp.newRow;
    }
    
    /**
     * getting the btns for each account in the list
     */
    ,showBtnsDiv: function(account) {
    	var tmp = {};
    	tmp.me = this;
    	//remove all open btn divs
    	$$('.btnListDiv').each(function(item){ item.remove(); });
    	
    	//getting the editing account row
    	tmp.accountRow = $(tmp.me.divIds.list).down('.row[accountid="' + account.id + '"]');
    	if(tmp.accountRow === undefined || tmp.accountRow === null) {
    		return;
    	}
    	//getting the menu button
    	tmp.btn = tmp.accountRow.down('.dropdownmenu');
    	if(tmp.btn === undefined || tmp.btn === null) {
    		return;
    	}
    	
    	tmp.newListDiv = new Element('ul').insert({'bottom': new Element('li').update(
			new Element('a', {'href': 'javascript:void(0);'})
				.update('New')
				.observe('click', function(){
					tmp.me._showAccSavingPanel(account, true);
				})
			)
		});
    	
    	if(account.level !== 0) {
    		//the create new button
    		tmp.newListDiv.insert({'bottom': new Element('li')
    			.update(new Element('a', {'href': 'javascript:void(0);'})
    				.update('Edit')
    				.observe('click', function(){
    					tmp.me._showAccSavingPanel(account, false);
    				})
    			)
    		})
    		//the move account button
    		.insert({'bottom': new Element('li')
	    		.update(new Element('a', {'href': 'javascript:void(0);'})
		    		.update('Move')
		    		.observe('click', function(){
		    			tmp.me._showAccMovingPanel(account, false);
		    		})
	    		)
    		});
    	}
    	
    	//the delete button
    	if(account.gotChildren !== true) {
    		tmp.newListDiv.insert({'bottom': new Element('li').update(
				new Element('a', {'href': 'javascript:void(0);'})
					.update('Delete')
					.observe('click', function(){
						tmp.me.delAcc(account.id, this);
					})
			)});
    	}
    	return tmp.newListDiv.wrap(new Element('div', {'class': 'btnListDiv'}));
    }
    
    /**
     * showing the account moving Panel
     */
    ,_showAccMovingPanel: function(account) {
    	var tmp = {};
    	tmp.me = this;
    	//remove all the dropdown menu
    	$$('.btnListDiv').each(function(item){item.remove();});
    	//remove all saving panel
    	$$('.newAccDiv').each(function(item){item.remove();});
    	
    	//getting the selectable accounts:
    	tmp.selectBox = new Element('select', {'class': 'moveToAccs inputbox'});
    	$$('.row[accountid]').each(function(item){
    		tmp.moveToAcc = item.retrieve('account');
    		if(!tmp.moveToAcc.accountNumber.startsWith(account.accountNumber) && account.parent.id !== tmp.moveToAcc.id) {
    			tmp.selectBox.insert({'bottom': new Element('option', {'value': tmp.moveToAcc.id}).update(tmp.moveToAcc.breadCrumbs.name) });
    		}
    	});
    	tmp.newDiv = new Element('div')
	    	.insert({'bottom': new Element('div', {'class': 'newRow'})
				.insert({'bottom': new Element('div', {'class': 'title'}).update('Please choose an account to move to?') })
			})
			.insert({'bottom': tmp.me._getAccountEditRow(' ',tmp.selectBox ) })
    		.insert({'bottom': new Element('div', {'class': 'newRow'})
	    		.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Save'}).observe('click', function(){
	    			tmp.me.moveAccount(this, account, $F($(this).up('.newAccDiv').down('.moveToAccs')));
	    		})})
		    	.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Cancel', 'style': 'float:right;'}).observe('click', function(){
		    		$(this).up('.newAccDiv').remove();
		    	})})
		    });
    	$$('.row[accountid=' + account.id + ']').first().insert({'bottom':  tmp.newDiv.wrap(new Element('div', {'class': 'newAccDiv'})) });
    }
    /**
     * submitting the the data to the backend to move the account
     */
    ,moveAccount: function(btn, moveAccount, moveToAccountId) {
    	var tmp = {};
    	tmp.me = this;
    	tmp.row = $(btn).up('.newRow');
    	tmp.savingInfo = new Element('div').update('saving ...');
    	tmp.accounts = appJs.getPageData('accounts');
    	appJs.postAjax(this.callBackIds.moveAccounts, {'accountId': moveAccount.id, 'parentId': moveToAccountId}, {
    		'onLoading': function(sender, param) {
    			tmp.row.hide().insert({'after': tmp.savingInfo});
    		},
	    	'onComplete': function(sender, param) {
	    		try{
	    			tmp.accounts[moveAccount.root.id] = appJs.getResp(param, false, true);
	    			appJs.setPageData('accounts', tmp.accounts);
    				alert('Moved successfully!');
    				tmp.me._showAccList(tmp.accounts[moveAccount.root.id]);
	    		} catch(e) {
	    			tmp.savingInfo.remove();
	    			tmp.row.show();
	    			alert(e);
	    		}
	    	}
    	});
    }
    /**
     * showing the account saving panel
     */
    ,_showAccSavingPanel: function(account, isNew) {
    	var tmp = {};
    	tmp.me = this;
    	//remove all the dropdown menu
    	$$('.btnListDiv').each(function(item){item.remove();});
    	tmp.newDivId = 'accSaveDiv_' + account.id;
    	//remove all saving panel
    	$$('.newAccDiv').each(function(item){item.remove();});
    	
    	tmp.newDiv = $(tmp.newDivId);
    	tmp.accName = tmp.comments = tmp.accId = tmp.parentId = '';
    	tmp.accValue = tmp.accBudget = '0.00';
    	if(isNew === true) {
    		tmp.parentId = account.id;
    	} else {
    		tmp.accName = account.name;
    		tmp.accValue = account.value;
    		tmp.accBudget = account.budget;
    		tmp.accId = account.id;
    		tmp.comments = account.comments;
    		tmp.parentId = '';
    	}
    		
    	tmp.newDiv = new Element('div', {'id': tmp.newDivId, 'class': 'newAccDiv'})
    	.insert({'bottom': new Element('div', {'class': 'newRow'})
    		.insert({'bottom': new Element('div', {'class': 'title'}).update(
				isNew === true ? 'Creating a new Account' : 'Updating selected account'
			)})
    	})
    	.insert({'bottom': tmp.me._getAccountEditRow('Name: ', 
			new Element('input', {'accinfo': 'name', 'type': 'text', 'class': 'accName inputbox', 'placeholder': 'Account Name', 'value': tmp.accName}) ) })
		.insert({'bottom': tmp.me._getAccountEditRow('Value: $', 
			new Element('input', {'accinfo': 'value', 'type': 'text', 'class': 'accValue inputbox', 'placeholder': 'Account Value', 'value': tmp.accValue}) ) })
		.insert({'bottom': tmp.me._getAccountEditRow('Budget: $', 
			new Element('input', {'accinfo': 'budget', 'type': 'text', 'class': 'accBudget inputbox', 'placeholder': 'Account Budget', 'value': tmp.accBudget}) ) })
		.insert({'bottom': tmp.me._getAccountEditRow('Comments: ', 
			new Element('input', {'accinfo': 'comments', 'type': 'text', 'class': 'accComments inputbox', 'placeholder': 'Comments', 'value': tmp.comments}) ) })
		.insert({'bottom': new Element('div', {'class': 'newRow'})
    		.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Save'}).observe('click', function(){
    			tmp.me.saveAccount(this, tmp.accId, tmp.parentId);
    		})})
	    	.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Cancel', 'style': 'float:right;'}).observe('click', function(){
	    		$(this).up('.newAccDiv').remove();
	    	})})
	    });
    	$$('.row[accountid=' + account.id + ']').first().insert({'bottom': tmp.newDiv});
    }
    
    ,_getAccountEditRow: function(label, typein) {
    	var tmp = {};
    	tmp.newRow = new Element('div', {'class': 'newRow'})
			.insert({'bottom': new Element('span', {'class': 'label'}).update(label)})
			.insert({'bottom': new Element('span', {'class': 'typein'}).update(typein)});
    	return tmp.newRow;
    }
    
    /**
     * saving account event
     */
    ,saveAccount: function(btn, accId, parentId) {
    	var tmp = {};
    	tmp.me = this;
    	tmp.row = $(btn).up('.newRow');
    	tmp.savingPanel = tmp.row.up('.newAccDiv');
    	tmp.savingInfo = new Element('div').update('saving ...');
    	
    	//collecting info
    	tmp.accInfo = {'accountId': accId, 'parentId': parentId};
    	tmp.hasError = false;
    	tmp.savingPanel.getElementsBySelector('.newAccError').each(function(item){item.remove();});
    	tmp.currency = /^\d*(\.|)\d{0,2}$/g;
    	tmp.savingPanel.getElementsBySelector('[accinfo]').each(function(item){
    		tmp.field = item.readAttribute('accinfo');
    		tmp.value = $F(item);
    		if (tmp.field === 'name' && tmp.value.blank())
    		{
    			item.insert({'after': new Element('span',{'class': 'newAccError'}).update('Name is needed!')});
    			tmp.hasError = true;
    		}
    		if (tmp.field === 'value' && !tmp.value.match(tmp.currency))
    		{
    			item.insert({'after': new Element('span',{'class': 'newAccError'}).update('Invalid value, expected: 0.00!')});
    			tmp.hasError = true;
    		}
    		if (tmp.field === 'budget' && !tmp.value.match(tmp.currency))
    		{
    			item.insert({'after': new Element('span',{'class': 'newAccError'}).update('Invalid budget, expected: 0.00!')});
    			tmp.hasError = true;
    		}
    		tmp.accInfo[tmp.field] = $F(item);
    	});
    	if(tmp.hasError === true) {
    		return;
    	}
    	
    	tmp.accounts = appJs.getPageData('accounts');
    	appJs.postAjax(this.callBackIds.saveAccount, tmp.accInfo, {
    		'onLoading': function(sender, param){
    			tmp.row.hide().insert({'after': tmp.savingInfo});
    		},
	    	'onComplete': function(sender, param){
	    		try{
	    			tmp.account = appJs.getResp(param, false, true);
	    			tmp.accounts[tmp.account.root.id][tmp.account.id] = tmp.account;
	    			appJs.setPageData('accounts', tmp.accounts);
	    			tmp.me._showAccList(tmp.accounts[tmp.account.root.id]);
    				alert('Saved successfully!');
    				//remove the saving panel
    				tmp.savingPanel.remove();
	    		} catch(e) {
	    			tmp.savingInfo.remove();
	    			tmp.row.show();
	    			alert(e);
	    		}
	    	}
    	});
    },
    /**
     * deleting an account
     */
    delAcc: function(accId, btn) {
    	var tmp = {};
    	tmp.me = this;
    	if(!confirm('Are you sure you want to delete this account?'))
    		return;
    	$(btn).up('.btnListDiv').remove();
    	
    	tmp.accounts = appJs.getPageData('accounts');
    	appJs.postAjax(this.callBackIds.deleteAccounts, {'accountId': accId}, {
	    	'onComplete': function(sender, param){
	    		try {
	    			tmp.account = appJs.getResp(param, false, true);
	    			delete tmp.accounts[tmp.account.root.id][tmp.account.id];
	    			appJs.setPageData('accounts', tmp.accounts);
	    			tmp.me._showAccList(tmp.accounts[tmp.account.root.id]);
	    		} catch(e) {
	    			alert(e);
	    		}
	    	}
    	});
    }
};