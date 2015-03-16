/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new BackEndPageJs(), {
	_pageSize : 30
	,_searchCriteria : null
	/**
	 * Getting the transaction row
	 */
	,_getTransactionRow : function(row) {
		var tmp = {};
		tmp.me = this;
		tmp.newRow = new Element('a', {'href': 'javascript: void(0);', 'class' : 'list-group-item', 'title': (row.id ? 'Description: ' + row.description : '')})
			.store('data', row)
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('div', {'class': 'col-xs-4 col-sm-2'}).update(!row.id ? 'Date' : tmp.me.loadUTCTime(row.logDate).toLocaleString() ) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-2 col-sm-2 hidden-sm hidden-xs'}).update(!row.id ? 'By' : (row.logBy && row.logBy.person ? row.logBy.person.fullName : (row.createdBy && row.createdBy.person ? row.createdBy.person.fullName : '') )) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-6 col-sm-5'})
					.update(!row.id ? 'Account' : row.accountEntry.breadCrumbs.join(' / '))
				})
				.insert({'bottom': new Element('div', {'class': 'col-xs-2 col-sm-1 hidden-sm hidden-xs text-right'}).update(!row.id ? 'Credit' : ((row.credit && !row.credit.blank()) ? tmp.me.getCurrency(row.credit) : '')) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-2 col-sm-1 hidden-sm hidden-xs text-right'}).update(!row.id ? 'Debit' : ((row.debit && !row.debit.blank()) ? tmp.me.getCurrency(row.debit) : '')) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-2 col-sm-1 hidden-sm hidden-xs text-right'}).update(!row.id ? 'Balance' : ((row.balance && !row.balance.blank()) ? tmp.me.getCurrency(row.balance) : '')) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-2 col-sm-1 visible-sm visible-xs text-right'})
					.addClassName((row.id && row.value < 0) ? 'text-danger' : '')
					.update(
							!row.id ? 'Value' : ((row.value < 0 ? '-' : '+') + tmp.me.getCurrency(Math.abs(row.value)))
					)
				})
			});
		if(row.attachments && row.attachments.size() > 0) {
			tmp.attachmentRow = new Element('div', {'class': ''});
			row.attachments.each(function(attachment) {
				tmp.attachmentRow.insert({'bottom': new Element('a', {'class': 'btn btn-success btn-xs', 'target': '_BLANK', 'href': '/asset/get?id=' + attachment.asset.skey}).update(attachment.asset.filename) });
			})
			tmp.newRow.insert({'bottom': tmp.attachmentRow });
		}
		return tmp.newRow;
	}
	/**
	 * getting the Transactions
	 */
	,_getTransactions : function(btn, pageNo) {
		var tmp = {};
		tmp.me = this;
		tmp.pageNo = (pageNo || 1);
		tmp.btn = btn;
		tmp.me._signRandID(tmp.btn);
		tmp.resultDiv = $(tmp.me.getHTMLID('result-list-div'));
		tmp._loadingDiv = tmp.me._getLoadingDiv();
		if(tmp.me._searchCriteria === null){
			tmp.me.showModalBox('<strong class="text-danger">No Search Criteria Provided.</strong>', 'No Search Criteria Provided. Please provide some first.');
			return;
		}
		tmp.data = {'searchCriteria' : tmp.me._searchCriteria, 'pagination' : {'pageNo' : tmp.pageNo, 'pageSize' : tmp.me._pageSize} };
		tmp.me.postAjax(tmp.me.getCallbackId('getTransactions'), tmp.data, {
			'onLoading' : function() {
				jQuery('#' + tmp.btn.id).button('loading');
				tmp.resultDiv.up('.panel').show();
				if (tmp.pageNo === 1) {
					if (!tmp.resultDiv.update(tmp._loadingDiv).hasClassName('panel-body'))
						tmp.resultDiv.addClassName('panel-body').removeClassName('group-list');
				}
			}
			,'onSuccess' : function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if (!tmp.result || !tmp.result.items)
						return;
					if (tmp.pageNo === 1) {
						tmp.resultDiv.update(new Element('div', {'class': 'list-group-item disabled'}).setStyle('font-weight:bold;').update(tmp.me._getTransactionRow({}).innerHTML) );
						tmp.resultDiv.removeClassName('panel-body').addClassName('list-group');
					}
					//removing the get more btns
					if(tmp.resultDiv.down('.get-more-btn-wrapper')) {
						tmp.resultDiv.down('.get-more-btn-wrapper').remove();
					}

					tmp.result.items.each(function(item){
						tmp.resultDiv.insert({'bottom': tmp.me._getTransactionRow(item) });
					});
					$(tmp.me.getHTMLID('item-count')).update(tmp.result.pagination.totalRows);
					if(tmp.result.pagination.pageNumber < tmp.result.pagination.totalPages) {
						tmp.resultDiv.insert({'bottom': new Element('a', {'href': 'javascript: void(0);', 'class': 'list-group-item list-group-item-info get-more-btn-wrapper text-center', 'data-loading-text': 'Getting More ...'})
							.update('Get More Transactions')
							.observe('click', function() {
								tmp.me._getTransactions(this, pageNo * 1 + 1);
							})
						})
					}
				} catch (e) {
					if (tmp.pageNo === 1) {
						if (!tmp.resultDiv.hasClassName('panel-body'))
							tmp.resultDiv.addClassName('panel-body').removeClassName('group-list');
						tmp.resultDiv.update(tmp.me.getAlertBox('Error: ', e).addClassName('alert-danger'));
					} else {
						tmp.me.showModalBox('<strong class="text-danger">Error:</strong>', e);
					}
				}
			}
			,'onComplete' : function() {
				jQuery('#' + tmp.btn.id).button('reset');
				tmp._loadingDiv.remove();
			}

		});
		return tmp.me;
	}
	/**
	 * initialize select2
	 */
	,_initSelect2: function (selectBox) {
		var tmp = {};
		tmp.me = this;
		tmp.me._signRandID(selectBox);
		tmp.preLoadedData = [];
		if(tmp.me._preSetData && tmp.me._preSetData.accounts && tmp.me._preSetData.accounts.size() > 0) {
			tmp.me._preSetData.accounts.each(function(account){
				tmp.preLoadedData.push({'id': account.id, 'text': account.breadCrumbs.join(' / '), 'data': account});
			});
		}
		jQuery('#' + selectBox.id).select2({
			 minimumInputLength: 3,
			 multiple: true,
			 allowClear: true,
			 ajax: {
				 delay: 250
				 ,url: '/ajax/getAccounts'
		         ,type: 'POST'
	        	 ,data: function (params) {
	        		 return {"searchTxt": params};
	        	 }
				 ,results: function(data, page, query) {
					 tmp.resultMap = {};
					 if(data.resultData && data.resultData.items) {
						 data.resultData.items.each(function(item) {
							 tmp.typeId = item.type.id;
							 if(!tmp.resultMap[tmp.typeId]) {
								 tmp.resultMap[tmp.typeId] = {'text': item.type.name, 'children': []};
							 }
							 tmp.resultMap[tmp.typeId]['children'].push({'id': item.id, 'text': item.breadCrumbs.join(' / ') , 'data': item})
						 });
					 }
					 tmp.result = [];
					 $H(tmp.resultMap).each(function(val){
						 tmp.result.push(val.value);
					 });
		    		 return { 'results' : tmp.result };
		    	 }
				 ,cache: true
			 },
			formatResult : function(result, label, query, escapeMarkup) {
				tmp.markup = [];
				tmp.option = this.text(result);
				if(!result.data)
					return tmp.option;

				return '<div>' + tmp.option + '<span class="badge pull-right">' + tmp.me.getCurrency(result.data.sumValue) + '</span></div>';
			}
			,formatNoMatches: function() {
				return '<div>No Accounts found.</div>';
			}
		});
		jQuery('#' + selectBox.id).select2('data', tmp.preLoadedData, true);
		return tmp.me;
	}
	/**
	 * collects search criteria
	 */
	,_collectSearchCriteria: function() {
		var tmp = {};
		tmp.me = this;
		tmp.foundData = false;
		tmp.data = {};
		$(tmp.me.getHTMLID('search-panel-div')).getElementsBySelector('[search-panel]').each(function(item){
			tmp.field = item.readAttribute('search-panel');
			if(!$F(item).blank()) {
				if(tmp.field === 'logDate_from' || tmp.field === 'logDate_to') {
					tmp.me._signRandID(item);
					tmp.data[tmp.field] = jQuery('#' + item.id).data('DateTimePicker').date().utc().format();
				} else {
					tmp.data[tmp.field] = $F(item);
				}
				tmp.foundData = true;
			}
		});
		if(tmp.foundData === true)
			tmp.me._searchCriteria = tmp.data;
		return tmp.me;
	}
	/**
	 * Initialize date picker
	 */
	,_initDatePicker: function() {
		var tmp = {};
		tmp.me = this;
		jQuery('.date-input')
			.datetimepicker({
				format: 'DD/MMM/YYYY HH:mm A'
			});
		return tmp.me;
	}
	,setAccountTypes: function(types) {
		var tmp = {};
		tmp.me = this;
		tmp.me._accTypes = types;
		return tmp.me;
	}
	/**
	 * Setting the preset data
	 */
	,_setPreData: function(preSetData) {
		var tmp = {};
		tmp.me = this;
		tmp.me._preSetData = (preSetData || []);
		return tmp.me;
	}
	,_initTypeSelection: function(selBox) {
		var tmp = {};
		tmp.me = this;
		tmp.selectedType = tmp.me._preSetData && tmp.me._preSetData.typeId ? tmp.me._preSetData.typeId : '';
		tmp.me._accTypes.each(function(type) {
			$(selBox).insert({'bottom': new Element('option', {'value': type.id, 'selected': tmp.selectedType === type.id}).update(type.name) });
		});
		tmp.me._signRandID(selBox);
		jQuery('#' + selBox.id).select2({
			allowClear: true
		});
		return tmp.me;
	}
	/**
	 * initialising
	 */
	,init : function() {
		var tmp = {};
		tmp.me = this;
		$(tmp.me.getHTMLID('search-btn')).observe('click', function() {
			tmp.me._collectSearchCriteria()
				._getTransactions(this, 1);
		});
		tmp.searchPanel = $(tmp.me.getHTMLID('search-panel-div'));
		if(tmp.me._preSetData) {
			if(tmp.me._preSetData.localFromDate)
				tmp.searchPanel.down('[search-panel="logDate_from"]').setValue(tmp.me._preSetData.localFromDate);
			if(tmp.me._preSetData.localToDate)
				tmp.searchPanel.down('[search-panel="logDate_to"]').setValue(tmp.me._preSetData.localToDate);
		}
		tmp.me._initDatePicker()
			._initSelect2(tmp.searchPanel.down('[search-panel="accountsIds"]'))
			._initTypeSelection(tmp.searchPanel.down('[search-panel="accountTypeId"]'));
		return tmp.me;
	}
});