<com:TPanel ID="ProfitPanel" CssClass="ProfitPanel">
    <img src="/contents/images/loading.gif" style="display:block; with:150px; height:150px;"/>
</com:TPanel>

<com:TCallback ID="getInfoBtn" OnCallback="getInfo" />
<com:TClientScript>
    var profitJs = new ProfitPanel('<%= $this->ProfitPanel->getClientId()%>', '<%= $this->getInfoBtn->getUniqueID()%>');
    profitJs.loadInfo('30003', '40002');
</com:TClientScript>