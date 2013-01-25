//this is the source file for the AssetTypeController
var AssetTypeJs = new Class.create();
AssetTypeJs.prototype = {
	canvas: '', // the div that holds the list
	callbackIds: {
		'list': ''
	},
	//constructor
	initialize: function (canvasId, listCBId) {
		this.callbackIds.list = listCBId;
		this.canvasId = canvasId;
	},
	
	//get the list
	getList: function() {
		var tmp = {};
		tmp.canvasId = this.canvasId;
		appJs.postAjax(this.callbackIds.list, {}, {
    		'onLoading': function(sender, param){
    			$(tmp.canvasId).update('<img src="/contents/images/loading.gif" />');
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		tmp.transCount = tmp.result.size();
	    		if(tmp.accCount === 0) {
	    			tmp.resultPanel.down('.box-content').update('No AssetType Found!');
	    			return;
	    		}
	    		
	    		$(tmp.canvasId).update.update('');
//	    		//display the result rows
//	    		for(tmp.i = 0; tmp.i < tmp.transCount; tmp.i++) {
//	    			tmp.rowNo = (pageJs.searchCriteria.pagination.pageNo - 1) * pageJs.searchCriteria.pagination.pageSize + tmp.i + 1;
//	    			tmp.newRow = pageJs.getRow(tmp.result.trans[tmp.i], tmp.rowNo);
//	    			tmp.resultPanel.down('.box-content').insert({'bottom': tmp.newRow});
//	    		}
//	    		
//	    		//display more btn
//	    		tmp.resultPanel.down('.box-content').insert({'bottom': pageJs.getMoreBtn(tmp.rowNo, tmp.result.total)});
	    	}
    	});
	}
};