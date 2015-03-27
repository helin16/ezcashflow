/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_showEditPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'id': 'save-panel'})
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('h4', {'class': 'col-sm-12'}).update(!tmp.me._entity.id || tmp.me._entity.id.blank() ?  'Creating a property:' : 'Editing an AccountEntry: ' + tmp.me._entity.name) })
				.insert({'bottom': new Element('div', {'class': 'col-sm-8'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('Name:') })
						.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'The title of the property.', 'name': 'name', 'save-panel': 'name', 'value': (tmp.me._entity.name ? tmp.me._entity.name : '')}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-4'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('Bought Value:') })
						.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'The price you paid for this property', 'name': 'boughtPrice', 'save-panel': 'boughtPrice', 'value': tmp.me.getCurrency(tmp.me._entity.boughtPrice ? tmp.me._entity.boughtPrice : 0)}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-4'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('Setup Account:') })
						.insert({'bottom': new Element('input', {'class': 'form-control select2', 'placeholder': 'The setup cost of the property, like bank fees, agent fees', 'name': 'setupAcc', 'save-panel': 'setupAccId', 'accTypeId': 4}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-4'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('Income Account:') })
						.insert({'bottom': new Element('input', {'class': 'form-control select2', 'placeholder': 'The income of the property, like rental', 'name': 'incomeAcc', 'save-panel': 'incomeAccId', 'accTypeId': 3}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-4'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('Expense Account:') })
						.insert({'bottom': new Element('input', {'class': 'form-control select2', 'placeholder': 'The expense of the property, like management fees, maintaince fees', 'name': 'expenseAcc', 'save-panel': 'expenseAccId', 'accTypeId': 4}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-12'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('Description:') })
						.insert({'bottom': new Element('textarea', {'class': 'form-control', 'placeholder': 'The description of this property', 'save-panel': 'description'}).update(tmp.me._entity.description ? tmp.me._entity.description : '') })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'control-lable col-sm-1'})
					.insert({'bottom': new Element('span', {'class': 'btn btn-success btn-sm'})
						.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-plus'}) })
						.insert({'bottom': new Element('span', {'class': 'hidden-xs'}).update(' Add files') })
						.insert({'bottom': new Element('input', {'type': 'file', 'class': 'file-uploader', 'name': 'files[]', 'multiple': true}).setStyle({'display': 'none'}) })
						.observe('click', function() {
							$(this).down('.file-uploader').click();
						})
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-11'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': tmp.fileListPanel = new Element('div', {'class': 'file-uploader-results'}) })
						.insert({'bottom': new Element('div', {'class': 'file-uploading-results'}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-sm-12'})
					.insert({'bottom': new Element('div', {'class': 'btn btn-primary pull-right'})
						.update('Save')
						.observe('click', function(){
							tmp.me._saveItem(this);
						})
					})
				})
			});
		if(tmp.me._entity.attachments && tmp.me._entity.attachments.size() > 0) {
			tmp.me._entity.attachments.each(function(attachment) {
				tmp.fileListPanel.insert({'bottom': tmp.me._getFileDiv(attachment) });
			});
		}
		$(tmp.me.getHTMLID('result-div')).update(tmp.newDiv);
		return tmp.me;
	}
	,_saveItem: function() {
		var tmp = {};
		tmp.me = this;
		tmp.data = {'files': []};
		tmp.resultPanel = $(tmp.me.getHTMLID('result-div'));
		tmp.savePanel = tmp.resultPanel.down('#save-panel');
		tmp.savePanel.getElementsBySelector('[save-panel]').each(function(item){
			tmp.field = item.readAttribute('save-panel');
			if(tmp.field === 'boughtPrice') {
				tmp.data[tmp.field] = tmp.me.getValueFromCurrency($F(item));
			} else if(tmp.field === 'files') {
				tmp.data.files.push(item.retrieve('data'));
			} else {
				tmp.data[tmp.field] = $F(item);
			}
		});
		//editing
		if(tmp.me._entity.id && !tmp.me._entity.id.blank()) {
			tmp.data.propertyId = tmp.me._entity.id;
		}

		tmp.loadingDiv = tmp.me._getLoadingDiv().addClassName("panel-body");
		tmp.me.postAjax(tmp.me.getCallbackId('saveItem'), tmp.data, {
			'onLoading': function() {
				tmp.savePanel.insert({'after': tmp.loadingDiv}).hide();
			}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.item || !tmp.result.item.id)
						return;
					tmp.me._entity = tmp.result.item
					tmp.resultPanel.update(new Element('div', {'class': 'text-center'})
						.insert({'bottom': new Element('h4', {'class': 'text-success'})
							.insert({'bottom': new Element('i', {'class': 'fa fa-check-circle fa-6'})})
						})
						.insert({'bottom': new Element('strong', {'class': 'text-success'}).update('"' + tmp.result.item.name + '" saved successfully.')})
					);
					tmp.me._refreshParentWindow(tmp.result.item);
				} catch(e) {
					tmp.me.showModalBox('<strong class="text-danger">Error</strong>', e);
				}
			}
			,'onComplete': function() {
				tmp.loadingDiv.remove();
				tmp.savePanel = tmp.resultPanel.down('#save-panel');
				if(tmp.savePanel)
					tmp.savePanel.show();
			}
		})
		return tmp.me;
	}
	,_refreshParentWindow: function(item) {
		var tmp = {};
		tmp.me = this;
		if(window.parent && window.parent.pageJs && window.parent.pageJs.updateRow) {
			window.parent.pageJs.updateRow(item);
		}
		return tmp.me;
	}
	/**
	 * Getting the file div for this attachment
	 */
	,_getFileDiv: function(attchment) {
		var tmp = {};
		tmp.me = this;
		tmp.fileName = attchment.id && attchment.asset ? attchment.asset.filename : attchment.file.name;
		tmp.newDiv = new Element('div', {'class': 'btn-group file-btn', 'save-panel': 'files'})
	    	.store('data', attchment)
			.setStyle('margin-right: 4px; margin-bottom: 4px;')
	    	.insert({'bottom': new Element('div', {'class': 'btn btn-info btn-sm'}).update( tmp.fileName ) })
	    	.insert({'bottom': new Element('div', {'class': 'btn btn-info btn-sm'})
	        	.insert({'bottom': new Element('span', {'class': 'text-danger'})
	        		.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-remove'}) })
	        	})
	        	.observe('click', function(){
	        		tmp.thisBtn = this;
	        		tmp.confirmDiv = new Element('div')
	        			.insert({'bottom': new Element('p').update('Are you sure you want to delete this file: <strong>' + tmp.fileName + '</strong>?') })
		        		.insert({'bottom': new Element('div')
			        		.insert({'bottom': new Element('span', {'class': 'btn btn-danger'})
			        			.update('YES')
			        			.observe('click', function(){
			        				tmp.fileBtn = $(tmp.thisBtn).up('.file-btn');
			        				tmp.fileBtnData = tmp.fileBtn.retrieve('data');
			        				if(tmp.fileBtnData.id) {
			        					tmp.fileBtnData.active = false;
			        					tmp.fileBtn.store('data', tmp.fileBtnData).hide();
			        				} else {
			        					tmp.fileBtn.remove();
			        				}
			        				tmp.me.hideModalBox();
			        			})
			        		})
			        		.insert({'bottom': new Element('span', {'class': 'btn btn-default pull-right'})
			        			.update('NO')
			        			.observe('click', function(){
			        				tmp.me.hideModalBox();
			        			})
			        		})
		        		})
	        		;
	        		tmp.me.showModalBox('Confirm Deletion:', tmp.confirmDiv, true);
	        	})
	    	});
		return tmp.newDiv;
	}
	,_initFileUploader: function (layout) {
		var tmp = {};
		tmp.me = this;
		tmp.fileInput = layout.down('.file-uploader');
		tmp.uploadingPanel = layout.down('.file-uploading-results');
		tmp.resultPanel = layout.down('.file-uploader-results');
		tmp.me._signRandID(tmp.fileInput);
		jQuery('#' + tmp.fileInput.id).fileupload({
	        url: '/asset/upload',
	        dataType: 'json',
	        add: function (e, data) {
	        	if(data.files && data.files.size() > 0 && tmp.uploadingPanel) {
	        		tmp.uploadingPanel.insert({'bottom': tmp.resultRow = new Element('div', {'class': 'file-list-item row'})
	        			.store('data', data)
		        		.insert({'bottom': new Element('div', {'class': 'col-xs-3'}).update(data.files[0].name) })
		        		.insert({'bottom': new Element('div', {'class': 'col-xs-9'})
		        			.insert({'bottom': new Element('div', {'class': 'progress'})
			        			.insert({'bottom': new Element('div', {'class': 'progress-bar progress-bar-success'}) })
		        			})
		        		})
	        		});
	        	}
	        	tmp.me._signRandID(tmp.resultRow);
	        	data.resultRowId = tmp.resultRow.id;
	        	data.fileName = data.files[0].name;
	        	data.submit();
	        },
	        done: function (e, data) {
	        	jQuery('#' + data.resultRowId).remove();
	        	if(data.result.errors && data.result.errors.size() > 0) {
	        		layout.down('.msg-div')
	        			.update(tmp.me.getAlertBox('Error:', '<strong>Error Occurred when Uploading file: ' + data.fileName + '</strong><br />' + data.result.errors.join(', ')).addClassName('alert-danger'));
	        		return;
	        	}
	        	tmp.result = data.result.resultData;
	        	if(!tmp.result || !tmp.result.file || !tmp.result.file.name)
	        		return;
	        	tmp.resultPanel.insert({'bottom': tmp.me._getFileDiv(tmp.result)});
	        },
	        progress: function (e, data) {
	        	tmp.percentage = parseInt(data.loaded / data.total * 100, 10) + '%';
	            jQuery('#' + data.resultRowId + ' .progress .progress-bar').css(
	                'width',
	                tmp.percentage
	            ).html(tmp.percentage);
	        }
	    }).prop('disabled', !jQuery.support.fileInput)
        	.parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');
		return tmp.me;
	}
	,_initForm: function() {
		var tmp = {};
		tmp.me = this;
		jQuery('#' + tmp.me.getHTMLID('page-form')).bootstrapValidator({
	        message: 'This value is not valid',
	        excluded: ':disabled',
	        feedbackIcons: {
	            valid: 'glyphicon glyphicon-ok',
	            invalid: 'glyphicon glyphicon-remove',
	            validating: 'glyphicon glyphicon-refresh'
	        },
	        fields: {
	        	'name': {
	        		validators: {
	        			notEmpty: {
	                        message: 'Name is required'
	                    }
                    }
	        	}
	        	,'boughtPrice': {
	        		validators: {
                        callback: {
                            message: 'Invalid value',
                            callback: function (value, validator, $field) {
                                tmp.value = tmp.me.getValueFromCurrency(jQuery($field).val());
                                return /^(-)?\d+(\.\d+)?$/.match(tmp.value);
                            }
                        }
                    }
	        	}
	        	,'setupAccId': {
	        		validators: {
	        			callback: {
	        				message: 'Invalid value',
	        				callback: function (value, validator, $field) {
	        					tmp.value = jQuery($field).val();
	        					return /^\d+$/.match(tmp.value);
	        				}
	        			}
	        		}
	        	}
	        }
		})
		.on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
            tmp.me._saveItem();
        })
        .on('error.field.bv', function(e, data) {
        	data.bv.disableSubmitButtons(false);
        })
        .on('success.field.bv', function(e, data) {
        	data.bv.disableSubmitButtons(false);
        })
        ;
		return tmp.me;
	}
	/**
	 * initialize select2
	 */
	,_initSelect2: function (selectBox, accTypeId, preData) {
		var tmp = {};
		tmp.me = this;
		tmp.me._signRandID(selectBox);
		tmp.preLoadedData = null;
		if(preData && preData.id) {
			tmp.preLoadedData = {'id': preData.id, 'text': preData.breadCrumbs.join(' / '), 'data': preData};
		}
		jQuery('#' + selectBox.id).select2({
			 minimumInputLength: 3,
			 multiple: false,
			 allowClear: true,
			 ajax: {
				 delay: 250
				 ,url: '/ajax/getAccounts'
		         ,type: 'POST'
	        	 ,data: function (params) {
	        		 return {"searchTxt": params, 'accTypeIds': [accTypeId]};
	        	 }
				 ,results: function(data, page, query) {
					 tmp.resultMap = {};
					 if(data.resultData && data.resultData.items) {
						 data.resultData.items.each(function(item) {
							 tmp.typeId = item.type.id;
							 if(!tmp.resultMap[tmp.typeId]) {
								 tmp.resultMap[tmp.typeId] = {'text': item.type.name, 'children': []};
							 }
							 tmp.resultMap[tmp.typeId]['children'].push({'id': item.id, 'text': item.breadCrumbs.join(' / ') , 'data': item})
						 });
					 }
					 tmp.result = [];
					 $H(tmp.resultMap).each(function(val){
						 tmp.result.push(val.value);
					 });
		    		 return { 'results' : tmp.result };
		    	 }
				 ,cache: true
			 },
			formatSelection: function(element) {
				tmp.option = element.text;
				if(!element.data)
					return tmp.option;
				return '<div><span class="pull-left">' + tmp.option + '</span><span class="badge pull-right">' + tmp.me.getCurrency(element.data.sumValue) + '</span></div>';
			},
			formatResult : function(result, label, query, escapeMarkup) {
				tmp.option = this.text(result);
				if(!result.data)
					return tmp.option;

				return '<div>' + tmp.option + '<span class="badge pull-right">' + tmp.me.getCurrency(result.data.sumValue) + '</span></div>';
			}
			,formatNoMatches: function() {
				return '<div>No Accounts found.</div>';
			}
		});
		if(tmp.preLoadedData && tmp.preLoadedData.id)
			jQuery('#' + selectBox.id).select2('data', tmp.preLoadedData, true);
		return tmp.me;
	}
	/**
	 * initialising
	 */
	,init: function() {
		var tmp = {};
		tmp.me = this;
		tmp.me._showEditPanel()
			._initFileUploader($(tmp.me.getHTMLID('result-div')))
			._initForm();
		$$('.select2').each(function(selBox){
			tmp.preData = {};
			if(tmp.me._entity && tmp.me._entity.id && tmp.me._entity[$(selBox).name]) {
				tmp.preData = tmp.me._entity[$(selBox).name];
			}
			tmp.me._initSelect2(selBox, $(selBox).readAttribute('accTypeId'), tmp.preData);
		})
		return tmp.me;
	}
});