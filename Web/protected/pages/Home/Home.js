var HomeJs=new Class.create();HomeJs.prototype={recentTrans:{holder:"",noOfTrans:5,callback:""},initialize:function(b,a){this.recentTrans.holder=b;this.recentTrans.callback=a},selectSummary:function(btn,postScript){var tmp={};tmp.clickedBtn=$(btn);tmp.clickedBtn.up("ul").getElementsBySelector("li").each(function(item){item.down("a").removeAttribute("selected")});tmp.clickedBtn.writeAttribute("selected");eval(postScript);return false},loadRecentTrans:function(){var a={};a.holderDiv=this.recentTrans.holder;appJs.postAjax(this.recentTrans.callback,{noOfTrans:this.recentTrans.noOfTrans},{onLoading:function(b,c){$(a.holderDiv).update('<img src="/contents/images/loading.gif" style="display:block; with:150px; height:150px;"/>')},onComplete:function(b,c){a.result=appJs.getResp(c);a.ul=new Element("ul");a.result.each(function(d){a.ul.insert({bottom:pageJs.getRecentTranRow(d)})});$(a.holderDiv).update(a.ul)}})},refreshTrans:function(b){var a={};$("summaryBtn").click();a.ul=$(this.recentTrans.holder).down("ul");b.each(function(c){a.lastLi=a.ul.getElementsBySelector("li").last();if(a.lastLi!==undefined){a.lastLi.remove()}a.ul.insert({top:pageJs.getRecentTranRow(c)})})},getRecentTranRow:function(b){var a={};a.li=new Element("li",{"class":"row",transid:b.id});a.href=new Element("a",{href:b.link});a.href.insert({bottom:new Element("p",{"class":"value"}).update(appJs.getCurrency(b.value))});a.href.insert({bottom:new Element("p",{"class":"from"}).update(b.fromAcc.name===undefined?"":b.fromAcc.name)});a.href.insert({bottom:new Element("p",{"class":"to"}).update(b.toAcc.name===undefined?"":b.toAcc.name)});a.href.insert({bottom:new Element("p",{"class":"comments"}).update(b.comments)});a.li.update(a.href);return a.li}};