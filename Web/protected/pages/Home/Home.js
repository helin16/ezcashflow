var HomeJs=new Class.create();HomeJs.prototype={recentTrans:{holder:"",noOfTrans:5,callback:""},initialize:function(){},getAccounts:function(b,a){appJs.postAjax(b,{},{onComplete:function(c,d){appJs.setPageData("accounts",appJs.getResp(d));a()}})},selectSummary:function(c,a){var b={};b.clickedBtn=$(c);b.clickedBtn.up("ul").getElementsBySelector("li").each(function(d){d.down("a").removeAttribute("selected")});b.clickedBtn.writeAttribute("selected");if(typeof(a)==="function"){a()}return false},loadRecentTrans:function(c,a){var b={};this.recentTrans.holder=c;this.recentTrans.callback=a;b.me=this;appJs.postAjax(this.recentTrans.callback,{noOfTrans:this.recentTrans.noOfTrans},{onLoading:function(d,e){$(b.me.recentTrans.holder).update('<img src="/contents/images/loading.gif" style="display:block; with:150px; height:150px;"/>')},onComplete:function(d,e){b.result=appJs.getResp(e);$(b.me.recentTrans.holder).update("");if(b.result.size()<=0){$(b.me.recentTrans.holder).update(new Element("div",{"class":"notrans"}).update("There is NO transactions yet!"))}b.ul=new Element("ul");b.result.each(function(f){b.ul.insert({bottom:pageJs.getRecentTranRow(f)})});$(b.me.recentTrans.holder).insert({bottom:b.ul})}})},refreshTrans:function(b){var a={};$("summaryBtn").click();$(this.recentTrans.holder).getElementsBySelector(".notrans").each(function(c){c.remove()});a.ul=$(this.recentTrans.holder).down("ul");a.lis=a.ul.getElementsBySelector("li");b.each(function(c){a.lastLi=a.lis.last();if(a.lis.size()>=pageJs.recentTrans.noOfTrans&&a.lastLi!==undefined){a.lastLi.remove()}a.ul.insert({top:pageJs.getRecentTranRow(c)})})},getRecentTranRow:function(b){var a={};a.li=new Element("li",{"class":"row",transid:b.id});a.href=new Element("a",{href:b.link});a.href.insert({bottom:new Element("p",{"class":"value"}).update(appJs.getCurrency(b.value))});a.href.insert({bottom:new Element("p",{"class":"from"}).update(b.fromAcc.name===undefined?"":b.fromAcc.name)});a.href.insert({bottom:new Element("p",{"class":"to"}).update(b.toAcc.name===undefined?"":b.toAcc.name)});a.href.insert({bottom:new Element("p",{"class":"comments"}).update(b.comments)});a.li.update(a.href);return a.li}};