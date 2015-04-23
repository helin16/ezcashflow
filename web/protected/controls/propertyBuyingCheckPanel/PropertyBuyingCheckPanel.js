var PropertyBuyingCheckPanel = new Class.create();
PropertyBuyingCheckPanel.prototype = {
	panelTitle: 'Quick buying Tool:'
	//constructor
	,initialize: function(_pageJs) {
		this._pageJs = _pageJs;
	}

	,_getTableRow: function(row, isTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.isTitle = (isTitle || false);
		tmp.tag = (tmp.isTitle === true ? 'th' : 'td')
		tmp.newDiv = new Element('tr')
			.insert({'bottom': new Element(tmp.tag).update(row.item) })
			.insert({'bottom': new Element(tmp.tag).update(row.value) });
		return tmp.newDiv;
	}

	,_getTable: function(tile, items){
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'panel panel-default'})
			.insert({'bottom': new Element('div', {'class': 'panel-heading'}).update(tile)
				.insert({'bottom': new Element('span', {'class': 'badge total-value pull-right'}).update(tmp.me._pageJs.getCurrency(0)) })
			})
			.insert({'bottom': new Element('table', {'class': 'table'})
				.insert({'bottom': new Element('thead').update( tmp.me._getTableRow({'item': 'Item', 'value': 'Value ($)'}, true) ) })
				.insert({'bottom': new Element('tbody')
					.insert({'bottom': tmp.me._getTableRow({'item': 'Item', 'value': 'Value ($)'}) })
				})
			})
		return tmp.newDiv;
	}

	,_getTotalRow: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'panel panel-default'})
			.insert({'bottom': new Element('div', {'class': 'panel-heading text-right'})
				.insert({'bottom': new Element('span').setStyle('padding-right: 10px;').update('Return Per Year: ') })
				.insert({'bottom': new Element('strong', {'class': 'return-percentage'}).update('0%') })
			})
		return tmp.newDiv;
	}

	,getPanel: function(noTitle) {
		var tmp = {};
		tmp.me = this;
		tmp.noTitle = (noTitle || false);
		tmp.newPanel = new Element('div', {'class': 'prop-buying-chk-panel'})
			.insert({'bottom': tmp.noTitle === true ? '' : new Element('h4').update('Quick buying Tool:') })
			.insert({'bottom': new Element('div')
				.insert({'bottom': new Element('small').update('I am just trying to help you to figure out some numbers before making a decision of buying a property')	})
			})
			.insert({'bottom': new Element('div')
				.insert({'bottom': tmp.me._getTotalRow()	})
				.insert({'bottom': tmp.me._getTable(new Element('strong').update('Buying Cost:') )	})
				.insert({'bottom': tmp.me._getTable(new Element('strong').update('Incomes:') )	})
				.insert({'bottom': tmp.me._getTable(new Element('strong').update('Expense:') )	})
				.insert({'bottom': tmp.me._getTotalRow()	})
			})
		return tmp.newPanel;
	}

	,render: function(panel) {
		var tmp = {};
		tmp.me = this;
		$(panel).update(tmp.me.getPanel());
		return tmp.me;
	}
	,show: function(inModalBox) {
		var tmp = {};
		tmp.me = this;
		tmp.inModalBox = (inModalBox || true);
		if(tmp.inModalBox === true)
			tmp.me._pageJs.hideModalBox();
		tmp.me._pageJs.showModalBox('<strong>Quick buying Tool:</strong>', tmp.me.getPanel(true));
		return tmp.me;
	}
};