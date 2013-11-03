var limit = 1000;
var cntrsml = 0;
function update() {
	refr = false;
	document.getElementById("counter").innerHTML = 1000 - document.getElementById("mess").value.length;
	if(document.getElementById("mess").value.length > 999){
		document.getElementById("mess").value = document.getElementById("mess").value.substr(0, 1000);
	}
}

function add_js(event){
	if (!event) event = window.event;
	if(document.getElementById("mess").value.length>999){
		document.getElementById("mess").value=document.getElementById("mess").value.substr(0,1000);
	}
	var klavesa=event.keyCode;
	switch(klavesa){
		case 13:
			if (event.preventDefault) event.preventDefault();
			else event.returnValue = false;
			document.forms['chat'].submit();
			return;
		break;
		case 18:
			znak=":";
		break;
		case 17:
			znak="#";
		break;
		default:
			znak="";
		break;
	}
	if (znak.length > 0){
		var content=document.forms['chat']['chat_js'].value;
		content=content.split(",");
		var porovnat=document.forms['chat']['mess'].value;
		for (i=0;i<content.length;i++){
			var test=content[i].substr(0,porovnat.length);
			if (test.toLowerCase() == porovnat.toLowerCase() && porovnat != ""){
				document.forms['chat']['mess'].value=content[i]+""+znak +" ";
				document.forms['chat']['mess'].focus();
				if (event.preventDefault) event.preventDefault();
				else event.returnValue = false;
				break;
			}
		}
	}
}

function add_smile(smile){if(cntrsml<3){cntrsml++;var consmile=document.forms['chat']['mess'].value;document.forms['chat']['mess'].value=consmile+" "+smile;}update();document.forms['chat']['mess'].focus();}