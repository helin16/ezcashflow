//this is the source file for the FieldTaskListController
var TransPaneJs = new Class.create();
TransPaneJs.prototype = {
	accountIds: {'fromRootIds': [], 'toRootIds': []}, //this is what the user selected as the transaction type
	recentTrans: [], //the most recent transactions
	formDivId: '',
	callbackIds: {
		'getAccounts': '',
		'saveTrans': '',
		'delFileCBId': ''
	},
	fileUploader: null, //the file uploader
	
	//constructor
	initialize: function (formDivId, getAccountsCBId, saveTransCBId, delFileCBId, fileUploaderWrapperId) {
		this.formDivId = formDivId;
		this.callbackIds.getAccounts = getAccountsCBId;
		this.callbackIds.saveTrans = saveTransCBId;
		this.callbackIds.delFileCBId = delFileCBId;
		this.fileUploader = new FileUploaderJs(fileUploaderWrapperId);
	},
	
	//buildForm
	buildFrom: function(fromIds, toIds) {
		this.accountIds.fromRootIds = fromIds;
		this.accountIds.toRootIds = toIds;
		
		this.getAccList(this.accountIds.fromRootIds, $(this.formDivId).down("[transpane=fromAccounts]"));
		this.getAccList(this.accountIds.toRootIds, $(this.formDivId).down("[transpane=toAccounts]"));
		$(this.formDivId).down("[transpane=value]").setValue('');
		$(this.formDivId).down("[transpane=description]").setValue('');
		$(this.formDivId).down("[transpane=transDate]").setValue(new Date().SimpleFormat('yyyy-MM-dd'));
		$(this.formDivId).down("#chk_Expense").checked = false;
		this.fileUploader.reset();
    	return false;
	},
	
	//formating the account info for the dropdownlist
	getAccList: function(rootIds, listBox) {
		var tmp = {};
		if($(listBox).retrieve('chosen'))
			$(listBox).retrieve('chosen').destroy();
		$(listBox).update('');
		tmp.accounts = appJs.getPageData('accounts');
		$H(tmp.accounts).each(function(acc){
			tmp.rootId = acc.key * 1;
			if(rootIds.indexOf(tmp.rootId) >= 0) {
				tmp.optGroup = new Element('optgroup', {'label': tmp.accounts[tmp.rootId][tmp.rootId].name});
				tmp.orderedAccounts = Object.keys(acc.value).map(function(k) { return acc.value[k]; }).sortBy(function(value) {
					return value.breadCrumbs.name;
				});
				tmp.orderedAccounts.each(function(item){
					if(item.allowTrans === true)
						tmp.optGroup.insert({'bottom': new Element('option', {'value': item.id}).update(item.breadCrumbs.name.replace(tmp.rootName + ' / ', '') +  ' - $' + item.sum)});
				});
				$(listBox).insert({'bottom': tmp.optGroup});
			}
		});
		$(listBox).store('chosen', new Chosen($(listBox)));
	},
	
	//reformat the value in the value box
	stripValue: function(value) {
		return value.strip().replace(' ', '').replace('$', '').replace(',', '');
	},
	
	//saving the transaction
	saveTrans: function(btn, postJsFunc) {
		var tmp = {};
		tmp.me = this;
		tmp.form = $(btn).up("div.transDiv");
		tmp.fromAccListBox = tmp.form.down("[transpane=fromAccounts]");
		tmp.toAccListBox = tmp.form.down("[transpane=toAccounts]");
		tmp.valueBox = tmp.form.down("[transpane=value]");
		tmp.commentsBox = tmp.form.down("[transpane=description]");
		tmp.transDate = tmp.form.down("[transpane=transDate]");
		tmp.saveBtn = tmp.form.down("[transpane=saveBtn]");
		if(this.validForm(tmp.fromAccListBox, tmp.toAccListBox, tmp.valueBox, tmp.transDate) === false)
			return false;
		
		tmp.saveBtnValue =tmp.saveBtn.value;
		tmp.data = {'fromAccId': $F(tmp.fromAccListBox), 
				'toAccId': $F(tmp.toAccListBox), 
				'value': tmp.me.stripValue($F(tmp.valueBox)), 
				'comments': $F(tmp.commentsBox).strip(), 
				'assets': tmp.me.fileUploader.uploadedFiles,
				'transDate': $F(tmp.transDate)
		};
		appJs.postAjax(this.callbackIds.saveTrans, tmp.data, {
    		'onLoading': function(sender, param){
    			tmp.saveBtn.value = 'Saving ...';
    			tmp.saveBtn.disabled = true;
    		},
	    	'onComplete': function(sender, param){
	    		try {
	    			tmp.result = appJs.getResp(param, false, true);
	    			tmp.me.recentTrans = tmp.result.trans;
	    			tmp.me.refreshAccounts(tmp.result.accounts);
	    			tmp.me.buildFrom(tmp.me.accountIds.fromRootIds, tmp.me.accountIds.toRootIds);
	    			tmp.saveBtn.value = tmp.saveBtnValue;
	    			tmp.saveBtn.disabled = false;
	    			alert('Saved Successfully!');
	    			if(typeof(postJsFunc) === 'function')
	    				postJsFunc();
	    		} catch(e) {
	    			alert(e);
	    			tmp.saveBtn.value = tmp.saveBtnValue;
		    		tmp.saveBtn.disabled = false;
	    		}
	    	}
    	});
	}
	
	//refresh Accounts
	,refreshAccounts: function(returnAccounts) {
		var tmp = {};
		tmp.accounts = appJs.getPageData('accounts');
		$H(returnAccounts).each(function(account){
			$H(account.value).each(function(acc){
				tmp.accounts[account.key][acc.value.id] = acc.value;
			});
		});
		appJs.setPageData('accounts', tmp.accounts);
	}
	
	//validate Form
	,validForm: function (fromAccountBox, toAccountBox, valueBox, transDate) {
		var tmp = {};
		tmp.me = this;
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
		
		tmp.regex = /^(\d+)(\.\d{1,2})?$/;
		if(!tmp.me.stripValue($F(valueBox)).match(tmp.regex)) {
			$(valueBox).up(".inline").down('span.title').insert({"bottom": new Element("span", {'class': "errorMsg"}).update('Invalid' + tmp.me.stripValue($F(valueBox)))});
			tmp.succ = false;
		}
		
		tmp.regex = /^(\d){4}-(\d){2}-(\d){2}$/;
		if(!tmp.me.stripValue($F(transDate)).match(tmp.regex)) {
			$(transDate).up(".inline").down('span.title').insert({"bottom": new Element("span", {'class': "errorMsg"}).update('Invalid') });
			tmp.succ = false;
		}
		return tmp.succ;
	},
	
	//toggling the file upload list
	toggleFileList: function(btn){
		if($(btn).checked) {
			this.fileUploader.initFileUploader();
		} else {
			this.fileUploader.reset();
		}
		return true;
	}
};