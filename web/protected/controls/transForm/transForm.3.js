var TransFormJs = new Class.create();
TransFormJs.prototype = {
	searchCallbackId: ''
	,saveTransCallbackId: ''
	,_saveSuccFunc: ''
	,_accTypeIds: {'from': [], 'to': []}
	//constructor
	,initialize: function( _pageJs, _jQueryFormSelector) {
		this._pageJs = _pageJs;
		this._jQueryFormSelector = _jQueryFormSelector;
		this.searchCallbackId = TransFormJs.searchCallbackId;
		this.saveTransCallbackId = TransFormJs.saveTransCallbackId;
	}
	,setAccTypeIds: function(fromAccTypeIds, toTypeIds) {
		var tmp = {};
		tmp.me = this;
		tmp.me._accTypeIds.from = fromAccTypeIds;
		tmp.me._accTypeIds.to = toTypeIds;
		return tmp.me;
	}
	,setSaveSuccFunc: function (_saveSuccFunc) {
		var tmp = {};
		tmp.me = this;
		tmp.me._saveSuccFunc = _saveSuccFunc;
		return tmp.me;
	}
	,_getFormGroup: function(control, label) {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'form-group'})
			.insert({'bottom': label ? label : ''})
			.insert({'bottom': control })
		return tmp.newDiv;
	}
	,_setTransaction: function(_trans) {
		var tmp = {};
		tmp.me = this;
		tmp.me._trans = _trans;
		return tmp.me;
	}
	,_refreshParentWindow: function(transactions) {
		var tmp = {};
		tmp.me = this;
		if(transactions.size() > 0 && window.parent && window.parent.pageJs && window.parent.pageJs.updateTransRow) {
			transactions.each(function(transaction) {
				window.parent.pageJs.updateTransRow(transaction);
			});
		}
		return tmp.me;
	}
	,_saveTrans: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.me._pageJs._signRandID(btn);
		tmp.inputPanel = $(btn).up('.trans-input-panel');
		tmp.data = {'files': []};
		tmp.inputPanel.getElementsBySelector('[input-panel]').each(function(item){
			tmp.fieldName = item.readAttribute('input-panel').strip();
			if(tmp.fieldName === 'files') {
				tmp.data['files'].push(item.retrieve('data'));
			} else if (tmp.fieldName === 'logDate') {
				tmp.dateBox = $(item);
				tmp.me._pageJs._signRandID(tmp.dateBox);
				tmp.data['logDate'] = jQuery('#' + tmp.dateBox.id).data('DateTimePicker').date().utc().format();
			}
			else
				tmp.data[tmp.fieldName] = $F(item);
		});
		tmp.me._pageJs.postAjax(tmp.me.saveTransCallbackId, tmp.data, {
			'onLoading': function() {
				tmp.me._pageJs._signRandID(tmp.inputPanel)
				jQuery('#' + tmp.inputPanel.id + ' .msg-div').html('');
				jQuery('#' + btn.id).button('loading');
			}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me._pageJs.getResp(param, false, true);
					if(!tmp.result || !tmp.result.items || tmp.result.items.size() !== 2)
						return;
					tmp.me.resetForm();
					tmp.me._refreshParentWindow(tmp.result.items);
					jQuery('#' + tmp.inputPanel.id + ' .msg-div').html(tmp.me._pageJs.getAlertBox('Saved Successfully!', '').addClassName('alert-success'));
					if(typeof(tmp.me._saveSuccFunc) === 'function')
						tmp.me._saveSuccFunc(tmp.result);
				} catch (e) {
					jQuery('#' + tmp.inputPanel.id + ' .msg-div').html(tmp.me._pageJs.getAlertBox('Error: ', e).addClassName('alert-danger'));
				}
			}
			,'onComplete': function(){
				jQuery('#' + btn.id).button('reset');
			}
		});
		return tmp.me;
	}
	,_getInputPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'trans-input-panel'})
			.insert({'bottom': new Element('div', {'class': 'panel-body form-horizontal'})
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
						.insert({'bottom': new Element('div', {'class': 'input-group date'})
							.insert({'bottom': new Element('span', {'class': 'input-group-addon'})
								.insert({'bottom': new Element('i', {'class': 'glyphicon glyphicon-calendar'}) })
							})
							.insert({'bottom': new Element('input', {'type': 'text', 'class': 'form-control', 'input-panel': 'logDate', 'placeholder': 'DD/MM/YYYY HH:MM:SS', 'name': 'log_date' })
								.observe('click', function() {
									$(this).up('.date').down('.input-group-addon').click();
								})
							})
						})
					, new Element('label', {'class': 'control-label col-sm-2'}).update('Log Date:')
				) })
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
						.insert({'bottom': new Element('input', {'class': 'form-control', 'input-panel': 'fromAccId', 'placeholder': 'Spending from:', 'name': 'from_acc_id'}) })
					, new Element('label', {'class': 'control-label col-sm-2'}).update('From:')
				) })
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
						.insert({'bottom': new Element('input', {'class': 'form-control', 'input-panel': 'toAccId', 'placeholder': 'Spending onto:', 'name': 'to_acc_id'}) })
					, new Element('label', {'class': 'control-label col-sm-2'}).update('To:')
				) })
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
					.insert({'bottom': new Element('input', {'class': 'form-control', 'input-panel': 'amount', 'placeholder': 'The amount', 'name': 'amount'}) })
					, new Element('label', {'class': 'control-label col-sm-2'}).update('Amount:')
				) })
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
						.insert({'bottom': new Element('input', {'class': 'form-control', 'input-panel': 'comments', 'placeholder': 'Some comments for this transaction', 'name': 'comments'}) })
					, new Element('label', {'class': 'control-label col-sm-2'}).update('Comments:')
				) })
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
						.insert({'bottom': new Element('div', {'class': 'file-uploader-results'}) })
						.insert({'bottom': new Element('div', {'class': 'file-uploading-results'}) })
					, new Element('div', {'class': 'control-lable col-sm-2'})
						.insert({'bottom': new Element('span', {'class': 'btn btn-success btn-sm'})
							.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-plus'}) })
							.insert({'bottom': new Element('span', {'class': 'hidden-xs'}).update(' Add files') })
							.insert({'bottom': new Element('input', {'type': 'file', 'class': 'file-uploader', 'name': 'files[]', 'multiple': true}).setStyle({'display': 'none'}) })
							.observe('click', function() {
								$(this).down('.file-uploader').click();
							})
						})
				) })
				.insert({'bottom': new Element('div', {'class': 'col-sm-10 col-sm-offset-2 msg-div'}) })
				.insert({'bottom': new Element('div', {'class': 'col-sm-10 col-sm-offset-2'})
						.insert({'bottom': new Element('button', {'class': 'btn btn-primary col-xs-12 col-sm-6 save-trans-btn', 'type': 'submit', 'data-loading-text': 'saving ...'}).update('Save') })
				})
			});
		return tmp.newDiv;
	}
	,resetForm: function() {
		var tmp = {};
		tmp.me = this;
		tmp._inputPane =$(tmp.me._inputPanel);
		jQuery(tmp.me._jQueryFormSelector).data('bootstrapValidator').resetForm(true);
		tmp.me._initSelect2(tmp._inputPane.down('[input-panel="fromAccId"]'), 'from')
			._initSelect2(tmp._inputPane.down('[input-panel="toAccId"]'), 'to');
		tmp._inputPane.down('.file-uploader-results').update('');
		tmp._inputPane.down('.file-uploading-results').update('');
		tmp._inputPane.down('.msg-div').update('');
		tmp.logDateBox = tmp._inputPane.down('[input-panel="logDate"]');
		if(tmp.logDateBox)
			tmp.logDateBox.setValue(tmp.logDateBox.retrieve('originalDate'));
		return tmp.me;
	}
	,_initValidator: function(_inputPane) {
		var tmp = {};
		tmp.me = this;
		tmp._inputPane = _inputPane;
		tmp.me._pageJs._signRandID(tmp._inputPane);
		jQuery(tmp.me._jQueryFormSelector).bootstrapValidator({
	        message: 'This value is not valid',
	        excluded: ':disabled',
	        feedbackIcons: {
	            valid: 'glyphicon glyphicon-ok',
	            invalid: 'glyphicon glyphicon-remove',
	            validating: 'glyphicon glyphicon-refresh'
	        },
	        fields: {
	        	'log_date': {
	        		validators: {
	        			notEmpty: {
	        				message: 'Date is required.'
	        			}
	        		}
	        	}
	        	,'from_acc_id': {
	        		validators: {
                        callback: {
                            message: 'Please Select a From Account.',
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                tmp.options = validator.getFieldElements('from_acc_id').val();
                                return (tmp.options != null && tmp.options.length > 0);
                            }
                        }
                    }
	        	}
	        	,'to_acc_id': {
	        		validators: {
	        			callback: {
                            message: 'Please Select a To Account.',
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                tmp.options = validator.getFieldElements('to_acc_id').val();
                                return (tmp.options != null && tmp.options.length > 0);
                            }
                        }
	        		}
	        	}
	        	,'amount': {
	        		validators: {
	        			callback: {
	        				message: 'Please provide a valid amount.',
	        				callback: function(value, validator, $field) {
	        					tmp.value = tmp.me._pageJs.getValueFromCurrency(validator.getFieldElements('amount').val());
	        					return /^(-)?\d+(\.\d+)*$/.match(tmp.value);
	        				}
	        			}
	        		}
	        	}
	        	,'comments': {}
	        }
		})
		.on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();
        })
        .on('error.field.bv', function(e, data) {
        	data.bv.disableSubmitButtons(false);
        })
        .on('success.field.bv', function(e, data) {
        	data.bv.disableSubmitButtons(false);
        })
        .on('click', '.save-trans-btn', function(e){
        	jQuery('#' + tmp._inputPane.id + ' .msg-div').html('');
            if(jQuery(tmp.me._jQueryFormSelector).bootstrapValidator('validate').data('bootstrapValidator').isValid()) {
            	tmp.me._saveTrans(tmp._inputPane.down('.save-trans-btn'));
            }
        })
        .find('[name="log_date"]')
        	.change(function(e) {
        		jQuery(tmp.me._jQueryFormSelector).bootstrapValidator('revalidateField', 'log_date');
        	})
        	.end()
    	.find('[name="from_acc_id"]')
        	.change(function(e) {
        		jQuery(tmp.me._jQueryFormSelector).bootstrapValidator('revalidateField', 'from_acc_id');
        	})
        	.end()
    	.find('[name="to_acc_id"]')
        	.change(function(e) {
        		jQuery(tmp.me._jQueryFormSelector).bootstrapValidator('revalidateField', 'to_acc_id');
        	})
        	.end()
        ;
		return tmp.me;
	}
	,_getFileDiv: function(attachment) {
		var tmp = {};
		tmp.me = this;
		tmp.fileName = attachment.id && attachment.asset ? attachment.asset.filename : attachment.file.name;
		tmp.newDiv = new Element('div', {'class': 'btn-group file-btn', 'input-panel': 'files'})
	    	.store('data', attachment)
			.setStyle('margin-right: 4px; margin-bottom: 4px;')
	    	.insert({'bottom': new Element('div', {'class': 'btn btn-info btn-sm'}).update(tmp.fileName) })
	    	.insert({'bottom': new Element('div', {'class': 'btn btn-info btn-sm'})
	        	.insert({'bottom': new Element('span', {'class': 'text-danger'})
	        		.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-remove'}) })
	        	})
	        	.observe('click', function(){
	        		tmp.thisBtn = this;
	        		tmp.fileBtn = $(this).up('.file-btn');
	        		tmp.fileBtnData = tmp.fileBtn.retrieve('data');
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
			        				tmp.me._pageJs.hideModalBox();
			        			})
			        		})
			        		.insert({'bottom': new Element('span', {'class': 'btn btn-default pull-right'})
			        			.update('NO')
			        			.observe('click', function(){
			        				tmp.me._pageJs.hideModalBox();
			        			})
			        		})
		        		})
	        		;
	        		tmp.me._pageJs.showModalBox('Confirm Deletion:', tmp.confirmDiv, true);
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
		tmp.me._pageJs._signRandID(tmp.fileInput);
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
	        	tmp.me._pageJs._signRandID(tmp.resultRow);
	        	data.resultRowId = tmp.resultRow.id;
	        	data.fileName = data.files[0].name;
	        	data.submit();
	        },
	        done: function (e, data) {
	        	jQuery('#' + data.resultRowId).remove();
	        	if(data.result.errors && data.result.errors.size() > 0) {
	        		layout.down('.msg-div')
	        			.update(tmp.me._pageJs.getAlertBox('Error:', '<strong>Error Occurred when Uploading file: ' + data.fileName + '</strong><br />' + data.result.errors.join(', ')).addClassName('alert-danger'));
	        		return;
	        	}
	        	tmp.result = data.result.resultData;
	        	if(!tmp.result || !tmp.result.file || !tmp.result.file.name)
	        		return;
	        	tmp.resultPanel.insert({'bottom': tmp.me._getFileDiv(tmp.result)	});
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
	,_initSelect2: function (selectBox, type) {
		var tmp = {};
		tmp.me = this;
		tmp.me._pageJs._signRandID(selectBox);
		jQuery('#' + selectBox.id).select2({
			 minimumInputLength: 3,
			 multiple: false,
			 allowClear: true,
			 ajax: {
				 delay: 250
				 ,url: '/ajax/getAccounts'
		         ,type: 'POST'
	        	 ,data: function (params) {
	        		 return {"searchTxt": params, 'accTypeIds': tmp.me._accTypeIds[type], 'isSumAcc': 0};
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
			 }
			,formatSelection: function(element) {
				tmp.option = element.text;
				if(!element.data)
					return tmp.option;
				return '<div><span class="pull-left">' + tmp.option + '</span><span class="badge pull-right">' + tmp.me._pageJs.getCurrency(element.data.sumValue) + '</span></div>';
			}
			,formatResult : function(result, label, query, escapeMarkup) {
				tmp.markup = [];
				tmp.option = this.text(result);
				if(!result.data)
					return tmp.option;

				return '<div>' + tmp.option + '<span class="badge pull-right">' + tmp.me._pageJs.getCurrency(result.data.sumValue) + '</span></div>';
			}
			,formatNoMatches: function() {
				return '<div><a href="javascript: void(0);" target="_BLANK" onclick="window.open(' + "'/accounts.html'" + ');">No Accounts Found, create one?</a></div>';
			}
		});
		tmp.preSelectedAcc = selectBox.retrieve('preSelectedAcc');
		if(tmp.preSelectedAcc && tmp.preSelectedAcc.id) {
			jQuery('#' + selectBox.id).select2('data', {'id': tmp.preSelectedAcc.id, 'text': tmp.preSelectedAcc.breadCrumbs.join(' / '), 'data': tmp.preSelectedAcc}, true);
		}
		return tmp.me;
	}
	,_initDatePicker: function(inputBox) {
		var tmp = {};
		tmp.me = this;
		tmp.me._pageJs._signRandID(inputBox);
		jQuery('#' + inputBox.id).datetimepicker({
			'format': 'DD/MMM/YYYY HH:mm A'
		});
		return tmp.me;
	}
	,render: function(_inputPanel, _trans) {
		var tmp = {};
		tmp.me = this;
		tmp.me._setTransaction(_trans);
		$(tmp.me._inputPanel = _inputPanel).update(tmp._inputPane = tmp.me._getInputPanel());
		tmp.logDate = moment(new Date()).format('DD/MMM/YYYY HH:mm A');
		tmp.fromAcc = tmp.toAcc = {};
		if(tmp.me._trans && tmp.me._trans.id) {
			tmp.logDate = moment(tmp.me._pageJs.loadUTCTime(tmp.me._trans.logDate)).format('DD/MMM/YYYY HH:mm A');
			tmp.fromAcc = tmp.me._trans.accounts.from;
			tmp.toAcc = tmp.me._trans.accounts.to;
			tmp._inputPane.down('[input-panel="amount"]').setValue(tmp.me._pageJs.getCurrency(Math.abs(tmp.me._trans.value)));
			tmp._inputPane.down('[input-panel="comments"]').setValue(tmp.me._trans.description);
			tmp._inputPane.insert({'bottom': new Element('input', {'type': 'hidden', 'value': tmp.me._trans.groupId, 'input-panel': 'groupId'})});
			if(tmp.me._trans.attachments && tmp.me._trans.attachments.size() > 0) {
				tmp.me._trans.attachments.each(function(attachment) {
					tmp._inputPane.down('.file-uploader-results').insert({'bottom': tmp.me._getFileDiv(attachment) });
				});
			}
		}
		tmp.me
			._initDatePicker(tmp._inputPane.down('[input-panel="logDate"]').store('originalDate', tmp.logDate).setValue(tmp.logDate))
			._initFileUploader(tmp._inputPane)
			._initSelect2(tmp._inputPane.down('[input-panel="fromAccId"]').store('preSelectedAcc', tmp.fromAcc), 'from')
			._initSelect2(tmp._inputPane.down('[input-panel="toAccId"]').store('preSelectedAcc', tmp.toAcc), 'to')
			._initValidator(tmp._inputPane);
		return tmp.me;
	}
};