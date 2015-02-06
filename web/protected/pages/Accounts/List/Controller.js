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
	,_getAccountRow: function (acc) {
		var tmp = {};
		tmp.me = this;
		tmp.tag = acc.id ? 'td' : 'th';
		tmp.newDiv = new Element('tr', {'class': (acc.id ? 'treegrid-' + acc.id : 'header') })
			.store('data', acc)
			.insert({'bottom': new Element(tmp.tag).update(new Element('abbr', {'title': acc.description}).update(acc.name)) })
			.insert({'bottom': new Element(tmp.tag).update(acc.accountNo) })
			.insert({'bottom': new Element(tmp.tag, {'title': 'Opening Balance', 'class': 'col-xs-1'}).update(acc.id ? tmp.me.getCurrency(acc.initValue) : acc.initValue) })
			.insert({'bottom': new Element(tmp.tag, {'title': 'Running Balance', 'class': 'col-xs-1'}).update(acc.id ? tmp.me.getCurrency(acc.sumValue) : acc.sumValue) })
			.insert({'bottom': new Element(tmp.tag, {'class': 'col-xs-1 text-right'}).update(!acc.id ? '' :
				new Element('span', {'class': 'btn-group btn-group-xs visible-lg visible-md visible-sm visible-xs'})
					.insert({'bottom': new Element('span', {'class': 'btn btn-success', 'title': 'Add an new account under: ' + acc.name})
						.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-plus'}) })
						.insert({'bottom': new Element('span', {'class': 'hidden-sm hidden-xs'}).update(' Add') })
						.observe('click', function() {
							tmp.me._openAccDetailsPanel(null, acc, null);
						})
					})
					.insert({'bottom': new Element('span', {'class': 'btn btn-success dropdown-toggle', 'data-toggle': 'dropdown'})
						.insert({'bottom': new Element('span', {'class': 'caret'}) })
					})
					.insert({'bottom': new Element('ul', {'class': 'dropdown-menu', 'role': 'menu'})
						.insert({'bottom': new Element('li', {'title': 'Edit this account: ' + acc.name}).update(new Element('a', {'href': 'javascript: void(0);'})
							.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-pencil'}) })
							.insert({'bottom': new Element('span', {'class': 'hidden-sm hidden-xs'}).update(' Edit') })
						)})
						.observe('click', function() {
							tmp.me._openAccDetailsPanel(acc, null, null);
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
			.insert({'bottom': tmp.me._getAccountRow({'name': 'Name', 'initValue': 'O.B.', 'sumValue': 'R.B.', 'accountNo': 'Acc. No.'}).wrap(new Element('thead'))})
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
	,init: function(_resultPanelId, accTypes) {
		var tmp = {};
		tmp.me = this;
		tmp.me._resultPanelId = _resultPanelId;
		tmp.me._accTypes = accTypes;
		tmp.me._showAccounts(tmp.me._accTypes[0]);
		return tmp.me;
	}
});