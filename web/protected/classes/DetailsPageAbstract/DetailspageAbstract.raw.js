var DetailsPageJs = new Class.create();
DetailsPageJs.prototype = Object.extend(new BackEndPageJs(), {
	_entity: {}
	,setEntity: function(entity) {
		this._entity = entity;
		return this;
	}
	,errWhenFirstLoad: function(msg) {
		var tmp = {};
		tmp.me = this;
		tmp.msg = msg || '';
		$(tmp.me.getHTMLID('result-div')).update(
			new Element('div').update(
				tmp.msg.blank() ? new Element('h4', {'class': 'text-center'}).update(new Element('div', {'class': 'label label-danger'}).update('Invalid entity')) : tmp.msg
			)
		);
		return tmp.me;
	}
});