/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	/**
	 * initialising
	 */
	init: function(_resultPanelId) {
		var tmp = {};
		tmp.me = this;
		tmp.me._resultPanelId = _resultPanelId;
		return tmp.me;
	}
});