var AppJs=new Class.create();AppJs.prototype={initialize:function(){},postAjax:function(c,b,d){var a={};a.request=new Prado.CallbackRequest(c,d);a.request.setCallbackParameter(b);a.request.dispatch();return a.request},getResp:function(b,a,d){var c={};c.expectNonJSONResult=(a!==true?false:true);c.result=b;if(c.result===null||c.result.blank()){c.error="Your request probably timed out, please try again later!";if(d===true){throw c.error}else{return alert(c.error)}}if(c.expectNonJSONResult===true){return c.result}if(!c.result.isJSON()){c.error="Invalid JSON string: "+c.result;if(d===true){throw c.error}else{return alert(c.error)}}c.result=c.result.evalJSON();if(c.result.errors.size()!==0){c.error="Error: \n\n"+c.result.errors.join("\n");if(d===true){throw c.error}else{return alert(c.error)}}return c.result.resultData},getCurrency:function(f,c,b,a,e){var d={};d.decimal=(isNaN(b=Math.abs(b))?2:b);d.dollar=(c==undefined?"$":c);d.decimalPoint=(a==undefined?".":a);d.thousandPoint=(e==undefined?",":e);d.sign=(f<0?"-":"");d.Int=parseInt(f=Math.abs(+f||0).toFixed(d.decimal))+"";d.j=(d.j=d.Int.length)>3?d.j%3:0;return d.dollar+d.sign+(d.j?d.Int.substr(0,d.j)+d.thousandPoint:"")+d.Int.substr(d.j).replace(/(\d{3})(?=\d)/g,"$1"+d.thousandPoint)+(d.decimal?d.decimalPoint+Math.abs(f-d.Int).toFixed(d.decimal).slice(2):"")}};var appJs=new AppJs();