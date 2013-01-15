//this is the source file for the AccountList page
var PropertiesJs = new Class.create();
PropertiesJs.prototype = {
	pagination:
	{
		pageSize: 30,
		pageNumber: 1
	},
	divIds: {
		list : ''
	},
	callBackIds: {
		'get': '',
		'save': '',
		'delete': '',
	},
	//constructor
	initialize: function (listDivId, getCallBackId, saveCallBackId, delCallBackId) {
		this.divIds.list = listDivId;
		this.callBackIds.get = getCallBackId;
		this.callBackIds.save = saveCallBackId;
		this.callBackIds.delete = delCallBackId;
	},
	//getting all properties
	loadProperties: function()
	{
		var tmp = {};
		tmp.resultDivId = this.divIds.list;
		tmp.pagination = this.pagination;
		appJs.postAjax(this.callBackIds.get, {'pagination': tmp.pagination}, {
    		'onLoading': function(sender, param){
    			$(tmp.resultDivId).update('<img src="/contents/images/loading.gif" />');
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		tmp.count = tmp.result.size();
	    		if(tmp.accCount === 0) {
	    			$(tmp.resultDivId).update('No Property Found!');
	    			return;
	    		}
	    		$(tmp.resultDivId).update('');
	    		for(tmp.i = 0; tmp.i < tmp.count; tmp.i++) {
	    			tmp.newRow = pageJs.formatProperty(tmp.result[tmp.i]);
	    			$(tmp.resultDivId).insert({'bottom': tmp.newRow});
	    		}
	    	}
    	});
	},
	//getting the html for the property
	formatProperty: function(data) {
		var tmp = {};
		tmp.html = '<div class="content-box propertyItem">';
			tmp.html += '<h3 class="box-title">' + data.address.substr(0,20)  + ' ... </h3>';
			tmp.html += '<div class="box-content">';
			tmp.html += 'test';
			tmp.html += '</div>';
		tmp.html += '</div>';
		return tmp.html;
	}
};