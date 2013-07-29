//this is the source file for the AccountList page
var AppJs = new Class.create();
AppJs.prototype = {
	//the localStorage of the javascript
	localStorage: {
		storageKey: 'bkWEB',
		
		//private function: get local storage
		getStorage: function() {
			var tmp = {};
			return (!localStorage[storageKey]) ? {} : $.parseJSON(localStorage[storageKey])[storageKey]
		},

		//private function: save storage
		saveStorage: function(newData) {
			var tmp = {};
			if (!localStorage[storageKey])
				localStorage[storageKey] = JSON.stringify(tmp);

			tmp.current = $.parseJSON(localStorage[storageKey]);
			tmp.current[storageKey] = newData;
			localStorage[storageKey] = JSON.stringify(tmp.current);
		},
		
		//public function: add section into localStorage
		getData: function(sectionId) {
			var tmp = {};
			tmp.Storage = getStorage();
			return (tmp.Storage[sectionId] || {});
		},

		//public function: add section into localStorage
		addToStorage: function(sectionId, newData) {
			var tmp = {};
			tmp.data = getStorage();
			tmp.data[sectionId] = newData;
			saveStorage(tmp.data);
			return this;
		},
		
		//public funtion: removing a section from localStorage
		removeFromStorage: function(sectionId) {
			var tmp = {};
			tmp.data = getStorage();
			if (tmp.data[sectionId])
				delete tmp.data[sectionId];
			saveStorage(tmp.data);
			return this;
		},

		// dangerous, this is clear the whole Bsuite Local storage
		clearStorage: function() {
			localStorage[storageKey] = JSON.stringify({});
			return this;
		}
	},
	
	//constructor
	initialize: function () {},
	postAjax: function(callbackId, data, requestProperty) {
		var tmp = {};
		tmp.request = new Prado.CallbackRequest(callbackId, requestProperty);
		tmp.request.setCallbackParameter(data);
		tmp.request.dispatch();
		return tmp.request;
	},
	getResp: function (response, expectNonJSONResult, noAlert) {
		var tmp = {};
		tmp.expectNonJSONResult = (expectNonJSONResult !== true ? false : true);
		tmp.result = response;
		if(tmp.result === null || tmp.result.blank()) {
//			tmp.error = 'Your request probably timed out, please try again later!';
//			if (noAlert === true)
//				throw tmp.error;
//			else 
//				return alert(tmp.error );
		}
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
	},
	//format the currency
	getCurrency: function(number, dollar, decimal, decimalPoint, thousandPoint) {
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
