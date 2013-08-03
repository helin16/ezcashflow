//this is the source file for the FieldTaskListController
var HomeJs = new Class.create();
HomeJs.prototype = {
	recentTrans: {
		holder: '', //the div holder
		noOfTrans: 5, //how many of recent transactions will be displayed on the list
		callback: ''  //the call back id to get recent trans
	},
	//constructor
	initialize: function () {}
	//getting the accounts
	,getAccounts: function(pageWrapper, getAccsBtn, afterFunc) {
		var tmp = {};
		tmp.accounts = appJs.getPageData('accounts');
		tmp.lastUpdatedTime = appJs.getPageData('lastUpdatedTime');
		$(pageWrapper).hide();
		$$('.loadingAccDiv').each(function(item) { item.remove(); });
		$(pageWrapper).insert({'after': new Element('div', {'class': 'loadingAccDiv'}).update('<center><div>Loading Accounts Details ... </div><img src="/contents/images/loading.gif" /></center>') });
		appJs.postAjax(getAccsBtn, {'lastUpdatedTime': tmp.lastUpdatedTime}, {
			'onComplete': function(sender, param){
				try {
					tmp.result = appJs.getResp(param, false, true);
					$H(tmp.result.accounts).each(function(account) {
						$H(account.value).each(function(acc){
							tmp.accounts[account.key] = (tmp.accounts[account.key] || {});
							tmp.accounts[account.key][acc.value.id] = acc.value;
						});
					});
					appJs.setPageData('accounts', tmp.accounts);
					appJs.setPageData('lastUpdatedTime', tmp.result.lastUpdatedTime);
					$(pageWrapper).show();
					$$('.loadingAccDiv').each(function(item) { item.remove(); });
					afterFunc();
				} catch (e) {
					console.error(e);
				}
	    	}
		});
	}
	/**
	 * click event for the table in the .box-title
	 */
	,selectSummary: function (btn, postFunc) {
        var tmp = {};
        tmp.clickedBtn = $(btn);
        tmp.clickedBtn.up('ul').getElementsBySelector('li').each(function(item){
            item.down('a').removeAttribute('selected');
        });
        tmp.clickedBtn.writeAttribute('selected');
        if(typeof(postFunc) === 'function')
        	postFunc();
        return false;
    }
    /**
     * loading the recent trans
     * @returns
     */
	,loadRecentTrans: function(recentTransDiv, recentTransCBId) {
		var tmp = {};
		this.recentTrans.holder = recentTransDiv;
		this.recentTrans.callback = recentTransCBId;
		tmp.me = this;
		appJs.postAjax(this.recentTrans.callback, {'noOfTrans': this.recentTrans.noOfTrans }, {
    		'onLoading': function(sender, param){
    			$(tmp.me.recentTrans.holder).update('<img src="/contents/images/loading.gif" style="display:block; with:150px; height:150px;"/>');
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		$(tmp.me.recentTrans.holder).update('');
	    		if(tmp.result.size() <= 0) {
	    			$(tmp.me.recentTrans.holder).update(new Element('div', {'class': 'notrans'}).update('There is NO transactions yet!'));
	    		}
	    		tmp.ul = new Element('ul');
	    		tmp.result.each(function(trans){
	    			tmp.ul.insert({'bottom': pageJs.getRecentTranRow(trans)});
	    		});
	    		$(tmp.me.recentTrans.holder).insert({'bottom': tmp.ul});
	    	}
    	});
	}
	//refereshing transaction list with the new results
	,refreshTrans: function(data) {
		var tmp = {};
		$('summaryBtn').click();
		$(this.recentTrans.holder).getElementsBySelector('.notrans').each(function(item){
			item.remove();
		});
		tmp.ul = $(this.recentTrans.holder).down('ul');
		tmp.lis = tmp.ul.getElementsBySelector('li');
		data.each(function(trans){
			tmp.lastLi = tmp.lis.last();
			if(tmp.lis.size() >= pageJs.recentTrans.noOfTrans && tmp.lastLi !== undefined)
				tmp.lastLi.remove();
			tmp.ul.insert({'top': pageJs.getRecentTranRow(trans)});
		});
	}
	/**
	 * getRecentTranRow
	 */
	,getRecentTranRow: function(trans) {
		var tmp = {};
		tmp.li = new Element('li', {'class': "row", 'transid': trans.id});
		tmp.href = new Element('a', {'href': trans.link});
		tmp.href.insert({'bottom': new Element('p', {'class': 'value'}).update(appJs.getCurrency(trans.value))});
		tmp.href.insert({'bottom': new Element('p', {'class': 'from'}).update(trans.fromAcc.name === undefined ? '': trans.fromAcc.name)});
		tmp.href.insert({'bottom': new Element('p', {'class': 'to'}).update(trans.toAcc.name === undefined ? '': trans.toAcc.name)});
		tmp.href.insert({'bottom': new Element('p', {'class': 'comments'}).update(trans.comments)});
		tmp.li.update(tmp.href);
		return tmp.li;
	}
};