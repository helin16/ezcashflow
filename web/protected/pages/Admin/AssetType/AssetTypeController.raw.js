//this is the source file for the AssetTypeController
var AssetTypeJs = new Class.create();
AssetTypeJs.prototype = {
	canvas: '', // the div that holds the list
	callbackIds: {
		'list': '',
		'edit': ''
	},
	//constructor
	initialize: function (canvasId, listCBId, editCBId) {
		this.canvasId = canvasId;
		this.callbackIds.list = listCBId;
		this.callbackIds.edit = editCBId;
	},
	
	//get the list
	getList: function(noLoading) {
		var tmp = {};
		tmp.canvasId = this.canvasId;
		tmp.loading = (noLoading === true ? false : true);
		appJs.postAjax(this.callbackIds.list, {}, {
    		'onLoading': function(sender, param){
    			if(tmp.loading === true)
    				$(tmp.canvasId).update('<img src="/contents/images/loading.gif" />');
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		tmp.count = tmp.result.size();
	    		if(tmp.count === 0) {
	    			$(tmp.canvasId).update('No AssetType Found!');
	    			return;
	    		}
	    		$(tmp.canvasId).update('');
	    		//display the result rows
	    		for(tmp.i = 0; tmp.i < tmp.count; tmp.i++) {
	    			tmp.newRow = assetJs.getRow(tmp.result[tmp.i], tmp.i);
	    			$(tmp.canvasId).insert({'bottom': tmp.newRow});
	    		}
	    	}
    	});
	},
	//get the transaction row dom object
	getRow: function(data, rowNo) {
		var tmp = {};
		tmp.newRow = new Element('div', {'class': 'row ' + (rowNo % 2 === 0 ? 'even' : 'odd'), 'assetId': data.id, 'rowno': rowNo});
		tmp.newRowContent = new Element('span', {'class': 'conent'});
		
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'fromacc inline'}).update('Name: ' + data.type)});
		tmp.newRowContent.insert({'bottom': new Element('span', {'class': 'toacc inline'}).update('Path: ' + data.path)});
		tmp.newRow.insert({'bottom': tmp.newRowContent});
		tmp.newRowBtns = new Element('span', {'class': 'btns'});
		tmp.newRowBtns.insert({'bottom': new Element('a', {'class': 'btn', 'href': 'javascript: void(0);'})
			.update('Edit')
			.observe('click', function(){
				assetJs.edit(this, data);
			})
		});
//		tmp.newRowBtns.insert({'bottom': new Element('a', {'class': 'btn', 'href': 'javascript: void(0);'})
//			.update('Delete')
//			.observe('click', function(){
//				pageJs.delTrans(trans.id);
//			})
//		});
		tmp.newRow.insert({'bottom': tmp.newRowBtns});
		
		return tmp.newRow;
	},
	/**
	 * show the edit panel for a transaction
	 */
	edit: function(btn, data) {
		var tmp = {};
		//removing all opened editing panel
		$$('.newAccDiv').each(function(item){ item.remove();});
		
		tmp.newDivId = 'edit_' + data.id
		tmp.newDiv = new Element('div', {'id': tmp.newDivId, 'class': 'newAccDiv'});
    	tmp.newRow= new Element('div', {'class': 'newRow'})
	    	.insert({'bottom': new Element('span', {'class': 'label'}).update('Name: ')})
	    	.insert({'bottom': new Element('span', {'class': 'typein'}).update(new Element('input', {'editinfo': 'name', 'type': 'text', 'class': 'transcomments inputbox', 'placeholder': 'Asset Type Name', 'value': data.type}))});
    	tmp.newDiv.insert({'bottom': tmp.newRow});
    	tmp.newRow1= new Element('div', {'class': 'newRow'})
	    	.insert({'bottom': new Element('span', {'class': 'label'}).update('Path: ')})
	    	.insert({'bottom': new Element('span', {'class': 'typein'}).update(new Element('input', {'editinfo': 'path', 'type': 'text', 'class': 'transcomments inputbox', 'placeholder': 'Asset Type Path', 'value': data.path}))});
    	tmp.newDiv.insert({'bottom': tmp.newRow1});
    	
    	tmp.newRow5 = new Element('div', {'class': 'newRow'})
    		.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Save'}).observe('click', function(){
    			assetJs.save(this, data.id)
    		})})
	    	.insert({'bottom': new Element('input', {'class': 'submitBtn', 'type': 'button', 'value': 'Cancel', 'style': 'float:right;'}).observe('click', function(){
	    		$(tmp.newDivId).remove();
	    	})});
		tmp.newDiv.insert({'bottom': tmp.newRow5});
		$(btn).up('.row').insert({'bottom': tmp.newDiv});
	},
	//event to save the assettype
	save: function(btn, dataId) {
		var tmp = {};
		tmp.row = $(btn).up('.newRow');
    	tmp.savingPanel = tmp.row.up('.newAccDiv');
    	tmp.savingInfo = new Element('div').update('saving ...');
    	
		//collecting info
    	tmp.editInfo = {'id': dataId};
    	tmp.hasError = false;
    	tmp.savingPanel.getElementsBySelector('.newAccError').each(function(item){item.remove();});
    	tmp.currency = /^\d*(\.|)\d{0,2}$/g;
    	tmp.savingPanel.getElementsBySelector('[editinfo]').each(function(item){
    		tmp.field = item.readAttribute('editinfo');
    		tmp.value = $F(item);
    		if (tmp.field === 'name' && tmp.value.blank())
    		{
    			item.insert({'after': new Element('span',{'class': 'newAccError'}).update('Name is needed!')});
    			tmp.hasError = true;
    		}
    		if (tmp.field === 'path' && tmp.value.blank())
    		{
    			item.insert({'after': new Element('span',{'class': 'newAccError'}).update('Path is needed!')});
    			tmp.hasError = true;
    		}
    		tmp.editInfo[tmp.field] = tmp.value;
    	});
    	if(tmp.hasError === true) {
    		return;
    	}
    	appJs.postAjax(this.callbackIds.edit, tmp.editInfo, {
    		'onLoading': function(sender, param){
    			tmp.row.hide().insert({'after': tmp.savingInfo});
    		},
	    	'onComplete': function(sender, param){
	    		try{
	    			tmp.result = appJs.getResp(param, false, true);
	    			if(tmp.result.id === undefined || tmp.result.id.blank())
	    				throw 'System Error:Invalid id!';
	    			
	    			tmp.transRow = $(btn).up('.row');
	    			tmp.transRow.replace( assetJs.getRow(tmp.result, tmp.transRow.readAttribute('rowno'))).scrollTo();
    				//remove the saving panel
    				tmp.savingPanel.remove();
	    		} catch(e) {
	    			tmp.savingInfo.remove();
	    			tmp.row.show();
	    			alert(e);
	    		}
	    	}
    	});
	}
};