//this is the source file for the FieldTaskListController
var ProfitPanel = new Class.create();
ProfitPanel.prototype = {
	accIds: {
		'income': [],
		'expense': []
	},
	resultDivId: '',
	callbackIds: {
		'getInfo': ''
	},
	//constructor
	initialize: function (resultDivId, getInfoCBId) {
		this.resultDivId = resultDivId;
		this.callbackIds.getInfo = getInfoCBId;
	},
	
	//buildForm
	loadInfo: function(excludeIncomePos, excludeExpensePos) {
		var tmp = {};
		tmp.holderDiv = this.resultDivId;
		tmp.excludeIncomePos = (excludeIncomePos === undefined ? '' : excludeIncomePos);
		tmp.excludeExpensePos = (excludeExpensePos === undefined ? '' : excludeExpensePos);
		appJs.postAjax(this.callbackIds.getInfo, {'excludeIncomePos': tmp.excludeIncomePos, 'excludeExpensePos' : tmp.excludeExpensePos}, {
    		'onLoading': function(sender, param){
    			$(tmp.holderDiv).update('<img src="/contents/images/loading.gif" style="display:block; with:150px; height:150px;"/>');
    		},
	    	'onComplete': function(sender, param){
	    		tmp.result = appJs.getResp(param);
	    		$(tmp.holderDiv).update(profitJs.formatInfo(tmp.result));
	    	}
    	});
    	return false;
	},
	
	//getting the header row dom
	getHeaderRow: function(data) {
		var tmp = {};
		tmp.headerRow = new Element('div', {'class': 'headerRow'});
		tmp.headerRow_headr = new Element('span', {'class': 'headerCell headerCol'});
		tmp.headerRow_headr.insert({'bottom': new Element('span', {'class': 'headerTitle'}).update('&nbsp;')});
		tmp.headerRow_headr.insert({'bottom': new Element('span', {'class': 'headerRange'}).update('&nbsp;')});
		tmp.headerRow.insert({'bottom': tmp.headerRow_headr});
		
		tmp.headerRow_day = new Element('span', {'class': 'headerCell dayCol'});
		tmp.headerRow_day.insert({'bottom': new Element('span', {'class': 'headerTitle'}).update('Day')});
		tmp.headerRow_day.insert({'bottom': new Element('span', {'class': 'headerRange'}).update(data.day.range.start + ' ~ ' + data.day.range.end)});
		tmp.headerRow.insert({'bottom': tmp.headerRow_day});
		
		tmp.headerRow_week = new Element('span', {'class': 'headerCell weekCol'});
		tmp.headerRow_week.insert({'bottom': new Element('span', {'class': 'headerTitle'}).update('Week')});
		tmp.headerRow_week.insert({'bottom': new Element('span', {'class': 'headerRange'}).update(data.week.range.start  + ' ~ ' + data.week.range.end)});
		tmp.headerRow.insert({'bottom': tmp.headerRow_week});
		
		tmp.headerRow_month = new Element('span', {'class': 'headerCell monthCol'});
		tmp.headerRow_month.insert({'bottom': new Element('span', {'class': 'headerTitle'}).update('Month')});
		tmp.headerRow_month.insert({'bottom': new Element('span', {'class': 'headerRange'}).update(data.month.range.start + ' ~ ' + data.month.range.end)});
		tmp.headerRow.insert({'bottom': tmp.headerRow_month});
		
		tmp.headerRow_year = new Element('span', {'class': 'headerCell yearCol'});
		tmp.headerRow_year.insert({'bottom': new Element('span', {'class': 'headerTitle'}).update('Year')});
		tmp.headerRow_year.insert({'bottom': new Element('span', {'class': 'headerRange'}).update(data.year.range.start + ' ~ ' + data.year.range.end)});
		tmp.headerRow.insert({'bottom': tmp.headerRow_year});
		
		tmp.headerRow_all = new Element('span', {'class': 'headerCell allCol'});
		tmp.headerRow_all.insert({'bottom': new Element('span', {'class': 'headerTitle'}).update('All')});
		tmp.headerRow_all.insert({'bottom': new Element('span', {'class': 'headerRange'}).update(data.all.range.start + ' ~ ' + data.all.range.end)});
		tmp.headerRow.insert({'bottom': tmp.headerRow_all});
		return tmp.headerRow;
	},
	
	//getting the income row
	getIncomeRow: function(title, day, week, month, year, all, dayRange, weekRange, monthRange, yearRange, allRange) {
		var tmp = {};
		tmp.row = new Element('div', {'class': title + 'Row'});
		tmp.isIncome = (title.toLowerCase() === 'income');
		tmp.headerRow_headr = new Element('div', {'class': 'headerCell headerCol'}).update(title);
		tmp.row.insert({'bottom': tmp.headerRow_headr});
		tmp.headerRow_day = this.formatCell(title, 'day', day, (dayRange !== undefined ? this.makeUrl(dayRange.start, dayRange.end, tmp.isIncome) : undefined));
		tmp.row.insert({'bottom': tmp.headerRow_day});
		tmp.headerRow_week = this.formatCell(title, 'week', week, (weekRange !== undefined ? this.makeUrl(weekRange.start, weekRange.end, tmp.isIncome) : undefined));
		tmp.row.insert({'bottom': tmp.headerRow_week});
		tmp.headerRow_month = this.formatCell(title, 'month', month, (monthRange !== undefined ? this.makeUrl(monthRange.start, monthRange.end, tmp.isIncome) : undefined));
		tmp.row.insert({'bottom': tmp.headerRow_month});
		tmp.headerRow_year = this.formatCell(title, 'year', year, (yearRange !== undefined ? this.makeUrl(yearRange.start, yearRange.end, tmp.isIncome) : undefined));
		tmp.row.insert({'bottom': tmp.headerRow_year});
		tmp.headerRow_all = this.formatCell(title, 'all', all, (allRange !== undefined ? this.makeUrl(allRange.start, allRange.end, tmp.isIncome) : undefined));
		tmp.row.insert({'bottom': tmp.headerRow_all});
		return tmp.row;
	},
	//format the cell for the income, expense and diff
	formatCell: function(title, dateTitle, data, href) {
		var tmp = {};
		tmp.cellData = appJs.getCurrency(data);
		if(href !== undefined)
			tmp.cellData = new Element('a', {'href': href}).update(appJs.getCurrency(data));
		tmp.cell = new Element('span', {'class': dateTitle + 'Col dataCol' + (data < 0 ? ' minusCurrency' : ''), 'value': data}).update(tmp.cellData);
		tmp.cell.writeAttribute(title.toLowerCase(), dateTitle);
		return tmp.cell;
	},
	
	//format the div
	formatInfo: function(data) {
		var tmp = {};
		this.accIds.income = data.accIds[3];
		this.accIds.expense = data.accIds[4];
		
		tmp.div = new Element('div', {'class': 'profitPanel'});
		//get the header
		tmp.div.insert({'bottom': this.getHeaderRow(data)});
		//get the Income Row
		tmp.div.insert({'bottom': 
			this.getIncomeRow('Income', data.day.income, data.week.income, data.month.income, data.year.income, data.all.income,
					data.day.range, data.week.range, data.month.range, data.year.range, data.all.range
		)});
		//get the expense Row
		tmp.div.insert({'bottom': this.getIncomeRow('Expense', data.day.expense, data.week.expense, data.month.expense, data.year.expense, data.all.expense,
				data.day.range, data.week.range, data.month.range, data.year.range, data.all.range
		)});
		//gen the diff Row
		tmp.diff = this.genDiff(tmp.div);
		tmp.div.insert({'bottom': this.getIncomeRow('Diff', tmp.diff.day, tmp.diff.week, tmp.diff.month, tmp.diff.year, tmp.diff.all)});
		return tmp.div;
	},
	//gen the difference row
	genDiff: function(profitpanel) {
		var tmp = {};
		tmp.income = {};
		$(profitpanel).getElementsBySelector('.dataCol[income]').each(function(item){
			tmp.colName = item.readAttribute('income');
			tmp.income[tmp.colName]= item.readAttribute('value');
		});
		
		tmp.diff = {};
		$(profitpanel).getElementsBySelector('.dataCol[expense]').each(function(item){
			tmp.colName = item.readAttribute('expense');
			tmp.diff[tmp.colName] = (tmp.income[tmp.colName] - item.readAttribute('value'));
		});
		return tmp.diff;
	},
	//getting the url to the transactions
	makeUrl: function(start, end, isIncome) {
		var tmp = {};
		tmp.toAccIds = (isIncome === true ? this.accIds.income : this.accIds.expense);
		tmp.array = {"fromAccountIds" : [],
                "toAccountIds": tmp.toAccIds,
                "fromDate": start,
                "toDate": end
		};
		return "/reports/" + Object.toJSON(tmp.array);
	}
};