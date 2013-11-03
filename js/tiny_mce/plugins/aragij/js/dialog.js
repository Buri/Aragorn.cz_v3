tinyMCEPopup.requireLangPack();

var ExampleDialog = {
	init : function() {
	//tady by šlo zařídit javascriptově načtení seznamu dostupných šablon, ale lepší bude udělat to přes PHP
	},

	insert : function() {
		// Insert the contents from the input into the document
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, getCheckedValue(document.forms[0].elements['journaltype']));
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(ExampleDialog.init, ExampleDialog);

// return the value of the radio button that is checked
// return an empty string if none are checked, or
// there are no radio buttons
// http://www.somacon.com/p143.php
function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}
