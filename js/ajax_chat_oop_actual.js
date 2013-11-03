<!--

var gShowTime = 1;
var mesOpt = 50;
var rfrshIntrvl = 7000;
var cpntsIntrvl = 11000;
var actvtIntrvl = 13000;
var el = null;
var crossForDel = "x";

function whisperTo(u){
	msg = $("message");
	if(msg.value.indexOf('#') > 1 && msg.value.split('#', 3).length < 3) {
		msg.value = msg.value.replace(/^([^#]{1,}#)/, u +"# ").replace(/([\s]{2,})/g, ' ');
	}
	else if (msg.value == '') {
		msg.value = u +"# ";
	}
	msg.focus();
}

function addSmiley(t){
	msg = $("message");
	msg.value = msg.value + ' ' + t;
	msg.focus();
}

Ajaxchat = new Class({

	initialize: function(what,address){

		this.where = $(what);
		this.bdy = $(document.body);
		this.nav = $('nav');
		this.forma = $('forma');
		this.users = $('users');
		this.msgBox = $('message');
		this.checkBox = $('ch-eck');
		if (gShowTime) this.where.addClass("visibleDate");
		this.mazaniZprav = $('mazani_zprav');

		this.ajaxiBar = null;
		this.toplistimg = null;

		this.defUrl = address;

		this.lastId = 0;
		this.lastLastId = 0;

		this.hasRun = [0,0,0,0,0,0,0,0,0,0];
		this.refreshing = false;
		this.msging = false;
		this.indel = false;
		this.commandTimes = [0,0,0,0,0,0,0,0,0,0];
		this.firstRun = 1;
		this.ending = false;

		this.trains = [];
		this.doStr = ['chat_refreshing', 'occupants_refresh', 'check_activity', 'chat_sending', 'chat_leave', 'select_filling'];
		this.runStateStr = ['Kontrola příspěvků ...', 'Kontrola uživatelů ...', 'Kontrola aktivity ...', 'Odesílám zprávu...', '', 'Načítám uživatele...'];
		this.finStateStr = ['Kontrola příspěvků ok', 'Kontrola uživatelů ok', 'Kontrola aktivity ok', 'Zpráva odeslána', '', 'Výběr uživatelů obnoven'];

		wideR = Cookie.read('widescreen') || false;
		if (wideR) this.bdy.addClass('wideScreen');

		this.setupGUI();
		window.addEvent('resize',function(){this.setupGUI()}.bind(this));

		this.ints = {
			refresh: this.cmd.periodical(rfrshIntrvl,this,0),
			occupants: this.cmd.periodical(cpntsIntrvl,this,1),
			activity: this.cmd.periodical(actvtIntrvl,this,2),
			toplist: this.toplistMaker.periodical(180000,this)
		};
		this.elmProgress = $('progress');

		mesOptN = Cookie.read('chMN') || mesOpt;
		mesOptN = mesOptN.toInt();
		if (mesOptN < 50 || mesOptN > 300) mesOptN = 50;
		mesOpt = mesOptN;
		new Element("span",{styles:{position:'absolute',fontSize:'10px',top:2,right:2,float:'right'}}).set("html","<select id='select4length' style='width:60px;cursor:help' title='Nastavení délky výpisu zpráv'><option value='50'>50</option><option value='100'>100</option><option value='150'>150</option><option value='200'>200</option><option value='300'>300</option></select>").inject(this.bdy);
		$('select4length').addEvent("change", function(){this.sForLen();}.bind(this));
		$each($('select4length').getElements('option'),function(val,ind){if(val.value==mesOpt){val.selected=true;}});

		this.dateChecker = new Element("span",{text:'T',title:'Dočasně přepnout viditelnost času zpráv'}).addEvent('click',function(){this.where.toggleClass('visibleDate');}.bind(this)).addClass("dateToggler").inject(this.bdy);
		this.wideScreen = new Element("span",{text:'W',title:'Přepnout rozložení WideScreen'}).addEvent('click',function(){this.bdy.toggleClass('wideScreen');Cookie.write('widescreen',this.bdy.hasClass('wideScreen'),{duration: 365});this.setupGUI();}.bind(this)).addClass("wideToggler").inject(this.bdy);
		this.elmSendMessage = this.elmProgress.getFirst().clone().inject(this.elmProgress.empty()).set('text','Žádná akce.');
		this.elmChatRefreshing = this.elmSendMessage.clone().inject(this.elmProgress).set('text','Kontrola příspěvků ok.');
		this.elmOccupantsRefresh = this.elmChatRefreshing.clone().inject(this.elmProgress).set('text','Kontrola uživatelů ok.');
		this.elmCheckActivity = this.elmChatRefreshing.clone().inject(this.elmProgress).set('text','Kontrola aktivity ok.');
		this.smileyPanel = $$('#smileys,#friends');
		this.smileyPanel.set('tween',{duration:500,transition:'expo:out'});
		this.smileyPanel[0].getElements('img').addEvent('click',function(){
			addSmiley(this.get('title'));
			return false;
		});
		this.smileyPanel.addEvents({
			'mouseenter':function(){
				this.setStyle("z-index",111111).tween('left',0)
			},
			'mouseleave':function(){
				this.setStyle("z-index",99999).tween('left',-200);
			}
		});

		this.cmd.delay(100,this,0);
		this.cmd.delay(300,this,5);
		this.login_timer_maker.delay(1000,this);

		this.msgBox.addEvent('keydown', this.MBoxKey.bindWithEvent(this));
		this.msgBox.addEvent('keyup', this.MBoxKey.bindWithEvent(this));

		$$('input').addEvent('focus',function(){this.addClass('selectedInput')}).addEvent('blur',function(){this.removeClass('selectedInput')});

	},
	
	setupGUI: function(){
		wide = window.getWidth();
		heit = window.getHeight();
		var h = 50;
		if (this.bdy.hasClass('wideScreen')) h = 35;
		if (heit<300)heit=300;
		if (wide<500)wide=500;
	  this.nav.setStyles({position:'absolute', top:5, left:5, height:65, width:(wide-185)});
	  this.where.setStyles({position:'absolute',top:75,left:13,height:(heit-75-h), width:(wide-193),overflow:'auto'}).getParent().setStyles({height:'auto',width:'auto'});
	  this.forma.setStyles({position:'absolute', top:(heit-h), left:0, height:h, width:wide}).getParent().setStyles({height:'auto',width:'auto'});
		if(!this.ajaxiBar){
			this.ajaxiBar = new Element('div').setProperty('id','ajaxi-bar').inject(this.bdy);
		}
	  this.ajaxiBar.setStyles({position:'absolute', top:120, right:0, height:(heit-50-120)});
	},

	sForLen: function(){
		selForLen = $('select4length');
		mesOpt = selForLen.options[selForLen.selectedIndex].value;
		if (mesOpt > 300) mesOpt = 300;
		Cookie.write('chMN', mesOpt, {duration: 365});
	},

	MBoxKey: function(event){
		if (this.msgBox.get('value').length < 1 || this.msgBox.get('value').length > 30) return;
		else {
			var event = new Event(event);
			var sZ = "";
			if (event.code == 18 || event.alt) {
				sZ = ":";
				event.stop();
			}
			else if (event.code == 17 || event.control) sZ = "#";
			if (sZ.length > 0){
				sMatch = this.msgBox.value.toLowerCase();
				for (i=0;i<G_occupants.length;i++){
					sTest = G_occupants[i].substr(0,sMatch.length).toLowerCase();
					if (sTest == sMatch && sMatch != ""){
						event.stop();
						this.msgBox.value = G_occupants[i]+""+sZ+" ";
						this.msgBox.focus();
						return false;
					}
				}
			}
		}
	},

	stating: function(t,elm){
		elm.addClass('loadAnim');
		if ($type(t)=='number') elm.set("html",this.runStateStr[t]);
		else elm.set("html",t);
	},

	finishing: function(t,elm){
		elm.removeClass('loadAnim');
		if ($type(t)=='number') elm.set("html",this.finStateStr[t]);
		else elm.set("html",t);
	},
	
	goToEnd: function(){
		if (this.ending) return;
		for(var i=0;i<this.hasRun.length;i++) if (this.hasRun[i]>0) if (this.trains[i].running) this.trains[i].cancel();
	},

	cmd: function(t,bIsCmd,sTxt,sAlter){
		if (this.ending) return;
		el = this;
		to = this.users.get("value");
		switch (t){
			case 0: // elmChatRefreshing
				if (!this.indel && !this.refreshing) {
					if (this.hasRun[t]>0) {
						if (this.trains[t].running) this.trains[t].cancel();
					}
					else this.trains[t] = new Request({link:'cancel', evalScripts: false, method: 'get', onRequest: function(){ el.stating(t,el.elmChatRefreshing); }, onComplete: function(){ el.refreshing = true; el.finishing(t,el.elmChatRefreshing); el.ChatRefreshing(this.response.xml,this.response.text); }, onFailure: function(){ el.refreshing = false; } });

					this.hasRun[t] = 1;
					this.trains[t].send({url:this.defUrl+'?do='+this.doStr[t]+'&last_id='+this.lastId+'&id='+g_RID+'&t='+$time()});
				}
				else this.cmd.delay(300,el,0);
			break;
			case 1: // elmOccupantsRefresh
				if (this.hasRun[t]>0) {
					if (this.trains[t].running) this.trains[t].cancel();
				}
				else this.trains[t] = new Request({
					link:'ignore', method: 'get',
					onRequest: function(){ el.stating(t,el.elmOccupantsRefresh); },
					onComplete: function(){
						el.finishing(t,el.elmOccupantsRefresh);
						el.OccupantsRefresh(this.response.xml,this.response.text);
					}, onFailure: function(){ el.cmd(t); } });
				this.hasRun[t] = 1;
				this.trains[t].send({url:this.defUrl+'?do='+this.doStr[t]+'&id='+g_RID+'&t='+$time()});
			break;
			case 2: // elmCheckActivity
				if (this.hasRun[t]>0) {
					if (this.trains[t].running) this.trains[t].cancel();
				}
				else this.trains[t] = new Request({link:'ignore', method: 'get', onRequest: function(){	el.stating(t,el.elmCheckActivity);	}, onComplete: function(){	el.finishing(t,el.elmCheckActivity); el.CheckActivity(this.response.xml,this.response.text);	} });

				this.hasRun[t] = 1;
				this.trains[t].send({url:this.defUrl+'?do='+this.doStr[t]+'&id='+g_RID+'&t='+$time()});

			break;
			case 3: // elmSendMessage
				if (!this.msging) {
					if (this.msgBox.value.clean().length > 0) {
						this.msgBox.value = this.msgBox.value.clean();
						sTxt = this.msgBox.value.split(" ",2)[0].toLowerCase();
						sAlter = this.msgBox.value.substr(sTxt.length).clean();
						switch(sTxt){
							case "/exit": case "/quit": case "/bye": case "/leave": case "/odchod": case "/odejit":
								this.cmd(4,true,sAlter);
							break;
							case "/refresh": case "/obnov":
								this.msgClr();
								this.cmd(0);
								this.cmd(1);
								this.cmd(8);
							break;
							case "/help": case "/find": case "/vypatlej":
								this.cmd(9,true,sTxt,sAlter);
							break;
							case "/ban": case "/msg": case "/sys":
								if (this.mazaniZprav) this.cmd(9,true,sTxt,sAlter);
							break;
							default:
								if (this.hasRun[t] > 0) {
									if (this.trains[t].running) this.trains[t].cancel();
								}
								else this.trains[t] = new Request({
									link:'ignore',
									method: 'post',
									onRequest: function(){ el.msgClr(); el.msging = true; el.stating(t,el.elmSendMessage); },
									onComplete: function(){ el.finishing(t,el.elmSendMessage); el.msging = false;
										if (el.checkBox.get('checked') != true){
											el.users.options[0].selected = true;
										}
									}, onFailure: function(){ el.msging = false; el.finishing('Odeslání zprávy selhalo!',el.elmSendMessage) } });

								this.hasRun[t] = 1;
								this.trains[t].send({url:this.defUrl+'?do='+this.doStr[t]+'&id='+g_RID+'&to='+to+'&t='+$time(), data: { 'message': this.msgBox.value.clean()}});
							break;
						}
					}
				}
				else this.cmd.delay(300,el,3);
			break;
			case 4: // ChatLeave
				this.goToEnd();
				var ms = this.msgBox.getProperty('value').clean();
				if (ms.length > 0 || sTxt) {
					if (!this.hasRun[t]) {
						this.trains[t] = new Request({method: 'post', onComplete: function(){ setTimeout('document.location.href = "/chat/"',200); }, onFailure: function(){ el.cmd(t); } });
					}
					this.hasRun[t] = 1;
					if (bIsCmd) {
						sTxt = $pick(sTxt,"");
					}
					else sTxt = ms;

					this.trains[t].send({url:this.defUrl+'?do='+this.doStr[t]+'&id='+g_RID+'&t='+$time(), data: { 'message': sTxt }});
				}
				else {
					if (!this.hasRun[t]) {
						this.trains[t] = new Request({method: 'post', onComplete: function(){ setTimeout('document.location.href = "/chat/"',100); }, onFailure: function(){ el.cmd(t); } });
					}

					this.hasRun[t] = 1;
					this.trains[t].send({url:this.defUrl+'?do='+this.doStr[t]+'&id='+g_RID+'&t='+$time()});
				}
			break;
			case 5: // FillSelect
				if (this.hasRun[t]>0) {
					if (this.trains[t].running) this.trains[t].cancel();
				}
				else this.trains[t] = new Request({
					method: 'get',
					onRequest: function(){ el.stating(t,el.elmOccupantsRefresh); },
					onComplete: function(){
						el.FillSelect(this.response.xml,this.response.text);
						el.finishing(t,el.elmOccupantsRefresh)
					}
				});

				this.hasRun[t] = 1;
				this.trains[t].send({url:this.defUrl+'?do='+this.doStr[t]+'&id='+g_RID+'&t='+$time()});
			break;
			case 6: // Kick
				if(to == '0'){
					this.finishing('Musíte vybrat uživatele!',this.elmSendMessage);
					return;
				}

				if (this.hasRun[t]>0) {
					if (this.trains[t].running) this.trains[t].cancel();
				}
				else this.trains[t] = new Request({onRequest: function(){ el.stating('Vyhazuji uživatele...',el.elmSendMessage); }, onComplete: function(){ if (this.response.text == 'ok') { el.finishing('Uživatel vyhozen',el.elmSendMessage); } else { el.finishing(this.response.text,el.elmSendMessage); } }, method:'get'});

				this.hasRun[t] = 1;
				this.trains[t].send({url:this.defUrl+'?do=kick&id='+g_RID+'&who='+to+'&t='+$time()});
			break;
			case 7: // MessageDelete
				if (this.hasRun[t]>0){
					if (this.trains[t].running) this.trains[t].cancel();
				}
				else this.trains[t] = new Request({onComplete: function(){ el.doDelete(this.response.text); }, method:'get' });

				this.hasRun[t] = 1;
				this.trains[t].send({url:this.defUrl+'?do=mess_delete&id='+g_RID+'&mid='+sTxt+'&t='+$time()});
			break;
			case 8: // ZalozkyRefresh
				if (this.hasRun[t]) {
					if (this.trains[t].running) this.trains[t].cancel();
				}
				else this.trains[t] = new Request({onRequest: function(){ el.stating('Kontrola záložek...',el.elmOccupantsRefresh) }, onComplete: function(){ el.login_checker(this.response.xml) }, method: 'get' });

				this.hasRun[t] = 1;
				this.trains[t].send({url:"/ajaxing.php?do=logining&zal=true&t="+$time()});
			break;
			case 9: // RunCommand
				if (bIsCmd) {
					sTxt = sTxt.substr(1);
					if(sTxt == 'help') iNmb = 5;
					else if (sTxt == 'find') iNmb = 1;
					else if (sTxt == 'vypatlej') iNmb = 1;
					else iNmb = 0;
					iTmr = (iNmb*1000*60)+50;
					if (this.commandTimes[iNmb]>0) {
						this.finishing('Příkaz '+sTxt+' je omezený na '+iNmb+' minut'+((iNmb>1)?((iNmb>4)?'':'y'):'u'),this.elmSendMessage);
					}
					else {
						this.commandTimes[iNmb] = iNmb;
						setTimeout(function(){ el.commandTimes[iNmb] = 0; }, iTmr);
						if (this.hasRun[t]>0) {
							if (this.trains[t].running) this.trains[t].cancel();
						}
						else this.trains[t] = new Request({onRequest: function(){ el.stating('Odesílám příkaz '+sTxt,el.elmSendMessage); }, onComplete: function(){ el.doCommand(this.response.text,sTxt,iNmb); }, method:'post' });

						this.hasRun[t] = 1;
						this.trains[t].send({url:this.defUrl+'?do=chat_command&id='+g_RID+'&t='+$time(), data: { 'cmd': sTxt,'cmd_add': sAlter }});
					}
				}
				else {
					this.finishing("Neznámý příkaz",this.elmSendMessage);
				}
			break;
		}
	},

	FillSelect: function(oXml,sTxt){
		if (!this.firstCheck(sTxt)) return;

		var selectedOption = this.users.get("value");

		var firstOne = this.users.getElement("option").clone();
		this.users.empty();
		firstOne.inject(this.users);

		G_occupantsN = [];

		var sl = oXml.getElementsByTagName('o');
		var fragment = document.createDocumentFragment();
		for(i = 0; i < sl.length; i++){

			var sli = sl.item(i);
			var slId = sli.getAttribute('id');
			var slVa = sli.getAttribute('name');

			G_occupantsN[i] = slVa;

			var optSelected = false;
			if (selectedOption == slId) optSelected = true;

			var opta = new Element("option",{selected:optSelected,text:slVa,value:slId}).set("text",slVa);
			fragment.appendChild(opta);
		}
		this.users.appendChild(fragment);
		G_occupants = G_occupantsN;
	},

	doCommand: function(sTxt,sCmd,iNmb){
		if (!this.firstCheck(sTxt)) return;
		if (sTxt == "ok") {
			this.finishing('Příkaz '+sCmd+' OK',this.elmSendMessage);
			if (this.mazaniZprav) this.commandTimes[iNmb] = 0;
			this.msgClr();
			return;
		}
		else {
			switch (sCmd) {
				case "ban": case "msg": case "help": case "find": case "sys":
					if (this.mazaniZprav) this.commandTimes[iNmb] = 0;
					this.msgClr();
					this.finishing(sTxt,this.elmSendMessage);
				break;
				default:
					this.finishing('Neznámý příkaz',this.elmSendMessage);
				break;
			}
		}
	},

	msgClr: function(){
		this.msgBox.set('value','');
	},

	ChatRefreshing: function(oXml,sTxt){
		if (!this.firstCheck(sTxt)) {
		  this.refreshing = false;
			return;
		}
		var chatBody = this.where;
		var chat = oXml.getElementsByTagName('ms');
		var chatLastId = 0;
		var a = 0;
		var xx = 0;
		this.lastLastId = this.lastId;
		for(xx = 0; xx < chat.length; xx++){
			var chI = chat.item(xx);
			var _cMd = chI.getAttribute('m');
			var _cClr = chI.getAttribute('c');
			this.lastId = chI.getAttribute('id');
			if(_cMd == '9') {
				this.runDeleter(_cClr);
			}
			else {
				chatLastId = chI.getAttribute('id');
				_cF = chI.getAttribute('f');
				_cT = chI.getAttribute('t');
				chatTime = chI.getAttribute('l');
				_cD = new Element("div",{"html":"<small style='color:#bbb' class='date'>"+chatTime+"</small> "});
				_cSD = new Element("span").addClass('hide').set("html","<a href='#' onclick='adel(event,"+chatLastId+");return false;'>"+crossForDel+"</a> ");
				_cSD.injectTop(_cD);

				//atributy u jmena
				_cSN = new Element("span",{"html":_cF}).setStyle('fontWeight','bold');
				_cD.appendChild(_cSN);
				chM = chI.firstChild.data;
				chM = chM.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, "");

				if (_cMd == '1'){
					_cMB = " &raquo; "+_cT+": "+chM;
				}else if(_cMd == '2'){
				}else if(_cMd == '3'){
					_cMB = ": "+chM;
				}else{
					_cMB = ": "+chM;
				}

				_cM = new Element("span",{"html":_cMB});
				_cD.setProperties({'rel':chatLastId,'id':'msg-'+chatLastId+'-nmb'}).appendChild(_cM);
				if (_cClr.charAt(0) != "#" && (_cClr.length == 6 || _cClr.length == 3) && !isNaN(parseInt(_cClr,16))) _cClr = "#"+_cClr;
				if (_cMd == '1'){
					_cD.setStyles({'fontWeight':'bold','color':_cClr});
				}else if(_cMd == '2'){
				}else if(_cMd == '3'){
					_cD.setStyles({color:"#7b6200",fontSize:"0.8em"});
				}else{
					_cD.setStyle('color',_cClr);
				}
				if (!$("msg-"+chatLastId+"-nmb")) {
					if (!this.firstRun) _cD.setOpacity(0.05);
					a+=1;
					_cD.injectTop(this.where);
					if (!this.firstRun) _cD.set("tween",{property:'opacity',duration:1000,fps:20}).tween(0.05,1);
				}
			}
		}
		//aplikovani efektu na nove zpravy
		if (this.firstRun) this.firstRun = false;
		else {
//			if (a>0) opacitee("msg-"+chatLastId+"-nmb",(a-1));
		}
		nLimit = mesOpt;
		while (this.where.hasChildNodes() && this.where.childNodes.length > nLimit) this.where.getLast().dispose();
		this.refreshing = false;
	},

	OccupantsRefresh: function(oXml,sTxt){
		if (!this.firstCheck(sTxt)) return;

		this.nav.empty();

		var selectedOption = this.users.options[this.users.selectedIndex].value;

		var firstOne = this.users.getElement("option").clone();
		this.users.empty();
		firstOne.inject(this.users);

		G_occupantsN = [];

		var sl = oXml.getElementsByTagName('oc');
		var fragment = document.createDocumentFragment();
		var optSelected = null;

		var tt = "";
		var occM = oXml.getElementsByTagName("oc");
		G_occupantsN = [];

		for(var i = 0; i < occM.length; i++){
			var occI = occM[i];
			var oT = occI.getAttribute("t");
			var oC = occI.getAttribute("i");
			var uid = occI.getAttribute("uid");
			var oL = occM[i].firstChild.data;

			var opta = new Element("option",{selected:optSelected,text:oL,value:uid}).set("text",oL).set("value", uid);
			if (selectedOption > 0 && selectedOption == uid) {
				opta.set("selected", "selected");
			}

			fragment.appendChild(opta);

			G_occupantsN[i] = oL;
			new Element("span",{"html":"<img src='http://s1.aragorn.cz/i/"+oC+"' class='oI' onclick=\"whisperTo('"+oL+"');\" onmouseover=\"ddrivetip('<img src=\\'http://s1.aragorn.cz/i/"+oC+"\\' style=\\'padding: 3px\\' /><div class=\\'dhtmlDiv\\'>"+oL+"<br />"+oT+"</div>');\" onmouseout='hidedrivetip();' />"}).inject(this.nav);
		}
		this.users.appendChild(fragment);
		G_occupants = G_occupantsN;
	},

	CheckActivity: function(oXml,sTxt){
		if (!this.firstCheck(sTxt)) return;
		var active = oXml.getElementsByTagName('ac');
		if (!oXml){
		}
		else {
			if (active[0].firstChild.data < 1){
				this.goToEnd();
				setTimeout('document.location.href = "/chat/"',500);
			}
		}
	},

	firstCheck: function(sTxt){
		if (sTxt.length < 1) return false;
		return true;
	},

	runDeleter: function(n){
		v = $('msg-'+n+'-nmb');
		if (v && this.where.hasChild(v)) v.dispose();
	},

	todel: function(e,x){
		if (x) {
			var i = parseInt(x);
		}
		else {
			if (this.refreshing) return;
			var t;
			if (!e) var e = window.event;
			if (e.target) t = e.target;
			else if (e.srcElement) t = e.srcElement;
			if (t.nodeType == 3) t = t.parentNode;
			while(t.parentNode && t.tagName.toUpperCase() != 'DIV' && t.id != this.where) t = t.parentNode;
			if (t.tagName.toUpperCase() != 'DIV') t = t.parentNode;
			var i = parseInt($(t).getProperty('rel'));
		}
		if (i>0) this.cmd(7,false,i);
		else this.deleter();
	},

	doDelete: function(sTxt){
		if(sTxt != '') this.runDeleter(sTxt);
	},

	deleter: function(){
		if(this.refreshing){
			setTimeout(function(){ this.deleter(); }.bind(this),200);
			return;
		}
		this.mazaniZprav.set('disable',true);
		if(this.indel){
			this.where.removeClass('visibleDelete');
			this.mazaniZprav.removeClass("redButton").setProperty('value','Mazání Zpráv');
		}
		else{
			this.where.addClass('visibleDelete');
			this.mazaniZprav.addClass("redButton").setProperty('value','Mazání Vypni');
		}
		this.mazaniZprav.set('disable',false);
		this.indel = !this.indel;
	},

	login_checker: function(oXml){
		el = this;
		if(!oXml){
			setTimeout(function(){el.check_login();},59999);
			return;
		}
		ajaxF = true;
		txt = 0;
		xtx = oXml.getElementsByTagName("xtx");
		for(var i = 0; i < xtx.length; i++){
			if (xtx[i].firstChild.data!="off") {
				txt = Math.abs(txt-xtx[i].firstChild.data);
				ajaxF = false;
			} else {
				ajaxF = true;
				break;
			}
		}
		if (!ajaxF) {
			var mn = oXml.getElementsByTagName("mn");
			if(mn) {
				this.finishing('Kontrola záložek ok',this.elmOccupantsRefresh);
				for (var i=0;i<mn.length;i++) {
					if(mn[i].getAttribute('id')=='dropmenu2'){
						this.smileyPanel[1].set('html',mn[i].firstChild.data).getChildren().setProperty('target','_blank');
					}
					if(mn[i].getAttribute("id")=="dropmenu4"){
						this.ajaxiBar.set("html",mn[i].firstChild.data).getElements('a').each(function(em){if(em.href=='#'){em.onclick='return false;'}else{em.setStyle('display','block').setProperty('target','_blank');}});
					}
				}
				setTimeout(function(){ el.check_login(); },59999);
			}
		}
		else {
		  this.finishing('Kontrola záložek ok',this.elmOccupantsRefresh);
		}
	},

	toplistMaker: function() {
		if (this.toplistimg) this.toplistimg.set('src','http://toplist.cz/dot.asp?id=40769&amp;rndnmb='+$time()+'&amp;http='+escape(top.document.referrer));
		else this.toplistimg = new Element("img",{'height':'1px','width':'1px','src':('http://toplist.cz/dot.asp?id=40769&amp;rndnmb='+$time()+'&amp;http='+escape(top.document.referrer)+''),'alt':'','title':''}).inject(this.bdy);
	},

	login_timer_maker: function(){
		this.check_login();
	},

	check_login: function(){
		this.cmd(8);
	}

});


// -->