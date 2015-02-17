/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_showEditPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.creatingTitle = 'Creating a ' + (tmp.me._entity.type && tmp.me._entity.type.id ? '<span class="label label-success">' + tmp.me._entity.type.name + '</span>' : '') + ' Account ' + (tmp.me._entity.parent && tmp.me._entity.parent.id ? 'under ' + tmp.me._entity.parent.breadCrumbs.join('/') : '') + ':';
		tmp.newDiv = new Element('div', {'id': 'save-panel'})
			.insert({'bottom': new Element('h4').update(!tmp.me._entity.id || tmp.me._entity.id.blank() ?  tmp.creatingTitle : 'Editing an AccountEntry: ' + tmp.me._entity.breadCrumbs.join('/')) })
			.insert({'bottom': new Element('div')
				.insert({'bottom': new Element('div', {'class': 'form-group'})
					.insert({'bottom': new Element('label', {'class': 'control-label hidden-xs hidden-sm'}).update('Account Name:') })
					.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'The name of the account.', 'name': 'name', 'save-panel': 'name', 'value': (tmp.me._entity.name ? tmp.me._entity.name : '')}) })
				})
				.insert({'bottom': new Element('div', {'class': 'form-group'})
					.insert({'bottom': new Element('label', {'class': 'control-label hidden-xs hidden-sm'}).update('Account Number:') })
					.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'The unique account number', 'name': 'accountNo', 'save-panel': 'accountNo', 'value': (tmp.me._entity.accountNo ? tmp.me._entity.accountNo : '')}) })
				})
				.insert({'bottom': new Element('div', {'class': 'form-group'})
					.insert({'bottom': new Element('label', {'class': 'control-label hidden-xs hidden-sm'}).update('Init Value:') })
					.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'The initial value or Opening Balance of this account.', 'name': 'initValue', 'save-panel': 'initValue', 'value': (tmp.me._entity.initValue ? tmp.me._entity.initValue : 0)}) })
				})
				.insert({'bottom': new Element('div', {'class': 'form-group'})
					.insert({'bottom': new Element('label', {'class': 'control-label hidden-xs hidden-sm'}).update('Description:') })
					.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'The description of this account', 'save-panel': 'description', 'value': (tmp.me._entity.description ? tmp.me._entity.description : '')}) })
				})
				.insert({'bottom': new Element('div', {'class': 'form-group'})
					.insert({'bottom': new Element('label')
						.insert({'bottom': new Element('span').update('Is a Summary Account: ') })
						.insert({'bottom': new Element('input', {'type': 'checkbox', 'save-panel': 'isSumAcc', 'checked': tmp.me._entity.isSumAcc}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'form-group'})
					.insert({'bottom': (tmp.me._entity.type && tmp.me._entity.type.id ? new Element('input', {'type': 'hidden', 'value': tmp.me._entity.type.id, 'save-panel': 'typeId'}) : '') })
					.insert({'bottom': (tmp.me._entity.parent && tmp.me._entity.parent.id ? new Element('input', {'type': 'hidden', 'value': tmp.me._entity.parent.id, 'save-panel': 'parentId'}) : '') })
					.insert({'bottom': new Element('button', {'type': 'submit', 'class': 'save-panel-submit-btn btn btn-primary col-xs-12 col-sm-6 col-md-4 col-lg-3'}).update('save') })
				})
			});
		$(tmp.me.getHTMLID('result-div')).update(tmp.newDiv);
		return tmp.me;
	}
	,_saveItem: function() {
		var tmp = {};
		tmp.me = this;
		tmp.data = {};
		tmp.resultPanel = $(tmp.me.getHTMLID('result-div'));
		tmp.savePanel = tmp.resultPanel.down('#save-panel');
		tmp.savePanel.getElementsBySelector('[save-panel]').each(function(item){
			tmp.field = item.readAttribute('save-panel');
			if(tmp.field === 'initValue') {
				tmp.data[tmp.field] = tmp.me.getValueFromCurrency($F(item));
			} else if(tmp.field === 'isSumAcc') {
				tmp.data[tmp.field] = $(item).checked;
			} else {
				tmp.data[tmp.field] = $F(item);
			}
		});
		//editing
		if(tmp.me._entity.id && !tmp.me._entity.id.blank()) {
			tmp.data.accId = tmp.me._entity.id;
		}

		tmp.loadingDiv = tmp.me._getLoadingDiv().addClassName("panel-body");
		tmp.me.postAjax(tmp.me.getCallbackId('saveItem'), tmp.data, {
			'onLoading': function() {
				tmp.savePanel.insert({'after': tmp.loadingDiv}).hide();
			}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.item)
						return;
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
	,_refreshParentWindow(item) {
		var tmp = {};
		tmp.me = this;
		if(window.parent && window.parent.pageJs && window.parent.pageJs._showAccounts) {
			window.parent.pageJs._showAccounts(item.type, function() {
				window.parent.pageJs._closeAccDetailsPanel();
			});
		}
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
		        ,'accountNo': {
		        	validators: {
		        		notEmpty: {
		        			message: 'Account is required'
		        		}
		        	}
		        }
	        	,'initValue': {
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
	 * initialising
	 */
	,init: function(_resultPanelId, _account) {
		var tmp = {};
		tmp.me = this;
		tmp.me._showEditPanel()
			._initForm();
		return tmp.me;
	}
});