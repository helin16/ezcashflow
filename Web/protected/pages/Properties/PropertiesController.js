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
	    		tmp.count = tmp.result.properties.size();
	    		if(tmp.accCount === 0) {
	    			$(tmp.resultDivId).update('No Property Found!');
	    			return;
	    		}
	    		
	    		//update the total count
	    		$(tmp.resultDivId).up('.content-box').getElementsBySelector('.noOfItems').each(function(item){
	    			item.update(tmp.result.total);
	    		});
	    		
	    		tmp.headerRow = new Element('div', {'class': "property row header"}).update(pageJs.getValueRow('', 'Bought', 'Setup', 'Income', 'Expense', 'Profit'));
	    		$(tmp.resultDivId).update(tmp.headerRow);
	    		for(tmp.i = 0; tmp.i < tmp.count; tmp.i++) {
	    			tmp.rowNo = (pageJs.pagination.pageNumber - 1) * pageJs.pagination.pageSize + tmp.i + 1;
	    			tmp.newProperty = pageJs.formatProperty(tmp.result.properties[tmp.i], tmp.rowNo);
	    			$(tmp.resultDivId).insert({'bottom': tmp.newProperty});
	    		}
	    		
	    		//display more btn
	    		$(tmp.resultDivId).insert({'bottom': pageJs.getMoreBtn(tmp.rowNo, tmp.result.total)});
	    	}
    	});
	},
	//getting the getMoreBtn Dom
	getMoreBtn: function(rowNo, total) {
		var tmp = {};
		if (rowNo === 0 || rowNo >= total)
			return;
		tmp.newMoreBtn = new Element('input', {'type': 'button', 'class': 'showMoreBtn', 'value': 'Show More Property'})
			.observe('click', function(){
				pageJs.getMore(this);
			});
		return tmp.newMoreBtn;
	},
	//event to get more Property
	getMore: function(btn) {
		var tmp = {};
		tmp.resultDivId = this.divIds.list;
		tmp.orgianlBtnValue = $(btn).value;
		tmp.resultPanel = $(this.resultPanelId);
		pageJs.pagination.pageNumber += 1;
		tmp.pagination = this.pagination;
		appJs.postAjax(this.callBackIds.get, {'pagination': tmp.pagination}, {
    		'onLoading': function(sender, param){
    			$(btn).writeAttribute('value', 'Getting more ...').disabled = true;
    		},
	    	'onComplete': function(sender, param){
	    		try {
		    		tmp.result = appJs.getResp(param);
		    		tmp.transCount = tmp.result.properties.size();
		    		$(btn).remove();
		    		//display the result rows
		    		for(tmp.i = 0; tmp.i < tmp.transCount; tmp.i++) {
		    			tmp.rowNo = (pageJs.pagination.pageNumber - 1) * pageJs.pagination.pageSize + tmp.i + 1;
		    			tmp.newProperty = pageJs.formatProperty(tmp.result.properties[tmp.i], tmp.rowNo);
		    			$(tmp.resultDivId).insert({'bottom': tmp.newProperty});
		    		}
		    		//display more btn
		    		$(tmp.resultDivId).insert({'bottom': pageJs.getMoreBtn(tmp.rowNo, tmp.result.total)});
	    		} catch(e) {
	    			alert(e);
	    			if($(btn) !== undefined && $(btn) !== null) {
	    				$(btn).writeAttribute('value', tmp.orgianlBtnValue).disabled = false;
	    			}
	    		}
	    	}
    	});
	},
	//get the dom element for the 
	getValueRow: function(title, boughtValue, setup, income, outgoing, profit) {
		var tmp = {};
		tmp.valuesDiv = new Element('div', {'class': "values"});
		
		tmp.titleSpan = new Element('span', {'class': "title"}).update(title);
		tmp.valuesDiv.insert({'bottom': tmp.titleSpan});
		
		tmp.boughtValueSpan = new Element('span', {'class': "boughtvalue"}).update(boughtValue);
		tmp.valuesDiv.insert({'bottom': tmp.boughtValueSpan});
		
		tmp.setupSpan = new Element('span', {'class': "setup"}).update(setup);
		tmp.valuesDiv.insert({'bottom': tmp.setupSpan});
		
		tmp.incomeSpan = new Element('span', {'class': "income"}).update(income);
		tmp.valuesDiv.insert({'bottom': tmp.incomeSpan});
		
		tmp.outgoingSpan = new Element('span', {'class': "outgoing"}).update(outgoing);
		tmp.valuesDiv.insert({'bottom': tmp.outgoingSpan});
		
		tmp.profitSpan = new Element('span', {'class': "profit"}).update(profit);
		tmp.valuesDiv.insert({'bottom': tmp.profitSpan});
		
		return tmp.valuesDiv;
	},
	//getting the html for the property
	formatProperty: function(data, rowNo) {
		var tmp = {};
		tmp.wrapper = new Element('div', {'class': "property row " + (rowNo % 2 === 0 ? 'odd' : 'even')});
		
		//summary div
		tmp.summaryDiv = new Element('div', {'class': "summery"});
		tmp.addressSpan = new Element('span', {'class': "address"}).update(data.address.full);
		tmp.summaryDiv.insert({'bottom': tmp.addressSpan});
		
		tmp.wrapper.insert({'bottom': tmp.summaryDiv});
		//get current financial year's data
		tmp.incomeAcc = data.currentFY.income;
		tmp.outgoingAcc = data.currentFY.outgoing;
		tmp.profit = tmp.incomeAcc - tmp.outgoingAcc;
		tmp.profit = tmp.profit >= 0 ? appJs.getCurrency(tmp.profit) : new Element('span', {'class': 'minusCurrency'}).update(appJs.getCurrency(tmp.profit));
		tmp.titleDiv = new Element('span');
		tmp.titleDiv.insert({'bottom': new Element('div', {'class': 'titlecontent'}).update('Current FY: ')});
//		tmp.titleDiv.insert({'bottom': new Element('div', {'class': 'daterange'}).update(data.currentFY.date.from + ' ~ ' + data.currentFY.date.to)});
		tmp.valueRow = this.getValueRow(tmp.titleDiv,
				data.boughtValue, 
				appJs.getCurrency(data.setupAcc.sum), 
				appJs.getCurrency(tmp.incomeAcc), 
				appJs.getCurrency(tmp.outgoingAcc), 
				tmp.profit
		);
		tmp.wrapper.insert({'bottom': tmp.valueRow});
		
		//value div
		tmp.incomeAcc = data.incomeAcc.sum;
		tmp.outgoingAcc = data.outgoingAcc.sum;
		tmp.profit = tmp.incomeAcc - tmp.outgoingAcc;
		tmp.profit = tmp.profit >= 0 ? appJs.getCurrency(tmp.profit) : new Element('span', {'class': 'minusCurrency'}).update(appJs.getCurrency(tmp.profit));
		tmp.valueRow = this.getValueRow('Total: ',
				data.boughtValue, 
				appJs.getCurrency(data.setupAcc.sum), 
				appJs.getCurrency(tmp.incomeAcc), 
				appJs.getCurrency(tmp.outgoingAcc), 
				tmp.profit
		);
		tmp.wrapper.insert({'bottom': tmp.valueRow});
		return tmp.wrapper;
	},
	//show add/edit property panel
	showPropertyPanel: function(property) {
		var tmp = {};
	}
};