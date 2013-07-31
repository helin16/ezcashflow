//this is the source file for the AccountList page
var AppJs = new Class.create();
AppJs.prototype = {
	_localStorage: {
		storageKey: 'bkWeb' //the localstorage key
		//private function: get local storage
		,getStorage: function() {
			if(!localStorage[this.storageKey])
				return {};
			var tmp = localStorage[this.storageKey].evalJSON();
			return tmp[this.storageKey] || {};
		}
		//private function: save storage
		,saveStorage:  function(newData) {
			var tmp = {};
			if (!localStorage[this.storageKey])
				localStorage[this.storageKey] = Object.toJSON(tmp);

			tmp.current = localStorage[this.storageKey].evalJSON();
			tmp.current[this.storageKey] = newData;
			localStorage[this.storageKey] = Object.toJSON(tmp.current);
		}
		//public function: add section into localStorage
		,getData: function(sectionId) {
			var tmp = {};
			tmp.Storage = this.getStorage();
			return (tmp.Storage[sectionId] || {});
		}
		//public function: add section into localStorage
		,saveData:  function(sectionId, newData) {
			var tmp = {};
			tmp.data = this.getStorage();
			tmp.data[sectionId] = newData;
			this.saveStorage(tmp.data);
		}
		//public funtion: removing a section from localStorage
		,removeFromStorage: function(sectionId) {
			var tmp = {};
			tmp.data = this.getStorage();
			if (tmp.data[sectionId])
				delete tmp.data[sectionId];
			this.saveStorage(tmp.data);
		}
		// dangerous, this is clear the whole Bsuite Local storage
		,clearStorage: function() {
			localStorage[this.storageKey] = Object.toJSON({});
		}
	}
	//the data we are holding for a page
	,_pageData: {
		accounts: {} //all the accountentries
	}

	//constructor
	,initialize: function () {
		this._pageData = this._localStorage.getStorage();
	}
	
	,setPageData: function(sectionId, newData) {
		this._localStorage.saveData(sectionId, newData);
		this._pageData[sectionId] = this._localStorage.getData(sectionId);
	}
	
	,getPageData: function(sectionId) {
		return this._pageData[sectionId] || {};
	}
	
	//posting an ajax request
	,postAjax: function(callbackId, data, requestProperty) {
		var tmp = {};
		tmp.request = new Prado.CallbackRequest(callbackId, requestProperty);
		tmp.request.setCallbackParameter(data);
		tmp.request.dispatch();
		return tmp.request;
	}
	//parsing an ajax response
	,getResp: function (response, expectNonJSONResult, noAlert) {
		var tmp = {};
		tmp.expectNonJSONResult = (expectNonJSONResult !== true ? false : true);
		tmp.result = response;
		if(tmp.expectNonJSONResult === true)
			return tmp.result;
		if(!tmp.result.isJSON()) {
			tmp.error = 'Invalid JSON string: ' + tmp.result;
			if (noAlert === true)
				throw tmp.error;
			else 
				return alert(tmp.error);
		}
		tmp.result = tmp.result.evalJSON();
		if(tmp.result.errors.size() !== 0) {
			tmp.error = 'Error: \n\n' + tmp.result.errors.join('\n');
			if (noAlert === true)
				throw tmp.error;
			else 
				return alert(tmp.error);
		}
		return tmp.result.resultData;
	}
	//format the currency
	,getCurrency: function(number, dollar, decimal, decimalPoint, thousandPoint) {
		var tmp = {};
		tmp.decimal = (isNaN(decimal = Math.abs(decimal)) ? 2 : decimal);
		tmp.dollar = (dollar == undefined ? "$" : dollar);
		tmp.decimalPoint = (decimalPoint == undefined ? "." : decimalPoint);
		tmp.thousandPoint = (thousandPoint == undefined ? "," : thousandPoint);
		tmp.sign = (number < 0 ? "-" : "");
		tmp.Int = parseInt(number = Math.abs(+number || 0).toFixed(tmp.decimal)) + "";
		tmp.j = (tmp.j = tmp.Int.length) > 3 ? tmp.j % 3 : 0;
		return tmp.dollar + tmp.sign + (tmp.j ? tmp.Int.substr(0, tmp.j) + tmp.thousandPoint : "") + tmp.Int.substr(tmp.j).replace(/(\d{3})(?=\d)/g, "$1" + tmp.thousandPoint) + (tmp.decimal ? tmp.decimalPoint + Math.abs(number - tmp.Int).toFixed(tmp.decimal).slice(2) : "");
	 }
};
var appJs = new AppJs();