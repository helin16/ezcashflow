//this is the source file for the FieldTaskListController
var TransPaneJs = new Class.create();
TransPaneJs.prototype = {
	accountIds: {'from': [], 'to': []},
	formDivId: '',
	callbackIds: {
		'getAccounts': '',
		'saveTrans': ''
	},
	
	//constructor
	initialize: function (formDivId, getAccountsCBId, saveTransCBId) {
		this.formDivId = formDivId;
		this.callbackIds.getAccounts = getAccountsCBId;
		this.callbackIds.saveTrans = saveTransCBId;
	},
	
	//buildForm
	buildFrom: function(fromIds, toIds) {
		var tmp = {};
		this.accountIds.from = fromIds;
		this.accountIds.to = toIds;
		tmp.formDivId = this.formDivId;
		tmp.fromAccListBox = $(tmp.formDivId).down("[transpane=fromAccounts]");
		tmp.toAccListBox = $(tmp.formDivId).down("[transpane=toAccounts]");
		tmp.valueBox = $(tmp.formDivId).down("[transpane=value]");
		tmp.commentsBox = $(tmp.formDivId).down("[transpane=description]");
		tmp.saveBtn = $(tmp.formDivId).down("[transpane=saveBtn]");
		tmp.saveBtnValue = tmp.saveBtn.value;
		appJs.postAjax(this.callbackIds.getAccounts, {'fromIds': fromIds, 'toIds': toIds}, {
    		'onLoading': function(sender, param){
    			tmp.fromAccListBox.update(new Element('option').update('Loading ...'));
    			tmp.toAccListBox.update(new Element('option').update('Loading ...'));
    			tmp.commentsBox.value = tmp.valueBox.value = '';
    			tmp.saveBtn.value = 'Loading ...';
    			tmp.saveBtn.disabled = true;
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		transJs.getAccList(tmp.result.from, tmp.fromAccListBox);
	    		transJs.getAccList(tmp.result.to, tmp.toAccListBox);
	    		tmp.saveBtn.value = tmp.saveBtnValue;
	    		tmp.saveBtn.disabled = false;
	    	}
    	});
    	return false;
	},
	
	//formating the account info for the dropdownlist
	getAccList: function(accounts, listBox)
	{
		var tmp = {};
		$(listBox).update('');
		$H(accounts).each(function(acc){
			tmp.rootName = acc.key;
			tmp.optGroup = new Element('optgroup', {'label': tmp.rootName});
			$H(acc.value).each(function(item){
				tmp.optGroup.insert({'bottom': new Element('option', {'value': item.value.id}).update(item.value.breadCrumbs.name.replace(tmp.rootName + ' / ', '') +  ' - $' + item.value.sum)});
			});
			$(listBox).insert({'bottom': tmp.optGroup});
		});
	},
	
	//saving the transaction
	saveTrans: function(btn, postJs) {
		var tmp = {};
		tmp.form = $(btn).up("div.transDiv");
		tmp.fromAccListBox = tmp.form.down("[transpane=fromAccounts]");
		tmp.toAccListBox = tmp.form.down("[transpane=toAccounts]");
		tmp.valueBox = tmp.form.down("[transpane=value]");
		tmp.commentsBox = tmp.form.down("[transpane=description]");
		tmp.saveBtn = tmp.form.down("[transpane=saveBtn]");
		if(this.validForm(tmp.fromAccListBox, tmp.toAccListBox, tmp.valueBox) === false)
			return false;
		
		tmp.saveBtnValue =tmp.saveBtn.value;
		tmp.data = {'fromAccId': $F(tmp.fromAccListBox), 'toAccId': $F(tmp.toAccListBox), 'value': $F(tmp.valueBox).strip(), 'comments': $F(tmp.commentsBox).strip(), 'fromIds': this.accountIds.from, 'toIds': this.accountIds.to};
		appJs.postAjax(this.callbackIds.saveTrans, tmp.data, {
    		'onLoading': function(sender, param){
    			tmp.saveBtn.value = 'Saving ...';
    			tmp.saveBtn.disabled = true;
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		transJs.getAccList(tmp.result.from, tmp.fromAccListBox);
	    		transJs.getAccList(tmp.result.to, tmp.toAccListBox);
	    		tmp.commentsBox.value = tmp.valueBox.value = '';
	    		tmp.saveBtn.value = tmp.saveBtnValue;
	    		tmp.saveBtn.disabled = false;
	    		alert('Saved Successfully!');
	    		if(postJs !== undefined)
	    			eval(postJs);
	    	}
    	});
	},
	
	//validate Form
	validForm: function (fromAccountBox, toAccountBox, valueBox) {
		var tmp = {};
		tmp.succ = true;
		$(fromAccountBox).up("div.transDiv").getElementsBySelector('.errorMsg').each(function(item){
			item.remove();
		});
		if($F(fromAccountBox).blank() || $F(fromAccountBox) <= 0) {
			$(fromAccountBox).up("div.row").down('span.title').insert({"bottom": new Element("span", {'class': "errorMsg"}).update('Invalid From')});
			tmp.succ = false;
		}
		
		if($F(toAccountBox).blank() || $F(toAccountBox) <= 0) {
			$(toAccountBox).up("div.row").down('span.title').insert({"bottom": new Element("span", {'class': "errorMsg"}).update('Invalid To')});
			tmp.succ = false;
		}
		
		tmp.regex = /^(\d{1,3}(\,\d{3})*|(\d+))(\.\d{1,2})?$/;
		if(!$F(valueBox).strip().match(tmp.regex)) {
			$(valueBox).insert({"after": new Element("span", {'class': "errorMsg"}).update('Invalid Value')});
			tmp.succ = false;
		}
		return tmp.succ;
	}
};