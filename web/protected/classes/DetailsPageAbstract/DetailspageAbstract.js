var DetailsPageJs=new Class.create;DetailsPageJs.prototype=Object.extend(new BackEndPageJs,{_entity:{},setEntity:function(e){return this._entity=e,this},errWhenFirstLoad:function(e){var t={};return t.me=this,t.msg=e||"",$(t.me.getHTMLID("result-div")).update(new Element("div").update(t.msg.blank()?new Element("h4",{"class":"text-center"}).update(new Element("div",{"class":"label label-danger"}).update("Invalid entity")):t.msg)),t.me}});