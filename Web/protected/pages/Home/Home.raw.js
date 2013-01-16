//this is the source file for the FieldTaskListController
var HomeJs = new Class.create();
HomeJs.prototype = {
	recentTrans: {
		holder: '', //the div holder
		noOfTrans: 5, //how many of recent transactions will be displayed on the list
		callback: ''  //the call back id to get recent trans
	},
	//constructor
	initialize: function (recentTransDiv, recentTransCBId) {
		this.recentTrans.holder = recentTransDiv;
		this.recentTrans.callback = recentTransCBId;
	},
	/**
	 * click event for the table in the .box-title
	 */
	selectSummary: function (btn, postScript) {
        var tmp = {};
        tmp.clickedBtn = $(btn);
        tmp.clickedBtn.up('ul').getElementsBySelector('li').each(function(item){
            item.down('a').removeAttribute('selected');
        });
        tmp.clickedBtn.writeAttribute('selected');
        eval(postScript);
        return false;
    },
    /**
     * loading the recent trans
     * @returns
     */
	loadRecentTrans: function() {
		var tmp = {};
		tmp.holderDiv = this.recentTrans.holder;
		appJs.postAjax(this.recentTrans.callback, {'noOfTrans': this.recentTrans.noOfTrans }, {
    		'onLoading': function(sender, param){
    			$(tmp.holderDiv).update('<img src="/contents/images/loading.gif" style="display:block; with:150px; height:150px;"/>');
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		tmp.ul = new Element('ul');
	    		tmp.result.each(function(trans){
	    			tmp.ul.insert({'bottom': pageJs.getRecentTranRow(trans)});
	    		});
	    		$(tmp.holderDiv).update(tmp.ul);
	    	}
    	});
	},
	//refereshing transaction list with the new results
	refreshTrans: function(data) {
		var tmp = {};
		$('summaryBtn').click();
		tmp.ul = $(this.recentTrans.holder).down('ul');
		data.each(function(trans){
			tmp.lastLi = tmp.ul.getElementsBySelector('li').last();
			if(tmp.lastLi !== undefined)
				tmp.lastLi.remove();
			tmp.ul.insert({'top': pageJs.getRecentTranRow(trans)});
		});
	},
	/**
	 * getRecentTranRow
	 */
	getRecentTranRow: function(trans) {
		var tmp = {};
		tmp.li = new Element('li', {'class': "row", 'transid': trans.id});
		tmp.href = new Element('a', {'href': trans.link});
		tmp.href.insert({'bottom': new Element('p', {'class': 'value'}).update(trans.value)});
		tmp.href.insert({'bottom': new Element('p', {'class': 'from'}).update(trans.fromAcc.name === undefined ? '': trans.fromAcc.name)});
		tmp.href.insert({'bottom': new Element('p', {'class': 'to'}).update(trans.toAcc.name === undefined ? '': trans.toAcc.name)});
		tmp.href.insert({'bottom': new Element('p', {'class': 'comments'}).update(trans.comments)});
		tmp.li.update(tmp.href);
		return tmp.li;
	}
};