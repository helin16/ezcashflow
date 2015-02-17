/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	_ressultPanelId: ''
	/**
	 * Getting the overview row
	 *
	 * @param array row     The row data
	 * @param bool  isTitle Whether this row is a title row
	 *
	 * @return Element The DOM Element row
	 */
	,_getOverviewRow: function (row, isTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'th' : 'td');
		tmp.newRow = new Element('tr')
			.insert({'bottom': new Element(tmp.tag).update(row.title)});
		if(row.dateRange) {
			$H(row.dateRange).each(function(item){
				if(!item.value.from && !item.value.to)
					return;
				tmp.popoverText = (item.value.from.toLocaleString ? item.value.from.toLocaleString() : '') + (item.value.to.toLocaleString ? '~' + item.value.to.toLocaleString() : '');
				if(tmp.isTitle === true) {
					tmp.newRow.insert({'bottom': new Element(tmp.tag, {'class': 'text-uppercase', 'title': tmp.popoverText}).update(item.key)});
				} else {
					tmp.sumValue = row.data[item.key];
					tmp.newRow.insert({'bottom': new Element(tmp.tag, {'title': row.title + ': ' + tmp.popoverText}).update(
						new Element('span', {'class': (row.title !== 'Profit' ? '' : (tmp.sumValue < 0 ? 'text-danger' : '')) })
							.update(tmp.me.getCurrency(tmp.sumValue))
					) });
				}
			});
		}
		return tmp.newRow;
	}
	/**
	 * Getting the date range for: today, week , month, year and all
	 *
	 * @return Object The date ranges
	 */
	,_getDateRange: function() {
		var tmp = {};
		tmp.me = this;
		tmp.now = new Date();
		tmp.dateRange = {};

		tmp.beginOfToday = new Date(tmp.now.getFullYear(), tmp.now.getMonth(), tmp.now.getDate());
		tmp.endOfToday = new Date(tmp.now.getFullYear(), tmp.now.getMonth(), tmp.now.getDate(), 23, 59, 59);
		tmp.dateRange.today = {'from': tmp.beginOfToday, 'to': tmp.endOfToday};

		tmp.beginOfWeek = new Date(tmp.now.getFullYear(), tmp.now.getMonth(), tmp.now.getDate() - tmp.now.getDay() + (tmp.now.getDay() == 0 ? -6:1));
		tmp.endOfWeek = new Date(tmp.now.getFullYear(), tmp.now.getMonth(), tmp.now.getDate() + 7 - tmp.now.getDay(), 23, 59, 59);
		tmp.dateRange.week = {'from': tmp.beginOfWeek, 'to': tmp.endOfWeek};

		tmp.beginOfMonth = new Date(tmp.now.getFullYear(), tmp.now.getMonth(), 1);
		tmp.endOfMonth = new Date(tmp.now.getFullYear(), tmp.now.getMonth() + 1, 0, 23, 59, 59);
		tmp.dateRange.month = {'from': tmp.beginOfMonth, 'to': tmp.endOfMonth};

		tmp.beginOfYear = new Date(tmp.now.getFullYear(), 0, 1);
		tmp.endOfYear = new Date(tmp.now.getFullYear(), 11, 31, 23, 59, 59);
		tmp.dateRange.year = {'from': tmp.beginOfYear, 'to': tmp.endOfYear};

		tmp.beginOfAll = new Date(tmp.now.getFullYear() - 10, 0, 1);
		tmp.endOfAll = new Date(tmp.now.getFullYear(), 11, 31, 23, 59, 59);
		tmp.dateRange.total = {'from': tmp.beginOfAll, 'to': tmp.endOfAll};
		return tmp.dateRange;
	}
	/**
	 * Ajax: Gettinghte overview data from the server end
	 *
	 * @param Element resultDiv The result div dom
	 *
	 * @return PageJs
	 */
	,_getOverviewData: function(resultDiv) {
		var tmp = {};
		tmp.me = this;
		tmp._resultDiv = resultDiv.down('.list-result');
		tmp._loadingDiv = tmp.me._getLoadingDiv();
		tmp.dateRange = tmp.me._getDateRange();
		tmp.me.postAjax(tmp.me.getCallbackId('getSummary'), tmp.dateRange, {
			'onLoading': function() {
				if(tmp._resultDiv.hasClassName('table-responsive'))
					tmp._resultDiv.removeClassName('table-responsive').addClassName('panel-body');
				tmp._resultDiv.update(tmp._loadingDiv);
			}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.items)
						return;
					tmp.resultTable = new Element('table', {'class': 'table table-hover table-striped'})
						.insert({'bottom': new Element('thead').update( tmp.me._getOverviewRow({'title': '&nbsp;', 'dateRange': tmp.dateRange}, true) ) })
						.insert({'bottom': tmp.tbody = new Element('tbody')});
					tmp.incomes = {};
					tmp.expenses = {};
					tmp.profits = {};
					$H(tmp.result.items).each(function(item){
						tmp.incomes[item.key] = item.value.income;
						tmp.expenses[item.key] = item.value.expense;
						tmp.profits[item.key] = item.value.income * 1 - (item.value.expense * 1);
					});
					tmp.tbody.insert({'bottom': tmp.me._getOverviewRow({'title': 'Income', 'dateRange': tmp.dateRange, 'data': tmp.incomes}) })
						.insert({'bottom': tmp.me._getOverviewRow({'title': 'Expense', 'dateRange': tmp.dateRange, 'data': tmp.expenses}) })
						.insert({'bottom': tmp.me._getOverviewRow({'title': 'Profit', 'dateRange': tmp.dateRange, 'data': tmp.profits}) });
					if(!tmp._resultDiv.hasClassName('table-responsive'))
						tmp._resultDiv.addClassName('table-responsive').removeClassName('panel-body');
					tmp._resultDiv.update(tmp.resultTable);
				} catch (e) {
					if(tmp._resultDiv.hasClassName('table-responsive'))
						tmp._resultDiv.removeClassName('table-responsive').addClassName('panel-body');
					tmp._resultDiv.update(tmp.me.getAlertBox('Error: ', e).addClassName('alert-danger'));
				}
			}
			,'onComplete': function() {
				if(tmp._loadingDiv) {
					tmp._loadingDiv.remove();
				}
			}
		});
		return tmp.me;
	}
	/**
	 * Getting the overview panel
	 *
	 * @return Element The Dom Element for the overview
	 */
	,_getOverviewPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'panel panel-default overview-summary-div'})
			.store('event:load', function() {
				tmp.me._getOverviewData(tmp.newDiv);
			})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'})
				.insert({'bottom': new Element('span').update('Overview:')
				})
			})
			.insert({'bottom': new Element('div', {'class': 'panel-body list-result'})
				.insert({'bottom': tmp.me._getLoadingDiv() })
			});
		return tmp.newDiv;
	}
	/**
	 * Ajax: Getting the lastest trans from server end
	 *
	 * @param Element resultDiv The dom for th result div
	 *
	 * @return PageJs
	 */
	,_getLastTrans: function (resultDiv) {
		var tmp = {};
		tmp.me = this;
		tmp._resultDiv = resultDiv.down('.list-result');
		tmp._loadingDiv = tmp.me._getLoadingDiv();
		tmp.me.postAjax(tmp.me.getCallbackId('getLatestTrans'), {}, {
			'onLoading': function() {
				if(tmp._resultDiv.hasClassName('list-group'))
					tmp._resultDiv.removeClassName('list-group').addClassName('panel-body');
				tmp._resultDiv.update(tmp._loadingDiv);
			}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.items)
						return;
					tmp._resultDiv.update('');
					if(tmp._resultDiv.hasClassName('panel-body'))
						tmp._resultDiv.removeClassName('panel-body').addClassName('list-group');
					tmp.result.items.each(function(item) {
						tmp.localeTime = tmp.me.loadUTCTime(item.created);
						tmp._resultDiv.insert({'bottom': new Element('a', {'class': 'list-group-item', 'href': 'javascript: void(0)'})
							.store('data', item)
							.insert({'bottom': new Element('strong', {'class': 'list-group-item-heading'}).update(item.accountEntry.breadCrumbs.join(' / ')) })
							.insert({'bottom': new Element('div', {'class': 'badge'}).update(tmp.me.getCurrency(item.value)) })
							.insert({'bottom': new Element('div', {'class': 'list-group-item-text row'})
								.insert({'bottom': new Element('div', {'class': 'col-sm-5'}).update( new Element('small').update(new Element('span').update(tmp.localeTime.toDateString()) ) ) })
								.insert({'bottom': new Element('div', {'class': 'col-sm-7'}).update(new Element('small').update(new Element('em').update(item.description))) })
							})
						});
					});

				} catch (e) {
					if(tmp._resultDiv.hasClassName('list-group'))
						tmp._resultDiv.removeClassName('list-group').addClassName('panel-body');
					tmp._resultDiv.update(tmp.me.getAlertBox('Error: ', e).addClassName('alert-danger'));
				}
			}
			,'onComplete': function() {
				$(tmp.me._ressultPanelId).down('.overview-summary-div').retrieve('event:load')();
				if(tmp._loadingDiv) {
					tmp._loadingDiv.remove();
				}
			}
		});
		return tmp.me;
	}
	/**
	 * Getting the Lastest transtions div
	 *
	 * @return Element The list div
	 */
	,_getLastTransDiv: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'panel panel-default lastest-trans-div'})
			.store('event:load', function() {
				tmp.me._getLastTrans(tmp.newDiv);
			})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'})
				.insert({'bottom': new Element('span').update('Lastest Transactions:')
				})
			})
			.insert({'bottom': new Element('div', {'class': 'panel-body list-result'})
				.insert({'bottom': tmp.me._getLoadingDiv() })
			});
		return tmp.newDiv;
	}
	/**
	 * Getting the transaction input div
	 *
	 * @return Element
	 */
	,_getInputPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'input-panel panel panel-default'})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'})
				.setStyle('padding: 8px 4px 0 4px; border-bottom:none;')
				.insert({'bottom': new Element('ul', {'id': 'trans-type-list', 'class': 'nav nav-tabs nav-justified', 'role': 'tablist'})
					.insert({'bottom': new Element('li', {'class': 'trans-type-switcher'})
						.store('data', {'fromAccTypeIds': [1, 2], 'toAccTypeIds': [4]})
						.insert({'bottom': new Element('a', {'href': 'javascript:void(0);'}).update('Spend') })
					})
					.insert({'bottom': new Element('li', {'class': 'trans-type-switcher'})
						.store('data', {'fromAccTypeIds': [3], 'toAccTypeIds': [1]})
						.insert({'bottom': new Element('a', {'href': 'javascript:void(0);'}).update('Earn') })
					})
					.insert({'bottom': new Element('li', {'class': 'trans-type-switcher'})
						.store('data', {'fromAccTypeIds': [1, 2], 'toAccTypeIds': [1, 2]})
						.insert({'bottom': new Element('a', {'href': 'javascript:void(0);'}).update('Transfer') })
					})
				})
			})
			.insert({'bottom': tmp.inputPanelWrapper = new Element('div', {'class': 'trans-input-panel-wrapper panel-body'}) });
		tmp.newDiv.getElementsBySelector('.trans-type-switcher').each(function(item){
			item.observe('click', function(){
				tmp.li = $(this);
				tmp.li.up('.input-panel').getElementsBySelector('.trans-type-switcher').each(function(li){
					li.removeClassName('active');
				});
				tmp.li.addClassName('active');
				tmp.AccTypeIds = tmp.li.retrieve('data');
				$(tmp.me._ressultPanelId)
					.retrieve('transForm')
					.setAccTypeIds(tmp.AccTypeIds.fromAccTypeIds, tmp.AccTypeIds.toAccTypeIds)
					.resetForm();
			}).down('a').setStyle('outline:none;');
		});
		return tmp.newDiv;
	}
	/**
	 * getting initial layout dom
	 *
	 * @return Element
	 */
	,_getLayout: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'row'})
			.insert({'bottom': new Element('div', {'class': 'col-md-8'})
				.insert({'bottom': tmp.me._getOverviewPanel().addClassName('hidden-sm hidden-xs') })
				.insert({'bottom': tmp.me._getInputPanel() })
			})
			.insert({'bottom': new Element('div', {'class': 'col-md-4'})
				.insert({'bottom': tmp.me._getLastTransDiv().addClassName('hidden-sm hidden-xs') })
			});
		return tmp.newDiv;
	}
	/**
	 * loading the data
	 *
	 * @return PageJs
	 */
	,_loadData: function() {
		var tmp = {};
		tmp.me = this;
		$(tmp.me._ressultPanelId).down('.lastest-trans-div').retrieve('event:load')();
		return tmp.me;
	}
	/**
	 * initialising the script
	 *
	 * @param string ressultPanelId     The html ID of the result div
	 * @param string jQueryFormSelector The form ID for the bootstrapvalidator
	 *
	 * @return PageJs
	 */
	,init: function(ressultPanelId, jQueryFormSelector) {
		var tmp = {};
		tmp.me = this;
		tmp.me._ressultPanelId = ressultPanelId;
		$(tmp.me._ressultPanelId).update(tmp.layout = tmp.me._getLayout())
			.store('transForm', new TransFormJs(tmp.me, jQueryFormSelector)
				.setSaveSuccFunc(function() {
					tmp.me._loadData()
				})
				.render(tmp.layout.down('.trans-input-panel-wrapper'))
			)
			.down('.trans-type-switcher').click();
		tmp.me._loadData();
		return tmp.me;
	}
});