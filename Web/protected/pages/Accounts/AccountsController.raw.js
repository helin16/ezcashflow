//this is the source file for the AccountList page
var AccountsJs = new Class.create();
AccountsJs.prototype = {
	rootId: 1,
	divIds: {
		list: ''
	},
	callBackIds: {
		getAccounts: '',
		saveAccount: '',
		deleteAccounts: '',
		moveAccounts: ''
	},
	//constructor
	initialize: function (accountListId, getAccCallBackId, saveAccCallBackId, delAccCallBackId, moveAccCallBackId) {
		this.divIds.list = accountListId;
		this.callBackIds.getAccounts = getAccCallBackId;
		this.callBackIds.saveAccount = saveAccCallBackId;
		this.callBackIds.deleteAccounts = delAccCallBackId;
		this.callBackIds.moveAccounts = moveAccCallBackId;
	},
	/**
	 * click event for the table in the .box-title
	 */
	selectAccountType: function (btn) {
        var tmp = {};
        tmp.clickedBtn = $(btn);
        tmp.clickedBtn.up('ul').getElementsBySelector('li').each(function(item){
            item.down('a').removeAttribute('selected');
        });
        tmp.clickedBtn.writeAttribute('selected');
    },
    /**
     * selecting a root type
     */
    showAccounts: function(btn) {
    	var tmp = {};
    	this.selectAccountType(btn);
    	this.rootId = $(btn).readAttribute('rootId');
    	tmp.resultDivId = this.divIds.list;
    	appJs.postAjax(this.callBackIds.getAccounts, {'rootId': this.rootId}, {
    		'onLoading': function(sender, param){
    			$(tmp.resultDivId).update('<img src="/contents/images/loading.gif" />');
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		tmp.accCount = tmp.result.size();
	    		if(tmp.accCount === 0) {
	    			$(tmp.resultDivId).update('No Accounts Found!');
	    			return;
	    		}
	    		$(tmp.resultDivId).update('');
	    		for(tmp.i = 0; tmp.i < tmp.accCount; tmp.i++) {
	    			tmp.newRow = pageJs.formatAccount(tmp.result[tmp.i], tmp.i);
	    			$(tmp.resultDivId).insert({'bottom': tmp.newRow});
	    		}
	    	}
    	});
    	return false;
    },
    /**
     * getting the accounts
     */
    formatAccount: function(account, rowNo) {
    	var tmp = {};
    	tmp.accountId = account.id;
    	tmp.newRow = new Element('div', {'class': 'row ' + (rowNo % 2 === 0 ? 'even' : 'odd'), 'accountId': tmp.accountId, 'accountno': account.accountNumber});
    	tmp.leftMargin = 0;
    	if(account.level != 0) {
    		tmp.leftMargin = account.level * 4 ;
    		tmp.newRow.insert({'bottom': new Element('div', {'class': 'space rowDivd', 'style': 'width: ' + tmp.leftMargin + '%'}).update('&nbsp;')});
    	}
    	tmp.rowContent = new Element('div', {'class': 'content rowDivd', 'style': 'width: ' + (100 - tmp.leftMargin - 5) + '%'});
    	tmp.rowContent.insert({'bottom': new Element('div', {'class': 'accountname'}).update(account.name)});
    	tmp.rowContent.insert({'bottom': new Element('div', {'class': 'value'}).update(account.sum === 0 ? '' : '$' + account.sum)});
    	tmp.rowContent.insert({'bottom': new Element('div', {'class': 'accountno'}).update(account.accountNumber)});
    	tmp.rowContent.insert({'bottom': new Element('div', {'class': 'budget'}).update((account.budget === 0 || account.budget.blank()) ? '' : '$' + account.budget)});
    	tmp.rowContent.insert({'bottom': new Element('div', {'class': 'comments'}).update(account.comments)});
    	tmp.newRow.insert({'bottom': tmp.rowContent});
    	tmp.newBtnImage = new Element('img', {'src': '/contents/images/arrow-down.gif'}).observe('click', function(){pageJs.showBtnsDiv(account, this); });
    	tmp.newRow.insert({'bottom': new Element('div', {'class': 'btns rowDivd', 'style': 'width: 5%'}).update(tmp.newBtnImage)});
    	return tmp.newRow;
    },
    /**
     * getting the btns for each account in the list
     */
    showBtnsDiv: function(account, btn) {
    	var tmp = {};
    	tmp.newListDivId = 'btnListDiv_' + account.id;
    	tmp.newListDiv = $(tmp.newListDivId);
    	//remove all open btn divs
    	$$('.btnListDiv').each(function(item){item.remove();});
    	if(tmp.newListDiv !== undefined && tmp.newListDiv !== null) {
    		return;
    	}
    	tmp.newListDiv = new Element('div', {'class': 'btnListDiv', 'id': tmp.newListDivId});
    	tmp.newListDivList = new Element('ul');
    	
    	tmp.listCreateNewA = new Element('a', {'href': 'javascript:void(0);'}).update('New').observe('click', function(){pageJs.showAcc(account, this, true);})
    	tmp.newListDivList.insert({'bottom': new Element('li').update(tmp.listCreateNewA)});
    	
    	if(account.level !== 0) {
    		tmp.listCreateNewA1 = new Element('a', {'href': 'javascript:void(0);'}).update('Edit').observe('click', function(){pageJs.showAcc(account, this, false);})
    		tmp.newListDivList.insert({'bottom': new Element('li').update(tmp.listCreateNewA1)});
    		
//    		tmp.listCreateNewA2 = new Element('a', {'href': 'javascript:void(0);'}).update('Move').observe('click', function(){pageJs.showMoveAcc(account.id, this);})
//    		tmp.newListDivList.insert({'bottom': new Element('li').update(tmp.listCreateNewA2)});
    	}
    	
    	if(account.gotChildren !== true) {
    		tmp.listCreateNewA3 = new Element('a', {'href': 'javascript:void(0);'}).update('Delete').observe('click', function(){pageJs.delAcc(account.id, this);})
    		tmp.newListDivList.insert({'bottom': new Element('li').update(tmp.listCreateNewA3)});
    	}
    	tmp.newListDiv.update(tmp.newListDivList);
    	$(btn).insert({'after': tmp.newListDiv});
    	return tmp.newDiv;
    },
    /**
     * showing the account saving panel
     */
    showAcc: function(account, btn, isNew) {
    	var tmp = {};
    	$(btn).up('.btnListDiv').remove();
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
    		tmp.accId = account.id;
    		tmp.parentId = '';
    	}
    		
    	tmp.newDiv = new Element('div', {'id': tmp.newDivId, 'class': 'newAccDiv'});
    	tmp.newRowTitle = new Element('div', {'class': 'newRow'})
	    	.insert({'bottom': new Element('div', {'class': 'title'}).update(isNew === true ? 'Creating a new Account' : 'Updating selected account')});
    	tmp.newDiv.insert({'bottom': tmp.newRowTitle});
    	
    	tmp.newRow= new Element('div', {'class': 'newRow'})
    		.insert({'bottom': new Element('span', {'class': 'label'}).update('Name: ')})
    		.insert({'bottom': new Element('span', {'class': 'typein'}).update(new Element('input', {'accinfo': 'name', 'type': 'text', 'class': 'accName inputbox', 'placeholder': 'Account Name', 'value': tmp.accName}))});
    	tmp.newDiv.insert({'bottom': tmp.newRow});
    	
    	tmp.newRow1 = new Element('div', {'class': 'newRow'})
	    	.insert({'bottom': new Element('span', {'class': 'label'}).update('Value: $')})
	    	.insert({'bottom': new Element('span', {'class': 'typein'}).update(new Element('input', {'accinfo': 'value', 'type': 'text', 'class': 'accValue inputbox', 'placeholder': 'Account Value', 'value': tmp.accValue}))});
    	tmp.newDiv.insert({'bottom': tmp.newRow1});
    	
    	tmp.newRow2 = new Element('div', {'class': 'newRow'})
	    	.insert({'bottom': new Element('span', {'class': 'label'}).update('Budget: $')})
	    	.insert({'bottom': new Element('span', {'class': 'typein'}).update(new Element('input', {'accinfo': 'budget', 'type': 'text', 'class': 'accBudget inputbox', 'placeholder': 'Account Budget', 'value': tmp.accBudget}))});
    	tmp.newDiv.insert({'bottom': tmp.newRow2});
    	
    	tmp.newRow2 = new Element('div', {'class': 'newRow'})
	    	.insert({'bottom': new Element('span', {'class': 'label'}).update('Comments: ')})
	    	.insert({'bottom': new Element('span', {'class': 'typein'}).update(new Element('input', {'accinfo': 'comments', 'type': 'text', 'class': 'accComments inputbox', 'placeholder': 'Comments', 'value': tmp.comments}))
	    });
    	tmp.newDiv.insert({'bottom': tmp.newRow2});
    	
    	tmp.newRow3 = new Element('div', {'class': 'newRow'})
    		.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Save'}).observe('click', function(){
    			pageJs.saveAccount(this, tmp.accId, tmp.parentId)
    		})})
	    	.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Cancel', 'style': 'float:right;'}).observe('click', function(){
	    		$(tmp.newDivId).remove();
	    	})});
		tmp.newDiv.insert({'bottom': tmp.newRow3});
    	$$('.row[accountId=' + account.id + ']').first().insert({'bottom': tmp.newDiv});
    },
    /**
     * saving account event
     */
    saveAccount: function(btn, accId, parentId) {
    	var tmp = {};
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
    	appJs.postAjax(this.callBackIds.saveAccount, tmp.accInfo, {
    		'onLoading': function(sender, param){
    			tmp.row.hide().insert({'after': tmp.savingInfo});
    		},
	    	'onComplete': function(sender, param){
	    		try{
	    			tmp.acc = appJs.getResp(param);
	    			if(tmp.acc.accountNumber === undefined || tmp.acc.accountNumber.blank())
	    				throw 'System Error:Invalid account number!';
	    			
	    			//created a new account row
	    			if(accId !== tmp.acc.id) {
	    				tmp.accountNumber = tmp.acc.accountNumber - 1;
	    				tmp.lastRow = $$('.row[accountno=' + tmp.accountNumber + ']').last();
	    				if(tmp.lastRow === undefined || tmp.lastRow === null)
	    					tmp.lastRow = tmp.row.up('.row');
	    				tmp.lastRow.insert({'after': pageJs.formatAccount(tmp.acc)}).scrollTo();
	    			} else {
	    				$$('.row[accountid=' + tmp.acc.id + ']').first().replace( pageJs.formatAccount(tmp.acc)).scrollTo();
	    			}
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
    	if(!confirm('Are you sure you want to delete this account?'))
    		return;
    	$(btn).up('.btnListDiv').remove();
    	appJs.postAjax(this.callBackIds.deleteAccounts, {'accountId': accId}, {
	    	'onComplete': function(sender, param){
	    		try{
	    			tmp.acc = appJs.getResp(param);
	    			if(tmp.acc.id === undefined || tmp.acc.id.blank())
	    				throw 'System Error:Invalid account id!';
	    			
    				$$('.row[accountid=' + tmp.acc.id + ']').first().remove();
    				if(tmp.acc.parent.id !== undefined && !tmp.acc.parent.id.blank()) {
    					$$('.row[accountid=' + tmp.acc.parent.id + ']').first().replace(pageJs.formatAccount(tmp.acc.parent));
    				}
	    		} catch(e) {
	    			alert(e);
	    		}
	    	}
    	});
    }
};