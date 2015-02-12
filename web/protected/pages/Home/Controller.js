/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	_ressultPanelId: ''
	,_getOverviewPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'panel panel-default'})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'})
				.insert({'bottom': new Element('span').update('Overview')
				})
			})
			.insert({'bottom': new Element('div', {'class': 'panel-body'})
				.insert({'bottom': tmp.me._getLoadingDiv() })
			});
		return tmp.newDiv;
	}
	,_getLastTrans: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'panel panel-default'})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'})
				.insert({'bottom': new Element('span').update('Last Transactions:')
				})
			})
			.insert({'bottom': new Element('div', {'class': 'panel-body'})
				.insert({'bottom': tmp.me._getLoadingDiv() })
			});
		return tmp.newDiv;
	}
	,_getInputPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'input-panel'})
			.insert({'bottom': new Element('ul', {'class': 'nav nav-tabs nav-justified', 'role': 'tablist'})
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
			.insert({'bottom': tmp.inputPanelWrapper = new Element('div', {'class': 'trans-input-panel-wrapper'}) });
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
			});
		});
		return tmp.newDiv;
	}
	,_getLayout: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'row'})
			.insert({'bottom': new Element('div', {'class': 'col-md-8'})
				.insert({'bottom': tmp.me._getOverviewPanel().addClassName('hidden-sm hidden-xs') })
				.insert({'bottom': tmp.me._getInputPanel() })
			})
			.insert({'bottom': new Element('div', {'class': 'col-md-4'})
				.insert({'bottom': tmp.me._getLastTrans().addClassName('hidden-sm hidden-xs') })
			});
		return tmp.newDiv;
	}
	/**
	 * initialising
	 */
	,init: function(ressultPanelId, jQueryFormSelector) {
		var tmp = {};
		tmp.me = this;
		tmp.me._ressultPanelId = ressultPanelId;
		$(tmp.me._ressultPanelId).update(tmp.layout = tmp.me._getLayout())
			.store('transForm', new TransFormJs(tmp.me, jQueryFormSelector).render(tmp.layout.down('.trans-input-panel-wrapper')))
			.down('.trans-type-switcher').click();
		return tmp.me;
	}
});