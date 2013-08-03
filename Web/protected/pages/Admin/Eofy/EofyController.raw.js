//this is the source file for the AssetTypeController
var EofyControllerJS = new Class.create();
EofyControllerJS.prototype = {
	listDivId : '', // the id of the list div
	pagination: { //this is the pagination variable
		pageSize: 30,
		pageNumber: 1
	},
	callbackIds: {}, //the callback ids
	preFixAttachmentID: 'attachments_' //the prefix for the html id of the attachment div

	// constructor
	,initialize : function(listDivId, saveEOFYBtn, delEOFYBtn) {
		this.listDivId = listDivId;
		this.callbackIds.saveEOFYBtn = saveEOFYBtn;
		this.callbackIds.delEOFYBtn = delEOFYBtn;
	}

	//getting the EOFY row
	,_getRowDiv: function(title, item) {
		return new Element('div')
			.insert({'bottom': new Element('span', {'class': 'title'}).update(title) })
			.insert({'bottom': new Element('span', {'class': 'item'}).update(item) });
	}
	
	//getting the EOFY div
	,_getEOFYDiv: function(eofy) {
		var tmp = {};
		tmp.me = this;
		tmp.eofyId = tmp.eofyStart = tmp.eofyEnd = tmp.eofyComments = '';
		tmp.assets = [];
		if(eofy) {
			tmp.eofyId = (eofy.id || '');
			tmp.eofyStart = (eofy.start || '');
			tmp.eofyEnd = (eofy.end || '');
			tmp.eofyComments = (eofy.comments || '');
			tmp.assets = (eofy.assets || [])
		}
		tmp.attachmentDivID = tmp.me.preFixAttachmentID + tmp.eofyId;
		tmp.div = new Element('div', {'class': 'editDiv row roundcorner', 'eofyid': tmp.eofyId, 'id': 'editDiv_' + tmp.eofyId})
			.insert({'bottom': new Element('div')
				.insert({'bottom': new Element('span', {'class': 'inlineblock half'})
					.update(tmp.me._getRowDiv('Start:', new Element('input', {'class': 'inputbox roundcorner datepicker', 'editdiv': 'starttime', 'hour': '00:00:00', 'placeholder': 'Start of the financial year', 'readonly': true, 'value': tmp.eofyStart}) ))
				})
				.insert({'bottom': new Element('span', {'class': 'inlineblock half'})
					.update(tmp.me._getRowDiv('End:', new Element('input', {'class': 'inputbox roundcorner datepicker', 'editdiv': 'endtime', 'hour': '23:59:59', 'placeholder': 'End of the financial year', 'readonly': true, 'value': tmp.eofyEnd}) ))
				})
			})
			.insert({'bottom': tmp.me._getRowDiv('Comments: ', new Element('input', {'class': 'inputbox roundcorner', 'editdiv': 'comments', 'placeholder': 'Comments against this financial year', 'value': tmp.eofyComments}) ) })
			.insert({'bottom': new Element('div').update(new Element('fieldset', {'class': 'filewrapper roundcorner', 'editdiv': 'assets'})
					.update(new Element('legend').update('Attach Files:'))
					.insert({'bottom': new Element('div', {'class': 'assets'}).update(tmp.me._getAssetListDiv(tmp.assets) )
					})
					.insert({'bottom': new Element('div', {'id': tmp.attachmentDivID, 'class': 'attachmentlist', 'editdiv': 'attachements'}) })
				) 
			})
			.insert({'bottom': new Element('div', {'class': 'editbtns'})
				.insert({'bottom': new Element('input', {'type': 'button', 'value': 'save'})
					.observe('click', function() {
						tmp.me._submitEofy(this);
					})
				})
				.insert({'bottom': new Element('input', {'type': 'button', 'value': 'Cancel'})
					.observe('click', function() {
						$(this).up('.editDiv').remove();
					})
				})
			})
			;
		return tmp.div;
	}
	//getting the asset list
	,_getAssetListDiv: function(assets) {
		var tmp = {};
		tmp.div = new Element('div', {'class': 'assets uploadedFileList', 'transinfo': 'assets'});
		assets.each(function(item) {
			tmp.div.insert({'bottom': new Element('div', {'class': 'uploadedfile', 'assetkey': item.assetKey})
				.update(item.filename)
				.insert({'bottom': new Element('span', {'class': 'delFile'}).update('x')
					.observe('click', function() {
						if(!confirm('Are you sure you want to delete this asset?'))
							return;
						$(this).up('.uploadedfile').hide().writeAttribute('delete', true);
					})
				})
			})
		});
		return tmp.div;
	}
	//saving Eofy
	,_submitEofy: function(btn) {
		var tmp = {};
		tmp.me = this;
		
		//collect the data
		tmp.editDiv = $(btn).up('.editDiv');
		tmp.eofy = {'id': tmp.editDiv.readAttribute('eofyid')};
		if(tmp.editDiv.down('[editdiv="starttime"]')) {
			tmp.eofy.start = $F(tmp.editDiv.down('[editdiv="starttime"]'));
			if(tmp.eofy.start.blank()) {
				alert('Start is needed!');
				return;
			}
		}
		if(tmp.editDiv.down('[editdiv="endtime"]')) {
			tmp.eofy.end = $F(tmp.editDiv.down('[editdiv="endtime"]'));
			if(tmp.eofy.end.blank()) {
				alert('End is needed!');
				return;
			}
		}
		if(tmp.editDiv.down('[editdiv="comments"]')) {
			tmp.eofy.comments = $F(tmp.editDiv.down('[editdiv="comments"]'));
		}
		tmp.eofy.attachments = tmp.editDiv.retrieve('fileUploader').uploadedFiles;
		
		tmp.btnDiv = $(btn).up('.editbtns');
		appJs.postAjax(tmp.me.callbackIds.saveEOFYBtn, tmp.eofy, {
			'onLoading': function(sender, param){
				tmp.btnDiv.insert({'top': new Element('span', {'class': 'loading'}).update('Loading') })
					.getElementsBySelector('input[type=button]').each(function(item) {
						item.hide();
					});
    		},
	    	'onComplete': function(sender, param){
	    		tmp.btnDiv.down('.loading').remove();
	    		tmp.btnDiv.getElementsBySelector('input[type=button]').each(function(item) {
	    			item.show();
	    		});
	    		try {
	    			tmp.result = appJs.getResp(param, false, true);
	    			if(!tmp.result.id)
	    				throw 'Invalid eofy returns!';
	    			
	    			tmp.eofyRow = $$('#' + tmp.me.listDivId + ' .eofyrow[eofyid=' + tmp.result.id + ']');
	    			if(tmp.eofyRow.size() > 0) { //this editing
	    				tmp.firstRow = tmp.eofyRow.first();
	    				tmp.eofyRow.first().replace(tmp.me._getEOFYRow(tmp.result, true).addClassName(tmp.firstRow.hasClassName('even') ? 'even' : 'odd'));
	    			} else { //this is creating new 
	    				tmp.eofyRows = $$('#' + tmp.me.listDivId + ' .eofyrow[eofyid]');
	    				if(tmp.eofyRows.size() > 0) {
	    					tmp.firstRow = $$('#' + tmp.me.listDivId + ' .eofyrow[eofyid]').first();
	    					tmp.firstRow.insert({'before': tmp.me._getEOFYRow(tmp.result, true).addClassName(tmp.firstRow.hasClassName('even') ? 'odd' : 'even')});
	    				} else {
	    					$$('#' + tmp.me.listDivId + ' .eofyrow.header').first()
	    						.insert({'after': tmp.me._getEOFYRow(tmp.result, true).addClassName('odd')});
	    				}
	    			}
	    			tmp.editDiv.remove();
	    		} catch(e) {
	    			alert(e);
	    		}
	    	}
		});
	}
	
	//showing the createEOFY
	,createEOFY: function(btn, eofy) {
		var tmp = {};
		tmp.me = this;
		//clear all other edit divs
		$$('.editDiv').each(function(item) {
			item.remove();
		});
		//insert the new div
		tmp.newDiv = tmp.me._getEOFYDiv(eofy);
		$(tmp.me.listDivId).insert({'top': tmp.newDiv});
		tmp.me.initialDatePicker('#' + tmp.newDiv.id + ' .datepicker');
		tmp.newDiv.store('fileUploader', new FileUploaderJs(tmp.me.preFixAttachmentID).initFileUploader());
	}
	
	//initialising the date picker
	,initialDatePicker: function(selector) {
		var tmp = {};
		$$(selector).each(function(item){
			tmp.hourString = item.readAttribute('hour');
			new Prado.WebUI.TDatePicker({'ID': item,'InputMode':'TextBox','Format':'yyyy-MM-dd ' + tmp.hourString,'FirstDayOfWeek':1,'ClassName':'datePicker','CalendarStyle':'default','FromYear':2007,'UpToYear':2030});
		});
	}
	
	//getting the list of EOFYS
	,getEOFYs : function(getListCallBack) {
		var tmp = {};
		tmp.me = this;

		appJs.postAjax(getListCallBack, {'pagination': tmp.me.pagination} , {
			'onLoading' : function(sender, param) {
					$(tmp.me.listDivId).update('<img src="/contents/images/loading.gif" />');
			},
			'onComplete' : function(sender, param) {
				try {
					tmp.result = appJs.getResp(param, false, true);
					tmp.count = tmp.result.size();
					
					//we need to clean up the previouse result
					$(tmp.me.listDivId).update('')
						.insert({'bottom': tmp.me._getEOFYRow({'start': 'Start', 'end': 'End', 'comments': 'Comments'}).addClassName('header') });
					//if we can't find any Eofys
					if (tmp.count === 0) {
						alert('No EOFYs Found!');
						return;
					}
					
					// display the result rows
					for(tmp.i = 0; tmp.i < tmp.count; tmp.i++) {
						tmp.rowNo = (pageJs.pagination.pageNumber - 1) * pageJs.pagination.pageSize + tmp.i + 1;
						$(tmp.me.listDivId).insert({'bottom': tmp.me._getEOFYRow(tmp.result[tmp.i], true).addClassName(tmp.rowNo % 2 === 0 ? 'even' : 'odd') });
					}
				} catch(e) {
					alert(e);
				}
			}
		});
	}
	
	//getting the eofy row
	,_getEOFYRow: function (eofy, showBtns) {
		var tmp = {};
		tmp.me = this;
		//getting all assets
		tmp.assetsDiv = new Element('div', {'class': 'assetsrow uploadedFileList'});
		if(eofy.assets) {
			eofy.assets.each(function(asset) {
				tmp.assetsDiv.insert({'bottom': new Element('span', {'class': 'assetlink uploadedfile', 'assetkey': asset.assetKey})
					.update(asset.filename)
					.observe('click', function() {
						window.open('/asset/' + $(this).readAttribute('assetkey'));
					})
				});
			});
		}
		
		//getting btns
		tmp.btnDiv = new Element('div', {'class': 'btns inlineblock'});
		if(showBtns === true) {
			tmp.btnDiv.insert({'bottom': new Element('div', {'class': 'btn editbtn'}).update('Edit')
				.observe('click', function(){
					tmp.me._editEOFY(this);
				})
			})
			.insert({'bottom': new Element('div', {'class': 'btn delbtn'}).update('Delete')
				.observe('click', function(){
					if(!confirm('Are you sure you want to delete this EOFY?'))
						return;
					tmp.me._deleteEOFY(this);
				})
			});
		}
		
		tmp.div = new Element('div', {'class': 'row eofyrow'})
			.insert({'bottom': new Element('div', {'class': 'content inlineblock'})
				.insert({'bottom': new Element('div', {'class': 'titlerow'})
					.update(new Element('span', {'class': 'start inlineblock'}).update(eofy.start)) 
					.insert({'bottom': new Element('span', {'class': 'end inlineblock'}).update(eofy.end) }) 
					.insert({'bottom': new Element('span', {'class': 'comments inlineblock'}).update(eofy.comments) }) 
				})
				.insert({'bottom': tmp.assetsDiv})
			})
			.insert({'bottom': tmp.btnDiv})
			.store('eofy', eofy);
		if(showBtns === true) {
			tmp.div.writeAttribute('eofyid', (eofy.id ? eofy.id : ''));
		}
		return tmp.div;
	}
	
	//show the edit panel of the EOFY
	,_editEOFY: function(btn) {
		var tmp = {};
		tmp.me = this;
		//clear all other edit divs
		$$('.editDiv').each(function(item) {
			item.remove();
		});
		tmp.eofyRow = $(btn).up('.eofyrow');
		tmp.eofy = tmp.eofyRow.retrieve('eofy');
		tmp.newDiv = tmp.me._getEOFYDiv(tmp.eofy).addClassName('editInLine')
		tmp.eofyRow.insert({'bottom': tmp.newDiv });
		tmp.me.initialDatePicker('#' + tmp.newDiv.id + ' .datepicker');
		tmp.newDiv.store('fileUploader', new FileUploaderJs(tmp.me.preFixAttachmentID + tmp.eofy.id).initFileUploader());
	}
	//deleting the EOFY
	,_deleteEOFY: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.row = $(btn).up('.eofyrow');
		tmp.eofy = tmp.row.retrieve('eofy');
		appJs.postAjax(tmp.me.callbackIds.saveEOFYBtn, tmp.eofy, {
			'onLoading': function(sender, param){
				tmp.row.hide();
    		},
	    	'onComplete': function(sender, param){
	    		try {
	    			tmp.result = appJs.getResp(param, false, true);
	    			if(!tmp.result.id)
	    				throw 'Invalid eofy returns!';
	    			tmp.row.remove();
	    		} catch(e) {
	    			tmp.row.show();
	    			alert(e);
	    		}
	    	}
		});
	}
};