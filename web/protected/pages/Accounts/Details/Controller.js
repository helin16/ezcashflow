/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_showEditPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div')
			.insert({'bottom': new Element('h4').update(!tmp.me._entity.id || tmp.me._entity.id.blank() ? 'Creating an AccountEntry:' : 'Editing an AccountEntry: ' + tmp.me._entity.breadCrumbs.join('/')) });
		$(tmp.me.getHTMLID('result-div')).update(tmp.newDiv);
		return tmp.me;
	}
	/**
	 * initialising
	 */
	,init: function(_resultPanelId, _account) {
		var tmp = {};
		tmp.me = this;
		tmp.me._account = _account;
		tmp.me._resultPanelId = _resultPanelId;
		tmp.me._showEditPanel();
		return tmp.me;
	}
});