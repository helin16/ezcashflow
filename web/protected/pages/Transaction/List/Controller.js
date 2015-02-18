/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new BackEndPageJs(), {
	_pageSize : 30
	,_getTransactionRow : function(row) {
		var tmp = {};
		tmp.me = this;
		tmp.newRow = new Element('a', {'href': 'javascript: void(0);', 'class' : 'list-group-item', 'title': (row.id ? 'Description: ' + row.description : '')})
			.store('data', row)
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('div', {'class': 'col-sm-6 col-xs-8'})
					.update(!row.id ? 'Account' : row.accountEntry.breadCrumbs.join(' / '))
				})
				.insert({'bottom': new Element('div', {'class': 'col-xs-1 hidden-sm hidden-xs'}).update(!row.id ? 'Type' : row.accountEntry.type.name) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-1 hidden-sm hidden-xs'}).update(!row.id ? 'Credit' : ((row.credit && !row.credit.blank()) ? tmp.me.getCurrency(row.credit) : '')) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-1 hidden-sm hidden-xs'}).update(!row.id ? 'Debit' : ((row.debit && !row.debit.blank()) ? tmp.me.getCurrency(row.debit) : '')) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-1 hidden-sm hidden-xs'}).update(!row.id ? 'Balance' : ((row.balance && !row.balance.blank()) ? tmp.me.getCurrency(row.balance) : '')) })
				.insert({'bottom': new Element('div', {'class': 'col-xs-1 visible-sm visible-xs'})
					.addClassName((row.id && row.value < 0) ? 'text-danger' : '')
					.update(
							!row.id ? 'Value' : ((row.value < 0 ? '-' : '+') + tmp.me.getCurrency(Math.abs(row.value)))
					)
				})
			});
		return tmp.newRow;
	}
	,_getTransactions : function(pageNo) {
		var tmp = {};
		tmp.me = this;
		tmp.pageNo = (pageNo || 1);
		tmp.btn = $(tmp.me.getHTMLID('search-btn'));
		tmp.me._signRandID(tmp.btn);
		tmp.resultDiv = $(tmp.me.getHTMLID('result-list-div'));
		tmp._loadingDiv = tmp.me._getLoadingDiv();
		tmp.data = {'searchCriteria' : tmp.me.searchCriteria, 'pagination' : {'pageNo' : tmp.pageNo, 'pageSize' : tmp.me._pageSize} };
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
	 * initialising
	 */
	,init : function() {
		var tmp = {};
		tmp.me = this;
		$(tmp.me.getHTMLID('search-btn')).observe('click', function() {
			tmp.me._getTransactions(1);
		});
		return tmp.me;
	}
});