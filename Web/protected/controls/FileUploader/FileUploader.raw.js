var FileUploaderJs = new Class.create();
FileUploaderJs.prototype = {
	serverUrl: '', //the url of handling the uploaded file
	totalWrapperId: '', //the id of the total wrapper
	uploadedFiles: {}, //the uploaded files
	
	//constructor
	initialize: function (totalWrapperId, serverUrl) {
		this.totalWrapperId = totalWrapperId;
		this.serverUrl = (!serverUrl ? '/asset/upload' : ''); 
	}
	
	,_getInitHtml: function() {
		var tmp = {};
		$(this.totalWrapperId).update(new Element('div', {'class': 'fileUploadWrapper'})
			.insert({'bottom': new Element('input', {'class': 'fileInitBtn', 'type': 'file', 'name': 'files[]', 'data-url': this.serverUrl, 'multiple': ''}) })
			.insert({'bottom': new Element('div', {'class': 'progressBarDiv'}).update(new Element('div', {'class': 'bar', 'style': 'width: 0%'})) })
			.insert({'bottom': new Element('div', {'class': 'uploadedFileList'}) })
		);
	}
	
	,reset: function() {
		$(this.totalWrapperId).down('.fileUploadWrapper').update('');
		this.uploadedFiles = {};
	}
	
	//initializing the file uploader using jquery
	,initFileUploader: function() {
		this._getInitHtml();
		var tmp = {};
		tmp.me = this;
		$j('#' + this.totalWrapperId + ' .fileInitBtn[type=file]').fileupload({
            dataType: "json",
            done: function (e, data) {
                $j.each(data.result.files, function (index, file) {
                    $j("<div class='uploadedfile' filepath='" + file.filepath + "'/>")
                    	.html(file.name)
                    	.append($j('<span class="delFile" />').html('x')
                    		.click(function() {
                    			tmp.me.delFile(this);
                    		})
                    	)
                    	.appendTo($j('#' + tmp.me.totalWrapperId + ' .uploadedFileList'));
                    tmp.me.uploadedFiles[file.filepath] = file;
                });
            },
            
            //showing the progress bar
            progressall: function (e, data) {
                tmp.progress = parseInt(data.loaded / data.total * 100, 10);
                tmp.barDiv = $j('#' + tmp.me.totalWrapperId + ' .progressBarDiv .bar').css('width', tmp.progress + '%').html('');
                if(tmp.progress < 100) {
                	tmp.barDiv.append($j('<span class="barTxt" />').html(tmp.progress + '%'));
                }
            }
        });
		return this;
	}
	
	,delFile: function(btn) {
		if(!confirm('Are you sure you want to delete this uploaded file?')) {
			return;
		}
		var tmp = {};
		tmp.me = this;
		tmp.fileItemDiv = $(btn).up('.uploadedfile');
		delete tmp.me.uploadedFiles[tmp.fileItemDiv.readAttribute('filepath')];
		tmp.fileItemDiv.remove();
	}
}