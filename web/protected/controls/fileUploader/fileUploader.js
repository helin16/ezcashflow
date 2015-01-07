/**
 * The file uploader Js file
 */
var FileUploaderJs = new Class.create();
FileUploaderJs.prototype = {
	//constructor
	initialize: function (pageJs) {
		this._pageJs = pageJs;
	}
   ,_handleFileSelect: function(files) {
		var tmp = {};
		tmp.me = this;
		tmp.files = files;
		console.debug(tmp.files);
	}
	,getFileUploader: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'file-uploader-wrapper'})
			.insert({'bottom': new Element('div', {'class': 'file-drop-zone text-center', 'title': 'Drag and drop some files here.'})
				.insert({'bottom': new Element('input', {'class': 'col-sm-6 file-input hidden-xs hidden-sm hidden-md hidden-lg', 'type': 'file', 'name': 'files[]', 'multiple': true})
					.observe('change', function(evt){
						tmp.me._handleFileSelect(evt.target.files);
					})
				})
				.insert({'bottom': new Element('span', {'class': ''}).update('Drag and drop some files here.') })
				.observe('click', function(){
					$(this).down('.file-input').click();
				})
				.observe('dragover', function(evt) {
					evt.stopPropagation();
				    evt.preventDefault();
				    evt.dataTransfer.dropEffect = 'copy';
				})
				.observe('drop', function(evt) {
					evt.stopPropagation();
				    evt.preventDefault();
				    tmp.me._handleFileSelect(evt.dataTransfer.files);
				})
			})
			.insert({'bottom': new Element('div', {'class': 'chosen-file'}) });
		return tmp.newDiv;
	}
};