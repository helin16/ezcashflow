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
	    			tmp.newProperty = pageJs.formatProperty(tmp.result[tmp.i]);
	    			$(tmp.resultDivId).insert({'bottom': tmp.newProperty});
	    		}
	    	}
    	});
	},
	//getting the html for the property
	formatProperty: function(data) {
		var tmp = {};
		console.debug(data);
		tmp.wrapper = new Element('div', {'class': "content-box propertyItem", 'title': data.address.full});
		tmp.titleDiv = new Element('h3', {'class': "box-title"}).update(data.address.full.substr(0,30));
		tmp.wrapper.insert({'bottom': tmp.titleDiv});
		tmp.contentDiv = new Element('div', {'class': "box-content"});
		tmp.wrapper.insert({'bottom': tmp.contentDiv});
		
		//add value
		tmp.boughtValueDiv = new Element('span', {'class': "boughtvalue"}).update(appJs.getCurrency(data.boughtValue)); 
		tmp.contentDiv.insert({'bottom': tmp.boughtValueDiv});
//		//add address
//		tmp.addressDiv = new Element('span', {'class': "address"}).update(); 
//		tmp.contentDiv.insert({'bottom': tmp.addressDiv});
		//add comments
		tmp.commentsDiv = new Element('span', {'class': "comments"}).update(data.comments); 
		tmp.contentDiv.insert({'bottom': tmp.commentsDiv});
		//add add Setup Acc
		if(data.setupAcc.breadCrumbs !== undefined) {
			tmp.setupAccDiv = new Element('span', {'class': "setupAcc"}).update(data.setupAcc.breadCrumbs.name); 
			tmp.contentDiv.insert({'bottom': tmp.setupAccDiv});
		}
		//add add Income Acc
		if(data.incomeAcc.breadCrumbs !== undefined) {
			tmp.incomeAccDiv = new Element('span', {'class': "incomeAcc"}).update(data.incomeAcc.breadCrumbs.name); 
			tmp.contentDiv.insert({'bottom': tmp.incomeAccDiv});
		}
		//add add outgoingAcc
		if(data.outgoingAcc.breadCrumbs !== undefined) {
			tmp.outgoingAccDiv = new Element('span', {'class': "outgoingAcc"}).update(data.outgoingAcc.breadCrumbs.name); 
			tmp.contentDiv.insert({'bottom': tmp.outgoingAccDiv});
		}
		
		return tmp.wrapper;
	},
	//show add/edit property panel
	showPropertyPanel: function(property) {
		var tmp = {};
	}
};