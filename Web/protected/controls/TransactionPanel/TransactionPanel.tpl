<com:TPanel ID="TransPanel" CssClass="TransPanel">
    <div class="transDiv">
		<div class="row">
	       <span class="title">From:</span>
	       <span class="item"><select Id="fromAccounts_<%= $this->getId()%>" transpane="fromAccounts" ></select></span>
       </div>
		<div class="row">
	       <span class="title">To:</span>
	       <span class="item"><select Id="toAccounts_<%= $this->getId()%>" transpane="toAccounts" ></select></span>
       </div>
	   <div class="row">
	       <span class="title">Value:</span>
	       <span class="item">$<input type="text" transpane="value" placeholder="0.00"/></span>
	   </div>
	   <div class="row">
	       <span class="title">Description:</span>
	       <span class="item"><input type="text" transpane="description" placeholder="comments"/></span>
	   </div>
	   <div class="row">
	       <span class="title"></span>
	       <span class="item"><input value="save" type="button" transpane="saveBtn" class="submitBtn" onclick="transJs.saveTrans(this, '<%= $this->getPostJs()%>');return false;"/></span>
	   </div>
    </div>
</com:TPanel>

<com:TCallback ID="getAccountsBtn" OnCallback="getAccounts" />
<com:TCallback ID="saveTrans" OnCallback="saveTrans" />
<com:TClientScript>
    var transJs = new TransPaneJs('<%= $this->TransPanel->getClientId()%>', '<%= $this->getAccountsBtn->getUniqueID()%>', '<%= $this->saveTrans->getUniqueID()%>');
    transJs.buildFrom([1,2], [4]);
</com:TClientScript>