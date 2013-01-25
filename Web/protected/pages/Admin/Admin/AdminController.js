//this is the source file for the AdminController
var AdminJs = new Class.create();
AdminJs.prototype = {
	canvasId: '',
	canvasTitleId: '',
	iframeId: 'myIframe',
	loadingImg: '<img id="loadingImg" src="/contents/images/loading.gif" style="display:block; with:150px; height:150px;"/>',
	
	//constructor
	initialize: function (canvasId, canvasTitleId) {
		this.canvasId = canvasId;
		this.canvasTitleId = canvasTitleId;
	},
	
	getIframe: function(url) {
		var tmp = {};
		tmp.iframeId = this.iframeId;
		tmp.canvasId = this.canvasId;
		tmp.iframe = new Element('iframe', {'src': url, 'id': tmp.iframeId, 'style': 'display:none;width:100%;height: 800px;', 'scrolling': true, 'allowtransparency': true, 'frameborder': 0}).observe('load', function() {
			$(tmp.canvasId).getElementsBySelector('img#loadingImg').each(function(item){
				item.remove();
			});
			if($(this).contentDocument) {
				$(this).height = $(this).contentDocument.documentElement.getHeight() + 30; //FF 3.0.11, Opera 9.63, and Chrome
			} else {
				$(this).height = $(this).contentWindow.document.body.scrollHeight + 30; //IE6, IE7 and Chrome
			}
			$(this).show();
		});
		return tmp.iframe;
	},
	
	
	//changePage
	changePage: function(btn) {
		var tmp = {};
		tmp.url = $(btn).readAttribute('href');
		tmp.title = $(btn).down('.link').innerHTML;
		$(this.canvasTitleId).update(tmp.title);
		
		//remove exsiting iframe
		tmp.iframeId = this.iframeId;
		$(this.canvasId).getElementsBySelector('iframe#' + tmp.iframeId).each(function(item){
			item.remove();
		});
		
		tmp.iframe = this.getIframe(tmp.url);
		$(this.canvasId).update(this.loadingImg);
		$(this.canvasId).insert({'bottom': tmp.iframe});
		return false;
	}
};