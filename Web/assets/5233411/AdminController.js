var AdminJs=new Class.create();AdminJs.prototype={canvasId:"",canvasTitleId:"",iframeId:"myIframe",loadingImg:'<img id="loadingImg" src="/contents/images/loading.gif" style="display:block; with:150px; height:150px;"/>',initialize:function(b,a){this.canvasId=b;this.canvasTitleId=a},getIframe:function(a){var b={};b.iframeId=this.iframeId;b.canvasId=this.canvasId;b.iframe=new Element("iframe",{src:a,id:b.iframeId,style:"display:none;width:100%;height: 800px;",scrolling:true,allowtransparency:true,frameborder:0}).observe("load",function(){$(b.canvasId).getElementsBySelector("img#loadingImg").each(function(c){c.remove()});if($(this).contentDocument){$(this).height=$(this).contentDocument.documentElement.scrollHeight+30}else{$(this).height=$(this).contentWindow.document.body.scrollHeight+30}$(this).show()});return b.iframe},changePage:function(b){var a={};a.url=$(b).readAttribute("href");a.title=$(b).down(".link").innerHTML;$(this.canvasTitleId).update(a.title);a.iframeId=this.iframeId;$(this.canvasId).getElementsBySelector("iframe#"+a.iframeId).each(function(c){c.remove()});a.iframe=this.getIframe(a.url);$(this.canvasId).update(this.loadingImg);$(this.canvasId).insert({bottom:a.iframe});return false}};