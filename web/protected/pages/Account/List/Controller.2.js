/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	_resultPanelId: ''
	,_accTypes: []
	,_getLayout: function(type) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = tmp.newDiv = new Element('div')
			.insert({'bottom': new Element('h4').update('Account List:') })
			.insert({'bottom': new Element('div', {'class': 'list-panel'})
				.insert({'bottom': tmp.tabs = new Element('ul', {'class': 'nav nav-tabs nav-justified', 'role': 'tablist'}) })
				.insert({'bottom': new Element('div', {'class': 'panel-body'})
					.insert({'bottom': new Element('div', {'class': 'accounts-table'}) })
				})
			});
		tmp.me._accTypes.each(function(accType){
			tmp.tabs.insert({'bottom': new Element('li', {'class': (type.id === accType.id ? 'active' : ''), 'type-id': type.id})
				.insert({'bottom': new Element('a', {'href': 'javascript:void(0);'}).update(accType.name) })
				.observe('click', function() {
					tmp.me._showAccounts(accType);
				})
			});
		});
		return tmp.newDiv;
	}
	,_openAccDetailsPanel: function(acc, parent, type) {
		var tmp = {};
		tmp.me = this;
		jQuery.fancybox({
			'autoScale'     : false,
			'autoDimensions': false,
			'fitToView'     : false,
			'autoSize'      : false,
			'type'			: 'iframe',
			'href'			: '/accounts/' + (acc && acc.id ? acc.id : 'new') + '.html?blanklayout=1' + (parent && parent.id ? '&parentId=' + parent.id : '') + (type && type.id ? '&typeId=' + type.id : '')
 		});
		return tmp.me;
	}
	,_closeAccDetailsPanel: function() {
		var tmp = {};
		tmp.me = this;
		jQuery.fancybox.close();
		return tmp.me;
	}
	,_getNewRootAccountEntryPanel: function(type) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'text-center'})
			.insert({'bottom': new Element('h4').update('You have NOT got any account here.')})
			.insert({'bottom': new Element('div', {'class': 'btn btn-success btn-sm'})
				.insert({'bottom': new Element('div', {'class': 'glyphicon glyphicon-plus'}) })
				.insert({'bottom': new Element('span').update(' create a ' + type.name + ' account') })
				.observe('click', function() {
					tmp.me._openAccDetailsPanel(null, null, type);
				})
			});
		return tmp.newDiv;
	}
	/**
	 * Ajax: delete account
	 */
	,_deleteAcc: function(btn, acc) {
		var tmp = {};
		tmp.me = this;
		tmp.confirmDiv = $(btn).up('.confirm-panel');
		tmp.loadDiv = tmp.me._getLoadingDiv();
		tmp.me.postAjax(tmp.me.getCallbackId('deleteAccount'), {'accId': acc.id}, {
			'onLoading': function() {
				tmp.confirmDiv
					.insert({'after': tmp.loadDiv})
					.hide();
				tmp.confirmDiv.down('.msg-wrapper').update('');
			}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.item || !tmp.result.item.id)
						return;
					tmp.me._showAccounts(acc.type);
					tmp.me.hideModalBox();
				} catch (e) {
					tmp.confirmDiv.show()
						.down('.msg-wrapper').update(tmp.me.getAlertBox('Error: ', e).addClassName('alert-danger'));
				}
			}
			,'onComplete': function() {
				if($(btn) && $(btn).up('.confirm-panel'))
					$(btn).up('.confirm-panel').show();
				tmp.loadDiv.remove();
			}
 		});
		return tmp.me;
	}
	,_showConfirmDeletePanel: function (acc) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'confirm-panel'})
			.insert({'bottom': new Element('h4').update('You are about to delete this account: ' + acc.breadCrumbs.join(' / '))})
			.insert({'bottom': new Element('div')
				.insert({'bottom': new Element('ul')
					.insert({'bottom': new Element('li').update(' You will NOT be able to see this account any more')})
					.insert({'bottom': new Element('li').update(' You will NOT be able to log a transaction against it any more.')})
					.insert({'bottom': new Element('li').update(' This action can NOT be reversed.')})
				})
			})
			.insert({'bottom': new Element('div').update(new Element('strong').update('Are you sure to continue?')) })
			.insert({'bottom': new Element('div', {'class': 'msg-wrapper'})})
			.insert({'bottom': new Element('div')
				.insert({'bottom': new Element('div', {'class': 'btn btn-default'})
					.update('NO, cancel this')
					.observe('click', function() {
						tmp.me.hideModalBox();
					})
				})
				.insert({'bottom': new Element('div', {'class': 'btn btn-danger pull-right'}).update('YES, Delete it')
					.observe('click', function() {
						tmp.me._deleteAcc(this, acc);
					})
				})
			});
		tmp.me.showModalBox('<strong>Deletion Confirmation:</strong>', tmp.newDiv);
		return tmp.me;
	}
	,_getAccountRow: function (acc) {
		var tmp = {};
		tmp.me = this;
		tmp.tag = acc.id ? 'td' : 'th';
		tmp.newDiv = new Element('tr', {'class': 'item-row ' + (acc.id ? 'treegrid-' + acc.id : 'header') + (acc.isSumAcc === true ? ' active' : ''), 'account-id': acc.id  })
			.store('data', acc)
			.setStyle(acc.isSumAcc === true ? 'font-weight: bold; font-style: italic;' : '')
			.insert({'bottom': new Element(tmp.tag).update(new Element('abbr', {'title': acc.description}).update(acc.name)) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-xs-1 col-sm-1'}).update(acc.accountNo) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-xs-1 col-sm-1 hidden-sm hidden-xs'}).update(!acc.id ? acc.isSumAcc :
				(acc.isSumAcc === true ? new Element('span', {'class': 'text-success'}).update(new Element('span', {'title': acc.name.toUpperCase() + ' is a summary account', 'class': 'glyphicon glyphicon-ok'})) : '')
			) })
			.insert({'bottom': new Element(tmp.tag, {'title': 'Opening Balance', 'class': 'col-xs-1 col-sm-1 hidden-sm hidden-xs'}).update(acc.id ? tmp.me.getCurrency(acc.initValue) : acc.initValue) })
			.insert({'bottom': new Element(tmp.tag, {'title': 'Running Balance', 'class': 'col-xs-1 col-sm-1 hidden-sm hidden-xs'}).update(acc.id ? tmp.me.getCurrency(acc.runingValue) : acc.runingValue) })
			.insert({'bottom': new Element(tmp.tag, {'title': 'Total Value', 'class': 'col-xs-1 col-sm-1'}).update(acc.id ? tmp.me.getCurrency(acc.sumValue) : acc.sumValue) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-xs-2 col-sm-1 text-right'}).update(!acc.id ? '' :
				new Element('span', {'class': 'btn-group btn-group-xs visible-lg visible-md visible-sm visible-xs'})
					.insert({'bottom': new Element('span', {'class': 'btn btn-success', 'title': 'Add an new account under: ' + acc.name, 'disabled': !acc.isSumAcc})
						.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-plus'}) })
						.insert({'bottom': new Element('span', {'class': 'hidden-sm hidden-xs'}).update(' Add') })
						.observe('click', function() {
							if(acc.id && acc.isSumAcc === true)
								tmp.me._openAccDetailsPanel(null, acc, null);
						})
					})
					.insert({'bottom': new Element('span', {'class': 'btn btn-success dropdown-toggle', 'data-toggle': 'dropdown'})
						.insert({'bottom': new Element('span', {'class': 'caret'}) })
					})
					.insert({'bottom': new Element('ul', {'class': 'dropdown-menu', 'role': 'menu'})
						.insert({'bottom': new Element('li', {'title': 'Edit this account: ' + acc.name})
							.update(new Element('a', {'href': 'javascript: void(0);'})
								.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-pencil'}) })
								.insert({'bottom': new Element('span', {'class': 'hidden-sm hidden-xs'}).update(' Edit') })
								.observe('click', function() {
									tmp.me._openAccDetailsPanel(acc, null, null);
								})
							)
						})
						.insert({'bottom': new Element('li', {'title': 'Delete this account: ' + acc.name})
							.update(new Element('a', {'href': 'javascript: void(0);', 'class': 'bg-danger'})
								.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-remove'}) })
								.insert({'bottom': new Element('span', {'class': 'hidden-sm hidden-xs'}).update(' Delete') })
								.observe('click', function() {
									if(acc.childrenCount > 0) {
										tmp.me.showModalBox('<strong>Oops:</strong>', '<h4 class="text-danger">Can NOT delete ' + acc.name + ', as there are ' + acc.childrenCount + ' children. You have to delete them first</h4>');
									} else if(acc.transactionCount > 0) {
										tmp.me.showModalBox('<strong>Oops:</strong>', '<h4 class="text-danger">Can NOT delete ' + acc.name + ', as there are ' + acc.transactionCount + ' Transaction(s) against this account.</h4>');
									} else {
										tmp.me._showConfirmDeletePanel(acc);
									}
								})
							)
						})
						.insert({'bottom': acc.isSumAcc ? '' : new Element('li', {'title': 'View Transactions for : ' + acc.name})
							.update(new Element('a', {'href': '/transactions.html?accountids=' + acc.id})
								.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-list'}) })
								.insert({'bottom': new Element('span', {'class': 'hidden-sm hidden-xs'}).update(' Transactions') })
							)
						})
					})
			) })
			;
		if(acc.parent && acc.parent.id) {
			tmp.newDiv.addClassName('treegrid-parent-' + acc.parent.id);
		}
		return tmp.newDiv;
	}
	,_getAccountsDiv: function(items) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('table', {'class': 'table table-hover'})
			.insert({'bottom': tmp.me._getAccountRow({'name': 'Name', 'initValue': 'O.B.', 'runingValue': 'R.B.', 'sumValue': 'Total', 'accountNo': 'Acc. No.', 'isSumAcc': 'Sum Acc?'}).wrap(new Element('thead'))})
			.insert({'bottom': tmp.tbody = new Element('tbody')});
		items.each(function(item) {
			tmp.tbody.insert({'bottom': tmp.me._getAccountRow(item) });
		})
		return tmp.newDiv;
	}
	,_showAccounts: function(type, afterFunc) {
		var tmp = {};
		tmp.me = this;
		tmp.layout = tmp.me._getLayout(type);
		$(tmp.me._resultPanelId).update(tmp.layout);
		tmp.resultDiv = tmp.layout.down('.accounts-table');
		tmp.loadingDiv = tmp.me._getLoadingDiv();
		tmp.me.postAjax(tmp.me.getCallbackId('getAccounts'), {'typeId': type.id}, {
			'onLoading': function() {
				tmp.resultDiv.update(tmp.loadingDiv);
			}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result)
						return;
					if(!tmp.result.items || tmp.result.items.size() === 0 )
						tmp.resultDiv.update(tmp.me._getNewRootAccountEntryPanel(type));
					else {
						tmp.table = tmp.me._getAccountsDiv(tmp.result.items);
						tmp.resultDiv.update(tmp.table);
						tmp.me._signRandID(tmp.table);
						jQuery('#' + tmp.table.id).treegrid();
					}
					if(typeof(afterFunc) === 'function')
						afterFunc(tmp.result.items);
				} catch (e) {
					tmp.resultDiv.update(tmp.me.getAlertBox('ERR: ', e).addClassName('alert-danger'));
				}
			}
			,'onComplete': function() {
				tmp.loadingDiv.remove();
			}
		});
		return tmp.me;
	}
	/**
	 * initialising
	 */
	,init: function(_resultPanelId, accTypes, firstShowAccType) {
		var tmp = {};
		tmp.me = this;
		tmp.me._resultPanelId = _resultPanelId;
		tmp.me._accTypes = accTypes;
		tmp.me._showAccounts(firstShowAccType && firstShowAccType.id ? firstShowAccType : tmp.me._accTypes[0]);
		return tmp.me;
	}
});