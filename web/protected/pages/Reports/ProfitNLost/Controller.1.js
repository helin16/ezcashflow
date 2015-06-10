/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new BackEndPageJs(), {
	/**
	 * initialising
	 */
	init : function() {
		var tmp = {};
		tmp.me = this;
		jQuery('.date-picker').datetimepicker({
			'format': 'DD/MMM/YYYY HH:mm A'
		});
		if(moment().month() > 5) { //later year
			jQuery('.date-picker[search-field="from-date"]').data('DateTimePicker').date(moment().month('July').startOf('month'));
			jQuery('.date-picker[search-field="to-date"]').data('DateTimePicker').date(moment().add(1, 'year').month('June').endOf('month'));
		} else { //first half year
			jQuery('.date-picker[search-field="from-date"]').data('DateTimePicker').date(moment().subtract(1, 'year').month('July').startOf('month'));
			jQuery('.date-picker[search-field="to-date"]').data('DateTimePicker').date(moment().month('June').endOf('month'));
		}

		return tmp.me;
	}
});