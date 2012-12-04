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
    	tmp.newRow = new Element('div', {'class': 'row ' + (rowNo % 2 === 0 ? 'even' : 'odd')});
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
    	tmp.newRow.insert({'bottom': new Element('div', {'class': 'btns rowDivd', 'style': 'width: 5%'}).update('&nbsp;')});
    	return tmp.newRow;
    }
};