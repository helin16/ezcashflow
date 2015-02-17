/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new BackEndPageJs(), {
	_pageSize : 30,
	_getTransactionRow : function(row) {
		var tmp = {};
		tmp.me = this;
		tmp.newRow = new Element('div', {'class' : 'list-group-item'})
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('div', {'class': 'col-sm-8'}) })
			})
	},
	_getTransactions : function(pageNo) {
		var tmp = {};
		tmp.me = this;
		tmp.pageNo = (pageNo || 1);
		tmp.btn = $(tmp.me.getHTMLID('search-btn'));
		tmp.me._signRandID(tmp.btn);
		tmp.resultDiv = $(tmp.me.getHTMLID('result-list-div'));
		tmp._loadingDiv = tmp.me._getLoadingDiv();
		tmp.data = {
			'searchCriteria' : tmp.me.searchCriteria,
			'pagination' : {
				'pageNo' : tmp.pageNo,
				'pageSize' : tmp.me._pageSize
			}
		};

		tmp.me.postAjax(tmp.me.getCallbackId('getTransactions'), tmp.data, {
			'onLoading' : function() {
				tmp.resultDiv.up('.panel').show();
				jQuery('#' + tmp.btn.id).button('loading');
				if (tmp.pageNo === 1) {
					if (!tmp.resultDiv.hasClassName('panel-body')) {
						tmp.resultDiv.addClassName('panel-body')
								.removeClassName('group-list');
					}
					tmp.resultDiv.update(tmp._loadingDiv);
				}
			},
			'onSuccess' : function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if (!tmp.result || !tmp.result.items)
						return;
					if (tmp.pageNo === 1) {
						if (!tmp.resultDiv.hasClassName('list-group')) {
							tmp.resultDiv.addClassName('list-group')
									.removeClassName('panel-body');
						}
						tmp.resultDiv.update('');
					}

				} catch (e) {
					if (tmp.pageNo === 1) {
						if (!tmp.resultDiv.hasClassName('panel-body')) {
							tmp.resultDiv.addClassName('panel-body')
									.removeClassName('group-list');
						}
						tmp.resultDiv.update(tmp.me.getAlertBox('Error: ', e)
								.addClassName('alert-danger'));
					} else {
						tmp.me.showModalBox(
								'<strong class="text-danger">Error:</strong>',
								e);
					}
				}
			},
			'onComplete' : function() {
				jQuery('#' + tmp.btn.id).button('reset');
				tmp._loadingDiv.remove();
			}

		});
		return tmp.me;
	}
	/**
	 * initialising
	 */
	,
	init : function() {
		var tmp = {};
		tmp.me = this;
		$(tmp.me.getHTMLID('search-btn')).observe('click', function() {
			tmp.me._getTransactions(1);
		});
		return tmp.me;
	}
});