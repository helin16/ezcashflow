(function($){
   var Bookkeeping = function()
   {
	   var settings = {
			   'serverPath' : '/easycash/svn/Web/app/app.php'
	   },
	   user = {};
	   
	   this.setServerPath = function(path)
	   {
		   settings.serverPath = path;
	   };
	   
       this.login = function(usernameBox, passwordBox)
       {
    	   var tmp = {};
    	   tmp.username = $(usernameBox).val();
    	   tmp.password = $(passwordBox).val();
    	   this.postMe(settings.serverPath, {'method': 'user.getUser'})
    	   		.success(function(data, textStatus, jqXHR){
    	   			console.debug(data);
    	   		});
       };
       
       this.postMe = function(url, data, loadingMsg)
       {
    	   var tmp = {};
    	   tmp.loadingMsg = (loadingMsg === undefined ? 'Loading...' : loadingMsg);
    	   $.mobile.showPageLoadingMsg("b", tmp.loadingMsg, true);
    	   return $.post(url, data, function(data, textStatus, jqXHR){}, 'json')
    	   			.complete(function() {$.mobile.hidePageLoadingMsg();})
    	   			.error(function(jqXHR, textStatus, errorThrown){
    	   				console.error(jqXHR.responseText);
    	   			});
       };
   };

   $.fn.bookeeping = function(options)
   {
	   var element = $(this);
       
       // Return early if this element already has a plugin instance
       if (element.data('bookkeeping')) return element.data('bookkeeping');

       // pass options to plugin constructor
       var bookkeeping = new Bookkeeping();

       // Store plugin object in this element's data
       element.data('bookkeeping', bookkeeping);
       
       return bookkeeping;
   };
})(jQuery);
var pageJs = $(document).bookeeping();
