//this is the source file for the AccountList page
var AccountsJs = new Class.create();
AccountsJs.prototype = {
	rootId: 1,
	callBackIds: {
		'getAccounts': '',
		'saveAccount': '',
		'deleteAccounts': '',
		'moveAccounts': ''
	}
	//constructor
	initialize: function (getAccCallBackId) {
		this.call //need to do this!!!
	},
	/**
	 * click event for the table in the .box-title
	 */
	selectAccountType: function (btn) {
        var tmp = {};
        tmp.clickedBtn = $(btn);
        tmp.clickedBtn.up('ul').getElementsBySelector('li').each(function(item){
            item.down('a').removeAttribute('selected');
        });
        tmp.clickedBtn.writeAttribute('selected');
    },
    /**
     * selecting a root type
     */
    showAccounts: function(btn) {
    	this.selectAccountType(btn);
    	this.rootId = $(btn).readAttribute('rootId');
    	return false;
    },
    getAccounts: function() {
    	appJs.postAjax();
    }
};