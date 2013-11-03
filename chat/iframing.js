refresh_this = function() {
	window.location.href=window.location.href;
}
/*uncounter = function() {
	if (progress<colorCnt) {
  	$(CH_p).innerHTML = "Preparing connection: "+Math.round(progress/colorCnt*100)+"%";
		$(CH_p).style.color = colorA[progress++];
		setTimeout('uncounter();',SEC*1000/colorCnt);
		return;
	}
	progress=0;
	r_i_c();
}*/
r_i_c = function () {
  if (lastId < 0) {
    $(CH_w).innerHTML = "Připojeno...";
    lastId = 0;
    return;
  }
	if (waiter>0) {
	  clearTimeout(waiter);
	  waiter = setTimeout('r_i_c();',(SEC-2)*1000);
	}
	else {
		waiter = setTimeout('r_i_c();',(SEC-2)*1000);
	}
 	$(CH_m).innerHTML = oldMess;
	$(CH_w).innerHTML = "Připojuji ...";
	a=$(IFRM);
	n=new Date();
	if(a.src.indexOf("?")>0){
		aa=a.src.substr(0,a.src.indexOf("?"));
	}else{
		aa=a.src;
	}
	$(IFRM).location = aa+'?id='+RID+'&last_id='+lastId+'&t='+n.getTime()+'&do=chat_refreshing';
	$(IFRM).src = aa+'?id='+RID+'&last_id='+lastId+'&t='+n.getTime()+'&do=chat_refreshing';
}

settimeout = function(a,t) {
	return setTimeout(a,t);
}

MakeInner = function(t) {
  return t.replace(new RegExp('(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)'), '');
}

r_i_divs = function(k) {
	var chatBdy = $(CH);
	if (waiter>0) {
	  clearTimeout(waiter);
	}
	$(CH_w).innerHTML = "Pracuji ...";
  var theDoc = window.frames["ramecek"].document;
	if (!theDoc) theDoc = $(IFRM).contentWindow.document;
	var divs = theDoc.getElementsByTagName("ms");
	var cntN=0;
	var lastLastId = lastId;
	for(var xx = 0; xx < divs.length; xx++){
		var chI = divs.item(xx);
		lastId = chI.getAttribute('id');
		if ($("msg-"+lastId+"-nmb")) continue;
		var chatMode = chI.getAttribute('m');
		var chatColor = chI.getAttribute('c');
		if(chatMode == '9'){
			runDeleter(chatColor);
		}
		else {
			var chatFrom = chI.getAttribute('f');
			var chatDiv = document.createElement("div");
			if (gShowTime) {
				chatDiv.innerHTML = "<small style='color:#bbb'>"+chI.getAttribute('l')+"</small> ";
			}
			//atributy u jmena
			chatSpanName = document.createElement("span");
			chatSpanName.innerHTML = chatFrom;
			chatSpanName.style.fontWeight = "bold";
			chatDiv.appendChild(chatSpanName);

			var chM = MakeInner(chI.firstChild.data);

			if (chatColor.charAt(0) != "#" && (chatColor.length == 6 || chatColor.length == 3) && !isNaN(parseInt(chatColor,16))) {
				chatColor = "#"+chatColor;
			}
			if (chatMode == '1'){
				chatMessageBody = " &raquo; "+chI.getAttribute('t')+": "+chM;
				chatDiv.style.fontWeight = "bold";
				chatDiv.style.color = chatColor;
			}else if(chatMode == '2'){
			}else if(chatMode == '3'){
				chatMessageBody = ": "+chM;
				chatDiv.style.color = "#7b6200";
				chatDiv.style.fontSize = "0.8em";
			}else{
				chatDiv.style.color = chatColor;
				chatMessageBody = ": "+chM;
			}
			chatMessage = document.createElement("span");
			chatMessage.innerHTML = chatMessageBody;
			chatDiv.rel = lastId;
			chatDiv.id = "msg-"+lastId+"-nmb";
			chatDiv.appendChild(chatMessage);

			var ddd = document.createElement("span");
			ddd.innerHTML = " <a href='#' onclick='adel(event)'>s</a>";
			ddd.style.visibility = "hidden";
			ddd.style.display = "none";
			chatDiv.appendChild(ddd);

			if (lastLastId != -1) {
				chatDiv.style.opacity = 0.001;
				chatDiv.style.MozOpacity = 0.001;
				chatDiv.style.KhtmlOpacity = 0.001;
				chatDiv.style.filter = "alpha(opacity=0.1)";
			}
		}
		chatBdy.insertBefore(chatDiv, chatBdy.firstChild);
		cntN++;
	}
	setTimeout('r_i_c()',1000*SEC);
	if (cntN>0 && lastLastId != -1) {
		opacitee("msg-"+lastId+"-nmb",(cntN-1));
	}
	$(CH_w).innerHTML = "Připojení OK.";
	if (cntN==1){konc1="á";konc2=konc3="a";}else if (cntN>2 && cntN<4){konc1="é";konc2=konc3="y";}else {konc1="ých";konc2="";konc3="o";}
	oldMess = cntN+" nov"+konc1+" zpráv"+konc2+" zapsán"+konc3;
	$(CH_m).innerHTML = oldMess;
	while(chatBdy.childNodes.length>mesOpt) chatBdy.removeChild(chatBdy.lastChild);
}

function opacitee(starter,howMany){
	if (window.ActiveXObject && !window.opera) {
		changeOpacIE(5,starter,howMany);
	}else {
		changeOpacCool(0.05,starter,howMany);
	}
}

function changeOpacIE(val,wh,nn) {
	if (val>=100) {
	  val=100;
	}
	who = $(wh);
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
	who = $(wh);
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


send_i_c = function() {
  var theDocX = window.frames["formular"].document;	if (!theDocX) theDocX = $(IFRM_F).contentWindow.document;
  theDocX.getElementById("formular");
}