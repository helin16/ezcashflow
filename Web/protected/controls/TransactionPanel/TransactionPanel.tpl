<com:TPanel ID="TransPanel" CssClass="TransPanel">
    <div class="transDiv">
		<div class="row">
	       <span class="title">From:</span>
	       <span class="item"><select Id="fromAccounts_<%= $this->getId()%>" transpane="fromAccounts" class="inputtxt"></select></span>
       </div>
		<div class="row">
	       <span class="title">To:</span>
	       <span class="item"><select Id="toAccounts_<%= $this->getId()%>" transpane="toAccounts" class="inputtxt"></select></span>
       </div>
       <div class="row">
		   <div class="inline date">
		       <span class="title">Date:</span>
		       <span class="item"><input type="text" transpane="transDate" placeholder="Transaction Date" class="inputtxt" readonly /></span>
		   </div>
		   <div class="inline value">
		       <span class="title">Value($):</span>
		       <span class="item"><input type="text" transpane="value" placeholder="0.00"  class="inputtxt"/></span>
		   </div>
		   <div class="inline description">
		       <span class="title">Description:</span>
		       <span class="item"><input type="text" transpane="description" placeholder="comments"  class="inputtxt"/></span>
		   </div>
	   </div>
       <fieldset class="fileset">
           <legend>
                <input id="chk_<%= $this->getId()%>" type="checkbox" onclick="return transJs.toggleFileList(this);">
                <label for="chk_<%= $this->getId()%>">Attach Files</label>
           </legend>
           <div id="fileUploaderWrapper" class="filewrapper"></div>
       </fieldset>
	   <div class="row">
	       <span class="title"></span>
	       <span class="item"><input value="save" type="button" transpane="saveBtn" class="submitBtn" onclick="transJs.saveTrans(this, function(){<%= $this->getPostJs()%>;return false;})"/></span>
	   </div>
    </div>
</com:TPanel>

<com:TCallback ID="getAccountsBtn" OnCallback="getAccounts" />
<com:TCallback ID="saveTrans" OnCallback="saveTrans" />
<com:TCallback ID="deleteFile" OnCallback="delFile" />
<com:Application.controls.Chosen.Chosen />
<com:Application.controls.FileUploader.FileUploader>
    var transJs = new TransPaneJs('<%= $this->TransPanel->getClientId()%>', 
        '<%= $this->getAccountsBtn->getUniqueID()%>', 
        '<%= $this->saveTrans->getUniqueID()%>', 
        '<%= $this->deleteFile->getUniqueID()%>',
        'fileUploaderWrapper');
    $$('.inputtxt[transpane="transDate"]').each(function(item){
	    new Prado.WebUI.TDatePicker({'ID': item,'InputMode':'TextBox','Format':'yyyy-MM-dd','FirstDayOfWeek':1,'ClassName':'datePicker','CalendarStyle':'default','FromYear':2007,'UpToYear':2030});
    });
</com:Application.controls.FileUploader.FileUploader>
