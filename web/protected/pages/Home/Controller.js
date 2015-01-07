/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new FrontPageJs(), {
	_ressultPanelId: ''
	,_getOverviewPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'panel panel-default'})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'})
				.insert({'bottom': new Element('span').update('Overview')
				})
			})
			.insert({'bottom': new Element('div', {'class': 'panel-body'})
				.insert({'bottom': tmp.me._getLoadingDiv() })
			});
		return tmp.newDiv;
	}
	,_getLastTrans: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'panel panel-default'})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'})
				.insert({'bottom': new Element('span').update('Last Transactions:')
				})
			})
			.insert({'bottom': new Element('div', {'class': 'panel-body'})
				.insert({'bottom': tmp.me._getLoadingDiv() })
			});
		return tmp.newDiv;
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
		tmp.newDiv = new Element('div', {'class': 'input-panel'})
			.insert({'bottom': new Element('ul', {'class': 'nav nav-tabs nav-justified', 'role': 'tablist'})
				.insert({'bottom': new Element('li', {'class': 'active'})
					.insert({'bottom': new Element('a', {'href': 'javascript:void(0);'}).update('Spend') })
				})
				.insert({'bottom': new Element('li')
					.insert({'bottom': new Element('a', {'href': 'javascript:void(0);'}).update('Earn') })
				})
				.insert({'bottom': new Element('li')
					.insert({'bottom': new Element('a', {'href': 'javascript:void(0);'}).update('Transfer') })
				})
			})
			.insert({'bottom': new Element('div', {'class': 'panel'})
				.insert({'bottom': new Element('div', {'class': 'panel-body form-horizontal'}) 
					.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
							.insert({'bottom': new Element('select', {'class': 'form-control', 'input-panel': 'from-acc-id', 'placeholder': 'From:', 'name': 'from_acc_id'}) })
						, new Element('label', {'class': 'control-label col-sm-2'}).update('From:')
					) })
					.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
							.insert({'bottom': new Element('select', {'class': 'form-control', 'input-panel': 'to-acc-id', 'placeholder': 'To:', 'name': 'to_acc_id'}) })
						, new Element('label', {'class': 'control-label col-sm-2'}).update('To:')
					) })
					.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10'})
							.insert({'bottom': new Element('input', {'class': 'form-control', 'input-panel': 'comments', 'placeholder': 'Comments:'}) })
						, new Element('label', {'class': 'control-label col-sm-2'}).update('Comments:')
					) })
					.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10 col-sm-offset-2'})
							.insert({'bottom': '' })
					) })
					.insert({'bottom': tmp.me._getFormGroup(new Element('div', {'class': 'col-sm-10 col-sm-offset-2'})
							.insert({'bottom': new Element('button', {'class': 'btn btn-primary', 'type': 'submit'}).update('Save') })
					) })
				})
			});
		return tmp.newDiv;
	}
	,_getLayout: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'row'})
			.insert({'bottom': new Element('div', {'class': 'col-md-8'})
				.insert({'bottom': tmp.me._getOverviewPanel().addClassName('hidden-sm hidden-xs') })
				.insert({'bottom': tmp.me._getInputPanel() })
			})
			.insert({'bottom': new Element('div', {'class': 'col-md-4'}) 
				.insert({'bottom': tmp.me._getLastTrans().addClassName('hidden-sm hidden-xs') })
			});
		return tmp.newDiv;
	}
	,_initValidator: function() {
		var tmp = {};
		tmp.me = this;
		jQuery(tmp.me._jQueryFormSelector).bootstrapValidator({
	        message: 'This value is not valid',
	        feedbackIcons: {
	            valid: 'glyphicon glyphicon-ok',
	            invalid: 'glyphicon glyphicon-remove',
	            validating: 'glyphicon glyphicon-refresh'
	        },
	        fields: {
	        	'from_acc_id': {
	                validators: {
	                    notEmpty: {
	                        message: 'From Account Required'
	                    },
	                }
	        	}
	        	,'to_acc_id': {
	        		validators: {
	        			notEmpty: {
	        				message: 'To Account Required'
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
	/**
	 * initialising
	 */
	,init: function(ressultPanelId, jQueryFormSelector) {
		var tmp = {};
		tmp.me = this;
		tmp.me._jQueryFormSelector = jQueryFormSelector;
		tmp.me._ressultPanelId = ressultPanelId;
		$(tmp.me._ressultPanelId).update(tmp.me._getLayout());
		return tmp.me;
	}
});