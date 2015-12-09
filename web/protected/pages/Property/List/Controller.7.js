/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new BackEndPageJs(), {
	_pageSize : 30
	,_searchCriteria : null
	/**
	 * Ajax: delete the transaction
	 */
	,_submitDelete: function (btn, data) {
		var tmp = {};
		tmp.me = this;
		tmp.data = data;
		tmp.me.postAjax(tmp.me.getCallbackId('delTrans'), {'id': tmp.data.id}, {
			'onLoading': function() {
				jQuery('.trans-item-row[trans-group-id="' + tmp.data.groupId + '"]').hide();
			}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result|| !tmp.result.item || !tmp.result.item.groupId)
						return;
					jQuery('.trans-item-row[trans-group-id="' + tmp.result.item.groupId + '"]').remove();
					tmp.me.hideModalBox();
				} catch (e) {
					tmp.modalContentDiv = $(btn).up('.modal-content');
					tmp.modalContentDiv.down('.modal-title').update('<h4 class="text-danger">Failed. Error:</h4>');
					tmp.modalContentDiv.down('.modal-body').update(e);
				}
			}
			,'onComplete': function() {
				jQuery('.trans-item-row[trans-group-id="' + tmp.data.groupId + '"]').show();
			}
		})
		return tmp.me;
	}
	/**
	 * showing the confirmation panel for deletion
	 */
	,_showConfirmDeletion: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.row = $(btn).up('.item-row');
		tmp.data = tmp.row.retrieve('data');
		tmp.newDiv = new Element('div')
			.insert({'bottom': new Element('div', {'class': 'text-danger'})
				.insert({'bottom': new Element('div').update('You are about to DELETE this property: <strong>' + tmp.data.name + '</strong> ?')  })
				.insert({'bottom': new Element('div')
					.insert({'bottom': new Element('strong', {'class': 'col-xs-4 text-right'}).update('Bought Value:') })
					.insert({'bottom': new Element('div', {'class': 'col-xs-8'}).update(tmp.me.getCurrency(tmp.data.broughtValue)) })
					.insert({'bottom': new Element('strong', {'class': 'col-xs-4 text-right'}).update('Setup Value:') })
					.insert({'bottom': new Element('div', {'class': 'col-xs-8'}).update( tmp.data.setupAcc && tmp.data.setupAcc.id ? tmp.me.getCurrency(tmp.data.setupAcc.sumValue) : 'N.A.' ) })
					.insert({'bottom': new Element('strong', {'class': 'col-xs-4 text-right'}).update('Income Value:') })
					.insert({'bottom': new Element('div', {'class': 'col-xs-8'}).update( tmp.data.incomeAcc && tmp.data.incomeAcc.id ? tmp.me.getCurrency(tmp.data.incomeAcc.sumValue) : 'N.A.' ) })
					.insert({'bottom': new Element('strong', {'class': 'col-xs-4 text-right'}).update('Expense Value:') })
					.insert({'bottom': new Element('div', {'class': 'col-xs-8'}).update( tmp.data.expenseAcc && tmp.data.expenseAcc.id ? tmp.me.getCurrency(tmp.data.expenseAcc.sumValue) : 'N.A.' ) })
					.insert({'bottom': new Element('strong', {'class': 'col-xs-4 text-right'}).update('Description:') })
					.insert({'bottom': new Element('div', {'class': 'col-xs-8'}).update(tmp.data.description) })
				})
				.insert({'bottom': new Element('div').update( new Element('p').update('Are You sure you want to continue?') ) })
			})
			.insert({'bottom': new Element('div')
				.insert({'bottom': new Element('span', {'class': 'btn btn-default'})
					.update('NO, Cancel this')
					.observe('click', function(){
						tmp.me.hideModalBox();
					})
				})
				.insert({'bottom': new Element('span', {'class': 'btn btn-danger pull-right'})
					.update('YES, Delete it')
					.observe('click', function(){
						tmp.me._submitDelete(this, tmp.data);
					})
				})
			});
		tmp.me.showModalBox('<h4 class="text-danger" style="margin:0px;">Confirm</h4>', tmp.newDiv);
		return tmp.me;
	}
	,openEditPage: function(row) {
		var tmp = {};
		tmp.me = this;
		jQuery.fancybox({
			'autoScale'     : false,
			'autoDimensions': false,
			'fitToView'     : false,
			'autoSize'      : false,
			'width'         : '90%',
			'type'			: 'iframe',
			'href'			: '/properties/' + (row && row.id ? row.id : 'new') + '.html?blanklayout=1'
 		});
		return tmp.me;
	}
	/**
	 * Getting the item row
	 */
	,_getItemRow : function(row) {
		var tmp = {};
		tmp.me = this;
		tmp.profit = '';
		if(row.id && row.expenseAcc && row.incomeAcc && row.expenseAcc.id && row.incomeAcc.id) {
			tmp.profit = (row.incomeAcc.sumValue * 1 - row.expenseAcc.sumValue * 1)
		}
		tmp.newRow = new Element('a', {'href': 'javascript: void(0);', 'class' : 'list-group-item item-row', 'title': (row.id ? 'Description: ' + row.description : ''), 'item-id': row.id ? row.id : '' })
			.store('data', row)
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('div', {'class': 'col-md-2 col-sm-10'}).update(!row.id ? 'Property' :
					new Element('a', {'href': 'javascript: void(0)'}).update(row.name)
						.observe('click', function(event){
							tmp.me.openEditPage(row);
						})
				) })
				.insert({'bottom': new Element('div', {'class': ' col-sm-2 col-md-push-9 col-md-1 text-right'})
						.insert({'bottom': !row.id ? '' : new Element('div', {'class': 'btn-group btn-group-sm', 'role': 'group'})
						.insert({'bottom': new Element('div', {'class': 'btn btn-default dropdown-toggle', 'data-toggle': 'dropdown', 'aria-expanded': 'false'})
							.insert({'bottom': new Element('span', {'class': 'glyphicon glyphicon-cog'}).setStyle('margin-right: 2px;') })
							.insert({'bottom': new Element('span', {'class': 'caret'}) })
						})
						.insert({'bottom': new Element('ul', {'class': 'dropdown-menu', 'role': 'menu'})
							.insert({'bottom': new Element('li')
								.insert({'bottom': new Element('a', {'href': 'javascript: void(0);'})
									.insert({'bottom': new Element('i', {'class': 'glyphicon glyphicon-pencil'}) })
									.insert({'bottom': new Element('span', {'class': 'hidden-xs hidden-sm'}).update(' Edit') })
									.observe('click', function(event){
										tmp.me.openEditPage(row);
									})
								})
							})
							.insert({'bottom': new Element('li')
								.insert({'bottom': new Element('a', {'href': 'javascript: void(0);'})
									.insert({'bottom': new Element('i', { "class": 'text-danger'})
										.insert({'bottom': new Element('i', {'class': 'glyphicon glyphicon-remove'}) })
									})
									.insert({'bottom': new Element('span', {'class': 'hidden-xs hidden-sm text-danger'}).update(' Delete') })
									.observe('click', function(event){
										tmp.me._showConfirmDeletion(this);
									})
								})
							})
						})
					})
				})
				.insert({'bottom': new Element('div', {'class': 'col-md-pull-1 col-md-9 col-sm-12'}).update(!row.id ? 
					new Element('div', {'class': 'row  hidden-xs  hidden-sm'})
						.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2'}).update('Bought') })
						.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2'}).update('Setup') })
						.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2'}).update('Income') })
						.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2'}).update('Expense') })
						.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2'}).update('Profit') })
						.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2'}).update('%') })
					:
					new Element('div')
						.insert({'bottom': new Element('div', {'class': 'row'})
							.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2 hidden-xs'}).update(tmp.me.getCurrency(row.boughtPrice) ) })
							.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2 hidden-xs'}).update(row.setupAcc && row.setupAcc.id ?
									new Element('abbr', {'title': row.setupAcc.breadCrumbs.join(' / ')}).update(tmp.me.getCurrency(row.setupAcc.sumValue)) : ''
							) })
							.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2 hidden-xs'}).update(row.incomeAcc && row.incomeAcc.id ?
									new Element('abbr', {'title': row.incomeAcc.breadCrumbs.join(' / ')}).update(tmp.me.getCurrency(row.incomeAcc.sumValue)) : ''
							) })
							.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2 hidden-xs'}).update( row.expenseAcc && row.expenseAcc.id ?
									new Element('abbr', {'title': row.expenseAcc.breadCrumbs.join(' / ')}).update(tmp.me.getCurrency(row.expenseAcc.sumValue) ) : ''
							) })
							.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-2'}).update(
								(tmp.profit === '' ? '' : '<strong class="' + (tmp.profit < 0 ? "text-danger" : "text-success") + '">' + tmp.me.getCurrency(tmp.profit) + '<strong>')
							) })
							.insert({'bottom': new Element('div', {'class': 'show-list-btn col-xs-1 hidden-sm '}).update(
								(tmp.profit === '' ? '' : '<strong class="' + (tmp.profit < 0 ? "text-danger" : "text-success") + '">' + Math.round(tmp.profit * 100 / row.boughtPrice) + '%<strong>')
							) })
						})
						.insert({'bottom': new Element('div', {'class': 'row performance-list-panel'})
							.store('PropertyPerformanceListPanelJs', tmp.PropertyPerformanceListPanelJs = new PropertyPerformanceListPanelJs(tmp.me, row))
							.update(tmp.PropertyPerformanceListPanelJs.getPanel())
						})
				) })
			});
		if(row.attachments && row.attachments.size() > 0) {
			tmp.attachmentRow = new Element('div', {'class': ''});
			row.attachments.each(function(attachment) {
				tmp.attachmentRow.insert({'bottom': new Element('a', {'class': 'btn btn-success btn-xs', 'target': '_BLANK', 'href': '/asset/get?id=' + attachment.asset.skey}).setStyle('margin-right: 3px').update(attachment.asset.filename) });
			})
			tmp.newRow.insert({'bottom': tmp.attachmentRow });
		}
		if(row.id) {
			tmp.newRow.getElementsBySelector('.show-list-btn').each(function(el) {
				el.observe('click', function() {
					tmp.btn = $(this);
					tmp.listPanel = tmp.btn.up('.item-row');
					if(!tmp.listPanel.hasClassName('loadedPerformancePanel')) {
						tmp.listPanel.addClassName('loadedPerformancePanel')
							.down('.performance-list-panel')
							.retrieve('PropertyPerformanceListPanelJs')
							.render();
					} else {
						tmp.listPanel.down('.performance-list-panel').toggle();
					}
				})
			});
		}
		return tmp.newRow;
	}
	,updateRow: function(item) {
		var tmp = {};
		tmp.me = this;
		if(!item.id)
			return tmp.me;
		tmp.resultPanel = $(tmp.me.getHTMLID('result-list-div'));
		tmp.row = tmp.resultPanel.down('.item-row[item-id=' + item.id + ']');
		if(tmp.row)
			tmp.row.replace(tmp.me._getItemRow(item));
		else {
			tmp.resultPanel.insert({'bottom': tmp.me._getItemRow(item)});
			$(tmp.me.getHTMLID('item-count')).update($(tmp.me.getHTMLID('item-count')).innerHTML * 1 + 1);
		}
		return tmp.me;
	}
	/**
	 * getting the Transactions
	 */
	,_getItems : function(pageNo) {
		var tmp = {};
		tmp.me = this;
		tmp.pageNo = (pageNo || 1);
		tmp.resultDiv = $(tmp.me.getHTMLID('result-list-div'));
		tmp._loadingDiv = tmp.me._getLoadingDiv();
		tmp.me.postAjax(tmp.me.getCallbackId('getItems'), tmp.data, {
			'onLoading' : function() {
				tmp.resultDiv.up('.panel').show();
				if (tmp.pageNo === 1) {
					if (!tmp.resultDiv.update(tmp._loadingDiv).hasClassName('panel-body'))
						tmp.resultDiv.addClassName('panel-body').removeClassName('group-list');
				}
			}
			,'onSuccess' : function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if (!tmp.result || !tmp.result.items)
						return;
					if (tmp.pageNo === 1) {
						tmp.resultDiv.update(new Element('div', {'class': 'list-group-item disabled'}).setStyle('font-weight:bold;').update(tmp.me._getItemRow({}).innerHTML) );
						tmp.resultDiv.removeClassName('panel-body').addClassName('list-group');
					}
					//removing the get more btns
					if(tmp.resultDiv.down('.get-more-btn-wrapper')) {
						tmp.resultDiv.down('.get-more-btn-wrapper').remove();
					}

					tmp.result.items.each(function(item){
						tmp.resultDiv.insert({'bottom': tmp.me._getItemRow(item) });
					});
					if(tmp.result.pagination && tmp.result.pagination.totalRows)
						$(tmp.me.getHTMLID('item-count')).update(tmp.result.pagination.totalRows);
					if(tmp.result.pagination.pageNumber < tmp.result.pagination.totalPages) {
						tmp.resultDiv.insert({'bottom': new Element('a', {'href': 'javascript: void(0);', 'class': 'list-group-item list-group-item-info get-more-btn-wrapper text-center', 'data-loading-text': 'Getting More ...'})
							.update('Get More Transactions')
							.observe('click', function() {
								tmp.me._getItems(pageNo * 1 + 1);
							})
						})
					}
				} catch (e) {
					if (tmp.pageNo === 1) {
						if (!tmp.resultDiv.hasClassName('panel-body'))
							tmp.resultDiv.addClassName('panel-body').removeClassName('group-list');
						tmp.resultDiv.update(tmp.me.getAlertBox('Error: ', e).addClassName('alert-danger'));
					} else {
						tmp.me.showModalBox('<strong class="text-danger">Error:</strong>', e);
					}
				}
			}
			,'onComplete' : function() {
				tmp._loadingDiv.remove();
			}
		});
		return tmp.me;
	}
	,showBuyingCheckPanel: function() {
		var tmp = {};
		tmp.me = this;
		if(typeof(tmp.me._propertyBuyingCheckPanel) === 'object')
			tmp.me._propertyBuyingCheckPanel.show();
		return tmp.me;
	}
	/**
	 * initialising
	 */
	,init : function(_propertyBuyingCheckPanel) {
		var tmp = {};
		tmp.me = this;
		tmp.me._propertyBuyingCheckPanel = _propertyBuyingCheckPanel;
		tmp.me._getItems();
		return tmp.me;
	}
});