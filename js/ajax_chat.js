var fadeSpeed = 40;
var lastLastId = -1;
var eE = 0;
var lastId = 0;
var in_del = false;
var in_msging = false;
var gShowTime = true;
var mesOpt = 50;
var sending_mess = false;
var waiter = false;

//ruzne stavy httpxml
function setState(text){
	document.getElementById('progress').innerHTML = text;
}
function gifGo(){
	document.getElementById('progress2').src = "/ajax_chat/css/ajax_loader.gif";
}
function gifStop(){
	document.getElementById('progress2').src = "/ajax_chat/css/stop.gif";
}

function loadChat(){
	document.getElementById('message').focus();
	refresh();
}
function whisperTo(user){
	msg = document.getElementById("message")
	if(msg.value == ""){
		msg.value = user +"# ";  
		msg.focus(); 
	}
}
function send_xmlhttprequest(after, meth, url, content, heads) {
var xmlhttp=false;
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
 try {xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
 }catch(e){
  try{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}catch(E){xmlhttp = false;}
 }
@end @*/
	if (!xmlhttp&&typeof XMLHttpRequest!='undefined'){
		try{xmlhttp=new XMLHttpRequest();
		}catch(e){xmlhttp=false;}
	}
	if (!xmlhttp&&window.createRequest) {
		try{xmlhttp = window.createRequest();
		}catch(e){xmlhttp=false;}
	}
	if(!xmlhttp){return false;}
	xmlhttp.open(meth,url);
	xmlhttp.onreadystatechange=function(){after(xmlhttp);};
	if(heads){for(var key in heads){xmlhttp.setRequestHeader(key,heads[key]);}}
	xmlhttp.setRequestHeader('Pragma','no-cache');
	xmlhttp.setRequestHeader("Cache-Control", "no-cache");
	xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
	if (xmlhttp.overrideMimeType) xmlhttp.setRequestHeader('Connection','close');
	if (content) {xmlhttp.send(content);}else {xmlhttp.send(null);}
	return true;
}

function refresh() {
	if (waiter>0) {
	  clearTimeout(waiter);
	  waiter = setTimeout('refresh();',2*1000);
	}
	else {
		waiter = setTimeout('refresh();',2*1000);
	}
	if (!in_del) {
		in_msging = true;
		if (eE > 0){
			var chatBody = document.getElementById('chat');
			lastId = lastId;
			if (chatBody.hasChildNodes()) {
				lastId = chatBody.childNodes[0].rel;
			}
		}else{
			lastId = 0;
		}
		if (!send_xmlhttprequest(ajax_chat, 'GET', '/ajax_chat/chat_ajaxing.php?do=chat_refreshing&id='+g_RID+'&last_id='+lastId+'')) { return false; }
	}
	else {
		setTimeout('refresh()',200);
	}
	return true;
}

function checkActivity() {
	if (!send_xmlhttprequest(doCheckActivity, 'GET', '/ajax_chat/chat_ajaxing.php?do=check_activity&id='+g_RID+'')) { return false; }
	var toplistimg = document.getElementById("toplist-img") || false;
	if (toplistimg) {
	  toplistimg.src = 'http://toplist.cz/dot.asp?id=40769&amp;http='+escape(top.document.referrer)+'&amp;rndnmb='+Math.round(Math.random()*10000000);
	}
	return true;
}

function send() {
	var message = document.getElementById('message').value;
	if (message.length > 0){
		document.getElementById('message').value = '';
		var to = document.getElementById('users').value;
		if (!send_xmlhttprequest(ajax_send, 'POST', '/ajax_chat/chat_ajaxing.php?do=chat_sending&id='+g_RID+'&to='+to, 'message='+encodeURIComponent(message))) { return false; }
	}
	return true;
}

function leave() {
	var message = document.getElementById('message').value;
	var to = document.getElementById('users').value;
	if (!send_xmlhttprequest(ajax_leave, 'POST', '/ajax_chat/chat_ajaxing.php?do=chat_leave&id='+g_RID+'', 'message='+encodeURIComponent(message))) { return false; }
	return true;
}

function fillSelect(){
	if (!send_xmlhttprequest(doFillSelect, 'GET', '/ajax_chat/chat_ajaxing.php?do=select_filling&id='+g_RID+'')) { return false; }
	return true;
}

function occupantsRefresh(){
	if (!send_xmlhttprequest(doOccupantsRefresh, 'GET', '/ajax_chat/chat_ajaxing.php?do=occupants_refresh&id='+g_RID+'')) { return false; }
	return true;
}

function doOccupantsRefresh(xmlhttp){
	if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
		o = document.getElementById('nav');
		var tt = "", occM = xmlhttp.responseXML.getElementsByTagName("oc");
		G_occupantsN = new Array();
		for(var i = 0; i < occM.length; i++){
			var occI = occM[i];
			var oT = occI.getAttribute("t");
			var oC = occI.getAttribute("i");
			var oL = occM[i].firstChild.data;
			G_occupantsN[i] = oL;
			tt += "<img src='/system/icos/"+oC+"' class='oI' onclick=\"whisperTo('"+oL+"');\" onmouseover=\"ddrivetip('<img src=\\'/system/icos/"+oC+"\\' style=\\'padding: 3px\\' /><div class=\\'dhtmlDiv\\'>"+oL+"<br />"+oT+"</div>');\" onmouseout='hidedrivetip();' />";
		}
		if (tt.length > 10) {
			o.innerHTML = '';
			o.innerHTML = tt;
			G_occupants = G_occupantsN;
		}
		setState("Kontrola uživatelů ok");
		gifStop();
	}else {
		setState("Kontrola uživatelů ...");
		gifGo();
	}
}

//odchod
function ajax_leave(xmlhttp){
	document.location.href = "/chat/";
}

function doCheckActivity(xmlhttp){
	if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		//neni jiz aktivni / byl vyhozen
		var active = xmlhttp.responseXML.getElementsByTagName('ac');
		if (active[0].firstChild.data < 1){
			document.location.href = "/chat/";
		}
	}
}

function runDeleter(n) {
	v = document.getElementById('msg-'+n+'-nmb') || false;
	p = document.getElementById('chat');
	if (v) {
		p.removeChild(v);
	}
}

//samotny chat
function ajax_chat(xmlhttp) {
	if (xmlhttp.readyState == 4) {
		if (waiter>0) clearTimeout(waiter);
	  if (xmlhttp.status != 200 || !xmlhttp.responseXML) {
	    clearTimeout(waiter);
	    in_msging = false;
	    refresh();
	    return;
		}
		var chatBody = document.getElementById('chat');
		if (eE > 0){
			if (chatBody.hasChildNodes()) {
				lastId = chatBody.childNodes[0].rel;
			}
		}else{
			lastId = 0;
		}
		var chat = xmlhttp.responseXML.getElementsByTagName('ms');
		var chatLastId = 0;
		var a = 0;
		var xx = 0;
		var lastLastId = lastId;
		for(xx = 0; xx < chat.length; xx++){
			var chI = chat.item(xx);
			var chatMode = chI.getAttribute('m');
			var chatColor = chI.getAttribute('c');

			if(chatMode == '9') runDeleter(chatColor);
			else {
				var chatFrom = chI.getAttribute('f');
				var chatTo = chI.getAttribute('t');
				chatLastId = chI.getAttribute('id');
				var chatDiv = document.createElement("div");
				if (gShowTime) {
					var chatTime = chI.getAttribute('l');
					chatDiv.innerHTML = "<small style='color:#bbb'>"+chatTime+"</small> ";
				}
				//atributy u jmena
				chatSpanName = document.createElement("span");
				chatSpanName.innerHTML = chatFrom;
				chatSpanName.style.fontWeight = "bold";
				chatDiv.appendChild(chatSpanName);
				var chM = chI.firstChild.data;

				if (chatMode == '1'){
					chatMessageBody = " &raquo; "+chatTo+": "+chM;
				}else if(chatMode == '2'){
				}else if(chatMode == '3'){
					chatMessageBody = ": "+chM;
				}else{
					chatMessageBody = ": "+chM;
				}

				chatMessage = document.createElement("span");
				chatMessage.innerHTML = chatMessageBody;
				chatDiv.rel = chatLastId;
				chatDiv.id = "msg-"+chatLastId+"-nmb";
				chatDiv.appendChild(chatMessage);
				if (chatColor.charAt(0) != "#" && (chatColor.length == 6 || chatColor.length == 3) && !isNaN(parseInt(chatColor,16))) chatColor = "#"+chatColor;
				if (chatMode == '1'){
					chatDiv.style.fontWeight = "bold";
					try{chatDiv.style.color = chatColor;}catch(e){}
				}else if(chatMode == '2'){
				}else if(chatMode == '3'){
					chatDiv.style.color = "#7b6200";
					chatDiv.style.fontSize = "0.8em";
				}else{
					try{chatDiv.style.color = chatColor;}catch(e){}
				}
				if (lastId>0) {
					chatDiv.style.opacity = 0;
					chatDiv.style.MozOpacity = 0;
					chatDiv.style.KhtmlOpacity = 0;
					chatDiv.style.filter = "alpha(opacity=0)";
				}
				var found = document.getElementById("msg-"+chatLastId+"-nmb") || false;
				if (!found) {
					chatBody.insertBefore(chatDiv, chatBody.firstChild);
					a+=1;
					eE = 1;
				}
				else {
				}
			}
		}
		in_msging = false;

		//aplikovani efektu na nove zpravy
		if (a>0 && lastId > 0) {
			opacitee("msg-"+chatLastId+"-nmb",(a-1));
		}

		nLimit = mesOpt;
		while (chatBody.childNodes.length > nLimit) chatBody.removeChild(chatBody.lastChild);

		setState("Kontrola příspěvků ok");
		gifStop();
	}else {
		setState("Kontrola příspěvků ...");
		gifGo();
	}
}

function opacitee(starter,howMany){
	if (window.ActiveXObject && !window.opera) {
		changeOpacIE(5,starter,howMany);
	}else {
		changeOpacCool(0.05,starter,howMany);
	}
}

function changeOpacIE(val,wh,nn) {
	if (val>=100) val=100;
	who = document.getElementById(wh);
  who.style.filter = "alpha(opacity="+val+")";
	for(var u=0;u<nn;u++){
	  who = who.nextSibling;
	  who.style.filter = "alpha(opacity="+val+")";
	}
	if (val<100) {
	  setTimeout(function(){changeOpacIE(val+5,wh,nn)},fadeSpeed);
	}
}
function changeOpacCool(val,wh,nn) {
	if (val>=0.999) {
	  val=0.999;
	}
	who = document.getElementById(wh);
  who.style.opacity = ""+val+"";
	who.style.MozOpacity = ""+val+"";
	who.style.KhtmlOpacity = ""+val+"";
	for(var u=0;u<nn;u++){
	  who = who.nextSibling;
	  who.style.opacity = val;
		who.style.MozOpacity = val;
		who.style.KhtmlOpacity = val;
	}
	if (val<0.999) {
	  setTimeout(function(){changeOpacCool(val+0.05,wh,nn)},fadeSpeed);
	}
}

function ajax_send(xmlhttp) {
	if (xmlhttp.readyState == 4) {
		setState("Příspěvek přidán");
		gifStop();
	}else {
		setState("Přidávám příspěvek");
		gifGo();
	}
	if (document.getElementById("ch-eck").checked != true) {
		document.getElementById('users').options[0].selected = true;
	}
}

//vyplneni selectu
function doFillSelect(xmlhttp) {
	if (xmlhttp.readyState == 4) {
		//info
		setState("Obnova selectu ok");
		gifStop();

		//odstraneni vsech optionu
		var allOpt = document.getElementById('users');
		for(i = allOpt.length-1; i > 0; i--){
			allOpt.remove(i);
		}

		var sl = xmlhttp.responseXML.getElementsByTagName('o');
		G_occupantsN = new Array();
		for(i = 0; i < sl.length; i++){
			var sli = sl.item(i);
			var slId = sli.getAttribute('id');
			var slVa = sli.getAttribute('name');
			var opt = new Option(0);
			G_occupantsN[i] = slVa;
			opt.text = slVa;
			opt.value = slId;
			if (document.all){
				actual = allOpt.length;
			}else{
				actual = null;
			}
			allOpt.add(opt, actual)
		}
		if (G_occupantsN.length > 0) {
			G_occupants = G_occupantsN;
		}
	}else {
		setState("Obnova selectu");
		gifGo();
	}
}

function add_js(event){
	klavesa=event.keyCode
	switch(klavesa){
		case 18:
			znak=":"
		break;
		case 17:
			znak="#"
		break;
		default:
			znak=""
		break;
	}
	if (znak.length > 0){
		porovnat=document.getElementById("message").value;
		for (i=0;i<G_occupants.length;i++){
			test=G_occupants[i].substr(0,porovnat.length);
			if (test.toLowerCase() == porovnat.toLowerCase() && porovnat != ""){
				document.getElementById("message").value=G_occupants[i]+""+znak+" ";
				document.getElementById("message").focus();
				break;
			}
		}
	}
}
