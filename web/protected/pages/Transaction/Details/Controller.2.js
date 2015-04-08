/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_acc: {}
	,_getEditPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'id': 'save-panel', 'class': 'save-panel'});
		return tmp.newDiv;
	}
	,_setFromNToAcc: function(from, to) {
		var tmp = {};
		tmp.me = this;
		tmp.me._acc.from = from;
		tmp.me._acc.to = to;
		return tmp.me;
	}
	/**
	 * initialising
	 */
	,init: function() {
		var tmp = {};
		tmp.me = this;
		if(tmp.me._entity && tmp.me._entity.id && tmp.me._acc && tmp.me._acc.from && tmp.me._acc.to) {
			tmp.me._entity.accounts = tmp.me._acc;
		}
		$(tmp.me.getHTMLID('result-div')).update(tmp.editPanel = tmp.me._getEditPanel())
			.store('transForm', new TransFormJs(tmp.me, jQuery('#' + tmp.me.getHTMLID('page-form')))
				.setSaveSuccFunc(function() {
					window.location = document.URL;
				})
				.render(tmp.editPanel, tmp.me._entity)
			);
		return tmp.me;
	}
});