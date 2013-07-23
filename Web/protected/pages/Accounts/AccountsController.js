var AccountsJs=new Class.create();AccountsJs.prototype={rootId:1,divIds:{list:""},callBackIds:{getAccounts:"",saveAccount:"",deleteAccounts:"",moveAccounts:""},initialize:function(c,b,e,d,a){this.divIds.list=c;this.callBackIds.getAccounts=b;this.callBackIds.saveAccount=e;this.callBackIds.deleteAccounts=d;this.callBackIds.moveAccounts=a},_selectAccountType:function(b){var a={};a.clickedBtn=$(b);a.clickedBtn.up("ul").getElementsBySelector("li").each(function(c){c.down("a").removeAttribute("selected")});a.clickedBtn.writeAttribute("selected");return $(b).readAttribute("rootId")},showAccounts:function(b){var a={};a.me=this;appJs.postAjax(this.callBackIds.getAccounts,{rootId:a.me._selectAccountType(b)},{onLoading:function(c,d){$(a.me.divIds.list).update('<img src="/contents/images/loading.gif" />')},onComplete:function(c,f){try{a.me._showAccList(appJs.getResp(f,false,true))}catch(d){alert(d)}}});return false},_showAccList:function(a){var b={};b.me=this;b.accCount=a.size();$(b.me.divIds.list).update("");if(b.accCount===0){$(b.me.divIds.list).update("No Accounts Found!");return}for(b.i=0;b.i<b.accCount;b.i++){$(b.me.divIds.list).insert({bottom:b.me._formatAccountRow(a[b.i]).addClassName(b.i%2===0?"even":"odd").store("account",a[b.i])})}},_formatAccountRow:function(b){var a={};a.me=this;a.leftMargin=b.level*4;a.newRow=new Element("div",{"class":"row",accountId:b.id,accountno:b.accountNumber}).insert({bottom:new Element("div",{"class":"space rowDivd",style:"width: "+a.leftMargin+"%"}).update("&nbsp;")}).insert({bottom:new Element("div",{"class":"content rowDivd",style:"width: "+(100-a.leftMargin-5)+"%"}).insert({bottom:new Element("div",{"class":"accountname"}).update(b.name)}).insert({bottom:new Element("div",{"class":"value"}).update(b.sum===0?"":"$"+b.sum)}).insert({bottom:new Element("div",{"class":"accountno"}).update(b.accountNumber)}).insert({bottom:new Element("div",{"class":"budget"}).update((b.budget===0||b.budget.blank())?"":"$"+b.budget)}).insert({bottom:new Element("div",{"class":"comments"}).update(b.comments)})}).insert({bottom:new Element("div",{"class":"btns rowDivd",style:"width: 5%"}).update(new Element("img",{"class":"dropdownmenu",src:"/contents/images/arrow-down.gif"}).observe("click",function(){a.dropdownmenu=$(this).up(".row").down(".btnListDiv");if(a.dropdownmenu){a.dropdownmenu.remove()}else{$(this).insert({after:a.me.showBtnsDiv(b)})}}))});return a.newRow},showBtnsDiv:function(b){var a={};a.me=this;$$(".btnListDiv").each(function(c){c.remove()});a.accountRow=$(a.me.divIds.list).down('.row[accountid="'+b.id+'"]');if(a.accountRow===undefined||a.accountRow===null){return}a.btn=a.accountRow.down(".dropdownmenu");if(a.btn===undefined||a.btn===null){return}a.newListDiv=new Element("ul").insert({bottom:new Element("li").update(new Element("a",{href:"javascript:void(0);"}).update("New").observe("click",function(){a.me._showAccSavingPanel(b,true)}))});if(b.level!==0){a.newListDiv.insert({bottom:new Element("li").update(new Element("a",{href:"javascript:void(0);"}).update("Edit").observe("click",function(){a.me._showAccSavingPanel(b,false)}))}).insert({bottom:new Element("li").update(new Element("a",{href:"javascript:void(0);"}).update("Move").observe("click",function(){a.me._showAccMovingPanel(b,false)}))})}if(b.gotChildren!==true){a.newListDiv.insert({bottom:new Element("li").update(new Element("a",{href:"javascript:void(0);"}).update("Delete").observe("click",function(){a.me.delAcc(b.id,this)}))})}return a.newListDiv.wrap(new Element("div",{"class":"btnListDiv"}))},_showAccMovingPanel:function(b){var a={};a.me=this;$$(".btnListDiv").each(function(c){c.remove()});$$(".newAccDiv").each(function(c){c.remove()});a.selectBox=new Element("select",{"class":"moveToAccs inputbox"});$$(".row[accountid]").each(function(c){a.moveToAcc=c.retrieve("account");if(!a.moveToAcc.accountNumber.startsWith(b.accountNumber)&&b.parent.id!==a.moveToAcc.id){a.selectBox.insert({bottom:new Element("option",{value:a.moveToAcc.id}).update(a.moveToAcc.breadCrumbs.name)})}});a.newDiv=new Element("div").insert({bottom:new Element("div",{"class":"newRow"}).insert({bottom:new Element("div",{"class":"title"}).update("Please choose an account to move to?")})}).insert({bottom:a.me._getAccountEditRow(" ",a.selectBox)}).insert({bottom:new Element("div",{"class":"newRow"}).insert({bottom:new Element("input",{"class":"submitBtn",type:"button",value:"Save"}).observe("click",function(){a.me.moveAccount(this,b.id,$F($(this).up(".newAccDiv").down(".moveToAccs")))})}).insert({bottom:new Element("input",{"class":"submitBtn",type:"button",value:"Cancel",style:"float:right;"}).observe("click",function(){$(this).up(".newAccDiv").remove()})})});$$(".row[accountId="+b.id+"]").first().insert({bottom:a.newDiv.wrap(new Element("div",{"class":"newAccDiv"}))})},moveAccount:function(d,b,a){var c={};c.me=this;c.row=$(d).up(".newRow");c.savingInfo=new Element("div").update("saving ...");appJs.postAjax(this.callBackIds.moveAccounts,{accountId:b,parentId:a},{onLoading:function(e,f){c.row.hide().insert({after:c.savingInfo})},onComplete:function(f,h){try{c.me._showAccList(appJs.getResp(h,false,true));alert("Moved successfully!");$$(".row[accountid="+b+"]").first().scrollTo();c.row.up(".newAccDiv").remove()}catch(g){c.savingInfo.remove();c.row.show();alert(g)}}})},_showAccSavingPanel:function(c,a){var b={};b.me=this;$$(".btnListDiv").each(function(d){d.remove()});b.newDivId="accSaveDiv_"+c.id;$$(".newAccDiv").each(function(d){d.remove()});b.newDiv=$(b.newDivId);b.accName=b.comments=b.accId=b.parentId="";b.accValue=b.accBudget="0.00";if(a===true){b.parentId=c.id}else{b.accName=c.name;b.accValue=c.value;b.accBudget=c.budget;b.accId=c.id;b.comments=c.comments;b.parentId=""}b.newDiv=new Element("div",{id:b.newDivId,"class":"newAccDiv"}).insert({bottom:new Element("div",{"class":"newRow"}).insert({bottom:new Element("div",{"class":"title"}).update(a===true?"Creating a new Account":"Updating selected account")})}).insert({bottom:b.me._getAccountEditRow("Name: ",new Element("input",{accinfo:"name",type:"text","class":"accName inputbox",placeholder:"Account Name",value:b.accName}))}).insert({bottom:b.me._getAccountEditRow("Value: $",new Element("input",{accinfo:"value",type:"text","class":"accValue inputbox",placeholder:"Account Value",value:b.accValue}))}).insert({bottom:b.me._getAccountEditRow("Budget: $",new Element("input",{accinfo:"budget",type:"text","class":"accBudget inputbox",placeholder:"Account Budget",value:b.accBudget}))}).insert({bottom:b.me._getAccountEditRow("Comments: ",new Element("input",{accinfo:"comments",type:"text","class":"accComments inputbox",placeholder:"Comments",value:b.comments}))}).insert({bottom:new Element("div",{"class":"newRow"}).insert({bottom:new Element("input",{"class":"submitBtn",type:"button",value:"Save"}).observe("click",function(){b.me.saveAccount(this,b.accId,b.parentId)})}).insert({bottom:new Element("input",{"class":"submitBtn",type:"button",value:"Cancel",style:"float:right;"}).observe("click",function(){$(this).up(".newAccDiv").remove()})})});$$(".row[accountId="+c.id+"]").first().insert({bottom:b.newDiv})},_getAccountEditRow:function(a,c){var b={};b.newRow=new Element("div",{"class":"newRow"}).insert({bottom:new Element("span",{"class":"label"}).update(a)}).insert({bottom:new Element("span",{"class":"typein"}).update(c)});return b.newRow},saveAccount:function(c,a,d){var b={};b.me=this;b.row=$(c).up(".newRow");b.savingPanel=b.row.up(".newAccDiv");b.savingInfo=new Element("div").update("saving ...");b.accInfo={accountId:a,parentId:d};b.hasError=false;b.savingPanel.getElementsBySelector(".newAccError").each(function(e){e.remove()});b.currency=/^\d*(\.|)\d{0,2}$/g;b.savingPanel.getElementsBySelector("[accinfo]").each(function(e){b.field=e.readAttribute("accinfo");b.value=$F(e);if(b.field==="name"&&b.value.blank()){e.insert({after:new Element("span",{"class":"newAccError"}).update("Name is needed!")});b.hasError=true}if(b.field==="value"&&!b.value.match(b.currency)){e.insert({after:new Element("span",{"class":"newAccError"}).update("Invalid value, expected: 0.00!")});b.hasError=true}if(b.field==="budget"&&!b.value.match(b.currency)){e.insert({after:new Element("span",{"class":"newAccError"}).update("Invalid budget, expected: 0.00!")});b.hasError=true}b.accInfo[b.field]=$F(e)});if(b.hasError===true){return}appJs.postAjax(this.callBackIds.saveAccount,b.accInfo,{onLoading:function(e,f){b.row.hide().insert({after:b.savingInfo})},onComplete:function(f,h){try{b.me._showAccList(appJs.getResp(h,false,true));alert("Saved successfully!");b.editRow=$$(".row[accountid="+a+"]").first();if(b.editRow){b.editRow.scrollTo()}b.savingPanel.remove()}catch(g){b.savingInfo.remove();b.row.show();alert(g)}}})},delAcc:function(a,c){var b={};b.me=this;if(!confirm("Are you sure you want to delete this account?")){return}$(c).up(".btnListDiv").remove();appJs.postAjax(this.callBackIds.deleteAccounts,{accountId:a},{onComplete:function(d,g){try{b.me._showAccList(appJs.getResp(g,false,true))}catch(f){alert(f)}}})}};