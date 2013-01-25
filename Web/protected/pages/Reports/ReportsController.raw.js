var ReportJs = new Class.create();
ReportJs.prototype = {
	resultPanelId: '',
	searchCriteria:{
		search:{},
		pagination: {
			pageNo: 1,
			pageSize: 30
		}
	},
	callbackIds: {
		search: '',
		edit: '',
		del: ''
	},
	//constructor
	initialize: function(resultPanelId, searchCallbackId, editCallbackId, delCallbackId){
		this.resultPanelId = resultPanelId;
		this.callbackIds.search = searchCallbackId;
		this.callbackIds.edit = editCallbackId;
		this.callbackIds.del = delCallbackId;
		
	},
	//initialising the date picker
	initialDatePicker: function(selector) {
		var tmp = {};
		$$(selector).each(function(item){
			tmp.hourString = '00:00:00';
			tmp.searchPanelAttr = item.readAttribute('searchpane');
			if (tmp.searchPanelAttr !== undefined && tmp.searchPanelAttr !== null) {
				tmp.hourString = tmp.searchPanelAttr.strip().include('_start') ? '00:00:00' : '23:59:59';
			}
			new Prado.WebUI.TDatePicker({'ID': item,'InputMode':'TextBox','Format':'yyyy-MM-dd ' + tmp.hourString,'FirstDayOfWeek':1,'ClassName':'datePicker','CalendarStyle':'default','FromYear':2007,'UpToYear':2030});
		});
	},
	//click on the search btn
	search: function(btn) {
		var tmp = {};
		tmp.resultPanel = $(this.resultPanelId);
		tmp.searchPane = $(btn).up('.searchPanel');
		//collects information
		tmp.searchCriterias = {};
		tmp.searchPane.getElementsBySelector('[searchpane]').each(function(item){
			tmp.field = item.readAttribute('searchpane').strip();
			tmp.value = $F(item);
			tmp.searchCriterias[tmp.field] = tmp.value;
		});
		
		this.searchCriteria.search = tmp.searchCriterias;
		this.searchCriteria.pagination.pageNo = 1;
		appJs.postAjax(this.callbackIds.search, this.searchCriteria, {
    		'onLoading': function(sender, param){
    			tmp.resultPanel.show().down('.box-content').update('<img src="/contents/images/loading.gif" />');
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		tmp.transCount = tmp.result.trans.size();
	    		if(tmp.accCount === 0) {
	    			tmp.resultPanel.down('.box-content').update('No Transactions Found!');
	    			return;
	    		}
	    		//update the total count
	    		tmp.resultPanel.getElementsBySelector('.noOfTrans').each(function(item){
	    			item.update(tmp.result.total);
	    		});
	    		
	    		tmp.resultPanel.down('.box-content').update('');
	    		//display the result rows
	    		for(tmp.i = 0; tmp.i < tmp.transCount; tmp.i++) {
	    			tmp.rowNo = (pageJs.searchCriteria.pagination.pageNo - 1) * pageJs.searchCriteria.pagination.pageSize + tmp.i + 1;
	    			tmp.newRow = pageJs.getRow(tmp.result.trans[tmp.i], tmp.rowNo);
	    			tmp.resultPanel.down('.box-content').insert({'bottom': tmp.newRow});
	    		}
	    		
	    		//display more btn
	    		tmp.resultPanel.down('.box-content').insert({'bottom': pageJs.getMoreBtn(tmp.rowNo, tmp.result.total)});
	    	}
    	});
	},
	getMoreBtn: function(rowNo, total) {
		var tmp = {};
		if (rowNo >= total)
			return;
		tmp.newMoreBtn = new Element('input', {'type': 'button', 'class': 'showMoreBtn', 'value': 'Show More Transactions'})
			.observe('click', function(){
				pageJs.getMoreTrans(this);
			});
		return tmp.newMoreBtn;
	},
	//get the transaction row dom object
	getRow: function(trans, rowNo) {
		var tmp = {};
		tmp.newRow = new Element('div', {'class': 'row ' + (rowNo % 2 === 0 ? 'even' : 'odd'), 'transId': trans.id, 'rowno': rowNo});
		tmp.newRowContent = new Element('span', {'class': 'conent'});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'fromacc'}).update('From: ' + (trans.fromAcc.name === undefined ? '' : trans.fromAcc.breadCrumbs.name))});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'created'}).update(trans.created)});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'toacc'}).update('To&nbsp;&nbsp;&nbsp;&nbsp;: ' + trans.toAcc.breadCrumbs.name)});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'value'}).update(appJs.getCurrency(trans.value))});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'comments'}).update(trans.comments)});
		tmp.assetsDiv = new Element('ul', {'class': 'assets'});
		trans.assets.each(function(item){
			tmp.assetsDivLi = new Element('li', {'class': 'assets'})
			tmp.assetsDivLi.update(new Element('a', {'class': 'assetlink', 'href': '/asset/' + item.assetKey, 'target': '_blank'}).update(item.filename));
			tmp.assetsDiv.insert({'bottom':tmp.assetsDivLi});
		});
		tmp.newRowContent.insert({'bottom': tmp.assetsDiv});
		tmp.newRow.insert({'bottom': tmp.newRowContent});
		
		tmp.newRowBtns = new Element('span', {'class': 'btns'});
		tmp.newRowBtns.insert({'bottom': new Element('a', {'class': 'btn', 'href': 'javascript: void(0);'})
			.update('Edit')
			.observe('click', function(){
				pageJs.showEditTrans(this, trans);
			})
		});
		tmp.newRowBtns.insert({'bottom': new Element('a', {'class': 'btn', 'href': 'javascript: void(0);'})
			.update('Delete')
			.observe('click', function(){
				pageJs.delTrans(trans.id);
			})
		});
		tmp.newRow.insert({'bottom': tmp.newRowBtns});
		
		return tmp.newRow;
	},
	//event to get more transactions
	getMoreTrans: function(btn) {
		var tmp = {};
		tmp.orgianlBtnValue = $(btn).value;
		tmp.resultPanel = $(this.resultPanelId);
		pageJs.searchCriteria.pagination.pageNo += 1;
		appJs.postAjax(this.callbackIds.search, this.searchCriteria, {
    		'onLoading': function(sender, param){
    			$(btn).writeAttribute('value', 'Getting more Transactions ...').disabled = true;
    		},
	    	'onComplete': function(sender, param){
	    		try {
		    		tmp.result = appJs.getResp(param);
		    		tmp.transCount = tmp.result.trans.size();
		    		$(btn).remove();
		    		//display the result rows
		    		for(tmp.i = 0; tmp.i < tmp.transCount; tmp.i++) {
		    			tmp.rowNo = (pageJs.searchCriteria.pagination.pageNo - 1) * pageJs.searchCriteria.pagination.pageSize + tmp.i + 1;
		    			tmp.newRow = pageJs.getRow(tmp.result.trans[tmp.i], tmp.rowNo);
		    			tmp.resultPanel.down('.box-content').insert({'bottom': tmp.newRow});
		    		}
		    		//display more btn
		    		tmp.resultPanel.down('.box-content').insert({'bottom': pageJs.getMoreBtn(tmp.rowNo, tmp.result.total)});
	    		} catch(e) {
	    			alert(e);
	    			if($(btn) !== undefined && $(btn) !== null) {
	    				$(btn).writeAttribute('value', tmp.orgianlBtnValue).disabled = false;
	    			}
	    		}
	    	}
    	});
	},
	//event to delete the transaction
	delTrans: function(transId) {
		var tmp = {};
		if(!confirm('Are you sure you want to delete this transaction?')) {
			return;
		}
		appJs.postAjax(this.callbackIds.del, {'transId': transId}, {
			'onComplete': function(sender, param){
				try {
					tmp.result = appJs.getResp(param);
					if (tmp.result.id === undefined || tmp.result.id === null || tmp.result.id.blank())
						throw 'System Error: trans.id not provided!';
					
					//remove that transaction
					$$('.row[transid=' + tmp.result.id + ']').each(function(item){
						item.remove();
					})
				} catch(e) {
					alert(e);
					if($(btn) !== undefined && $(btn) !== null) {
						$(btn).writeAttribute('value', tmp.orgianlBtnValue).disabled = false;
					}
				}
			}
		});
	},
	//event to save the transaction
	saveTrans: function(btn, transId) {
		var tmp = {};
		tmp.row = $(btn).up('.newRow');
    	tmp.savingPanel = tmp.row.up('.newAccDiv');
    	tmp.savingInfo = new Element('div').update('saving ...');
    	
		//collecting info
    	tmp.transInfo = {'transId': transId};
    	tmp.hasError = false;
    	tmp.savingPanel.getElementsBySelector('.newAccError').each(function(item){item.remove();});
    	tmp.currency = /^\d*(\.|)\d{0,2}$/g;
    	tmp.savingPanel.getElementsBySelector('[transinfo]').each(function(item){
    		tmp.field = item.readAttribute('transinfo');
    		tmp.value = $F(item);
    		if (tmp.field === 'date' && tmp.value.blank())
    		{
    			item.insert({'after': new Element('span',{'class': 'newAccError'}).update('Date is needed!')});
    			tmp.hasError = true;
    		}
    		if (tmp.field === 'toacc' && tmp.value.blank())
    		{
    			item.insert({'after': new Element('span',{'class': 'newAccError'}).update('To Account is needed!')});
    			tmp.hasError = true;
    		}
    		if (tmp.field === 'value' && !tmp.value.match(tmp.currency))
    		{
    			item.insert({'after': new Element('span',{'class': 'newAccError'}).update('Invalid value, expected: 0.00!')});
    			tmp.hasError = true;
    		}
    		tmp.transInfo[tmp.field] = tmp.value;
    	});
    	if(tmp.hasError === true) {
    		return;
    	}
    	appJs.postAjax(this.callbackIds.edit, tmp.transInfo, {
    		'onLoading': function(sender, param){
    			tmp.row.hide().insert({'after': tmp.savingInfo});
    		},
	    	'onComplete': function(sender, param){
	    		try{
	    			tmp.trans = appJs.getResp(param);
	    			if(tmp.trans.id === undefined || tmp.trans.id.blank())
	    				throw 'System Error:Invalid trans id!';
	    			
	    			tmp.transRow = $(btn).up('.row');
	    			tmp.transRow.replace( pageJs.getRow(tmp.trans, tmp.transRow.readAttribute('rowno'))).scrollTo();
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
	 * show the edit panel for a transaction
	 */
	showEditTrans: function(btn, trans) {
		var tmp = {};
		//removing all opened editing panel
		$$('.newAccDiv').each(function(item){ item.remove();});
		
		tmp.newDivId = 'edittrans_' + trans.id
		tmp.newDiv = new Element('div', {'id': tmp.newDivId, 'class': 'newAccDiv'});
    	tmp.newRow= new Element('div', {'class': 'newRow'})
	    	.insert({'bottom': new Element('span', {'class': 'label'}).update('Date: ')})
	    	.insert({'bottom': new Element('span', {'class': 'typein'}).update(new Element('input', {'transinfo': 'date', 'type': 'text', 'class': 'transdate inputbox', 'placeholder': 'Created Date', 'value': trans.created}))});
    	tmp.newDiv.insert({'bottom': tmp.newRow});
    	
    	tmp.accSelect = $$('select[searchpane="fromacc"]').first();
    	tmp.selectCloneFrom = new Element('select', {'transinfo': 'fromacc', 'class': 'transfrom inputbox'}).update(tmp.accSelect.innerHTML);
    	tmp.selectCloneFrom.insert({'top': new Element('option', {'value': ''}).update('Please select ...')});
    	if(trans.fromAcc.id !== undefined && !trans.fromAcc.id.blank()) {
    		tmp.selectCloneFrom.down('[value=' + trans.fromAcc.id + ']').writeAttribute('selected', 'true');
    	} else {
    		tmp.selectCloneFrom.selectedIndex = 0;
    	}
    	
    	tmp.newRow1= new Element('div', {'class': 'newRow'})
    		.insert({'bottom': new Element('span', {'class': 'label'}).update('From: ')})
    		.insert({'bottom': new Element('span', {'class': 'typein'}).update(tmp.selectCloneFrom)});
    	tmp.newDiv.insert({'bottom': tmp.newRow1});
    	
    	tmp.selectCloneTo = new Element('select', {'transinfo': 'toacc', 'class': 'transfrom inputbox'}).update(tmp.accSelect.innerHTML);
    	tmp.selectCloneTo.down('[value=' + trans.toAcc.id + ']').writeAttribute('selected', 'true');
    	tmp.newRow2 = new Element('div', {'class': 'newRow'})
	    	.insert({'bottom': new Element('span', {'class': 'label'}).update('To:')})
	    	.insert({'bottom': new Element('span', {'class': 'typein'}).update(tmp.selectCloneTo)});
    	tmp.newDiv.insert({'bottom': tmp.newRow2});
    	
    	tmp.newRow3 = new Element('div', {'class': 'newRow'})
	    	.insert({'bottom': new Element('span', {'class': 'label'}).update('Value: $')})
	    	.insert({'bottom': new Element('span', {'class': 'typein'}).update(new Element('input', {'transinfo': 'value', 'type': 'text', 'class': 'transvalue inputbox', 'placeholder': 'Value', 'value': trans.value}))});
    	tmp.newDiv.insert({'bottom': tmp.newRow3});
    	
    	tmp.newRow4 = new Element('div', {'class': 'newRow'})
	    	.insert({'bottom': new Element('span', {'class': 'label'}).update('Comments: ')})
	    	.insert({'bottom': new Element('span', {'class': 'typein'}).update(new Element('input', {'transinfo': 'comments', 'type': 'text', 'class': 'transcomments inputbox', 'placeholder': 'Comments', 'value': trans.comments}))
	    });
    	tmp.newDiv.insert({'bottom': tmp.newRow4});
    	
    	tmp.newRow5 = new Element('div', {'class': 'newRow'})
    		.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Save'}).observe('click', function(){
    			pageJs.saveTrans(this, trans.id)
    		})})
	    	.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Cancel', 'style': 'float:right;'}).observe('click', function(){
	    		$(tmp.newDivId).remove();
	    	})});
		tmp.newDiv.insert({'bottom': tmp.newRow5});
		$(btn).up('.row').insert({'bottom': tmp.newDiv});
		pageJs.initialDatePicker('input[transinfo=date]');
	}
}