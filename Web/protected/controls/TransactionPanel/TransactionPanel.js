var TransPaneJs=new Class.create();TransPaneJs.prototype={accountIds:{from:[],to:[]},formDivId:"",callbackIds:{getAccounts:"",saveTrans:""},initialize:function(a,b,c){this.formDivId=a;this.callbackIds.getAccounts=b;this.callbackIds.saveTrans=c},buildFrom:function(c,a){var b={};this.accountIds.from=c;this.accountIds.to=a;b.formDivId=this.formDivId;b.fromAccListBox=$(b.formDivId).down("[transpane=fromAccounts]");b.toAccListBox=$(b.formDivId).down("[transpane=toAccounts]");b.valueBox=$(b.formDivId).down("[transpane=value]");b.commentsBox=$(b.formDivId).down("[transpane=description]");b.saveBtn=$(b.formDivId).down("[transpane=saveBtn]");b.saveBtnValue=b.saveBtn.value;appJs.postAjax(this.callbackIds.getAccounts,{fromIds:c,toIds:a},{onLoading:function(d,e){b.fromAccListBox.update(new Element("option").update("Loading ..."));b.toAccListBox.update(new Element("option").update("Loading ..."));b.commentsBox.value=b.valueBox.value="";b.saveBtn.value="Loading ...";b.saveBtn.disabled=true},onComplete:function(d,e){b.result=appJs.getResp(e);transJs.getAccList(b.result.from,b.fromAccListBox);transJs.getAccList(b.result.to,b.toAccListBox);b.saveBtn.value=b.saveBtnValue;b.saveBtn.disabled=false}});return false},getAccList:function(b,c){var a={};$(c).update("");$H(b).each(function(d){a.rootName=d.key;a.optGroup=new Element("optgroup",{label:a.rootName});$H(d.value).each(function(e){a.optGroup.insert({bottom:new Element("option",{value:e.value.id}).update(e.value.breadCrumbs.name.replace(a.rootName+" / ","")+" - $"+e.value.sum)})});$(c).insert({bottom:a.optGroup})})},saveTrans:function(btn,postJs){var tmp={};tmp.form=$(btn).up("div.transDiv");tmp.fromAccListBox=tmp.form.down("[transpane=fromAccounts]");tmp.toAccListBox=tmp.form.down("[transpane=toAccounts]");tmp.valueBox=tmp.form.down("[transpane=value]");tmp.commentsBox=tmp.form.down("[transpane=description]");tmp.saveBtn=tmp.form.down("[transpane=saveBtn]");if(this.validForm(tmp.fromAccListBox,tmp.toAccListBox,tmp.valueBox)===false){return false}tmp.saveBtnValue=tmp.saveBtn.value;tmp.data={fromAccId:$F(tmp.fromAccListBox),toAccId:$F(tmp.toAccListBox),value:$F(tmp.valueBox).strip(),comments:$F(tmp.commentsBox).strip(),fromIds:this.accountIds.from,toIds:this.accountIds.to};appJs.postAjax(this.callbackIds.saveTrans,tmp.data,{onLoading:function(sender,param){tmp.saveBtn.value="Saving ...";tmp.saveBtn.disabled=true},onComplete:function(sender,param){tmp.result=appJs.getResp(param);transJs.getAccList(tmp.result.from,tmp.fromAccListBox);transJs.getAccList(tmp.result.to,tmp.toAccListBox);tmp.commentsBox.value=tmp.valueBox.value="";tmp.saveBtn.value=tmp.saveBtnValue;tmp.saveBtn.disabled=false;alert("Saved Successfully!");if(postJs!==undefined){eval(postJs)}}})},validForm:function(c,d,b){var a={};a.succ=true;$(c).up("div.transDiv").getElementsBySelector(".errorMsg").each(function(e){e.remove()});if($F(c).blank()||$F(c)<=0){$(c).up("div.row").down("span.title").insert({bottom:new Element("span",{"class":"errorMsg"}).update("Invalid From")});a.succ=false}if($F(d).blank()||$F(d)<=0){$(d).up("div.row").down("span.title").insert({bottom:new Element("span",{"class":"errorMsg"}).update("Invalid To")});a.succ=false}a.regex=/^(\d{1,3}(\,\d{3})*|(\d+))(\.\d{1,2})?$/;if(!$F(b).strip().match(a.regex)){$(b).insert({after:new Element("span",{"class":"errorMsg"}).update("Invalid Value")});a.succ=false}return a.succ}};