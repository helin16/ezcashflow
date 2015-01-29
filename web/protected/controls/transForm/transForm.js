var transFormJs = new Class.create();
transFormJs.prototype = {
	_searchAccUrl: '/ajax/getAccounts'
	//constructor
	,initialize: function() {}
	,init: function(_inputPanel, _pageJs, _jQueryFormSelector) {
		var tmp = {};
		tmp.me = this;
		tmp.me._pageJs = _pageJs;
		tmp.me._jQueryFormSelector = _jQueryFormSelector;
		$(_inputPanel).update(tmp._inputPane = tmp.me._getInputPanel());
		tmp.me._initFileUploader(tmp._inputPane)
			._initSelect2(tmp._inputPane)
			._initValidator(tmp.me._jQueryFormSelector);
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
	,_getInputPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'trans-input-panel'})
			.insert({'bottom': new Element('div', {'class': 'panel-body form-horizontal'})
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
						.insert({'bottom': new Element('input', {'class': 'form-control', 'input-panel': 'from-acc-id', 'placeholder': 'Spending from:', 'name': 'from_acc_id'}) })
					, new Element('label', {'class': 'control-label col-sm-2 hidden-xs'}).update('From:')
				) })
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
						.insert({'bottom': new Element('input', {'class': 'form-control', 'input-panel': 'to-acc-id', 'placeholder': 'Spending onto:', 'name': 'to_acc_id'}) })
					, new Element('label', {'class': 'control-label col-sm-2 hidden-xs'}).update('To:')
				) })
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
					.insert({'bottom': new Element('input', {'class': 'form-control', 'input-panel': 'amount', 'placeholder': 'The amount', 'name': 'amount'}) })
					, new Element('label', {'class': 'control-label col-sm-2 hidden-xs'}).update('Amount:')
				) })
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
						.insert({'bottom': new Element('input', {'class': 'form-control', 'input-panel': 'comments', 'placeholder': 'Some comments for this transaction'}) })
					, new Element('label', {'class': 'control-label col-sm-2 hidden-xs'}).update('Comments:')
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
				.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10 col-sm-offset-2'})
						.insert({'bottom': new Element('button', {'class': 'btn btn-primary col-sm-6', 'type': 'submit'}).update('Save') })
				) })
			});
		return tmp.newDiv;
	}
	,_initValidator: function() {
		var tmp = {};
		tmp.me = this;
		jQuery(tmp.me._jQueryFormSelector).bootstrapValidator({
	        message: 'This value is not valid',
	        excluded: ':disabled',
	        feedbackIcons: {
	            valid: 'glyphicon glyphicon-ok',
	            invalid: 'glyphicon glyphicon-remove',
	            validating: 'glyphicon glyphicon-refresh'
	        },
	        fields: {
	        	'from_acc_id': {
	        		validators: {
                        callback: {
                            message: 'Please Select a From Account.',
                            callback: function(value, validator, $field) {
                                // Get the selected options
                                tmp.options = validator.getFieldElements('from_acc_id').val();
                                return (tmp.options != null && tmp.options.length === 1);
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
                                return (tmp.options != null && tmp.options.length === 1);
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
        ;
		return tmp.me;
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
		        		.insert({'bottom': new Element('div', {'class': 'col-xs-3'}).update(data.files[0].name) })
		        		.insert({'bottom': new Element('div', {'class': 'col-xs-9'})
		        			.insert({'bottom': new Element('div', {'class': 'progress'})
			        			.insert({'bottom': new Element('div', {'class': 'progress-bar progress-bar-success'}) })
		        			})
		        		})
	        		});
	        	}
	        	tmp.me._pageJs_signRandID(tmp.resultRow);
	        	data.resultRowId = tmp.resultRow.id;
	        	data.submit();
	        },
	        done: function (e, data) {
	        	tmp.result = data.result.resultData;
	        	if(!tmp.result || !tmp.result.file.name)
	        		return;
	        	tmp.resultPanel.insert({'bottom': new Element('div', {'class': 'btn-group', 'input-panel': 'files'}).setStyle('margin-right: 4px; margin-bottom: 4px;').store('file', tmp.result)
		        	.insert({'bottom': new Element('div', {'class': 'btn btn-info btn-sm'}).update(tmp.result.file.name) })
		        	.insert({'bottom': new Element('div', {'class': 'btn btn-info btn-sm'})
			        	.insert({'bottom': new Element('span', {'class': 'text-danger'})
			        		.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-remove'}) })
			        	})
		        	})
	        	});
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
	,_initSelect2: function (layout) {
		var tmp = {};
		tmp.me = this;
		tmp.fromAccBox = layout.down('[input-panel="from-acc-id"]');
		tmp.me._pageJs._signRandID(tmp.fromAccBox);
		jQuery('#' + tmp.fromAccBox.id).select2({
			 minimumInputLength: 1,
			 multiple: false,
			 ajax: {
				 delay: 250,
				 url: tmp.me._searchAccUrl
			 }
		});

		tmp.toAccBox = layout.down('[input-panel="to-acc-id"]');
		tmp.me._pageJs._signRandID(tmp.toAccBox);
		jQuery('#' + tmp.toAccBox.id).select2({
			minimumInputLength: 1,
			 multiple: false,
			 ajax: {
				 delay: 250,
				 url: tmp.me._searchAccUrl
			 }
		});
		return tmp.me;
	}
};