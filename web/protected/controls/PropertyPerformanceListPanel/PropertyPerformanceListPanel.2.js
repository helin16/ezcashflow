var PropertyPerformanceListPanelJs = new Class.create();
PropertyPerformanceListPanelJs.prototype = {
	_pageJs : null
	,_property: null
	,_panelHTMLID: ''
	,_noOfYears: 5
	/**
	 * constructor
	 */
	,initialize : function(_pageJs, _property) {
		this._pageJs = _pageJs;
		this._property = _property;
		this._panelHTMLID = 'PropertyPerformanceListPanelJs_' + String.fromCharCode(65 + Math.floor(Math.random() * 26)) + Date.now();
	}
	/**
	 * Getting the panel
	 */
	,getPanel: function () {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'id': tmp.me._panelHTMLID, 'class': 'list-group'});
		return tmp.newDiv;
	}
	/**
	 * Getting the Date range
	 */
	,_getDateRange: function(pageNo) {
		var tmp = {};
		tmp.me = this;
		tmp.dateRange = [];
		tmp.now = new Date();
		tmp.i = (pageNo * 1 - 1) * tmp.me._noOfYears;
		tmp.end = tmp.i +  tmp.me._noOfYears;
		for(tmp.i; tmp.i < tmp.end; tmp.i = tmp.i * 1 + 1) {
			tmp.dateRange.push({'startTime': moment(tmp.now).subtract(tmp.i, 'years').startOf('year'), 'endTime': moment(tmp.now).subtract(tmp.i, 'years').endOf('year') });
		}
		return tmp.dateRange;
	}
	/**
	 * Getting the data row for display
	 */
	,_getDataRow: function(row) {
		var tmp = {};
		tmp.me = this;
		tmp.startTime = moment(tmp.me._pageJs.loadUTCTime(row.startTime));
		tmp.endTime = moment(tmp.me._pageJs.loadUTCTime(row.endTime));
		tmp.timeString = '&localFromDate=' + tmp.startTime.format('YYYY-MM-DD[T]hh:mm:ss') + '&localToDate=' + tmp.endTime.format('YYYY-MM-DD[T]hh:mm:ss');
		tmp.income = (row.income === '' ? '' :
			new Element('a', {'target': '_BLANK', 'href': '/transactions.html?lookDownAccId=' + tmp.me._property.incomeAcc.id + tmp.timeString }).update( tmp.me._pageJs.getCurrency(row.income) ));
		tmp.expense = (row.expense === '' ? '' :
			new Element('a', {'target': '_BLANK', 'href': '/transactions.html?lookDownAccId=' + tmp.me._property.expenseAcc.id + tmp.timeString}).update( tmp.me._pageJs.getCurrency(row.expense) ));
		tmp.profit = ((row.income === '' || row.expense === '') ? '' : tmp.me._pageJs.getCurrency(row.income - row.expense));
		tmp.profitPercentage = (tmp.profit === '' ? '' : (tmp.me._property.boughtPrice > 0 ? Math.round(tmp.me._pageJs.getValueFromCurrency(tmp.profit) * 100 / tmp.me._property.boughtPrice) + '%' : ''));
		tmp.newDiv = new Element('a', {'class': 'list-group-item', 'href': 'javascript: void(0);'})
			.setStyle("margin: 0; padding: 0;")
			.store(row)
			.insert({'bottom': new Element('div', {'class': 'row'})
				.insert({'bottom': new Element('div', {'class': 'col-xs-1 col-sm-4 text-right'})
					.insert({'bottom': new Element('abbr', {'title': tmp.startTime.format('llll') + ' ~ ' + tmp.endTime.format('llll')}).update(tmp.startTime.format('YYYY') + ': ') })
				})
				.insert({'bottom': new Element('div', {'class': 'col-xs-9 col-sm-8 text-right'})
					.insert({'bottom': new Element('div', {'class': 'row'})
						.insert({'bottom': new Element('div', {'class': 'col-xs-3'})
							.insert({'bottom': tmp.income })
						})
						.insert({'bottom': new Element('div', {'class': 'col-xs-3'})
							.insert({'bottom': tmp.expense })
						})
						.insert({'bottom': new Element('div', {'class': 'col-xs-3'})
							.insert({'bottom': new Element('span').update(tmp.profit).addClassName(tmp.profit === '' ? '' : (tmp.profit < 0 ? 'text-danger' : 'text-success')) })
						})
						.insert({'bottom': new Element('div', {'class': 'col-xs-3'})
							.insert({'bottom': new Element('abbr', {'title': 'Profit / boughtPrice'}).update(tmp.profitPercentage).addClassName(tmp.profit === '' ? '' : (tmp.profit < 0 ? 'text-danger' : 'text-success')) })
						})
					})
				})
			});
		return tmp.newDiv;
	}
	/**
	 * Ajax: getting the data
	 */
	,_getData: function(pageNo, btn) {
		var tmp = {};
		tmp.me = this;
		tmp.pageNo = (pageNo || 1);
		tmp.btn = btn;
		if(tmp.btn)
			tmp.me._pageJs._signRandID(tmp.btn);
		tmp.loadingDiv = tmp.me._pageJs._getLoadingDiv();
		$(tmp.me._panelHTMLID).getElementsBySelector('.msg').each(function(el){ el.remove(); });
		tmp.data = {'dateRange': tmp.me._getDateRange(tmp.pageNo), 'propertyId': tmp.me._property.id};
		tmp.me._pageJs.postAjax(PropertyPerformanceListPanelJs.callbackIds.getData, tmp.data, {
			'onLoading': function() {
				if(tmp.pageNo === 1)
					$(tmp.me._panelHTMLID).update(tmp.loadingDiv);
				if(tmp.btn)
					jQuery('#' + tmp.btn.id).button('loading');
			}
			,'onSuccess': function (sender, param) {
				try {
					tmp.result = tmp.me._pageJs.getResp(param, false, true);
					if(!tmp.result || !tmp.result.items)
						return;
					tmp.result.items.each(function(item){
						$(tmp.me._panelHTMLID).insert({'bottom': tmp.me._getDataRow(item) });
					});
					tmp.getMoreBtnWrapper = $(tmp.me._panelHTMLID).down('.get-more-btn-wrapper');
					if(tmp.getMoreBtnWrapper)
						tmp.getMoreBtnWrapper.remove();
					$(tmp.me._panelHTMLID).insert({'bottom': new Element('div', {'class': 'list-group-item get-more-btn-wrapper text-center'})
						.insert({'bottom': new Element('div', {'class': 'btn btn-xs btn-primary', 'data-loading-text': '<i class="fa fa-spinner fa-spin"></i>'})
							.update('Get More')
							.observe('click', function(evt) {
								Event.stop(evt);
								tmp.me._getData(tmp.pageNo * 1 + 1, this);
							})
						})
					});
				} catch(e) {
					$(tmp.me._panelHTMLID).insert({'bottom': tmp.me._pageJs.getAlertBox('Err: ', e).addClassName('alert-danger msg') });
				}
			}
			, 'onComplete': function() {
				tmp.loadingDiv.remove();
				if(tmp.btn)
					jQuery('#' + tmp.btn.id).button('reset');
			}
		});
		return tmp.me;
	}
	/**
	 * Rendering the div
	 */
	,render: function(_inputPanel) {
		var tmp = {};
		tmp.me = this;
		if($(tmp.me._panelHTMLID)) {
			tmp.me._getData();
		}
		return tmp.me;
	}
};