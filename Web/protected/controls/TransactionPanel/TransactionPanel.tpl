<com:TPanel ID="TransPanel" DefaultButton="submitBtn" CssClass="TransPanel">
	<div class="transDiv">
	   <div class="row">
	       <com:TActiveLabel Id="errorMsg" ForeColor="red"/>
	       <com:TActiveLabel Id="infoMsg" ForeColor="green"/>
	   </div>
	   <div class="row">
	       <span class="title">From: <com:TActiveLabel Id="fromAccountsMsg" ForeColor="red"/></span>
	       <span class="item">
	           <com:TDropDownList Id="fromAccounts" DataValueField="id" DataTextField="longshot" Attributes.transpane="fromAccounts"/>
	       </span>
	   </div>
	   <div class="row">
	       <span class="title">To: <com:TActiveLabel Id="toAccountsMsg" ForeColor="red"/></span>
	       <span class="item">
	           <com:TDropDownList Id="toAccounts" DataValueField="id" DataTextField="longshot" Attributes.transpane="toAccounts"/>
	       </span>
	   </div>
	   <div class="row">
	       <span class="title">Value:</span>
	       <span class="item">
               $<com:TTextBox Id="transValue" Attributes.transpane="value"/>
               <com:TActiveLabel Id="valueMsg" ForeColor="red"/>
	       </span>
	   </div>
	   <div class="row">
	       <span class="title">Description:</span>
	       <span class="item">
               <com:TTextBox Id="description" Attributes.transpane="description"/>
	       </span>
	   </div>
	   <div class="row">
	       <span class="title"></span>
	       <span class="item">
               <com:TButton Id="submitBtn" Text="save" OnClick="save" Attributes.transpane="saveBtn"/>
	       </span>
	   </div>
	</div>
</com:TPanel>