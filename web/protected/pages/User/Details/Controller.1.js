/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new DetailsPageJs(), {
	_showEditPanel: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'id': 'save-panel'})
			.insert({'bottom': new Element('h4').update(!tmp.me._entity.id ? 'Creating a new user:': 'Editing User: ' +  tmp.me._entity.person.fullName) })
			.insert({'bottom': new Element('div')
				.insert({'bottom': new Element('div', {'class': 'col-md-6'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('First Name:') })
						.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'The First Name of the user.', 'name': 'firstName', 'save-panel': 'firstName', 'value': (tmp.me._entity.person && tmp.me._entity.person.firstName ? tmp.me._entity.person.firstName : '')}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-md-6'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('Last Name:') })
						.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'The Last Name of the user.', 'name': 'lastName', 'save-panel': 'lastName', 'value': (tmp.me._entity.person && tmp.me._entity.person.lastName ? tmp.me._entity.person.lastName : '')}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-md-12'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('Email:') })
						.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'The Email of the user.', 'name': 'email', 'save-panel': 'email', 'value': (tmp.me._entity.person && tmp.me._entity.person.email ? tmp.me._entity.person.email : '')}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-md-6'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('Password:') })
						.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'The new password for this user', 'name': 'password', 'save-panel': 'password', 'value': '', 'type': 'password'}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-md-6'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': new Element('label', {'class': 'control-label'}).update('Confirm Password:') })
						.insert({'bottom': new Element('input', {'class': 'form-control', 'placeholder': 'Please confirm the password you type before', 'name': 'confirmPassword', 'save-panel': 'confirmPassword', 'value': '', 'type': 'password'}) })
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-md-12'})
					.insert({'bottom': new Element('div', {'class': 'form-group'})
						.insert({'bottom': (tmp.me._entity.id ? new Element('input', {'type': 'hidden', 'value': tmp.me._entity.id, 'save-panel': 'id'}) : '') })
						.insert({'bottom': new Element('button', {'type': 'submit', 'class': 'pull-right save-panel-submit-btn btn btn-primary col-xs-12 col-sm-6 col-md-4 col-lg-3'}).update('save') })
					})
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
			tmp.data.userId = tmp.me._entity.id;
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
						.insert({'bottom': new Element('strong', {'class': 'text-success'}).update('"' + tmp.result.item.person.fullName + '" saved successfully.')})
					);
					window.location = document.URL;
					tmp.me._refreshParentWindow();
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
	,_refreshParentWindow: function() {
		var tmp = {};
		tmp.me = this;
		if(window.parent && window.parent.pageJs && window.parent.pageJs.hideModalBox) {
			window.parent.pageJs.hideModalBox();
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
	        	'firstName': {
	        		validators: {
	        			notEmpty: {
	                        message: 'FirstName is required'
	                    }
                    }
	        	}
		        ,'lastName': {
		        	validators: {
		        		notEmpty: {
		        			message: 'LastName is required'
		        		}
		        	}
		        }
	        	,'email': {
	        		validators: {
	                    notEmpty: {
	                        message: 'Email is needed here'
	                    },
	                    emailAddress: {
	                        message: 'The input is not a valid email address'
	                    }
	                }
	        	}
	        	,'password': {
	        		validators: {
                        callback: {
                            message: 'The new password is required, min 6 charactors',
                            callback: function(value, validator, $field) {
                            	if(tmp.me._entity.id)
                            		return true;
                                tmp.newPassword = jQuery('#' + tmp.me.getHTMLID('page-form')).find('[name="password"]').val();
                                return tmp.newPassword !== '' && tmp.newPassword.length > 6;
                            }
                        }
                    }
	        	}
	        	,'confirmPassword': {
	        		validators: {
                        callback: {
                            message: 'Retyped password not matching the new password.',
                            callback: function(value, validator, $field) {
                            	tmp.newPassword = jQuery('#' + tmp.me.getHTMLID('page-form')).find('[name="password"]').val();
                            	if(!tmp.me._entity.id)
                            		return value.length > 0 && tmp.newPassword === value;
                                return tmp.newPassword === value;
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
	,init: function() {
		var tmp = {};
		tmp.me = this;
		tmp.me._showEditPanel()
			._initForm();
		return tmp.me;
	}
});