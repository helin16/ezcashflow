var ReportJs = new Class.create();
ReportJs.prototype = {
	resultPanelId: '',
	searchPanelId: '',
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
		del: '',
		output: ''
	},
	//constructor
	initialize: function(resultPanelId, searchPanelId, searchCallbackId, editCallbackId, delCallbackId, outputCallbackId){
		this.resultPanelId = resultPanelId;
		this.searchPanelId = searchPanelId;
		this.callbackIds.search = searchCallbackId;
		this.callbackIds.edit = editCallbackId;
		this.callbackIds.del = delCallbackId;
		this.callbackIds.output = outputCallbackId;
	}
	//getting the edit panel row
	,_getRowDiv: function(title, content) {
		return new Element('div', {'class': 'rowwrapper'})
			.insert({'bottom': new Element('span', {'class': 'label'}).update(title) })
			.insert({'bottom': new Element('span', {'class': 'input'}).update(content) });
	}
	//initialize the search panel
	,initSearchPane: function(transId, fromDate, toDate, fromAccIds, toAccIds) {
		var tmp = {};
		tmp.me = this;
		tmp.fromDate = (fromDate || '');
		tmp.toDate = (toDate || '');
		tmp.fromAccIds = (fromAccIds || []);
		tmp.toAccIds = (toAccIds || []);
		tmp.transId = (transId || '');
		$(tmp.me.searchPanelId).update(new Element('div', {'class': 'content-box searchPanel'})
			.insert({'bottom': new Element('h3', {'class': 'box-title'}).update('Search Criterias: ')
				.insert({'bottom': new Element('span', {'class': 'inlineblock hidesearchwrapper'})
					.insert({'bottom': new Element('input', {'type': 'checkbox', 'class': 'hidesearchbtn', 'checked': true}) 
						.observe('click', function () {
							$(this).up('.content-box').down('.box-content').toggle();
						})
					})
					.insert({'bottom': new Element('label').update('Show Search') })
				})
			})
			.insert({'bottom': new Element('div', {'class': 'box-content'})
				.insert({'bottom': new Element('div', {'class': 'row fullwidth'})
					.insert({'bottom': tmp.me._getRowDiv('From Date:', 
							new Element('input', {'class': 'searchdate rndcnr fullwidth', 'type': 'text', 'searchpane': 'date_start', 'value': tmp.fromDate, 'readyonly': true})
					).addClassName('halfcut') })
					.insert({'bottom': tmp.me._getRowDiv('To Date:', 
							new Element('input', {'class': 'searchdate rndcnr fullwidth', 'type': 'text', 'searchpane': 'date_end', 'value': tmp.toDate, 'readyonly': true})
					).addClassName('halfcut') })
				})
				.insert({'bottom': tmp.me._getRowDiv('From Account:', tmp.me._getAccList('Select some AccountEntries', tmp.fromAccIds).writeAttribute('searchpane', 'fromacc')).addClassName('row fullwidth') })
				.insert({'bottom': tmp.me._getRowDiv('To Account:', tmp.me._getAccList('Select some AccountEntries', tmp.toAccIds).writeAttribute('searchpane', 'toacc')).addClassName('row fullwidth') })
				.insert({'bottom': new Element('div', {'class': 'row'})
					.insert({'bottom': new Element('input', {'type': 'hidden', 'value': tmp.transId, 'searchpane': 'transId'}) })
					.insert({'bottom': new Element('input', {'type': 'button', 'value': 'Search', 'class': 'submitBtn'})
						.observe('click', function() {
							tmp.me.search();
						})
					})
					.insert({'bottom': new Element('input', {'type': 'button', 'value': 'Output to Excel', 'class': 'submitBtn'})
						.observe('click', function() {
							tmp.me.outputToExcel();
						})
					})
				})
			})
		);
		return this;
	}
	,getSearchPanel: function() {
		return $(this.searchPanelId);
	}
	//get the account list
	,_getAccList: function(placeholder, selectedValues) {
		var tmp = {};
		tmp.selectedValues = selectedValues || [];
		tmp.selectBox = new Element('select', {'class': 'chosen-select', 'multiple': true, 'data-placeholder': placeholder});
		tmp.accounts = appJs.getPageData('accounts');
		$H(tmp.accounts).each(function(account){
			tmp.optgroup = new Element('optgroup', {'label': tmp.accounts[account.key][account.key].name});
			$H(account.value).each(function(acc){
				if(account.key !== acc.value.id) {
					tmp.option = new Element('option', {'value': acc.key}).update(acc.value.breadCrumbs.name);
					if(tmp.selectedValues.indexOf(acc.key * 1) >= 0) {
						tmp.option.writeAttribute('selected', true);
					}
					tmp.optgroup.insert({'bottom': tmp.option });
				}
			});
			tmp.selectBox.insert({'bottom': tmp.optgroup });
		});
		return tmp.selectBox;
	}
	//initialising the date picker
	,initialDatePicker: function(selector) {
		var tmp = {};
		tmp.results = [];
		$$(selector).each(function(item){
			tmp.hourString = '00:00:00';
			tmp.searchPanelAttr = item.readAttribute('searchpane');
			if (tmp.searchPanelAttr !== undefined && tmp.searchPanelAttr !== null) {
				tmp.hourString = tmp.searchPanelAttr.strip().include('_start') ? '00:00:00' : '23:59:59';
			}
			tmp.results.push(new Prado.WebUI.TDatePicker({'ID': item,'InputMode':'TextBox','Format':'yyyy-MM-dd ' + tmp.hourString,'FirstDayOfWeek':1,'ClassName':'datePicker','CalendarStyle':'default','FromYear':2007,'UpToYear':2030}));
		});
		return tmp.results;
	}
	,initChosen: function(selector) {
		var tmp = {};
		tmp.results = [];
		$$(selector).each(function(item){
			tmp.results.push(new Chosen(item, {'over-flow': "auto"}));
		});
	    return tmp.results;
	}
	//click on the search btn
	,search: function() {
		var tmp = {};
		tmp.me = this;
		tmp.resultPanel = $(this.resultPanelId);
		tmp.searchPane = $(this.searchPanelId);
		
		//collects information
		tmp.searchCriterias = {};
		tmp.searchPane.getElementsBySelector('[searchpane]').each(function(item){
			tmp.field = item.readAttribute('searchpane').strip();
			tmp.value = $F(item);
			tmp.searchCriterias[tmp.field] = tmp.value;
		});
		
		tmp.me.searchCriteria.search = tmp.searchCriterias;
		tmp.me.searchCriteria.pagination.pageNo = 1;
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
	    			tmp.rowNo = (tmp.me.searchCriteria.pagination.pageNo - 1) * tmp.me.searchCriteria.pagination.pageSize + tmp.i + 1;
	    			tmp.newRow = tmp.me.getRow(tmp.result.trans[tmp.i], tmp.rowNo);
	    			tmp.resultPanel.down('.box-content').insert({'bottom': tmp.newRow});
	    		}
	    		
	    		//display more btn
	    		if(tmp.rowNo < tmp.result.total) {
	    			tmp.resultPanel.down('.box-content').insert({'bottom': tmp.me.getMoreBtn(tmp.rowNo, tmp.result.total)});
	    		}
	    	}
    	});
	},
	//click to output to excel
	outputToExcel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.searchPane = $(this.searchPanelId);
		
		//collects information
		tmp.searchCriterias = {};
		tmp.searchPane.getElementsBySelector('[searchpane]').each(function(item){
			tmp.field = item.readAttribute('searchpane').strip();
			tmp.value = $F(item);
			tmp.searchCriterias[tmp.field] = tmp.value;
		});
		
		tmp.me.searchCriteria.search = tmp.searchCriterias;
		appJs.postAjax(this.callbackIds.output, this.searchCriteria, {
			'onLoading': function(sender, param){},
			'onComplete': function(sender, param){
				tmp.result = appJs.getResp(param);
				if(!tmp.result || !tmp.result.trans) {
					alert('ERROR: NOthing come back');
					return;
				}
				
				tmp.data = [];
				tmp.data.push(['created time', 'from Account', 'to Account', 'amount', 'Comments'].join(', ') + '\n');
				tmp.result.trans.each(function(item) {
					tmp.row = item.created + ', ' + (!item.fromAcc.id ? '' : item.fromAcc.breadCrumbs.name) + ', ' + item.toAcc.breadCrumbs.name + ', ' + item.value + ', ' + item.comments + '\n';
					tmp.data.push(tmp.row);
				})
				
				tmp.now = new Date();
				tmp.fileName = 'transactions_' + tmp.now.getFullYear() + '_' + tmp.now.getMonth() + '_' + tmp.now.getDate() + '_' + tmp.now.getHours() + '_' + tmp.now.getMinutes() + '_' + tmp.now.getSeconds() + '.csv';
				tmp.blob = new Blob(tmp.data, {type: "text/csv;charset=utf-8"});
				saveAs(tmp.blob, tmp.fileName);
			}
		});
	},
	getMoreBtn: function(rowNo, total) {
		var tmp = {};
		if (rowNo >= total)
			return;
		tmp.me = this;
		tmp.newMoreBtn = new Element('input', {'type': 'button', 'class': 'showMoreBtn', 'value': 'Show More Transactions'})
			.observe('click', function(){
				tmp.me.getMoreTrans(this);
			});
		return tmp.newMoreBtn;
	},
	//get the transaction row dom object
	getRow: function(trans, rowNo) {
		var tmp = {};
		tmp.me = this;
		tmp.newRow = new Element('div', {'class': 'row ' + (rowNo % 2 === 0 ? 'even' : 'odd'), 'transId': trans.id, 'rowno': rowNo});
		tmp.newRowContent = new Element('span', {'class': 'conent'});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'fromacc'}).update('From: ' + (trans.fromAcc.name === undefined ? '' : trans.fromAcc.breadCrumbs.name))});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'created'}).update(trans.created)});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'toacc'}).update('To&nbsp;&nbsp;&nbsp;&nbsp;: ' + trans.toAcc.breadCrumbs.name)});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'value'}).update(appJs.getCurrency(trans.value))});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'comments'}).update(trans.comments)});
		tmp.assetsDiv = new Element('ul', {'class': 'assets'});
		trans.assets.each(function(item){
			tmp.assetsDivLi = new Element('li', {'class': 'assets'});
			tmp.assetsDivLi.update(new Element('a', {'class': 'assetlink', 'href': '/asset/' + item.assetKey, 'target': '_blank'}).update(item.filename));
			tmp.assetsDiv.insert({'bottom':tmp.assetsDivLi});
		});
		tmp.newRowContent.insert({'bottom': tmp.assetsDiv});
		tmp.newRow.insert({'bottom': tmp.newRowContent});
		
		tmp.newRowBtns = new Element('span', {'class': 'btns'});
		tmp.newRowBtns.insert({'bottom': new Element('a', {'class': 'btn', 'href': 'javascript: void(0);'})
			.update('Edit')
			.observe('click', function(){
				tmp.me.showEditTrans(this, trans);
			})
		});
		tmp.newRowBtns.insert({'bottom': new Element('a', {'class': 'btn', 'href': 'javascript: void(0);'})
			.update('Delete')
			.observe('click', function(){
				tmp.me.delTrans(trans.id);
			})
		});
		tmp.newRow.insert({'bottom': tmp.newRowBtns});
		
		return tmp.newRow;
	},
	//event to get more transactions
	getMoreTrans: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.orgianlBtnValue = $(btn).value;
		tmp.resultPanel = $(this.resultPanelId);
		tmp.me.searchCriteria.pagination.pageNo += 1;
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
		    			tmp.rowNo = (tmp.me.searchCriteria.pagination.pageNo - 1) * tmp.me.searchCriteria.pagination.pageSize + tmp.i + 1;
		    			tmp.newRow = tmp.me.getRow(tmp.result.trans[tmp.i], tmp.rowNo);
		    			tmp.resultPanel.down('.box-content').insert({'bottom': tmp.newRow});
		    		}
		    		//display more btn
		    		if(tmp.rowNo < tmp.result.total) {
		    			tmp.resultPanel.down('.box-content').insert({'bottom': tmp.me.getMoreBtn(tmp.rowNo, tmp.result.total)});
		    		}
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
					});
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
		tmp.me = this;
		tmp.row = $(btn).up('.editrow');
    	tmp.savingPanel = tmp.row.up('.editDiv');
    	
    	//removing the errors
    	tmp.savingPanel.getElementsBySelector('.newAccError').each(function(item){item.remove();});
    	
		//collecting info
    	tmp.transInfo = {'transId': transId};
    	tmp.hasError = false;
    	tmp.savingPanel.getElementsBySelector('[transinfo]').each(function(item){
    		tmp.field = item.readAttribute('transinfo').strip();
    		switch(tmp.field) {
	    		case 'fromacc': 
	    		case 'comments': {
	    			tmp.transInfo[tmp.field] = $F(item);
	    			break;
	    		}
	    		case 'date': 
    			case 'toacc': {
    				tmp.value = $F(item);
    				if(tmp.value.blank()) {
    					item.up('.rowwrapper').down('.label').insert({'bottom': new Element('span',{'class': 'newAccError'}).update( tmp.field + ' is needed!')});
    					tmp.hasError = true;
    				}
    				tmp.transInfo[tmp.field] = tmp.value;
    				break;
    			}
    			case 'value': {
    				tmp.value = $F(item);
    				if(!tmp.value.match(/^\d*(\.|)\d{0,2}$/g)) {
    					item.up('.rowwrapper').down('.label').insert({'bottom': new Element('span',{'class': 'newAccError'}).update('Invalid value, expected: 0.00!')});
    					tmp.hasError = true;
    				}
    				tmp.transInfo[tmp.field] = tmp.value;
    				break;
    			}
    			case 'assets': {
    				tmp.assets = {};
    				item.getElementsBySelector('.uploadedfile').each(function(fileItem) {
    					tmp.assets[fileItem.readAttribute('assetkey')] = (fileItem.readAttribute('delete') ? false : true);
    				});
    				tmp.transInfo[tmp.field] = tmp.assets;
    				break;
    			}
    			case 'attachments': {
    				tmp.fileHandler = $(item).retrieve('fileHandler');
    				tmp.transInfo[tmp.field] = tmp.fileHandler.uploadedFiles;
    				break;
    			}
    		}
    	});
    	if(tmp.hasError === true) {
    		return;
    	}
    	//start saving to the server
    	tmp.savingInfo = new Element('div').update('saving ...');
    	appJs.postAjax(this.callbackIds.edit, tmp.transInfo, {
    		'onLoading': function(sender, param){
    			tmp.row.hide().insert({'after': tmp.savingInfo});
    		},
	    	'onComplete': function(sender, param){
	    		try{
	    			tmp.trans = appJs.getResp(param, false, true);
	    			if(tmp.trans.id === undefined || tmp.trans.id.blank())
	    				throw 'System Error:Invalid trans id!';
	    			
	    			tmp.transRow = $(btn).up('.row');
	    			tmp.transRow.replace( tmp.me.getRow(tmp.trans, tmp.transRow.readAttribute('rowno'))).scrollTo();
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
		tmp.me = this;
		//removing all opened editing panel
		$$('.editDiv').each(function(item){ item.remove();});
		
		tmp.accSelect = $$('select[searchpane="fromacc"]').first();
		tmp.fileUploaderWrapperId = 'attachments_' + trans.id;
		//creating a new div for the transaction
		tmp.transRow = $(btn).up('.row');
		tmp.transRow.insert({'bottom': new Element('div', {'id': 'edittrans_' + trans.id, 'class': 'editDiv'})
	    	//gett the from account
	    	.insert({'bottom': tmp.me._getRowDiv('From: ', tmp.me._getAccSelectBox(tmp.accSelect.innerHTML, trans.fromAcc.id).writeAttribute('transinfo', 'fromacc').addClassName('editrow') ) })
	    	//gett the to account
			.insert({'bottom': tmp.me._getRowDiv('To: ', tmp.me._getAccSelectBox(tmp.accSelect.innerHTML, trans.toAcc.id).writeAttribute('transinfo', 'toacc').addClassName('editrow') ) })
			.insert({'bottom': new Element('div').addClassName('editrow')
				//getting the date row
				.insert({'bottom': new Element('span', {'class': 'date inlineblock'}).update(
					tmp.me._getRowDiv('Date: ', new Element('input', {'transinfo': 'date', 'type': 'text', 'class': 'transdate inputbox fullwidth rndcnr', 'placeholder': 'Created Date', 'value': trans.created}))
				) })
				//getting the value row
				.insert({'bottom': new Element('span', {'class': 'amount inlineblock'}).update(
						tmp.me._getRowDiv('Value: $', new Element('input', {'transinfo': 'value', 'type': 'text', 'class': 'transvalue inputbox fullwidth rndcnr', 'placeholder': 'Value', 'value': trans.value}) )
				) })
				//getting the comments row
				.insert({'bottom': new Element('span', {'class': 'descr inlineblock'}).update(
						tmp.me._getRowDiv('Comments: ', new Element('input', {'transinfo': 'comments', 'type': 'text', 'class': 'transcomments inputbox fullwidth rndcnr', 'placeholder': 'Comments', 'value': trans.comments}) )
				) })
			})
	    	//getting the attachment row
	    	.insert({'bottom': tmp.me._getRowDiv('Attachments: ', new Element('div').addClassName('editrow')
		    	.insert({'bottom': tmp.me._getAssetListDiv(trans.assets) }) 
	    		.insert({'bottom': new Element('span', {'transinfo': 'attachments', 'id': tmp.fileUploaderWrapperId}) }) 
	    	) })
	    	//getting the button row
	    	.insert({'bottom': new Element('div').addClassName('editrow')
	    		.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Save'}).observe('click', function(){
	    			tmp.me.saveTrans(this, trans.id);
	    		})})
		    	.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Cancel', 'style': 'float:right;'}).observe('click', function(){
		    		$(btn).up('.row').down('.editDiv').remove();
		    	})})
	    	})
		});
		tmp.me.initialDatePicker('input[transinfo=date]');
		tmp.me.initChosen('[transinfo=fromacc]');
		tmp.me.initChosen('[transinfo=toacc]');
		tmp.fileHandler = new FileUploaderJs(tmp.fileUploaderWrapperId).initFileUploader();
		tmp.transRow.down('#' + tmp.fileUploaderWrapperId).store('fileHandler', tmp.fileHandler);
		tmp.transRow.store('trans', trans);
		
	}
	//getting the asset list
	,_getAssetListDiv: function(assets) {
		var tmp = {};
		tmp.div = new Element('div', {'class': 'assets uploadedFileList', 'transinfo': 'assets'});
		assets.each(function(item) {
			tmp.div.insert({'bottom': new Element('div', {'class': 'uploadedfile', 'assetkey': item.assetKey})
				.update(item.filename)
				.insert({'bottom': new Element('span', {'class': 'delFile'}).update('x')
					.observe('click', function() {
						if(!confirm('Are you sure you want to delete this asset?'))
							return;
						$(this).up('.uploadedfile').hide().writeAttribute('delete', true);
					})
				})
			});
		});
		return tmp.div;
	}
	//getting the account select box
	,_getAccSelectBox: function($options, selectedValue) {
		var tmp = {};
		tmp.selectCloneFrom = new Element('select', {'class': 'transfrom inputbox'})
			.update($options)
			.insert({'top': new Element('option', {'value': ''}).update('Please select ...')});
		if(selectedValue !== undefined && !selectedValue.blank()) {
			tmp.selectCloneFrom.down('[value=' + selectedValue + ']').writeAttribute('selected', true);
		} else {
			tmp.selectCloneFrom.selectedIndex = 0;
		}
		return tmp.selectCloneFrom;
	}
};