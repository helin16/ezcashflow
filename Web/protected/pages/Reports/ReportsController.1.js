var ReportJs=new Class.create();ReportJs.prototype={resultPanelId:"",searchPanelId:"",searchCriteria:{search:{},pagination:{pageNo:1,pageSize:30}},callbackIds:{search:"",edit:"",del:"",output:""},initialize:function(d,a,f,c,e,b){this.resultPanelId=d;this.searchPanelId=a;this.callbackIds.search=f;this.callbackIds.edit=c;this.callbackIds.del=e;this.callbackIds.output=b},_getRowDiv:function(b,a){return new Element("div",{"class":"rowwrapper"}).insert({bottom:new Element("span",{"class":"label"}).update(b)}).insert({bottom:new Element("span",{"class":"input"}).update(a)})},initSearchPane:function(e,c,a,f,d){var b={};b.me=this;b.fromDate=(c||"");b.toDate=(a||"");b.fromAccIds=(f||[]);b.toAccIds=(d||[]);b.transId=(e||"");$(b.me.searchPanelId).update(new Element("div",{"class":"content-box searchPanel"}).insert({bottom:new Element("h3",{"class":"box-title"}).update("Search Criterias: ").insert({bottom:new Element("span",{"class":"inlineblock hidesearchwrapper"}).insert({bottom:new Element("input",{type:"checkbox","class":"hidesearchbtn",checked:true}).observe("click",function(){$(this).up(".content-box").down(".box-content").toggle()})}).insert({bottom:new Element("label").update("Show Search")})})}).insert({bottom:new Element("div",{"class":"box-content"}).insert({bottom:new Element("div",{"class":"row fullwidth"}).insert({bottom:b.me._getRowDiv("From Date:",new Element("input",{"class":"searchdate rndcnr fullwidth",type:"text",searchpane:"date_start",value:b.fromDate,readyonly:true})).addClassName("halfcut")}).insert({bottom:b.me._getRowDiv("To Date:",new Element("input",{"class":"searchdate rndcnr fullwidth",type:"text",searchpane:"date_end",value:b.toDate,readyonly:true})).addClassName("halfcut")})}).insert({bottom:b.me._getRowDiv("From Account:",b.me._getAccList("Select some AccountEntries",b.fromAccIds).writeAttribute("searchpane","fromacc")).addClassName("row fullwidth")}).insert({bottom:b.me._getRowDiv("To Account:",b.me._getAccList("Select some AccountEntries",b.toAccIds).writeAttribute("searchpane","toacc")).addClassName("row fullwidth")}).insert({bottom:new Element("div",{"class":"row"}).insert({bottom:new Element("input",{type:"hidden",value:b.transId,searchpane:"transId"})}).insert({bottom:new Element("input",{type:"button",value:"Search","class":"submitBtn"}).observe("click",function(){b.me.search()})}).insert({bottom:new Element("input",{type:"button",value:"Output to Excel","class":"submitBtn"}).observe("click",function(){b.me.outputToExcel()})})})}));return this},getSearchPanel:function(){return $(this.searchPanelId)},_getAccList:function(c,a){var b={};b.selectedValues=a||[];b.selectBox=new Element("select",{"class":"chosen-select",multiple:true,"data-placeholder":c});b.accounts=appJs.getPageData("accounts");$H(b.accounts).each(function(d){b.optgroup=new Element("optgroup",{label:b.accounts[d.key][d.key].name});$H(d.value).each(function(e){if(d.key!==e.value.id){b.option=new Element("option",{value:e.key}).update(e.value.breadCrumbs.name);if(b.selectedValues.indexOf(e.key*1)>=0){b.option.writeAttribute("selected",true)}b.optgroup.insert({bottom:b.option})}});b.selectBox.insert({bottom:b.optgroup})});return b.selectBox},initialDatePicker:function(a){var b={};b.results=[];$$(a).each(function(c){b.hourString="00:00:00";b.searchPanelAttr=c.readAttribute("searchpane");if(b.searchPanelAttr!==undefined&&b.searchPanelAttr!==null){b.hourString=b.searchPanelAttr.strip().include("_start")?"00:00:00":"23:59:59"}b.results.push(new Prado.WebUI.TDatePicker({ID:c,InputMode:"TextBox",Format:"yyyy-MM-dd "+b.hourString,FirstDayOfWeek:1,ClassName:"datePicker",CalendarStyle:"default",FromYear:2007,UpToYear:2030}))});return b.results},initChosen:function(a){var b={};b.results=[];$$(a).each(function(c){b.results.push(new Chosen(c,{"over-flow":"auto"}))});return b.results},search:function(){var a={};a.me=this;a.resultPanel=$(this.resultPanelId);a.searchPane=$(this.searchPanelId);a.searchCriterias={};a.searchPane.getElementsBySelector("[searchpane]").each(function(b){a.field=b.readAttribute("searchpane").strip();a.value=$F(b);a.searchCriterias[a.field]=a.value});a.me.searchCriteria.search=a.searchCriterias;a.me.searchCriteria.pagination.pageNo=1;appJs.postAjax(this.callbackIds.search,this.searchCriteria,{onLoading:function(b,c){a.resultPanel.show().down(".box-content").update('<img src="/contents/images/loading.gif" />')},onComplete:function(b,c){a.result=appJs.getResp(c);a.transCount=a.result.trans.size();if(a.accCount===0){a.resultPanel.down(".box-content").update("No Transactions Found!");return}a.resultPanel.getElementsBySelector(".noOfTrans").each(function(d){d.update(a.result.total)});a.resultPanel.down(".box-content").update("");for(a.i=0;a.i<a.transCount;a.i++){a.rowNo=(a.me.searchCriteria.pagination.pageNo-1)*a.me.searchCriteria.pagination.pageSize+a.i+1;a.newRow=a.me.getRow(a.result.trans[a.i],a.rowNo);a.resultPanel.down(".box-content").insert({bottom:a.newRow})}if(a.rowNo<a.result.total){a.resultPanel.down(".box-content").insert({bottom:a.me.getMoreBtn(a.rowNo,a.result.total)})}}})},outputToExcel:function(){var a={};a.me=this;a.searchPane=$(this.searchPanelId);a.searchCriterias={};a.searchPane.getElementsBySelector("[searchpane]").each(function(b){a.field=b.readAttribute("searchpane").strip();a.value=$F(b);a.searchCriterias[a.field]=a.value});a.me.searchCriteria.search=a.searchCriterias;appJs.postAjax(this.callbackIds.output,this.searchCriteria,{onLoading:function(b,c){},onComplete:function(b,c){a.result=appJs.getResp(c);if(!a.result||!a.result.trans){alert("ERROR: NOthing come back");return}a.data=[];a.data.push(["created time","from Account","to Account","amount","Comments"].join(", ")+"\n");a.result.trans.each(function(d){a.row=d.created+", "+(!d.fromAcc.id?"":d.fromAcc.breadCrumbs.name)+", "+d.toAcc.breadCrumbs.name+", "+d.value+", "+d.comments+"\n";a.data.push(a.row)});a.now=new Date();a.fileName="transactions_"+a.now.getFullYear()+"_"+a.now.getMonth()+"_"+a.now.getDate()+"_"+a.now.getHours()+"_"+a.now.getMinutes()+"_"+a.now.getSeconds()+".csv";a.blob=new Blob(a.data,{type:"text/csv;charset=utf-8"});saveAs(a.blob,a.fileName)}})},getMoreBtn:function(c,b){var a={};if(c>=b){return}a.me=this;a.newMoreBtn=new Element("input",{type:"button","class":"showMoreBtn",value:"Show More Transactions"}).observe("click",function(){a.me.getMoreTrans(this)});return a.newMoreBtn},getRow:function(b,c){var a={};a.me=this;a.newRow=new Element("div",{"class":"row "+(c%2===0?"even":"odd"),transId:b.id,rowno:c});a.newRowContent=new Element("span",{"class":"conent"});a.newRowContent.insert({bottom:new Element("span",{"class":"fromacc"}).update("From: "+(b.fromAcc.name===undefined?"":b.fromAcc.breadCrumbs.name))});a.newRowContent.insert({bottom:new Element("span",{"class":"created"}).update(b.created)});a.newRowContent.insert({bottom:new Element("span",{"class":"toacc"}).update("To&nbsp;&nbsp;&nbsp;&nbsp;: "+b.toAcc.breadCrumbs.name)});a.newRowContent.insert({bottom:new Element("span",{"class":"value"}).update(appJs.getCurrency(b.value))});a.newRowContent.insert({bottom:new Element("span",{"class":"comments"}).update(b.comments)});a.assetsDiv=new Element("ul",{"class":"assets"});b.assets.each(function(d){a.assetsDivLi=new Element("li",{"class":"assets"});a.assetsDivLi.update(new Element("a",{"class":"assetlink",href:"/asset/"+d.assetKey,target:"_blank"}).update(d.filename));a.assetsDiv.insert({bottom:a.assetsDivLi})});a.newRowContent.insert({bottom:a.assetsDiv});a.newRow.insert({bottom:a.newRowContent});a.newRowBtns=new Element("span",{"class":"btns"});a.newRowBtns.insert({bottom:new Element("a",{"class":"btn",href:"javascript: void(0);"}).update("Edit").observe("click",function(){a.me.showEditTrans(this,b)})});a.newRowBtns.insert({bottom:new Element("a",{"class":"btn",href:"javascript: void(0);"}).update("Delete").observe("click",function(){a.me.delTrans(b.id)})});a.newRow.insert({bottom:a.newRowBtns});return a.newRow},getMoreTrans:function(b){var a={};a.me=this;a.orgianlBtnValue=$(b).value;a.resultPanel=$(this.resultPanelId);a.me.searchCriteria.pagination.pageNo+=1;appJs.postAjax(this.callbackIds.search,this.searchCriteria,{onLoading:function(c,d){$(b).writeAttribute("value","Getting more Transactions ...").disabled=true},onComplete:function(c,f){try{a.result=appJs.getResp(f);a.transCount=a.result.trans.size();$(b).remove();for(a.i=0;a.i<a.transCount;a.i++){a.rowNo=(a.me.searchCriteria.pagination.pageNo-1)*a.me.searchCriteria.pagination.pageSize+a.i+1;a.newRow=a.me.getRow(a.result.trans[a.i],a.rowNo);a.resultPanel.down(".box-content").insert({bottom:a.newRow})}if(a.rowNo<a.result.total){a.resultPanel.down(".box-content").insert({bottom:a.me.getMoreBtn(a.rowNo,a.result.total)})}}catch(d){alert(d);if($(b)!==undefined&&$(b)!==null){$(b).writeAttribute("value",a.orgianlBtnValue).disabled=false}}}})},delTrans:function(b){var a={};if(!confirm("Are you sure you want to delete this transaction?")){return}appJs.postAjax(this.callbackIds.del,{transId:b},{onComplete:function(c,f){try{a.result=appJs.getResp(f);if(a.result.id===undefined||a.result.id===null||a.result.id.blank()){throw"System Error: trans.id not provided!"}$$(".row[transid="+a.result.id+"]").each(function(e){e.remove()})}catch(d){alert(d);if($(btn)!==undefined&&$(btn)!==null){$(btn).writeAttribute("value",a.orgianlBtnValue).disabled=false}}}})},saveTrans:function(b,c){var a={};a.me=this;a.row=$(b).up(".editrow");a.savingPanel=a.row.up(".editDiv");a.savingPanel.getElementsBySelector(".newAccError").each(function(d){d.remove()});a.transInfo={transId:c};a.hasError=false;a.savingPanel.getElementsBySelector("[transinfo]").each(function(d){a.field=d.readAttribute("transinfo").strip();switch(a.field){case"fromacc":case"comments":a.transInfo[a.field]=$F(d);break;case"date":case"toacc":a.value=$F(d);if(a.value.blank()){d.up(".rowwrapper").down(".label").insert({bottom:new Element("span",{"class":"newAccError"}).update(a.field+" is needed!")});a.hasError=true}a.transInfo[a.field]=a.value;break;case"value":a.value=$F(d);if(!a.value.match(/^\d*(\.|)\d{0,2}$/g)){d.up(".rowwrapper").down(".label").insert({bottom:new Element("span",{"class":"newAccError"}).update("Invalid value, expected: 0.00!")});a.hasError=true}a.transInfo[a.field]=a.value;break;case"assets":a.assets={};d.getElementsBySelector(".uploadedfile").each(function(e){a.assets[e.readAttribute("assetkey")]=(e.readAttribute("delete")?false:true)});a.transInfo[a.field]=a.assets;break;case"attachments":a.fileHandler=$(d).retrieve("fileHandler");a.transInfo[a.field]=a.fileHandler.uploadedFiles;break}});if(a.hasError===true){return}a.savingInfo=new Element("div").update("saving ...");appJs.postAjax(this.callbackIds.edit,a.transInfo,{onLoading:function(d,e){a.row.hide().insert({after:a.savingInfo})},onComplete:function(d,g){try{a.trans=appJs.getResp(g,false,true);if(a.trans.id===undefined||a.trans.id.blank()){throw"System Error:Invalid trans id!"}a.transRow=$(b).up(".row");a.transRow.replace(a.me.getRow(a.trans,a.transRow.readAttribute("rowno"))).scrollTo();a.savingPanel.remove()}catch(f){a.savingInfo.remove();a.row.show();alert(f)}}})},showEditTrans:function(c,b){var a={};a.me=this;$$(".editDiv").each(function(d){d.remove()});a.accSelect=$$('select[searchpane="fromacc"]').first();a.fileUploaderWrapperId="attachments_"+b.id;a.transRow=$(c).up(".row");a.transRow.insert({bottom:new Element("div",{id:"edittrans_"+b.id,"class":"editDiv"}).insert({bottom:a.me._getRowDiv("From: ",a.me._getAccSelectBox(a.accSelect.innerHTML,b.fromAcc.id).writeAttribute("transinfo","fromacc").addClassName("editrow"))}).insert({bottom:a.me._getRowDiv("To: ",a.me._getAccSelectBox(a.accSelect.innerHTML,b.toAcc.id).writeAttribute("transinfo","toacc").addClassName("editrow"))}).insert({bottom:new Element("div").addClassName("editrow").insert({bottom:new Element("span",{"class":"date inlineblock"}).update(a.me._getRowDiv("Date: ",new Element("input",{transinfo:"date",type:"text","class":"transdate inputbox fullwidth rndcnr",placeholder:"Created Date",value:b.created})))}).insert({bottom:new Element("span",{"class":"amount inlineblock"}).update(a.me._getRowDiv("Value: $",new Element("input",{transinfo:"value",type:"text","class":"transvalue inputbox fullwidth rndcnr",placeholder:"Value",value:b.value})))}).insert({bottom:new Element("span",{"class":"descr inlineblock"}).update(a.me._getRowDiv("Comments: ",new Element("input",{transinfo:"comments",type:"text","class":"transcomments inputbox fullwidth rndcnr",placeholder:"Comments",value:b.comments})))})}).insert({bottom:a.me._getRowDiv("Attachments: ",new Element("div").addClassName("editrow").insert({bottom:a.me._getAssetListDiv(b.assets)}).insert({bottom:new Element("span",{transinfo:"attachments",id:a.fileUploaderWrapperId})}))}).insert({bottom:new Element("div").addClassName("editrow").insert({bottom:new Element("input",{"class":"submitBtn",type:"button",value:"Save"}).observe("click",function(){a.me.saveTrans(this,b.id)})}).insert({bottom:new Element("input",{"class":"submitBtn",type:"button",value:"Cancel",style:"float:right;"}).observe("click",function(){$(c).up(".row").down(".editDiv").remove()})})})});a.me.initialDatePicker("input[transinfo=date]");a.me.initChosen("[transinfo=fromacc]");a.me.initChosen("[transinfo=toacc]");a.fileHandler=new FileUploaderJs(a.fileUploaderWrapperId).initFileUploader();a.transRow.down("#"+a.fileUploaderWrapperId).store("fileHandler",a.fileHandler);a.transRow.store("trans",b)},_getAssetListDiv:function(b){var a={};a.div=new Element("div",{"class":"assets uploadedFileList",transinfo:"assets"});b.each(function(c){a.div.insert({bottom:new Element("div",{"class":"uploadedfile",assetkey:c.assetKey}).update(c.filename).insert({bottom:new Element("span",{"class":"delFile"}).update("x").observe("click",function(){if(!confirm("Are you sure you want to delete this asset?")){return}$(this).up(".uploadedfile").hide().writeAttribute("delete",true)})})})});return a.div},_getAccSelectBox:function(a,c){var b={};b.selectCloneFrom=new Element("select",{"class":"transfrom inputbox"}).update(a).insert({top:new Element("option",{value:""}).update("Please select ...")});if(c!==undefined&&!c.blank()){b.selectCloneFrom.down("[value="+c+"]").writeAttribute("selected",true)}else{b.selectCloneFrom.selectedIndex=0}return b.selectCloneFrom}};