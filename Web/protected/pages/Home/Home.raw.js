//this is the source file for the FieldTaskListController
var HomeJs = new Class.create();
HomeJs.prototype = {
	//constructor
	initialize: function () {},
	/**
	 * click event for the table in the .box-title
	 */
	selectSummary: function (btn, detailsDiv) {
        var tmp = {};
        tmp.clickedBtn = $(btn);
        tmp.clickedBtn.up('ul').getElementsBySelector('li').each(function(item){
            item.down('a').removeAttribute('selected');
        });
        tmp.clickedBtn.writeAttribute('selected');
        
        tmp.clickedBtn.up('.content-box').down('.box-content').getElementsBySelector('[summary]').each(function(item){
            item.hide();
        });
        $$('[summary=' + detailsDiv + ']').first().show();
        return false;
    }
};