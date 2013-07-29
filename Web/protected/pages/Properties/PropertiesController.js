//this is the source file for the AccountList page
var PropertiesJs = new Class.create();
PropertiesJs.prototype = {
	accEntries: null, //the account entries
	states: null, //the states
	pagination:
	{
		pageSize: 30,
		pageNumber: 1
	},
	divIds: {
		list : ''
	},
	callBackIds: {
		'get': '',
		'save': '',
		'del': '',
		'getAcc': '',
	},
	//constructor
	initialize: function (listDivId, getCallBackId, saveCallBackId, delCallBackId, getAccBtn) {
		this.divIds.list = listDivId;
		this.callBackIds.get = getCallBackId;
		this.callBackIds.save = saveCallBackId;
		this.callBackIds.del = delCallBackId;
		this.callBackIds.getAcc = getAccBtn;
	},
	//getting all properties
	loadProperties: function() {
		var tmp = {};
		tmp.me = this;
		appJs.postAjax(this.callBackIds.get, {'pagination': tmp.me.pagination}, {
    		'onLoading': function(sender, param){
    			$(tmp.me.divIds.list).update('<img src="/contents/images/loading.gif" />');
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		tmp.count = tmp.result.properties.size();
	    		if(tmp.accCount === 0) {
	    			$(tmp.me.divIds.list).update('No Property Found!');
	    			return;
	    		}
	    		
	    		//update the total count
	    		$(tmp.me.divIds.list).up('.content-box').getElementsBySelector('.noOfItems').each(function(item){
	    			item.update(tmp.result.total);
	    		});
	    		
	    		tmp.headerRow = new Element('div', {'class': "property row header"}).update(tmp.me._getValueRow('', 'Bought', 'Setup', 'Income', '(%)', 'Expense', 'Profit'));
	    		$(tmp.me.divIds.list).update(tmp.headerRow);
	    		for(tmp.i = 0; tmp.i < tmp.count; tmp.i++) {
	    			tmp.rowNo = (tmp.me.pagination.pageNumber - 1) * tmp.me.pagination.pageSize + tmp.i + 1;
	    			$(tmp.me.divIds.list).insert({'bottom': tmp.me._getPropertyRow(tmp.result.properties[tmp.i]).addClassName(tmp.rowNo % 2 === 0 ? 'odd' : 'even') });
	    		}
	    		
	    		//display more btn
	    		$(tmp.me.divIds.list).insert({'bottom': tmp.me._getMoreBtn(tmp.rowNo, tmp.result.total)});
	    	}
    	});
	},
	//getting the _getMoreBtn Dom
	_getMoreBtn: function(rowNo, total) {
		var tmp = {};
		if (rowNo === 0 || rowNo >= total)
			return;
		
		tmp.me = this;
		tmp.newMoreBtn = new Element('input', {'type': 'button', 'class': 'showMoreBtn', 'value': 'Show More Property'})
			.observe('click', function(){
				tmp.me.getMore(this);
			});
		return tmp.newMoreBtn;
	},
	//event to get more Property
	getMore: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.orgianlBtnValue = $(btn).value;
		tmp.me.pagination.pageNumber += 1;
		appJs.postAjax(this.callBackIds.get, {'pagination': tmp.me.pagination}, {
    		'onLoading': function(sender, param){
    			$(btn).writeAttribute('value', 'Getting more ...').disabled = true;
    		},
	    	'onComplete': function(sender, param){
	    		try {
		    		tmp.result = appJs.getResp(param, false, true);
		    		tmp.transCount = tmp.result.properties.size();
		    		$(btn).remove();
		    		//display the result rows
		    		for(tmp.i = 0; tmp.i < tmp.transCount; tmp.i++) {
		    			tmp.rowNo = (tmp.me.pagination.pageNumber - 1) * tmp.me.pagination.pageSize + tmp.i + 1;
		    			$(tmp.me.divIds.list).insert({'bottom': tmp.me._getPropertyRow(tmp.result.properties[tmp.i], tmp.rowNo).addClassName(tmp.rowNo % 2 === 0 ? 'odd' : 'even')});
		    		}
		    		//display more btn
		    		$(tmp.me.divIds.list).insert({'bottom': tmp.me._getMoreBtn(tmp.rowNo, tmp.result.total)});
	    		} catch(e) {
	    			alert(e);
	    			if($(btn) !== undefined && $(btn) !== null) {
	    				$(btn).writeAttribute('value', tmp.orgianlBtnValue).disabled = false;
	    			}
	    		}
	    	}
    	});
	},
	//get the dom element for the 
	_getValueRow: function(title, boughtValue, setup, income, returnp, outgoing, profit) {
		return new Element('div', {'class': "values"})
			.insert({'bottom': new Element('span', {'class': "title"}).update(title) })
			.insert({'bottom': new Element('span', {'class': "boughtvalue"}).update(boughtValue) })
			.insert({'bottom': new Element('span', {'class': "setup"}).update(setup) })
			.insert({'bottom': new Element('span', {'class': "income"}).update(income) })
			.insert({'bottom': new Element('span', {'class': "returnpercentage"}).update(returnp) })
			.insert({'bottom': new Element('span', {'class': "outgoing"}).update(outgoing) })
			.insert({'bottom': new Element('span', {'class': "profit"}).update(profit) });
	}
	//getting the url to the transactions
	,_makeUrl: function(start, end, toAccIds, fromAccIds) {
		var tmp = {};
		tmp.array = {"fromAccountIds" : fromAccIds !== undefined ? fromAccIds : [],
                "toAccountIds": toAccIds,
                "fromDate": start,
                "toDate": end
		};
		return "/reports/" + Object.toJSON(tmp.array);
	} 
	
	//getting the html for the property
	,_getPropertyRow: function(property) {
		var tmp = {};
		tmp.me = this;
		tmp.assetsUl = new Element('ul', {'class': 'assetslist'});
		property.assets.each(function(item){
			tmp.assetsUl.insert({'bottom': new Element('li', {'class': 'assets inlineblock'})
				.update(new Element('a', {'class': 'assetlink', 'href': '/asset/' + item.assetKey, 'target': '_blank'}).update(item.filename)) 
			});
		});
		return new Element('div', {'class': "property row"})
			//summary div
			.insert({'bottom': new Element('div', {'class': "summary"}) 
				.insert({'bottom': new Element('span', {'class': "address"}).update(property.address.full) })
				.observe('click', function() {
					tmp.row = $(this).up('.property');
					//we have the account entries already
					if (tmp.me.accEntries !== null) {
						tmp.me._showPropertyPanel(property, tmp.row);
						return;
					}
					//else we have to fetch them from the backend
					appJs.postAjax(tmp.me.callBackIds.getAcc, {}, {
			    		'onLoading': function(sender, param){
			    			tmp.row.insert({'bottom': new Element('div', {'class': 'editDiv newAccDiv'}).update('Getting Accounts Information ...<img src="/contents/images/loading.gif" />') });
			    		},
				    	'onComplete': function(sender, param){
				    		tmp.result = appJs.getResp(param);
				    		tmp.me.accEntries = tmp.result.accounts;
				    		tmp.me.states = tmp.result.states;
				    		tmp.me._showPropertyPanel(property, tmp.row);
				    	}
			    	});
				})
			})
			//current FY
			.insert({'bottom': tmp.me._getFYDiv('Current FY: ', property.currentFY, property.boughtValue, property.setupAcc.sum) })
			//Last FY
			.insert({'bottom': tmp.me._getFYDiv('Last FY: ', property.lastFY, property.boughtValue, property.setupAcc.sum) })
			//get total data
			.insert({'bottom': this._getTotalDiv('Total: ', property) })
			.insert({'bottom': new Element('div', {'class': 'comments smlTxt alignLeft fullwidth'}).update(property.comments) })
			.insert({'bottom': new Element('div', {'class': 'assets smlTxt alignLeft fullwidth'}).update(tmp.assetsUl) })
			;
	}
	
	//show add/edit property panel
	,_showPropertyPanel: function(property, rowDiv) {
		var tmp = {};
		tmp.me = this;
		//clearout all editpane
		$$('.editDiv').each(function(item) { item.remove(); });
		
		tmp.pId = (property.id || '');
		tmp.boughtValue = (property.boughtValue || '');
		tmp.comments = (property.comments || '');
		tmp.setupAcc = (property.setupAcc || null);
		tmp.incomeAcc = (property.incomeAcc || null);
		tmp.outgoingAcc = (property.outgoingAcc || null);
		tmp.addrLine1 = (property.address.line1 || '');
		tmp.addrSuburb = (property.address.suburb || '');
		tmp.addrState = (property.address.state || '');
		tmp.addrPostCode = (property.address.postCode || '');
		
		tmp.stateSelectBox = new Element('select', {'class': 'fullwidth', 'editaddr': 'stateId'});
		$H(tmp.me.states).each(function(st) {
			tmp.option = new Element('option', {'value': st.key}).update(st.value.name + ' (' + st.value.country.name + ')');
			if(st.value.id === tmp.addrState.id) {
				tmp.option.writeAttribute('selected', true);
			}
			tmp.stateSelectBox.insert({'bottom': tmp.option});
		});
		
		tmp.fileUploaderWrapperId = 'attachments_' + tmp.pId;
		tmp.newDiv = new Element('div', {'class': 'editDiv newAccDiv', 'propid': tmp.pId})
			.insert({'bottom': tmp.me._getShowPropertyRow('Bought: $', new Element('input', {'class': 'inputbox fullwidth', 'editpane': 'boughtValue', 'value': tmp.boughtValue}).writeAttribute('placeholder', 'The amount of money when assign contract') ) })
			.insert({'bottom': tmp.me._getShowPropertyRow('Setup Acc: ', tmp.me._getAccListBox([4], tmp.setupAcc).writeAttribute('editpane', 'setupAcc') ) })
			.insert({'bottom': tmp.me._getShowPropertyRow('Income Acc: ', tmp.me._getAccListBox([3], tmp.incomeAcc).writeAttribute('editpane', 'incomeAcc') ) })
			.insert({'bottom': tmp.me._getShowPropertyRow('Expense Acc: ', tmp.me._getAccListBox([4], tmp.outgoingAcc).writeAttribute('editpane', 'outgoingAcc') ) })
			.insert({'bottom': tmp.me._getShowPropertyRow('Comments: ', new Element('input', {'class': 'inputbox fullwidth', 'editpane': 'comments', 'value': tmp.comments}).writeAttribute('placeholder', 'Some comments') ) })
			.insert({'bottom': tmp.me._getShowPropertyRow('Address: ',
					new Element('div', {'class': 'fullwidth address'})
						.insert({'bottom': new Element('span').update('Addr.: ') })
						.insert({'bottom': new Element('input', {'class': 'fullwidth', 'editaddr': 'line1', 'value': tmp.addrLine1}) })
						.insert({'bottom': new Element('div', {'class': 'fullwidth'}) 
							.insert({'bottom': new Element('span', {'class': 'inlineblock suburb'})
								.insert({'bottom': new Element('span').update('Suburb: ') })
								.insert({'bottom': new Element('input', {'class': 'fullwidth', 'editaddr': 'suburb', 'value': tmp.addrSuburb}) })
							})
							.insert({'bottom': new Element('span', {'class': 'inlineblock postcode'})
								.insert({'bottom': new Element('span').update('PostCode: ') })
								.insert({'bottom': new Element('input', {'class': 'fullwidth', 'editaddr': 'postcode', 'value': tmp.addrPostCode}) })
							})
							.insert({'bottom': new Element('span', {'class': 'inlineblock state'})
								.insert({'bottom': new Element('span').update('State: ') })
								.insert({'bottom': tmp.stateSelectBox })
							})
						})
			) })
			//getting the comments row
	    	.insert({'bottom': tmp.me._getShowPropertyRow('Attachments: ', new Element('div')
	    		.insert({'bottom': tmp.me._getAssetListDiv(property.assets) }) 
	    		.insert({'bottom': new Element('span', {'attachments': 'attachments', 'id': tmp.fileUploaderWrapperId}) }) 
	    	) })
			.insert({'bottom': new Element('div', {'class': 'newRow btns'})
				.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Save'}).observe('click', function(){
						tmp.me._submitProperty(this);
				})})
				.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Cancel', 'style': 'float:right;'}).observe('click', function(){
					$(this).up('.newAccDiv').remove();
				})})
			});
		if(tmp.pId !== '') {
			tmp.newDiv.insert({'bottom': new Element('input', {'type': 'hidden', 'value': tmp.pId, 'editpane': 'id'}) });
		}
		
		rowDiv.insert({'bottom': tmp.newDiv });
		tmp.fileHandler = new FileUploaderJs(tmp.fileUploaderWrapperId).initFileUploader();
		tmp.newDiv.store('fileHandler', tmp.fileHandler);
		return this;
	}
	
	//getting the asset list
	,_getAssetListDiv: function(assets) {
		var tmp = {};
		tmp.div = new Element('div', {'class': 'assets uploadedFileList', 'assets': 'assets'});
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
	
	,_submitProperty: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.row = $(btn).up('.editDiv');
		tmp.requestData = {'address': {}, 'assets': {}};
		tmp.row.getElementsBySelector('[editpane]').each(function(item){
			tmp.requestData[item.readAttribute('editpane')] = $F(item);
		});
		tmp.row.getElementsBySelector('[editaddr]').each(function(item){
			tmp.requestData.address[item.readAttribute('editaddr')] = $F(item);
		});
		
		tmp.assets = {};
		tmp.row.getElementsBySelector('.uploadedfile').each(function(fileItem) {
			tmp.assets[fileItem.readAttribute('assetkey')] = (fileItem.readAttribute('delete') ? false : true);
		});
		tmp.requestData.assets.assets = tmp.assets;
		tmp.requestData.assets.attachments = tmp.row.retrieve('fileHandler').uploadedFiles;
		
		//start saving to the server
    	tmp.savingInfo = new Element('div').update('saving ...');
    	appJs.postAjax(tmp.me.callBackIds.save, tmp.requestData, {
    		'onLoading': function(sender, param){
    			tmp.row.down('.btns').hide().insert({'after': tmp.savingInfo});
    		},
	    	'onComplete': function(sender, param){
	    		try{
	    			tmp.property = appJs.getResp(param, false, true);
	    			if(tmp.property.id === undefined || tmp.property.id.blank())
	    				throw 'System Error:Invalid property id!';
	    			
	    			tmp.propRow = tmp.row.up('.property');
	    			tmp.oddRow = tmp.propRow.hasClassName('odd') ? 'odd' : 'even';
	    			tmp.propRow.replace( tmp.me._getPropertyRow(tmp.property).addClassName(tmp.oddRow)).scrollTo();
    				//remove the saving panel
	    			tmp.row.remove();
	    		} catch(e) {
	    			tmp.savingInfo.remove();
	    			tmp.row.down('.btns').show();
	    			console.error(e);
	    		}
	    	}
    	});
		return this;
	}
	
	//getting the accounts list box for editing
	,_getAccListBox: function(rootIds, selectedAcc) {
		var tmp = {};
		tmp.me = this;
		tmp.selectBox = new Element('select', {'class': 'inputbox fullwidth'});
		tmp.shownSelectedAccount = (selectedAcc === undefined || selectedAcc === null ? true : false);
		rootIds.each(function(rootId){
			tmp.selectBox.insert({'bottom': new Element('optgroup', {'label': tmp.me.accEntries[rootId][rootId].name}) });
			$H(tmp.me.accEntries[rootId]).each(function(acc){
				tmp.option =new Element('option', {'value': acc.key}).update(acc.value.breadCrumbs.name + ' - ' + appJs.getCurrency(acc.value.sum));
				if(acc.key === selectedAcc.id) {
					tmp.option.writeAttribute('selected', true);
					tmp.shownSelectedAccount = true;
				}
				tmp.selectBox.insert({'bottom':  tmp.option});
			});
		});
		if(tmp.shownSelectedAccount === false) {
			tmp.selectBox.insert({'top': new Element('option', {'value': selectedAcc.id}).update(selectedAcc.breadCrumbs.name + ' - ' + appJs.getCurrency(selectedAcc.sum)) });
		}
		return tmp.selectBox;
	}
	
	//getting property row for listing
	,_getShowPropertyRow: function(title, content) {
		return new Element('div', {'class': 'newRow'})
			.insert({'bottom': new Element('span', {'class': 'label inlineblock valignTop'}).update(title) })
			.insert({'bottom': new Element('span', {'class': 'typein inlineblock'}).update(content) });
	}
	
	//get financial year's data
	,_getFYDiv: function(title, data, boughtValue, setupValue) {
		var tmp = {};
		tmp.me = this;
		tmp.incomeAcc = data.income;
		tmp.outgoingAcc = data.outgoing;
		tmp.profit = tmp.incomeAcc - tmp.outgoingAcc;
		return this._getValueRow(
			(new Element('span').update(new Element('div', {'class': 'titlecontent', 'title': (data.date.from + ' ~ ' + data.date.to)}).update(title)) ),
			appJs.getCurrency(boughtValue), 
			appJs.getCurrency(setupValue), 
			(new Element('a', {'href': tmp.me._makeUrl(data.date.from, data.date.to, data.incomeAccIds)}).update(appJs.getCurrency(tmp.incomeAcc)) ), 
			'(' + (Math.round(data.boughtValue) === 0 ? 0 : ((tmp.incomeAcc / data.boughtValue) * 100).toFixed(2)) + '%)', 
			(new Element('a', {'href': tmp.me._makeUrl(data.date.from, data.date.to, data.outgoingAccIds)}).update(appJs.getCurrency(tmp.outgoingAcc)) ), 
			(tmp.profit >= 0 ? appJs.getCurrency(tmp.profit) : new Element('span', {'class': 'minusCurrency'}).update(appJs.getCurrency(tmp.profit)))
		);
	}
	
	//get total data
	,_getTotalDiv: function(title, data) {
		var tmp = {};
		tmp.incomeAcc = data.incomeAcc.sum;
		tmp.outgoingAcc = data.outgoingAcc.sum;
		tmp.profit = tmp.incomeAcc - tmp.outgoingAcc;
		tmp.profit = tmp.profit >= 0 ? appJs.getCurrency(tmp.profit) : new Element('span', {'class': 'minusCurrency'}).update(appJs.getCurrency(tmp.profit));
		tmp.returnP = '(' + (Math.round(data.boughtValue) === 0 ? 0 : ((tmp.incomeAcc / data.boughtValue) * 100).toFixed(2)) + '%)';
		return this._getValueRow(title,
				appJs.getCurrency(data.boughtValue), 
				appJs.getCurrency(data.setupAcc.sum), 
				appJs.getCurrency(tmp.incomeAcc), 
				tmp.returnP, 
				appJs.getCurrency(tmp.outgoingAcc), 
				tmp.profit
		);
	}
};