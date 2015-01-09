/**
 * The file uploader Js file
 */
var FileUploaderJs = new Class.create();
FileUploaderJs.prototype = {
	//constructor
	initialize: function (pageJs) {
		this._pageJs = pageJs;
	}
	,_getProgressBar: function(theFile) {
		var tmp = {};
		tmp.me = this;
		tmp.file = theFile;
		tmp.newDiv = new Element('div', {'class': 'chosen-file-progress-div row'})
			.insert({'bottom': new Element('div', {'class': 'col-sm-4'}).update(theFile.name) })
			.insert({'bottom': new Element('div', {'class': 'col-sm-8'})
				.insert({'bottom': new Element('div', {'class': 'progress', 'style': 'margin: 0px;'})
					.insert({'bottom': new Element('div', {'class': 'progress-bar progress-bar-success', 'aria-valuemax': '100', 'aria-valuemin': '0', 'aria-valuenow': '0', 'role': 'progressbar'}).setStyle({'width': '0%'})
						.insert({'bottom': new Element('span', {'class': 'percentage'}).update(0) })
						.insert({'bottom': '%' })
					})
				})
			});
		return tmp.newDiv;
	}
	,_getChosenFileListItem: function(theFile) {
		var tmp = {};
		tmp.me = this;
		tmp.file = theFile;
		tmp.newDiv = new Element('div', {'class': 'chosen-file-list-item', 'style': 'padding: 3px 0;'})
			.insert({'bottom': tmp.me._getProgressBar(theFile)});
		return tmp.newDiv;
	}
	,_readFile: function (theFile, listPanel) {
		var tmp = {};
		tmp.me = this;
		tmp.file = theFile;
		tmp.fileReader = new FileReader();
		tmp.fileReader.onloadstart = function(evt) {
			$(listPanel).insert({'bottom': tmp.fileItemDiv = tmp.me._getChosenFileListItem(theFile) });
		};
		tmp.fileReader.onloadend = function(evt) {
			if (evt.target.readyState == FileReader.DONE) {
				console.debug('done');
			}
		};
		tmp.fileReader.onerror = function(evt) {
			tmp.errMsg = '';
			switch(evt.target.error.code) {
		      case evt.target.error.NOT_FOUND_ERR:
		    	  tmp.errMsg = 'File Not Found!';
		        break;
		      case evt.target.error.NOT_READABLE_ERR:
		    	  tmp.errMsg = 'File Not readable!';
		        break;
		      case evt.target.error.ABORT_ERR:
		        break; // noop
		      default:
	    	    tmp.errMsg = 'An error occurred reading this file.';
		    };
		    if(!tmp.errMsg.blank())
		    	tmp.fileItemDiv.down('.progress-bar').removeClassName('progress-bar-success').addClassName('progress-bar-danger').update('Error: ' + tmp.errMsg);
		};
		tmp.fileReader.readAsBinaryString(tmp.file);
		return tmp.me;
	}
    ,_handleFileSelect: function(files, listPanel) {
		var tmp = {};
		tmp.me = this;
		tmp.files = files;
		for (tmp.i = 0; tmp.file = tmp.files[tmp.i]; tmp.i++) {
			tmp.me._readFile(tmp.file, listPanel);
		};
		return tmp.me;
	}
	,getFileUploader: function() {
		var tmp = {};
		tmp.me = this;
		tmp.newDiv = new Element('div', {'class': 'file-uploader-wrapper'})
			.insert({'bottom': new Element('div', {'class': 'file-drop-zone text-center', 'title': 'Drag and drop some files here.'})
				.insert({'bottom': new Element('input', {'class': 'col-sm-6 file-input hidden-xs hidden-sm hidden-md hidden-lg', 'type': 'file', 'name': 'files[]', 'multiple': true})
					.observe('change', function(evt){
						tmp.me._handleFileSelect(evt.target.files, $(this).up('.file-uploader-wrapper').down('.chosen-file-list'));
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
				    tmp.me._handleFileSelect(evt.dataTransfer.files, $(this).up('.file-uploader-wrapper').down('.chosen-file-list'));
				})
			})
			.insert({'bottom': new Element('div', {'class': 'chosen-file-list'}) });
		return tmp.newDiv;
	}
};