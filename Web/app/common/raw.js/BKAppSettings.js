//this is the app settings
var BKAppSettings = function(storageKey, serverUrl) {
	//getting the server url
	this.getServerUrl = function() {
		return serverUrl;
	};
	
	//private function: get local storage
	getStorage = function() {
		var tmp = {};
		return (!localStorage[storageKey]) ? {} : $.parseJSON(localStorage[storageKey])[storageKey]
	};

	//private function: save storage
	saveStorage = function(newData) {
		var tmp = {};
		if (!localStorage[storageKey])
			localStorage[storageKey] = JSON.stringify(tmp);

		tmp.current = $.parseJSON(localStorage[storageKey]);
		tmp.current[storageKey] = newData;
		localStorage[storageKey] = JSON.stringify(tmp.current);
	};
	
	//public function: add section into localStorage
	this.getData = function(sectionId) {
		var tmp = {};
		tmp.Storage = getStorage();
		return (tmp.Storage[sectionId] || {});
	};

	//public function: add section into localStorage
	this.addToStorage = function(sectionId, newData) {
		var tmp = {};
		tmp.data = getStorage();
		tmp.data[sectionId] = newData;
		saveStorage(tmp.data);
		return this;
	};
	
	//public funtion: removing a section from localStorage
	this.removeFromStorage = function(sectionId) {
		var tmp = {};
		tmp.data = getStorage();
		if (tmp.data[sectionId])
			delete tmp.data[sectionId];
		saveStorage(tmp.data);
		return this;
	};

	// dangerous, this is clear the whole Bsuite Local storage
	this.clearStorage = function() {
		localStorage[storageKey] = JSON.stringify({});
		return this;
	};
};