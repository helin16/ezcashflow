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
				.insert({'bottom': new Element('div', {'class': 'col-xs-6 col-sm-4'})
					.update(!row.id ? 'Account' : row.accountEntry.breadCrumbs.join(' / '))
				})
				.insert({'bottom': new Element('div', {'class': 'col-xs-2 hidden-sm hidden-xs text-right'}).update(!row.id ? 'Credit' : ((row.credit && !row.credit.blank()) ? tmp.me.getCurrency(row.credit) : '')) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-2 hidden-sm hidden-xs text-right'}).update(!row.id ? 'Debit' : ((row.debit && !row.debit.blank()) ? tmp.me.getCurrency(row.debit) : '')) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-2 hidden-sm hidden-xs text-right'}).update(!row.id ? 'Balance' : ((row.balance && !row.balance.blank()) ? tmp.me.getCurrency(row.balance) : '')) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-2 visible-sm visible-xs text-right'})
					.addClassName((row.id && row.value < 0) ? 'text-danger' : '')
					.update(
							!row.id ? 'Value' : ((row.value < 0 ? '-' : '+') + tmp.me.getCurrency(Math.abs(row.value)))
					)
				})
			});
		return tmp.newRow;
	}
	/**
	 * getting the Transactions
	 */
	,_getTransactions : function(pageNo) {
		var tmp = {};
		tmp.me = this;
		tmp.pageNo = (pageNo || 1);
		tmp.btn = $(tmp.me.getHTMLID('search-btn'));
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
				tmp.resultDiv.up('.panel').show();
				jQuery('#' + tmp.btn.id).button('loading');
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
						if (!tmp.resultDiv.update(tmp.me._getTransactionRow({}).setStyle('font-weight:bold;')).hasClassName('list-group'))
							tmp.resultDiv.addClassName('list-group').removeClassName('panel-body');
					}
					tmp.result.items.each(function(item){
						tmp.resultDiv.insert({'bottom': tmp.me._getTransactionRow(item) });
					});
					$(tmp.me.getHTMLID('item-count')).update(tmp.result.pagination.totalRows);
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
		jQuery('#' + selectBox.id).select2({
			 minimumInputLength: 3,
			 multiple: true,
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
			 }
			,
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
		jQuery('.datepicker')
			.datetimepicker();
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
				._getTransactions(1);
		});
		tmp.me._initDatePicker()
			._initSelect2($(tmp.me.getHTMLID('search-panel-div')).down('[search-panel="accountsIds"]'));
		return tmp.me;
	}
});