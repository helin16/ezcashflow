/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	_pageSize: 30
	._getTransactions: function(btn, pageNo) {
		var tmp = {};
		tmp.me = this;
		tmp.pageNo = (pageNo || 1);
		tmp.me._signRandID(btn);
		tmp.data = {'searchCriteria': tmp.me.searchCriteria, 'pagination': {'pageNo': tmp.pageNo, 'pageSize': tmp.me._pageSize}}
		tmp.me.postAjax(tmp.me.getCallbackId('getTransactions'), tmp.data, {
			'onLoading': function() {
				jQuery('#' + btn.id).button('loading');
			}
			,'onSuccess': function() {
				try {

				} catch (e) {

				}
			}
			,'onComplete': function

		});
		return tmp.me;
	}
	/**
	 * initialising
	 */
	,init: function() {
		var tmp = {};
		tmp.me = this;
		$(tmp.me.getHTMLID('search-btn')).observe('click', function() {
			tmp.me._getTransactions(this);
		});
		return tmp.me;
	}
});