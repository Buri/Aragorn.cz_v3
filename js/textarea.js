/*
	by Amunak
	sep 2012
*/

var taBackup = {
	interval: 5000,
	last: "",
	ta: undefined,
	timer: undefined,
	id: window.location.pathname.split('/')[1] + ':' + window.location.pathname.split('/')[2],
	init: function(){
		if(window.localStorage){
			taBackup.ta = $('km');
			if(taBackup.ta && document.forms['txt']) {
				$(document.forms['txt']).addEvent('submit', taBackup.clear);
				if(!taBackup.ta.value.length && taBackup.load().length){
					taBackup.last = taBackup.load();
					taBackup.ta.value = taBackup.last;
				}
				taBackup.timer = setInterval(taBackup.check, taBackup.interval);
				taBackup.ta.addEvent('blur', taBackup.check);
			}
		}
		
	},
	load: function(){
		return localStorage.getItem(taBackup.id) || "";
	},
	save: function(){
		if(taBackup.ta.value.length)
			localStorage.setItem(taBackup.id, taBackup.ta.value);
		else
			taBackup.clear();
		
	},
	clear: function(){
		localStorage.removeItem(taBackup.id);
	},
	check: function(){
		if(taBackup.last === taBackup.ta.value) return;
		taBackup.save();
		taBackup.last = taBackup.ta.value;
	}
};
window.addEvent('domready', function() {
	taBackup.init();
});
