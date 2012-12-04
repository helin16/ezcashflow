//this is the source file for the AccountList page
var AppJs = new Class.create();
AppJs.prototype = {
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
			tmp.error = 'Your session/request probably timed out, please logout / refine your search criteria and search again!';
			if (noAlert === true)
				throw tmp.error;
			else 
				return alert(tmp.error );
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
};
var appJs = new AppJs();