/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	_resultPanelId: ''
	,_accTypes: []
	,_getLayout: function(typeId) {
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
		tmp.me._accTypes.each(function(type){
			tmp.tabs.insert({'bottom': new Element('li', {'class': (typeId === type.id ? 'active' : ''), 'type-id': type.id})
				.insert({'bottom': new Element('a', {'href': 'javascript:void(0);'}).update(type.name) })
			});
		});
		return tmp.newDiv;
	}
	,_openAccDetailsPanel: function(accId, parentId, typeId) {
		var tmp = {};
		tmp.me = this;
		return tmp.me;
	}
	,_getNewRootAccountEntryPanel: function(typeId) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'text-center'})
			.insert({'bottom': new Element('h4').update('You have NOT got any AccountEntry here.')})
			.insert({'bottom': new Element('div', {'class': 'btn btn-success btn-sm'})
				.insert({'bottom': new Element('div', {'class': 'glyphicon glyphicon-plus'}) })
				.insert({'bottom': new Element('span').update(' create a one') })
				.observe('click', function() {
					tmp.me._openAccDetailsPanel('', '', typeId);
				})
			});
		return tmp.newDiv;
	}
	,_showAccounts: function(typeId) {
		var tmp = {};
		tmp.me = this;
		tmp.layout = tmp.me._getLayout(typeId);
		$(tmp.me._resultPanelId).update(tmp.layout);
		tmp.resultDiv = tmp.layout.down('.accounts-table');
		tmp.loadingDiv = tmp.me._getLoadingDiv();
		tmp.me.postAjax(tmp.me.getCallbackId('getAccounts'), {'typeId': typeId}, {
			'onLoading': function() {
				tmp.resultDiv.update(tmp.loadingDiv);
			}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result)
						return;
					if(!tmp.result.items || tmp.result.items.size() === 0 )
						tmp.resultDiv.update(tmp.me._getNewRootAccountEntryPanel(typeId));
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
		tmp.me._showAccounts(tmp.me._accTypes[0].id);
		return tmp.me;
	}
});