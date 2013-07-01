//this is the source file for the AssetTypeController
var EofyControllerJS = new Class.create();
EofyControllerJS.prototype = {
		//constructor
		initialize: function () {},
		
		//load the first date of the last financial year
		loadLastFY: function(fromDateBox, toDateBox) {
			var tmp = {};
			tmp.now = new Date();
			$(fromDateBox).value = (tmp.now.getFullYear() - 1) + '-07-01 00:00:00'; 
			$(toDateBox).value = (tmp.now.getFullYear()) + '-06-30 23:59:59';
		}
};