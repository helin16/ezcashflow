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
						console.debug(item);
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
					tmp._resultDiv.update(tmp.me.getAlertBox('Error: ', e).addClassName('alert-danger').wrap(new Element('div', {'class': 'panel-body list-result'})));
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
	,_getInputPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'input-panel panel panel-default'})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'})
				.setStyle('padding: 8px 0 0 0; border-bottom:none;')
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
	 * initialising
	 */
	,init: function(ressultPanelId, jQueryFormSelector) {
		var tmp = {};
		tmp.me = this;
		tmp.me._ressultPanelId = ressultPanelId;
		$(tmp.me._ressultPanelId).update(tmp.layout = tmp.me._getLayout())
			.store('transForm', new TransFormJs(tmp.me, jQueryFormSelector).render(tmp.layout.down('.trans-input-panel-wrapper')))
			.down('.trans-type-switcher').click();
		tmp.layout.down('.lastest-trans-div').retrieve('event:load')();
		return tmp.me;
	}
});