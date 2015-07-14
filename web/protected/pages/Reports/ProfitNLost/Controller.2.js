/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new BackEndPageJs(), {
	genReport: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.data = {};
		jQuery.each(jQuery('[search-field]'), function(index, item) {
			if(jQuery(item).hasClass('date-picker')) {
				tmp.data[jQuery(item).attr('search-field')] = jQuery(item).data('DateTimePicker').date().utc();
			} else {
				tmp.data[jQuery(item).attr('search-field')] = jQuery(item).val();
			}
		});
		tmp.data.utcOffset = moment().utcOffset();
		tmp.me.postAjax(tmp.me.getCallbackId("genReports"), tmp.data, {
			'onLoading': function() {
				jQuery(btn).button('loading');
			}
			,'onSuccess': function(sender, param) {
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					if(!tmp.result || !tmp.result.file || !tmp.result.file.path || !tmp.result.file.name)
						return;
					window.open('/asset/getFile?fileName=' + tmp.result.file.name + '&filePath=' + tmp.result.file.path);
				} catch (e) {
					tmp.me.showModalbox('<strong class="color: red">Error:</strong>', e);
				}
			}
			,'onComplete': function() {
				jQuery(btn).button('reset');
			}
		})
		return tmp.me;
	}
	/**
	 * initialising
	 */
	,init : function() {
		var tmp = {};
		tmp.me = this;
		jQuery('.date-picker').datetimepicker({
			'format': 'DD/MMM/YYYY HH:mm A'
		});
		if(moment().month() > 5) { //later year
			jQuery('.date-picker[search-field="fromDate"]').data('DateTimePicker').date(moment().month('July').startOf('month'));
			jQuery('.date-picker[search-field="toDate"]').data('DateTimePicker').date(moment().add(1, 'year').month('June').endOf('month'));
		} else { //first half year
			jQuery('.date-picker[search-field="fromDate"]').data('DateTimePicker').date(moment().subtract(1, 'year').month('July').startOf('month'));
			jQuery('.date-picker[search-field="toDate"]').data('DateTimePicker').date(moment().month('June').endOf('month'));
		}
		jQuery('#' + tmp.me.getHTMLID('genBtnId')).click(function() {
			tmp.me.genReport(this);
		});
		return tmp.me;
	}
});