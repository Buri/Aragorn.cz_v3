function hide(obj){
	obj=$(obj);if(!obj){return;}
	if(obj.getStyle("display")=='none'){if(obj.hasClass("hide")){obj.removeClass("hide").setStyle("display","");}else{obj.setStyle("display",'').setStyle("visibility","visible");}
	}else if(obj.hasClass('hide')){obj.removeClass('hide');if(obj.getProperty("id")=="k" || obj.getProperty("id")=="zprava"){document.getElementById('km').focus();}
	}else{obj.addClass('hide');}
}

function check_mail(mail){
	re = /^[^.]+(\.[^.]+)*@([^.]+[.])+[a-z]{2,3}$/;
	return mail.search(re) == 0;
}
function conf(link){
	r=confirm("Jste si jist(a)?");
	if(r==true){
		window.location.href = link;
	}
}
function vloz_tag(znacka){var spravne=false;var kom=document.getElementById('km');switch(znacka){case "b":case "i":case "u":case "color1":case "color2":case "color3":start="{"+znacka+"}";end="{/}";spravne=true;break;}if(spravne){if(document.selection){kom.focus();sel=document.selection.createRange();sel.text=start+sel.text+end;kom.focus();}else if(kom.selectionStart||kom.selectionStart=='0'){var startPos=kom.selectionStart;var endPos=kom.selectionEnd;var cursorPos=endPos;if(startPos!=endPos){kom.value=kom.value.substring(0,startPos)+start+kom.value.substring(startPos,endPos)+end+kom.value.substring(endPos,kom.value.length);cursorPos+=end.length;}else{kom.value=kom.value.substring(0,startPos)+start+end+kom.value.substring(endPos,kom.value.length);}cursorPos+=start.length;kom.focus();kom.selectionStart=cursorPos;kom.selectionEnd=cursorPos;}else{kom.value+=start+end;kom.focus();}}}
function editor(type){switch (type){case 1:request = "{b}{/}";break;case 2:request = "{i}{/}";break;case 3:request = "{u}{/}";break;case 4:po = prompt("Zadejte odkaz včetně http://","");if (po.length > 0){if (po.indexOf('http://') == 0 || po.indexOf('ftp://') == 0 || po.indexOf('https://') == 0) request = "{link}"+po+"{/}";else {request = "{link}http://"+po+"{/}";}}break;case 5:po = prompt("Zadejte cestu k obrázku","");if (po.length > 0){request = "{link}"+po+"{/}";}break;}textContent = document.forms['txt']['mess'].value;textContent = textContent.concat(request);document.forms['txt']['mess'].value=textContent;po = "";request = "";}
function setCursor(el) {
end=el.value.length;
st=end; 
if(el.setSelectionRange) { 
el.focus(); 
el.setSelectionRange(st,end); 
} 
else { 
if(el.createTextRange) { 
range=el.createTextRange(); 
range.collapse(true); 
range.moveEnd('character',end); 
range.moveStart('character',st); 
range.select();
} 
} 
}
function react(re){
	re="{i}"+re+"{/}";
	document.getElementById('km').value=document.getElementById('km').value.concat(re+"\n");
	if($('k').hasClass('hide')){
		$('k').removeClass('hide');
	}
	else if (document.getElementById('k').style.display=='none') {
		document.getElementById('k').style.display="block";
	}
	document.getElementById('km').focus();
       setCursor(document.getElementById('km'));
	return false;
}
function throw_dices(t){if(t.nodeType==3){t=t.parentNode;}while(t.parentNode&&t.tagName.toUpperCase()!="A"){t=t.parentNode;}xK=prompt("Počet kostek (1 - 30)");if(xK>0&&!isNaN(xK)&&(xK>0&&xK<=30)){yK=prompt("Nejvyšší hodnota na kostce (minimální hod je napevno 1, maximální 10000)");if(yK>0&&!isNaN(yK)&&(yK>1&&yK<=10000)){if(t.href.indexOf("&")>0){a=t.href.split("&");t.href=a[0];}t.href+="&x="+xK+"&y="+yK;return true;}}return false;}
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
if(!xmlhttp&&typeof XMLHttpRequest!='undefined'){try{xmlhttp=new XMLHttpRequest();}catch(e){xmlhttp=false;}}if(!xmlhttp&&window.createRequest){try{xmlhttp=window.createRequest();}catch(e){xmlhttp=false;}}if(!xmlhttp){return false;}xmlhttp.open(meth,url);xmlhttp.onreadystatechange=function(){after(xmlhttp);};if(heads){for(var key in heads){xmlhttp.setRequestHeader(key,heads[key]);}}xmlhttp.setRequestHeader('Pragma','no-cache');xmlhttp.setRequestHeader("Cache-Control", "no-cache");xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');if(xmlhttp.overrideMimeType){xmlhttp.setRequestHeader('Connection','close');}if(content) {xmlhttp.send(content);}else{xmlhttp.send(null);}return true;}

function check_login(z){new Request({'url':'/ajaxing.php?do=logining&heads=1&zal='+z+'&rndtmr='+$time(),'method':'get','onComplete':function(txt,xml){login_checker(xml);},'onFailure': function(txt,xml){setTimeout('check_login(true);',30*1000);}}).send();return true;}
function cestinaSklonuj(pocet,kus,kusy,kusu){
	if (pocet > 4 || pocet == 0) return kusu;
	else if (pocet > 1) return kusy;
	else return kus;
}
function login_checker(oXml){
	var ajaxF=true;
	var txt=0;
	var xtx=oXml.getElementsByTagName('xtx');
	for(var i=0;i<xtx.length;i++){if(xtx[i].firstChild.data!="off"){txt=Math.abs(txt-xtx[i].firstChild.data);ajaxF=false;}else{ajaxF=true;break;}}
	if(!ajaxF){
		var ajaxiBar = $('ajaxi-bar');
		var theprofilepostlink = $('theprofilepostlink');
		if (theprofilepostlink.hasClass('doT')) {
			if (!ajaxiBar.retrieve('puvTitle')) {ajaxiBar.store('puvTitle',document.title);}
			document.title = ajaxiBar.retrieve('puvTitle');
		}

		ajaxiBar.getFirst().setStyle('width',(100-Math.round(100*txt/60/60))+'%').setProperty('title','Do automatického odhlášení zbývá: '+Math.round(Math.abs(txt-60*60)/60)+' minut').getParent().setProperty('title','Do automatického odhlášení zbývá: '+Math.round(Math.abs(txt-60*60)/60)+' minut').setStyle('display','block');
		var mn=oXml.getElementsByTagName('mn');
		if(mn){
			for(var i=0;i<mn.length;i++){
				$(mn[i].getAttribute("id")).set("html",mn[i].firstChild.data);
			}
		}
		var npost=oXml.getElementsByTagName('np');
		if(npost.length==1){
			if(npost[0].firstChild.data>0){
				theprofilepostlink.set('html','pošta ('+npost[0].firstChild.data+') <img src="/system/ruzne/neprecteno.gif" title="Máte nepřečtenou poštu" alt="Máte nepřečtenou poštu)" />');
				if (theprofilepostlink.hasClass('doT'))
					document.title = npost[0].firstChild.data+'p] ' + ajaxiBar.retrieve('puvTitle');
			}
			else{
				theprofilepostlink.set('html','pošta');
				if (theprofilepostlink.hasClass('doT'))
					document.title = '0p] '+ajaxiBar.retrieve('puvTitle');
			}
			if (theprofilepostlink.hasClass('doT')) {
				var tx = $('dropmenu4').getElements('span');
				if (tx)
					document.title = '['+tx.length+'z/' + document.title;
				else
					document.title = '[0z/' + document.title;
			}
		}
		
		aTx = setTimeout('check_login(true);',60*1000);
	}
	else{
		$('ajaxi-bar').setStyle('display','none');
	}
}
function login_timer_maker(){new Element("div",{'id':'ajaxi-bar','title':'Ukazatel poslední aktivity','styles':{'cursor':'help','display':'none'}}).adopt(new Element("a",{'href':'#','title':'Ukazatel poslední aktivity','styles':{'cursor':'help'}}).adopt(new Element("small",{'text':"neaktivita",'styles':{'cursor':'help','fontSize':'9px','textIndent':'2px'}}))).inject(document.body);check_login(false);return true;}

var submited_checkfornewX = false;

function checkfornewX(xmlhttp){
	if(xmlhttp.readyState==4){
		var frm=document.forms["form_for_new"];
		var status=0;
		try{status=xmlhttp.status;}catch(e){};
		if(status!=200||!xmlhttp.responseXML){frm.submit();}
		var elm=xmlhttp.responseXML.documentElement.firstChild;
		if(elm.getAttribute("t")=="ok"){
			if (submited_checkfornewX){
				alert('Formulář se již odesílá.');
			}
			else {
				submited_checkfornewX = true;
				frm.submit();
			}
		}
		else{
			if(document.getElementById("ajax_for_new_info")){
				var aaa=document.getElementById("ajax_for_new_info");
			}
			else{
				var aaa=document.createElement("div");
				aaa.id="ajax_for_new_info";
				var dc=frm.parentNode.previousSibling;
				while(dc.nodeType==3){
					dc=dc.previousSibling;
				}
				dc=dc.previousSibling;
				dc.parentNode.insertBefore(aaa,dc);
			}
			aaa.innerHTML="<p class='info' id='inf-x'><span class='war' title='Varování'></span>"+elm.firstChild.data+" <a href=\"javascript: hide('inf-x')\" class='permalink2' title='Zavřít'>Zavřít</a></p>";
			if(elm.hasAttribute("f")){
				document.forms["form_for_new"][elm.getAttribute("f")].focus();
			}
			aaa.firstChild.style.visibility="visible";
			aaa.firstChild.style.display="";
		}
	}
}

function checkForNew(w,f,a,b){
	var f=document.getElementById(f);
	f=f.value;
	var frm=document.forms["form_for_new"];
	adv=new Array();
	if(b){
		adv=a;
	}
	else{
		for(var aa=0;aa<a.length;aa++){
			frm[a[aa]].value=frm[a[aa]].value.trim();
			var t=frm[a[aa]];
			adv[aa]=a[aa]+':'+t.value.length;
			if((isNaN(t.value)&&t.value.length>=3)||t.value.length>0){
				continue;
			}
			while(t.parentNode&&t.tagName.toUpperCase()!="LABEL"){
				t=t.parentNode;
			}
			t=t.firstChild.innerHTML;
			if(t.indexOf(" (")>0){
				t=t.substr(0,t.indexOf(" ("));
			}
			if(frm[a[aa]].tagName.toLowerCase()=="select" && frm[a[aa]].options[frm[a[aa]].selectedIndex].value == ""){
				alert("Musíte vybrat jednu z možností pro '"+t+"'");
			}
			else if(frm[a[aa]].tagName.toLowerCase()=="input"&&frm[a[aa]].type.toLowerCase()=="file"&&frm[a[aa]].value.length<4){
				alert("Musíte vybrat soubor k odeslání.");
			}
			else{
				alert("Musíte vyplnit políčko '"+t+"'");
			}
			frm[a[aa]].focus();
			return false;
		}
		adv=adv.join(",");
	}
	if(!send_xmlhttprequest(checkfornewX,'POST','/ajaxing.php?do=checker&sekce='+w,'adv='+adv+'&co='+encodeURIComponent(f))){
		return true;
	}
	else{
		return false;
	}
}

var aTx;

window.addEvent("load",function(){
	if (document.body.className.indexOf("js") != -1 && Browser.Features.xhr) {
		aTx = setTimeout('login_timer_maker()', 5000);
		var frm = $('k');
		var profilePost = $('theprofilepostlink');
		if (profilePost && frm) {
			if (profilePost.getParent().getElement("a").get("text") === "apophis") {
				disModuleAjaxSend(frm);
			}
		}
	}
});

function makeStats(section, login){

aP = new Request({'url':"/ajaxing.php?do=stats&sec="+section+"&loginStats="+login, 'method': 'post', 'onComplete': function(txt, xml){ flushStats(txt, xml); } });
aP.setHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
aP.setHeader('Pragma','no-cache');
aP.setHeader("Cache-Control", "no-cache");
aP.send();

}

function flushStats(txt, xml){
 var rows = "";

 data = xml.getElementsByTagName('stats');
 dataC = data.length;
 for (i = 0; i < dataC; i++){

    autor = data[i].getElementsByTagName('autor');
    pocet = data[i].getElementsByTagName('pocet');
    prumer = data[i].getElementsByTagName('prumer');
    koef = data[i].getElementsByTagName('koef');
    rows = rows.concat("<tr><td>"+autor[0].firstChild.data+"</td><td>"+pocet[0].firstChild.data+"</td><td>"+prumer[0].firstChild.data+"</td><td class='jsStatsK'>"+koef[0].firstChild.data+"</td></tr>");

 }
msg = xml.getElementsByTagName('msg');

statsContent = "<table class='jsStats' id='jsStatsContent' width='100%' cellspacing='3'><thead><tr><th>Autor</th><th>Počet děl</th><th>Průměrné hodnocení</th><th>Koeficient</th></tr></thead><tbody>";
statsContent = statsContent.concat(rows);
statsContent = statsContent.concat("</tbody></table>");
statsContent = statsContent.concat(msg[0].firstChild.data);

$('jsStats').set('html',statsContent); 

}

var thePreviewSender = false;
var thePreviewPSender = false;
var prew;

function do_preview(id){
	var firstTable = $('dis-module-x').getElement('table');
	if (!firstTable) {
		prew = new Element('div').adopt(new Element('table',{'cellspacing':0,'class':'commtb'}).adopt(new Element("tbody").adopt(new Element('tr').adopt(new Element('td',{'colspan':2,'class':'c1'}))).adopt(new Element('tr').adopt(new Element('td',{'class':'c2',"html":"<a href='#'></a>"})).adopt(new Element('td',{'class':'c3','html':"<p class='c4'></p>"}))))).injectTop($('dis-module-x')).addClass('hide');
	}
	else var prew = firstTable.getParent().clone(true);
	if (!thePreviewSender) {
		thePreviewSender = new Request({'url':"/ajaxing.php?do=preview",
			'method':"post",
			'onRequest':function(){
				if(!document.getElementById('preview_table')){
					firstTable = $('dis-module-x').getElement('table').getParent().removeClass('hide');
					prew.getElements('tr').erase('id');
					if (prew.getElement('tr').getNext().getFirst().hasClass('cspt')){
						prew.getElement('tr').getNext().dispose();
					}
					prew.getElement('tr').getNext().getElement('td').getElement('a').dispose();
					prew.setProperty('id','preview_table').getElement('p').empty().removeClass("unr");

					if (!firstTable) {
						prew.injectTop($('dis-module-x'));
					}
					prew.injectBefore(firstTable);
				}
				$("preview_table").getElement('td').set('html','Náhled příspěvku: Načítání ...');
			},
			'onComplete':function(txt,xml){
				prvTbl = $("preview_table");
				prvTbl.getElement('td').set('html','Náhled příspěvku');
				prvTbl.getElement('p').set('html',txt);
			},
			'onFailure':function(){
				if (document.getElementById('preview_table')) {
					prvTbl = $("preview_table");
					prvTbl.getElement('td').set('html','Náhled příspěvku: Chyba');
					prvTbl.getElement('p').set('html',"Načítání náhledu selhalo!<br /><br />Patrně je chyba ve spojení se serverem Aragorn.cz");
				}
			}
		});
	}
	thePreviewSender.send({'data':{'txt':$(id).get('value')}});
}

function do_previewP(id){
	if (!thePreviewPSender) {
		thePreviewPSender = new Request({'url':"/ajaxing.php?do=preview",
			'method':"post",
			'onRequest':function(){
				if(!document.getElementById('preview_table')){
					new Element("div",{'html':"<table class='commtb' cellspacing='0' id='preview_table' cellpadding='0'><tr><td class='c1' colspan='2'></td></tr><tr><td class='c2'></td><td class='c3'><p class='c4'></p></td></tr></table>"}).injectAfter($('zprava'));
				}
				$("preview_table").getElement('td').set('html','Náhled zprávy: Načítání ...');
			},
			'onComplete':function(txt,xml){
				prevTable = $("preview_table");
				prevTable.getElement('td').set('html','Náhled zprávy');
				prevTable.getElement('p').set('html',txt);
			},
			'onFailure':function(){
				if (document.getElementById('preview_table')) {
					prevTable = $("preview_table");
					prevTable.getElement('td').set('html','Náhled zprávy: Chyba');
					prevTable.getElement('p').set('html',"Načítání náhledu zprávy selhalo!<br /><br />Patrně je chyba ve spojení se serverem Aragorn.cz");
				}
			}
		});
	}
	thePreviewPSender.send({'data':{'txt':$(id).get('value')}});
}

function comm_del(){
pole = [];
cn=0;
ids = $$("#dis-module-x input[type=checkbox]");
for(i=0,idsl = ids.length; i < idsl; i++){
	if (ids[i].checked == true && ids[i].value != "") {
		pole[cn] = ids[i].value;
		cn++;
	}
}
if (pole.length > 0){
	var konc = "ek";
	doVymaz = false;
	if (pole.length > 1) konc = "ky";
	if (pole.length > 4) konc = "ků";

	if (pole.length < 2) doVymaz = true;
	else if (confirm('Opravdu smazat '+pole.length+' příspěv'+konc+' ???')) doVymaz = true;

	if (doVymaz) {
		vymaz = pole.join(",");
		window.location.assign(urlPartDiskuze+(urlPartDiskuze.indexOf("?") == -1?'?':'&')+"akce=comm-delete&ids="+vymaz);
	}
	else {
		return false;
	}
}else{
	alert("Žádné vybrané položky ke smazání");
}

}

function rep(to){
$('zprava').removeClass('hide');
document.getElementById('to').value = to;
document.getElementById('km').focus();
return false;
}

function rep2(toP){
$('zprava').removeClass('hide');
var to = $('to');
to.setProperty('value',to.getProperty('value').clean());

if ($type(toP) != "string") {
	toP = $(toP).get('text');
}
if (to.getProperty('value') != ""){
	toP = ", "+toP;
	to.setProperty('value',to.getProperty('value')+toP);
}
else {
	to.setProperty('value',toP);
}
document.getElementById('km').focus();
return false;
}

function post_del(){
pole=[];ids=$$('#postolka-all input[type=checkbox]');idsl=ids.length;cn=0;
for(i=0;i<idsl;i++){
	if(ids[i].checked && ids[i].value != ""){
		pole[cn]=ids[i].value;
		cn++;
	}
}
if(pole.length>0){
	vymaz=pole.join(",");
	window.location.href = urlPartPosta+"/?akce=postolka-delete&ids="+vymaz;
}else{
	alert("Žádné vybrané zprávy ke smazání");
}
}

function PostolkaMaker() {

	var t='#postolka-all ';
	var tpr0 = $$(t+'.pr-0').setProperty('title','Nepřečteno').set('text',' ');
	var tpr1 = $$(t+'.pr-1').setProperty('title','Přečteno').set('text',' ');
	var tpr2 = $$(t+'.pr-2').setProperty('title','Smazáno').set('text',' ');
	var tpr3 = $$(t+'.pr-3').setProperty('title','Smazáno').set('text',' ');
	var tprX = $$(t+'.m-in-unr').setProperty('title','Počet nepřečtených příchozích zpráv');
	$$(t+'#dis-module-x .c2 span').addClass('post-status');

	var GiveThemTips = new Tips($$(tpr0,tpr1,tpr2,tpr3,tprX), {
		'className':'tool-tip',fixed:true,offsets:{x:15,y:25},showDelay:0,hideDelay:0
	});
	var TRs = $$('#vypis-out TR','#vypis-in TR').each(function(t){
		t.lastChild.className='pd';
		t.lastChild.previousSibling.className='pt';
		t.lastChild.previousSibling.previousSibling.className='po';
		t.firstChild.nextSibling.className='pp';
		t.addEvent('mouseout',function(){this.removeClass('postolka-hlight')});
		t.addEvent('mouseover',function(){this.addClass('postolka-hlight')});
		t.addEvent('click',	function(e){var t;if(!e)var e=window.event;if(e.target){t=e.target;}else if(e.srcElement){t=e.srcElement;}if(t.nodeType==3)t=t.parentNode;if(t.tagName.toLowerCase()=="input"||t.tagName.toLowerCase()=="a"){return;}while(t.parentNode&&t.tagName.toUpperCase()!= "TR"){t=t.parentNode;}var q=t.getElementsByTagName("INPUT");for(var qw=0;qw<q.length;qw++){q[qw].checked=!q[qw].checked;}});
	});
	
	if (!Browser.Engine.trident4 && Browser.Features.xhr) {

		var ajaxCommSubmiter = function(event){
			var t,prev,prevId,trgtDiv,qq;
			t = $(event.target);
			if (t.get('tag') == 'a') {
				prev = t.getPrevious();
				prevId = 'tmp_'+prev.getProperty('href').replace('/posta/in/','').replace('/posta/out/','');
				prevId = prevId.replace(/\/posta\/konverzace\/([^\?]{1,})\?/g,'').replace('p=','').replace(/\&index=([\w]{1,})$/g,'').replace(/\//g,'') + '_view';
				if (document.getElementById(prevId)){
					prevId = $(prevId);
					if (prevId.hasClass('opened')) {
						prevId.removeClass('opened').tween('height',0);
					}
					else {
						qq = prevId.removeClass('hide').getFirst().getHeight();prevId.addClass('opened').tween('height',qq);
					}
				}
				else {
					qq = prev;
					while(qq.get('tag') != 'tr' && qq.parentNode){
						qq = qq.getParent();
					}
					trgtDiv = new Element('tr',{
						styles:{
							'overflow':'hidden','height':'auto'
						}
					}).adopt(
						new Element('td',{
							colspan:5,styles:{
								'height':'auto','overflow':'hidden','padding':'0'
							}
						}).adopt(
							new Element('div',{
								'id':prevId,'styles':{'padding':'0','position':'relative','height':'0','overflow':'hidden'}
							}).set('tween',{
								'duration':500,'link':'cancel','onComplete':function(){
									if(this.element.offsetHeight<2){this.element.getParent().tween('height',0);this.element.addClass('hide');}
								},
								'onStart':function(){
									if(this.element.hasClass('hide')){this.element.removeClass('hide').getParent().tween('height',this.element.getFirst().getHeight());}
								}
							}).adopt(
								new Element('div',{
									'class':'postolkaPreviewText','styles':{'position':'absolute'}
								}).adopt(
									new Element('p',{'class':'c4','text':'Načítám...'})
								)
							)
						)
					).injectAfter(qq);
					trgtDiv = trgtDiv.getElement('div');
					qq = trgtDiv;
					trgtDiv = trgtDiv.getFirst();
					qq.tween('height',trgtDiv.getHeight())

					trgtDiv.set('load',{
						onSuccess:function(rTree,rEls,rHTML,rJS){
							this.set('html',rHTML);this.getParent().addClass('opened').tween('height',this.getHeight());
						}.bind(trgtDiv)
					});

					qq = prevId.replace('tmp_','').replace('_view','');
					trgtDiv.load('/ajaxing.php?do=postolka&num='+qq);
				}
			}
			event.stop();
			return false;
		};

		var q = new Element('a',{'class':'permalink2 nahled','href':'#','text':'Náhled','styles':{'fontSize':'80%','lineHeight':'150%','float':'right'}});
		q.addEvent('click',ajaxCommSubmiter.bindWithEvent(q));
		$$('#vypis-all a.r').each(function(el){
			q.clone(true).cloneEvents(q,'click').injectAfter(el);
		});
	}
}

function readIt(t){
	var q,e;
	e = $('unr_link_'+t) || false;
	if (e) {
		q = new Request({
			'url':'/ajaxing.php?do=postolka&read=1&num='+t,
			'method':'get',
			'onRequest':function(){
				this.removeEvent('click').addEvent('click',function(){return false;}).set('text','Zpracovávám...');
			}.bind(e.getElement('a')),
			'onSuccess':function(txt,xml){
				var a;
				txt = $('unr_link_'+txt) || false;
				if (txt) {
					txt.set('text','Status zprávy změněn na přečteno.').setStyle('overflow','hidden');
					a = txt;
					while(a.get('tag') != 'tr') {
						a = a.getParent();
					}
					a.getPrevious().getFirst().set({'class':'pr-1','title':'Přečteno'}).store('tip:title','Přečteno');
					txt.set('morph',{
						duration:2000,
						transition:'quad:in',
						onComplete:function(){
							var aa;
							aa = this.element.getParent().getParent();
							this.element.dispose();
							aa.tween('height',aa.getFirst().getHeight());
						}
					}).morph({'opacity':0,'height':0,'padding-top':0,'padding-bottom':0});
				}
			}
		});
		q.send();
	}
	return false;
}

function zaskrtnout(t){
	$$('#'+t+' input').each(function(el){el.checked=!el.checked;});
}

Element.Properties.disabled = {
 
    get: function(){
        return this.disabled;
    },
 
    set: function(value){
        this.disabled = !!value;
        this.setAttribute('disabled', !!value);
    }
 
};

function doAjaxSendForm(el,uri){
	el = $(el);
	el.getElements('input[type=submit]').setProperty('disabled',false);
	el.set('send',{
		'url':el.getProperty('action')+'&ajaxed=1',
		'onRequest':function(){
			form_ajax_loader.setStyles({'opacity':1,'top':0}).getElement("p").set("text",'Odesílám ...');
		},
		'onSuccess':function(txt,xml){
			if (txt == "off") {
				alert("Již nejste na serveru Aragorn.cz jako online uživatel.\n\nDoporučujeme otevřít nové okno, přihlásit se a odeslat tento příspěvek znovu.");
			}
			else if (txt == "--" || txt == "-") {
				alert('Neznámá chyba s odesláním příspěvku.');
				window.location.reload();
				return;
			}
			else if (txt == "right") {
				txt = "Nemáte práva na odeslání příspěvku.";
			}
			else if (txt == "text") {
				txt = "Příspěvek musí obsahovat nějaký text.";
			}
			else {
				txt = "Příspěvek odeslán.";
				this.reset();
			}
			var comms = $A(xml.getElementsByTagName('comm'));
			setTimeout(function(){
				form_ajax_loader.setStyle('top','100%');
			},2000);
			if (comms) {
				form_ajax_loader.getElement('p').set('html',txt);
				var disModule = $('dis-module-x').getElement('p');
				if (disModule && comms.length > 0) {
					if (document.getElementById('preview_table')){
						disModule = $('preview_table');
						if (disModule.getNext() && disModule.getNext().get('tag') == 'table' && disModule.getParent().get('tag') == 'div'){
							disModule = disModule.getParent();
						}
					}
					for (var a=0,cl=comms.length;a<cl;a++) {
						var elmnt = new Element("div",{'html':comms[a].firstChild.data}).inject(disModule,'after');
						disModule = elmnt;
					}
				}
			}
		}.bind(el),
		'onFailure':function(){
			alert('AJAX odeslání příspěvku selhalo na neznámé chybě.\n\nObnovuji stránku...');
			window.location.reload();
		}
	}).send();
	
}

var form_ajax_loader;

function disModuleAjaxSend(frm){
	var urlHere = window.location.pathname;
	if (urlHere.length > 1 && urlHere.indexOf("/",2)>0) {
		frm = frm.getElement('form');
		if (frm) {
			frm.setStyles({'position':'relative','top':0,'left':0,'overflow':'hidden'}).addEvent('submit',function(event){new Event(event).stop();doAjaxSendForm(this,urlHere);return false;});
			var sizes = frm.getSize();
			form_ajax_loader = new Element("div",{
				'class':'FormAjaxLoader t-a-c',
				'id':'form_ajax_loader',
				'styles':{
					'fontSize':'30px',
					'position':'absolute',
					'top':'100%','left':'0',
					'display':'block',
					'width':'100%',
					'height':'100%'
				}
			}).inject(frm,'bottom').adopt(new Element('div',{
				'styles':{
					'position':'absolute','top':0,'left':0,'height':'100%','width':'100%',
					'backgroundColor':'#000000','opacity':0.75
				}
			}),new Element('p',{'text':'Odesílám ...','class':'t-a-c','styles':{'color':'#ffffff','position':'absolute','padding':'0 0 32px 0','background':'url("/graphic/ajax-loader.gif") center bottom no-repeat','left':'0','width':'100%','top':'30px','lineHeight':'40px'}}));
		}
	}
}

function toplistMaker(){
	$('toplist_link_footer').adopt(new Element('img',{src:'http://toplist.cz/dot.asp?id=40769&wi='+escape(screen.width)+'&he='+escape(screen.height)+'&cd='+escape(window.screen.colorDepth)+'&t='+escape(document.title)+'&http='+escape(document.referrer)+'&_t='+$time(),width:1,height:1,border:0,alt:'TOPlist'}));
}

document.addEvent('domready', function(){
	$$('div.menu a').addEvent('click', function() {
		if (this.get('id') !== 'marked') {
			$('marked').erase('id');
			this.set('id', 'marked');
		}
	});
});


function setActiveStyleSheet(title) {
  var i, a, main;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute("title")) {
      a.disabled = true;
      if(a.getAttribute("title") == title) {
				a.disabled = false;
			}
    }
  }
}

function setStyleSheeter(title){
	var aX;
	//  setActiveStyleSheet(title);
	if (title.camelCase) {
		aX = document.body;
		aX.className = 'js '+title.camelCase().toLowerCase();
		aX.parentNode.id='ht'+title.camelCase().toLowerCase();
		Cookie.dispose('style');
		aX = new Cookie.write('style', title.camelCase().toLowerCase(),{'path':'/','domain':window.location.host,'duration':365});
	}
}

function getActiveStyleSheet() {
  return document.body.className.replace('js','').clean().toLowerCase();
}

function getPreferredStyleSheet() {
  var i, a;
  return document.body.className.replace('js','').clean().lowerCase();
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) {
    if(a.getAttribute("rel").indexOf("style") != -1
       && a.getAttribute("rel").indexOf("alt") == -1
       && a.getAttribute("title")
       ) return a.getAttribute("title");
  }
  return null;
}

function createCookie(name,value,days,v2) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

window.onload = function(e) {
  var cookiee = Cookie.read("style");
  var title = cookiee ? cookiee : "gallery";
  setStyleSheeter(title);
}

window.onunload = function(e) {
	var a;
	Cookie.dispose('style');
	a = new Cookie.write('style', getActiveStyleSheet(),{'path':'/','domain':window.location.host,'duration':365});
}

//var cookiee = readCookie("style");
//var titleeX = cookiee ? cookiee : 'gallery';


//Chrome Drop Down Menu- Author: Dynamic Drive (http://www.dynamicdrive.com)
//Last updated: Jan 1st, 06'

var cssdropdown={
disappeardelay:500,dropmenuobj:null,ie:document.all,firefox:document.getElementById&&!document.all,
getposOffset:function(what,offsettype){
var totaloffset=(offsettype=="left")?what.offsetLeft:what.offsetTop;
var parentEl=what.offsetParent;
while(parentEl!=null){
totaloffset=(offsettype=="left")?totaloffset+parentEl.offsetLeft:totaloffset+parentEl.offsetTop;
parentEl=parentEl.offsetParent;
}
return totaloffset;
},
showhide:function(obj,e,visible,hidden){
if(this.ie||this.firefox){this.dropmenuobj.style.left=this.dropmenuobj.style.top="-500px";}
if(e.type=="click"&&obj.visibility==hidden||e.type=="mouseover"){obj.visibility=visible;}
else if(e.type=="click"){obj.visibility=hidden;}
},
iecompattest:function(){
return (document.compatMode&&document.compatMode!="BackCompat")?document.documentElement:document.body;
},

clearbrowseredge:function(obj, whichedge){
var edgeoffset=0;
if(whichedge=="rightedge"){
var windowedge=this.ie&&!window.opera?this.iecompattest().scrollLeft+this.iecompattest().clientWidth-15:window.pageXOffset+window.innerWidth-15;
this.dropmenuobj.contentmeasure=this.dropmenuobj.offsetWidth;
if(windowedge-this.dropmenuobj.x<this.dropmenuobj.contentmeasure){edgeoffset=this.dropmenuobj.contentmeasure-obj.offsetWidth;}
}
else{
var topedge=((this.ie&&!window.opera)?this.iecompattest().scrollTop:window.pageYOffset);
var windowedge=(this.ie&&!window.opera)?this.iecompattest().scrollTop+this.iecompattest().clientHeight-15:window.pageYOffset+window.innerHeight-18;
this.dropmenuobj.contentmeasure=this.dropmenuobj.offsetHeight;
if(windowedge-this.dropmenuobj.y<this.dropmenuobj.contentmeasure){edgeoffset=this.dropmenuobj.contentmeasure+obj.offsetHeight;if((this.dropmenuobj.y-topedge)<this.dropmenuobj.contentmeasure){edgeoffset=this.dropmenuobj.y+obj.offsetHeight-topedge;}}
}
return edgeoffset;
},

dropit:function(obj,e,dropmenuID){
if(this.dropmenuobj!=null){this.dropmenuobj.style.visibility="hidden";}
this.clearhidemenu();
if(this.ie||this.firefox){
obj.onmouseout=function(){cssdropdown.delayhidemenu()};
this.dropmenuobj=document.getElementById(dropmenuID);
this.dropmenuobj.onmouseover=function(){cssdropdown.clearhidemenu()};
this.dropmenuobj.onmouseout=function(){cssdropdown.dynamichide(e)};
this.dropmenuobj.onclick=function(){cssdropdown.delayhidemenu()};
this.showhide(this.dropmenuobj.style, e,"visible","hidden");
this.dropmenuobj.x=this.getposOffset(obj,"left");
this.dropmenuobj.y=this.getposOffset(obj,"top");
this.dropmenuobj.style.left=this.dropmenuobj.x-this.clearbrowseredge(obj,"rightedge")+"px";
this.dropmenuobj.style.top=this.dropmenuobj.y-this.clearbrowseredge(obj,"bottomedge")+obj.offsetHeight+1+"px";
}
},

contains_firefox:function(a,b){
while(b.parentNode){if((b=b.parentNode)==a){return true;}}
return false;
},

dynamichide:function(e){
var evtobj=window.event?window.event:e;
if(this.ie&&!this.dropmenuobj.contains(evtobj.toElement)){this.delayhidemenu();}
else if(this.firefox&&e.currentTarget!=evtobj.relatedTarget&&!this.contains_firefox(evtobj.currentTarget,evtobj.relatedTarget)){this.delayhidemenu();}
},

delayhidemenu:function(){
this.delayhide=setTimeout("cssdropdown.dropmenuobj.style.visibility='hidden'",this.disappeardelay);
},

clearhidemenu:function(){if(this.delayhide!="undefined"){clearTimeout(this.delayhide);}}
}


/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Version 2.1 Copyright (C) Paul Johnston 1999 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */

var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */

function hex_md5(s){ return binl2hex(core_md5(str2binl(s), s.length * chrsz));}
function b64_md5(s){ return binl2b64(core_md5(str2binl(s), s.length * chrsz));}
function str_md5(s){ return binl2str(core_md5(str2binl(s), s.length * chrsz));}
function hex_hmac_md5(key, data) { return binl2hex(core_hmac_md5(key, data)); }
function b64_hmac_md5(key, data) { return binl2b64(core_hmac_md5(key, data)); }
function str_hmac_md5(key, data) { return binl2str(core_hmac_md5(key, data)); }

function core_md5(x, len)
{
  x[len >> 5] |= 0x80 << ((len) % 32);
  x[(((len + 64) >>> 9) << 4) + 14] = len;

  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;

  for(var i = 0; i < x.length; i += 16)
  {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;

    a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
    b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
    c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);

    a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
    b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
    b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);

    a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
    a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
    c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
    b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);

    a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
    b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
    c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);

    a = safe_add(a, olda);
    b = safe_add(b, oldb);
    c = safe_add(c, oldc);
    d = safe_add(d, oldd);
  }
  return Array(a, b, c, d);

}

function md5_cmn(q, a, b, x, s, t)
{
  return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
}
function md5_ff(a, b, c, d, x, s, t)
{
  return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t)
{
  return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t)
{
  return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t)
{
  return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}

function core_hmac_md5(key, data)
{
  var bkey = str2binl(key);
  if(bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);

  var ipad = Array(16), opad = Array(16);
  for(var i = 0; i < 16; i++)
  {
    ipad[i] = bkey[i] ^ 0x36363636;
    opad[i] = bkey[i] ^ 0x5C5C5C5C;
  }

  var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
  return core_md5(opad.concat(hash), 512 + 128);
}

function safe_add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}
function bit_rol(num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}

function str2binl(str)
{
  var bin = Array();
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < str.length * chrsz; i += chrsz)
    bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
  return bin;
}

function binl2str(bin)
{
  var str = "";
  var mask = (1 << chrsz) - 1;
  for(var i = 0; i < bin.length * 32; i += chrsz)
    str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
  return str;
}

function binl2hex(binarray)
{
  var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i++)
  {
    str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
           hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
  }
  return str;
}

function binl2b64(binarray)
{
  var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  var str = "";
  for(var i = 0; i < binarray.length * 4; i += 3)
  {
    var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
                | (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
                |  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
    for(var j = 0; j < 4; j++)
    {
      if(i * 8 + j * 6 > binarray.length * 32) str += b64pad;
      else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
    }
  }
  return str;
}

function _to_utf8(s) {
  var c, d = "";
  for (var i = 0; i < s.length; i++) {
    c = s.charCodeAt(i);
    if (c <= 0x7f) {
      d += s.charAt(i);
    } else if (c >= 0x80 && c <= 0x7ff) {
      d += String.fromCharCode(((c >> 6) & 0x1f) | 0xc0);
      d += String.fromCharCode((c & 0x3f) | 0x80);
    } else {
      d += String.fromCharCode((c >> 12) | 0xe0);
      d += String.fromCharCode(((c >> 6) & 0x3f) | 0x80);
      d += String.fromCharCode((c & 0x3f) | 0x80);
    }
  }
  return d;
} 

var loginFormElement = null;

function getChallenge(t){
	loginFormElement = t;
	t = $(t);
	t.getElements('input').setProperty('readonly', 'readonly');
	t.getElement('input[type=submit]').addClass('ajaxBgLoader');
	new Asset.javascript('/ajaxing.php?challenge',{evalScript:true,onload:function(){md5form();}});
	return false;
}

function md5form(f) {
	if (!f) f = loginFormElement;
	f['password_hmac'].disabled = false;
	f['password_hmac'].value = hex_hmac_md5(hex_md5(_to_utf8(f['pass'].value)), f['challenge'].value);
	f['pass'].disabled = true;
	f.submit();
	f['pass'].disabled = false;
	f['password_hmac'].disabled = true;
	return false;
}


/*
 *  Hyphenator 3.3.0 - client side hyphenation for webbrowsers
 *  Copyright (C) 2011  Mathias Nater, Zürich (mathias at mnn dot ch)
 *  Project and Source hosted on http://code.google.com/p/hyphenator/
 * 
 *  This JavaScript code is free software: you can redistribute
 *  it and/or modify it under the terms of the GNU Lesser
 *  General Public License (GNU LGPL) as published by the Free Software
 *  Foundation, either version 3 of the License, or (at your option)
 *  any later version.  The code is distributed WITHOUT ANY WARRANTY;
 *  without even the implied warranty of MERCHANTABILITY or FITNESS
 *  FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.
 *
 *  As additional permission under GNU GPL version 3 section 7, you
 *  may distribute non-source (e.g., minimized or compacted) forms of
 *  that code without the copy of the GNU GPL normally required by
 *  section 4, provided you include this license notice and a URL
 *  through which recipients can access the Corresponding Source.
 */

var Hyphenator=(function(window){var
supportedLang={'be':'be.js','ca':'ca.js','cs':'cs.js','da':'da.js','bn':'bn.js','de':'de.js','el':'el-monoton.js','el-monoton':'el-monoton.js','el-polyton':'el-polyton.js','en':'en-us.js','en-gb':'en-gb.js','en-us':'en-us.js','es':'es.js','fi':'fi.js','fr':'fr.js','grc':'grc.js','gu':'gu.js','hi':'hi.js','hu':'hu.js','hy':'hy.js','it':'it.js','kn':'kn.js','la':'la.js','lt':'lt.js','lv':'lv.js','ml':'ml.js','no':'no-nb.js','no-nb':'no-nb.js','nl':'nl.js','or':'or.js','pa':'pa.js','pl':'pl.js','pt':'pt.js','ru':'ru.js','sl':'sl.js','sv':'sv.js','ta':'ta.js','te':'te.js','tr':'tr.js','uk':'uk.js'},languageHint=(function(){var k,r='';for(k in supportedLang){if(supportedLang.hasOwnProperty(k)){r+=k+', ';}}
r=r.substring(0,r.length-2);return r;}()),prompterStrings={'be':'Мова гэтага сайта не можа быць вызначаны аўтаматычна. Калі ласка пакажыце мову:','cs':'Jazyk této internetové stránky nebyl automaticky rozpoznán. Určete prosím její jazyk:','da':'Denne websides sprog kunne ikke bestemmes. Angiv venligst sprog:','de':'Die Sprache dieser Webseite konnte nicht automatisch bestimmt werden. Bitte Sprache angeben:','en':'The language of this website could not be determined automatically. Please indicate the main language:','es':'El idioma del sitio no pudo determinarse autom%E1ticamente. Por favor, indique el idioma principal:','fi':'Sivun kielt%E4 ei tunnistettu automaattisesti. M%E4%E4rit%E4 sivun p%E4%E4kieli:','fr':'La langue de ce site n%u2019a pas pu %EAtre d%E9termin%E9e automatiquement. Veuillez indiquer une langue, s.v.p.%A0:','hu':'A weboldal nyelvét nem sikerült automatikusan megállapítani. Kérem adja meg a nyelvet:','hy':'Չհաջողվեց հայտնաբերել այս կայքի լեզուն։ Խնդրում ենք նշեք հիմնական լեզուն՝','it':'Lingua del sito sconosciuta. Indicare una lingua, per favore:','kn':'ಜಾಲ ತಾಣದ ಭಾಷೆಯನ್ನು ನಿರ್ಧರಿಸಲು ಸಾಧ್ಯವಾಗುತ್ತಿಲ್ಲ. ದಯವಿಟ್ಟು ಮುಖ್ಯ ಭಾಷೆಯನ್ನು ಸೂಚಿಸಿ:','lt':'Nepavyko automatiškai nustatyti šios svetainės kalbos. Prašome įvesti kalbą:','lv':'Šīs lapas valodu nevarēja noteikt automātiski. Lūdzu norādiet pamata valodu:','ml':'ഈ വെ%u0D2C%u0D4D%u200Cസൈറ്റിന്റെ ഭാഷ കണ്ടുപിടിയ്ക്കാ%u0D28%u0D4D%u200D കഴിഞ്ഞില്ല. ഭാഷ ഏതാണെന്നു തിരഞ്ഞെടുക്കുക:','nl':'De taal van deze website kan niet automatisch worden bepaald. Geef de hoofdtaal op:','no':'Nettstedets språk kunne ikke finnes automatisk. Vennligst oppgi språk:','pt':'A língua deste site não pôde ser determinada automaticamente. Por favor indique a língua principal:','ru':'Язык этого сайта не может быть определен автоматически. Пожалуйста укажите язык:','sl':'Jezika te spletne strani ni bilo mogoče samodejno določiti. Prosim navedite jezik:','sv':'Spr%E5ket p%E5 den h%E4r webbplatsen kunde inte avg%F6ras automatiskt. V%E4nligen ange:','tr':'Bu web sitesinin dili otomatik olarak tespit edilememiştir. Lütfen dökümanın dilini seçiniz%A0:','uk':'Мова цього веб-сайту не може бути визначена автоматично. Будь ласка, вкажіть головну мову:'},basePath=(function(){var s=document.getElementsByTagName('script'),i=0,p,src,t;while(!!(t=s[i++])){if(!t.src){continue;}
src=t.src;p=src.indexOf('Hyphenator.js');if(p!==-1){return src.substring(0,p);}}
return'http://hyphenator.googlecode.com/svn/trunk/';}()),isLocal=(function(){var re=false;if(window.location.href.indexOf(basePath)!==-1){re=true;}
return re;}()),documentLoaded=false,documentCount=0,persistentConfig=false,contextWindow=window,doFrames=false,dontHyphenate={'script':true,'code':true,'pre':true,'img':true,'br':true,'samp':true,'kbd':true,'var':true,'abbr':true,'acronym':true,'sub':true,'sup':true,'button':true,'option':true,'label':true,'textarea':true,'input':true},enableCache=true,storageType='local',storage,enableReducedPatternSet=false,enableRemoteLoading=true,displayToggleBox=false,hyphenateClass='hyphenate',dontHyphenateClass='donthyphenate',min=6,orphanControl=1,isBookmarklet=(function(){var loc=null,re=false,jsArray=document.getElementsByTagName('script'),i,l;for(i=0,l=jsArray.length;i<l;i++){if(!!jsArray[i].getAttribute('src')){loc=jsArray[i].getAttribute('src');}
if(!loc){continue;}else if(loc.indexOf('Hyphenator.js?bm=true')!==-1){re=true;}}
return re;}()),mainLanguage=null,defaultLanguage='',elements=[],exceptions={},countObjProps=function(obj){var k,l=0;for(k in obj){if(obj.hasOwnProperty(k)){l++;}}
return l;},docLanguages={},state=0,url='(\\w*:\/\/)?((\\w*:)?(\\w*)@)?((([\\d]{1,3}\\.){3}([\\d]{1,3}))|((www\\.|[a-zA-Z]\\.)?[a-zA-Z0-9\\-\\.]+\\.([a-z]{2,4})))(:\\d*)?(\/[\\w#!:\\.?\\+=&%@!\\-]*)*',mail='[\\w-\\.]+@[\\w\\.]+',urlOrMailRE=new RegExp('('+url+')|('+mail+')','i'),zeroWidthSpace=(function(){var zws,ua=navigator.userAgent.toLowerCase();zws=String.fromCharCode(8203);if(ua.indexOf('msie 6')!==-1){zws='';}
if(ua.indexOf('opera')!==-1){zws='';}
return zws;}()),createElem=function(tagname,context){context=context||contextWindow;if(document.createElementNS){return context.document.createElementNS('http://www.w3.org/1999/xhtml',tagname);}else if(document.createElement){return context.document.createElement(tagname);}},onHyphenationDone=function(){},onError=function(e){window.alert("Hyphenator.js says:\n\nAn Error ocurred:\n"+e.message);},selectorFunction=function(){var tmp,el=[],i,l;if(document.getElementsByClassName){el=contextWindow.document.getElementsByClassName(hyphenateClass);}else{tmp=contextWindow.document.getElementsByTagName('*');l=tmp.length;for(i=0;i<l;i++)
{if(tmp[i].className.indexOf(hyphenateClass)!==-1&&tmp[i].className.indexOf(dontHyphenateClass)===-1){el.push(tmp[i]);}}}
return el;},intermediateState='hidden',hyphen=String.fromCharCode(173),urlhyphen=zeroWidthSpace,safeCopy=true,Expando=(function(){var container={},name="HyphenatorExpando_"+Math.random(),uuid=0;return{getDataForElem:function(elem){return container[elem[name].id];},setDataForElem:function(elem,data){var id;if(elem[name]&&elem[name].id!==''){id=elem[name].id;}else{id=uuid++;elem[name]={'id':id};}
container[id]=data;},appendDataForElem:function(elem,data){var k;for(k in data){if(data.hasOwnProperty(k)){container[elem[name].id][k]=data[k];}}},delDataOfElem:function(elem){delete container[elem[name]];}};}()),runOnContentLoaded=function(w,f){var DOMContentLoaded=function(){},toplevel,hyphRunForThis={};if(documentLoaded&&!hyphRunForThis[w.location.href]){f();hyphRunForThis[w.location.href]=true;return;}
function init(context){contextWindow=context||window;if(!hyphRunForThis[contextWindow.location.href]&&(!documentLoaded||contextWindow!=window.parent)){documentLoaded=true;f();hyphRunForThis[contextWindow.location.href]=true;}}
function doScrollCheck(){try{document.documentElement.doScroll("left");}catch(error){setTimeout(doScrollCheck,1);return;}
init(window);}
function doOnLoad(){var i,haveAccess,fl=window.frames.length;if(doFrames&&fl>0){for(i=0;i<fl;i++){haveAccess=undefined;try{haveAccess=window.frames[i].document.toString();}catch(e){haveAccess=undefined;}
if(!!haveAccess){init(window.frames[i]);}}
contextWindow=window;f();hyphRunForThis[window.location.href]=true;}else{init(window);}}
if(document.addEventListener){DOMContentLoaded=function(){document.removeEventListener("DOMContentLoaded",DOMContentLoaded,false);if(doFrames&&window.frames.length>0){return;}else{init(window);}};}else if(document.attachEvent){DOMContentLoaded=function(){if(document.readyState==="complete"){document.detachEvent("onreadystatechange",DOMContentLoaded);if(doFrames&&window.frames.length>0){return;}else{init(window);}}};}
if(document.addEventListener){document.addEventListener("DOMContentLoaded",DOMContentLoaded,false);window.addEventListener("load",doOnLoad,false);}else if(document.attachEvent){document.attachEvent("onreadystatechange",DOMContentLoaded);window.attachEvent("onload",doOnLoad);toplevel=false;try{toplevel=window.frameElement===null;}catch(e){}
if(document.documentElement.doScroll&&toplevel){doScrollCheck();}}},getLang=function(el,fallback){if(!!el.getAttribute('lang')){return el.getAttribute('lang').toLowerCase();}
try{if(!!el.getAttribute('xml:lang')){return el.getAttribute('xml:lang').toLowerCase();}}catch(ex){}
if(el.tagName!=='HTML'){return getLang(el.parentNode,true);}
if(fallback){return mainLanguage;}
return null;},autoSetMainLanguage=function(w){w=w||contextWindow;var el=w.document.getElementsByTagName('html')[0],m=w.document.getElementsByTagName('meta'),i,text,e,ul;mainLanguage=getLang(el,false);if(!mainLanguage){for(i=0;i<m.length;i++){if(!!m[i].getAttribute('http-equiv')&&(m[i].getAttribute('http-equiv').toLowerCase()==='content-language')){mainLanguage=m[i].getAttribute('content').toLowerCase();}
if(!!m[i].getAttribute('name')&&(m[i].getAttribute('name').toLowerCase()==='dc.language')){mainLanguage=m[i].getAttribute('content').toLowerCase();}
if(!!m[i].getAttribute('name')&&(m[i].getAttribute('name').toLowerCase()==='language')){mainLanguage=m[i].getAttribute('content').toLowerCase();}}}
if(!mainLanguage&&doFrames&&contextWindow!=window.parent){autoSetMainLanguage(window.parent);}
if(!mainLanguage&&defaultLanguage!==''){mainLanguage=defaultLanguage;}
if(!mainLanguage){text='';ul=navigator.language?navigator.language:navigator.userLanguage;ul=ul.substring(0,2);if(prompterStrings.hasOwnProperty(ul)){text=prompterStrings[ul];}else{text=prompterStrings.en;}
text+=' (ISO 639-1)\n\n'+languageHint;mainLanguage=window.prompt(unescape(text),ul).toLowerCase();}
if(!supportedLang.hasOwnProperty(mainLanguage)){if(supportedLang.hasOwnProperty(mainLanguage.split('-')[0])){mainLanguage=mainLanguage.split('-')[0];}else{e=new Error('The language "'+mainLanguage+'" is not yet supported.');throw e;}}},gatherDocumentInfos=function(){var elToProcess,tmp,i=0,process=function(el,hide,lang){var n,i=0,hyphenatorSettings={};if(hide&&intermediateState==='hidden'){if(!!el.getAttribute('style')){hyphenatorSettings.hasOwnStyle=true;}else{hyphenatorSettings.hasOwnStyle=false;}
hyphenatorSettings.isHidden=true;el.style.visibility='hidden';}
if(el.lang&&typeof(el.lang)==='string'){hyphenatorSettings.language=el.lang.toLowerCase();}else if(lang){hyphenatorSettings.language=lang.toLowerCase();}else{hyphenatorSettings.language=getLang(el,true);}
lang=hyphenatorSettings.language;if(supportedLang[lang]){docLanguages[lang]=true;}else{if(supportedLang.hasOwnProperty(lang.split('-')[0])){lang=lang.split('-')[0];hyphenatorSettings.language=lang;}else if(!isBookmarklet){onError(new Error('Language '+lang+' is not yet supported.'));}}
Expando.setDataForElem(el,hyphenatorSettings);elements.push(el);while(!!(n=el.childNodes[i++])){if(n.nodeType===1&&!dontHyphenate[n.nodeName.toLowerCase()]&&n.className.indexOf(dontHyphenateClass)===-1&&!(n in elToProcess)){process(n,false,lang);}}};if(isBookmarklet){elToProcess=contextWindow.document.getElementsByTagName('body')[0];process(elToProcess,false,mainLanguage);}else{elToProcess=selectorFunction();while(!!(tmp=elToProcess[i++]))
{process(tmp,true,'');}}
if(!Hyphenator.languages.hasOwnProperty(mainLanguage)){docLanguages[mainLanguage]=true;}else if(!Hyphenator.languages[mainLanguage].prepared){docLanguages[mainLanguage]=true;}
if(elements.length>0){Expando.appendDataForElem(elements[elements.length-1],{isLast:true});}},convertPatterns=function(lang){var plen,anfang,ende,pats,pat,key,tmp={};pats=Hyphenator.languages[lang].patterns;for(plen in pats){if(pats.hasOwnProperty(plen)){plen=parseInt(plen,10);anfang=0;ende=plen;while(!!(pat=pats[plen].substring(anfang,ende))){key=pat.replace(/\d/g,'');tmp[key]=pat;anfang=ende;ende+=plen;}}}
Hyphenator.languages[lang].patterns=tmp;Hyphenator.languages[lang].patternsConverted=true;},convertExceptionsToObject=function(exc){var w=exc.split(', '),r={},i,l,key;for(i=0,l=w.length;i<l;i++){key=w[i].replace(/-/g,'');if(!r.hasOwnProperty(key)){r[key]=w[i];}}
return r;},loadPatterns=function(lang){var url,xhr,head,script;if(supportedLang[lang]&&!Hyphenator.languages[lang]){url=basePath+'patterns/'+supportedLang[lang];}else{return;}
if(isLocal&&!isBookmarklet){xhr=null;if(typeof XMLHttpRequest!=='undefined'){xhr=new XMLHttpRequest();}
if(!xhr){try{xhr=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){xhr=null;}}
if(xhr){xhr.open('HEAD',url,false);xhr.setRequestHeader('Cache-Control','no-cache');xhr.send(null);if(xhr.status===404){onError(new Error('Could not load\n'+url));delete docLanguages[lang];return;}}}
if(createElem){head=window.document.getElementsByTagName('head').item(0);script=createElem('script',window);script.src=url;script.type='text/javascript';head.appendChild(script);}},prepareLanguagesObj=function(lang){var lo=Hyphenator.languages[lang],wrd;if(!lo.prepared){if(enableCache){lo.cache={};lo['cache']=lo.cache;}
if(enableReducedPatternSet){lo.redPatSet={};}
if(lo.hasOwnProperty('exceptions')){Hyphenator.addExceptions(lang,lo.exceptions);delete lo.exceptions;}
if(exceptions.hasOwnProperty('global')){if(exceptions.hasOwnProperty(lang)){exceptions[lang]+=', '+exceptions.global;}else{exceptions[lang]=exceptions.global;}}
if(exceptions.hasOwnProperty(lang)){lo.exceptions=convertExceptionsToObject(exceptions[lang]);delete exceptions[lang];}else{lo.exceptions={};}
convertPatterns(lang);wrd='[\\w'+lo.specialChars+'@'+String.fromCharCode(173)+String.fromCharCode(8204)+'-]{'+min+',}';lo.genRegExp=new RegExp('('+url+')|('+mail+')|('+wrd+')','gi');lo.prepared=true;}
if(!!storage){try{storage.setItem('Hyphenator_'+lang,window.JSON.stringify(lo));}catch(e){}}},prepare=function(callback){var lang,interval,tmp1,tmp2;if(!enableRemoteLoading){for(lang in Hyphenator.languages){if(Hyphenator.languages.hasOwnProperty(lang)){prepareLanguagesObj(lang);}}
state=2;callback();return;}
state=1;for(lang in docLanguages){if(docLanguages.hasOwnProperty(lang)){if(!!storage&&storage.getItem('Hyphenator_'+lang)){Hyphenator.languages[lang]=window.JSON.parse(storage.getItem('Hyphenator_'+lang));if(exceptions.hasOwnProperty('global')){tmp1=convertExceptionsToObject(exceptions.global);for(tmp2 in tmp1){if(tmp1.hasOwnProperty(tmp2)){Hyphenator.languages[lang].exceptions[tmp2]=tmp1[tmp2];}}}
if(exceptions.hasOwnProperty(lang)){tmp1=convertExceptionsToObject(exceptions[lang]);for(tmp2 in tmp1){if(tmp1.hasOwnProperty(tmp2)){Hyphenator.languages[lang].exceptions[tmp2]=tmp1[tmp2];}}
delete exceptions[lang];}
tmp1='[\\w'+Hyphenator.languages[lang].specialChars+'@'+String.fromCharCode(173)+String.fromCharCode(8204)+'-]{'+min+',}';Hyphenator.languages[lang].genRegExp=new RegExp('('+url+')|('+mail+')|('+tmp1+')','gi');delete docLanguages[lang];continue;}else{loadPatterns(lang);}}}
if(countObjProps(docLanguages)===0){state=2;callback();return;}
interval=window.setInterval(function(){var finishedLoading=true,lang;for(lang in docLanguages){if(docLanguages.hasOwnProperty(lang)){finishedLoading=false;if(!!Hyphenator.languages[lang]){delete docLanguages[lang];prepareLanguagesObj(lang);}}}
if(finishedLoading){window.clearInterval(interval);state=2;callback();}},100);},toggleBox=function(){var myBox,bdy,myIdAttribute,myTextNode,myClassAttribute,text=(Hyphenator.doHyphenation?'Hy-phen-a-tion':'Hyphenation');if(!!(myBox=contextWindow.document.getElementById('HyphenatorToggleBox'))){myBox.firstChild.data=text;}else{bdy=contextWindow.document.getElementsByTagName('body')[0];myBox=createElem('div',contextWindow);myIdAttribute=contextWindow.document.createAttribute('id');myIdAttribute.nodeValue='HyphenatorToggleBox';myClassAttribute=contextWindow.document.createAttribute('class');myClassAttribute.nodeValue=dontHyphenateClass;myTextNode=contextWindow.document.createTextNode(text);myBox.appendChild(myTextNode);myBox.setAttributeNode(myIdAttribute);myBox.setAttributeNode(myClassAttribute);myBox.onclick=Hyphenator.toggleHyphenation;myBox.style.position='absolute';myBox.style.top='0px';myBox.style.right='0px';myBox.style.margin='0';myBox.style.backgroundColor='#AAAAAA';myBox.style.color='#FFFFFF';myBox.style.font='6pt Arial';myBox.style.letterSpacing='0.2em';myBox.style.padding='3px';myBox.style.cursor='pointer';myBox.style.WebkitBorderBottomLeftRadius='4px';myBox.style.MozBorderRadiusBottomleft='4px';bdy.appendChild(myBox);}},hyphenateWord=function(lang,word){var lo=Hyphenator.languages[lang],parts,i,l,w,wl,s,hypos,p,maxwins,win,pat=false,patk,c,t,n,numb3rs,inserted,hyphenatedword,val,subst,ZWNJpos=[];if(word===''){return'';}
if(word.indexOf(hyphen)!==-1){return word;}
if(enableCache&&lo.cache.hasOwnProperty(word)){return lo.cache[word];}
if(lo.exceptions.hasOwnProperty(word)){return lo.exceptions[word].replace(/-/g,hyphen);}
if(word.indexOf('-')!==-1){parts=word.split('-');for(i=0,l=parts.length;i<l;i++){parts[i]=hyphenateWord(lang,parts[i]);}
return parts.join('-');}
w='_'+word+'_';if(word.indexOf(String.fromCharCode(8204))!==-1){parts=w.split(String.fromCharCode(8204));w=parts.join('');for(i=0,l=parts.length;i<l;i++){parts[i]=parts[i].length.toString();}
parts.pop();ZWNJpos=parts;}
wl=w.length;s=w.split('');if(!!lo.charSubstitution){for(subst in lo.charSubstitution){if(lo.charSubstitution.hasOwnProperty(subst)){w=w.replace(new RegExp(subst,'g'),lo.charSubstitution[subst]);}}}
if(word.indexOf("'")!==-1){w=w.toLowerCase().replace("'","’");}else{w=w.toLowerCase();}
hypos=[];numb3rs={'0':0,'1':1,'2':2,'3':3,'4':4,'5':5,'6':6,'7':7,'8':8,'9':9};n=wl-lo.shortestPattern;for(p=0;p<=n;p++){maxwins=Math.min((wl-p),lo.longestPattern);for(win=lo.shortestPattern;win<=maxwins;win++){if(lo.patterns.hasOwnProperty(patk=w.substring(p,p+win))){pat=lo.patterns[patk];if(enableReducedPatternSet&&(typeof pat==='string')){lo.redPatSet[patk]=pat;}
if(typeof pat==='string'){t=0;val=[];for(i=0;i<pat.length;i++){if(!!(c=numb3rs[pat.charAt(i)])){val.push(i-t,c);t++;}}
pat=lo.patterns[patk]=val;}}else{continue;}
for(i=0;i<pat.length;i++){c=p-1+pat[i];if(!hypos[c]||hypos[c]<pat[i+1]){hypos[c]=pat[i+1];}
i++;}}}
inserted=0;for(i=lo.leftmin;i<=(wl-2-lo.rightmin);i++){if(ZWNJpos.length>0&&ZWNJpos[0]===i){ZWNJpos.shift();s.splice(i+inserted-1,0,String.fromCharCode(8204));inserted++;}
if(!!(hypos[i]&1)){s.splice(i+inserted+1,0,hyphen);inserted++;}}
hyphenatedword=s.slice(1,-1).join('');if(enableCache){lo.cache[word]=hyphenatedword;}
return hyphenatedword;},hyphenateURL=function(url){return url.replace(/([:\/\.\?#&_,;!@]+)/gi,'$&'+urlhyphen);},removeHyphenationFromElement=function(el){var h,i=0,n;switch(hyphen){case'|':h='\\|';break;case'+':h='\\+';break;case'*':h='\\*';break;default:h=hyphen;}
while(!!(n=el.childNodes[i++])){if(n.nodeType===3){n.data=n.data.replace(new RegExp(h,'g'),'');n.data=n.data.replace(new RegExp(zeroWidthSpace,'g'),'');}else if(n.nodeType===1){removeHyphenationFromElement(n);}}},registerOnCopy=function(el){var body=el.ownerDocument.getElementsByTagName('body')[0],shadow,selection,range,rangeShadow,restore,oncopyHandler=function(e){e=e||window.event;var target=e.target||e.srcElement,currDoc=target.ownerDocument,body=currDoc.getElementsByTagName('body')[0],targetWindow='defaultView'in currDoc?currDoc.defaultView:currDoc.parentWindow;if(target.tagName&&dontHyphenate[target.tagName.toLowerCase()]){return;}
shadow=currDoc.createElement('div');shadow.style.overflow='hidden';shadow.style.position='absolute';shadow.style.top='-5000px';shadow.style.height='1px';body.appendChild(shadow);if(!!window.getSelection){selection=targetWindow.getSelection();range=selection.getRangeAt(0);shadow.appendChild(range.cloneContents());removeHyphenationFromElement(shadow);selection.selectAllChildren(shadow);restore=function(){shadow.parentNode.removeChild(shadow);selection.addRange(range);};}else{selection=targetWindow.document.selection;range=selection.createRange();shadow.innerHTML=range.htmlText;removeHyphenationFromElement(shadow);rangeShadow=body.createTextRange();rangeShadow.moveToElementText(shadow);rangeShadow.select();restore=function(){shadow.parentNode.removeChild(shadow);if(range.text!==""){range.select();}};}
window.setTimeout(restore,0);};if(!body){return;}
el=el||body;if(window.addEventListener){el.addEventListener("copy",oncopyHandler,false);}else{el.attachEvent("oncopy",oncopyHandler);}},hyphenateElement=function(el){var hyphenatorSettings=Expando.getDataForElem(el),lang=hyphenatorSettings.language,hyphenate,n,i,controlOrphans=function(part){var h,r;switch(hyphen){case'|':h='\\|';break;case'+':h='\\+';break;case'*':h='\\*';break;default:h=hyphen;}
if(orphanControl>=2){r=part.split(' ');r[1]=r[1].replace(new RegExp(h,'g'),'');r[1]=r[1].replace(new RegExp(zeroWidthSpace,'g'),'');r=r.join(' ');}
if(orphanControl===3){r=r.replace(/[ ]+/g,String.fromCharCode(160));}
return r;};if(Hyphenator.languages.hasOwnProperty(lang)){hyphenate=function(word){if(!Hyphenator.doHyphenation){return word;}else if(urlOrMailRE.test(word)){return hyphenateURL(word);}else{return hyphenateWord(lang,word);}};if(safeCopy&&(el.tagName.toLowerCase()!=='body')){registerOnCopy(el);}
i=0;while(!!(n=el.childNodes[i++])){if(n.nodeType===3&&n.data.length>=min){n.data=n.data.replace(Hyphenator.languages[lang].genRegExp,hyphenate);if(orphanControl!==1){n.data=n.data.replace(/[\S]+ [\S]+$/,controlOrphans);}}}}
if(hyphenatorSettings.isHidden&&intermediateState==='hidden'){el.style.visibility='visible';if(!hyphenatorSettings.hasOwnStyle){el.setAttribute('style','');el.removeAttribute('style');}else{if(el.style.removeProperty){el.style.removeProperty('visibility');}else if(el.style.removeAttribute){el.style.removeAttribute('visibility');}}}
if(hyphenatorSettings.isLast){state=3;documentCount--;if(documentCount>(-1000)&&documentCount<=0){documentCount=(-2000);onHyphenationDone();}}},hyphenateDocument=function(){function bind(fun,arg){return function(){return fun(arg);};}
var i=0,el;while(!!(el=elements[i++])){if(el.ownerDocument.location.href===contextWindow.location.href){window.setTimeout(bind(hyphenateElement,el),0);}}},removeHyphenationFromDocument=function(){var i=0,el;while(!!(el=elements[i++])){removeHyphenationFromElement(el);}
state=4;},createStorage=function(){try{if(storageType!=='none'&&typeof(window.localStorage)!=='undefined'&&typeof(window.sessionStorage)!=='undefined'&&typeof(window.JSON.stringify)!=='undefined'&&typeof(window.JSON.parse)!=='undefined'){switch(storageType){case'session':storage=window.sessionStorage;break;case'local':storage=window.localStorage;break;default:storage=undefined;break;}}}catch(f){}},storeConfiguration=function(){if(!storage){return;}
var settings={'STORED':true,'classname':hyphenateClass,'donthyphenateclassname':dontHyphenateClass,'minwordlength':min,'hyphenchar':hyphen,'urlhyphenchar':urlhyphen,'togglebox':toggleBox,'displaytogglebox':displayToggleBox,'remoteloading':enableRemoteLoading,'enablecache':enableCache,'onhyphenationdonecallback':onHyphenationDone,'onerrorhandler':onError,'intermediatestate':intermediateState,'selectorfunction':selectorFunction,'safecopy':safeCopy,'doframes':doFrames,'storagetype':storageType,'orphancontrol':orphanControl,'dohyphenation':Hyphenator.doHyphenation,'persistentconfig':persistentConfig,'defaultlanguage':defaultLanguage};storage.setItem('Hyphenator_config',window.JSON.stringify(settings));},restoreConfiguration=function(){var settings;if(storage.getItem('Hyphenator_config')){settings=window.JSON.parse(storage.getItem('Hyphenator_config'));Hyphenator.config(settings);}};return{version:'3.3.0',doHyphenation:true,languages:{},config:function(obj){var assert=function(name,type){if(typeof obj[name]===type){return true;}else{onError(new Error('Config onError: '+name+' must be of type '+type));return false;}},key;if(obj.hasOwnProperty('storagetype')){if(assert('storagetype','string')){storageType=obj.storagetype;}
if(!storage){createStorage();}}
if(!obj.hasOwnProperty('STORED')&&storage&&obj.hasOwnProperty('persistentconfig')&&obj.persistentconfig===true){restoreConfiguration();}
for(key in obj){if(obj.hasOwnProperty(key)){switch(key){case'STORED':break;case'classname':if(assert('classname','string')){hyphenateClass=obj[key];}
break;case'donthyphenateclassname':if(assert('donthyphenateclassname','string')){dontHyphenateClass=obj[key];}
break;case'minwordlength':if(assert('minwordlength','number')){min=obj[key];}
break;case'hyphenchar':if(assert('hyphenchar','string')){if(obj.hyphenchar==='&shy;'){obj.hyphenchar=String.fromCharCode(173);}
hyphen=obj[key];}
break;case'urlhyphenchar':if(obj.hasOwnProperty('urlhyphenchar')){if(assert('urlhyphenchar','string')){urlhyphen=obj[key];}}
break;case'togglebox':if(assert('togglebox','function')){toggleBox=obj[key];}
break;case'displaytogglebox':if(assert('displaytogglebox','boolean')){displayToggleBox=obj[key];}
break;case'remoteloading':if(assert('remoteloading','boolean')){enableRemoteLoading=obj[key];}
break;case'enablecache':if(assert('enablecache','boolean')){enableCache=obj[key];}
break;case'enablereducedpatternset':if(assert('enablereducedpatternset','boolean')){enableReducedPatternSet=obj[key];}
break;case'onhyphenationdonecallback':if(assert('onhyphenationdonecallback','function')){onHyphenationDone=obj[key];}
break;case'onerrorhandler':if(assert('onerrorhandler','function')){onError=obj[key];}
break;case'intermediatestate':if(assert('intermediatestate','string')){intermediateState=obj[key];}
break;case'selectorfunction':if(assert('selectorfunction','function')){selectorFunction=obj[key];}
break;case'safecopy':if(assert('safecopy','boolean')){safeCopy=obj[key];}
break;case'doframes':if(assert('doframes','boolean')){doFrames=obj[key];}
break;case'storagetype':if(assert('storagetype','string')){storageType=obj[key];}
break;case'orphancontrol':if(assert('orphancontrol','number')){orphanControl=obj[key];}
break;case'dohyphenation':if(assert('dohyphenation','boolean')){Hyphenator.doHyphenation=obj[key];}
break;case'persistentconfig':if(assert('persistentconfig','boolean')){persistentConfig=obj[key];}
break;case'defaultlanguage':if(assert('defaultlanguage','string')){defaultLanguage=obj[key];}
break;default:onError(new Error('Hyphenator.config: property '+key+' not known.'));}}}
if(storage&&persistentConfig){storeConfiguration();}},run:function(){documentCount=0;var process=function(){try{if(contextWindow.document.getElementsByTagName('frameset').length>0){return;}
documentCount++;autoSetMainLanguage(undefined);gatherDocumentInfos();prepare(hyphenateDocument);if(displayToggleBox){toggleBox();}}catch(e){onError(e);}},i,haveAccess,fl=window.frames.length;if(!storage){createStorage();}
if(!documentLoaded&&!isBookmarklet){runOnContentLoaded(window,process);}
if(isBookmarklet||documentLoaded){if(doFrames&&fl>0){for(i=0;i<fl;i++){haveAccess=undefined;try{haveAccess=window.frames[i].document.toString();}catch(e){haveAccess=undefined;}
if(!!haveAccess){contextWindow=window.frames[i];process();}}}
contextWindow=window;process();}},addExceptions:function(lang,words){if(lang===''){lang='global';}
if(exceptions.hasOwnProperty(lang)){exceptions[lang]+=", "+words;}else{exceptions[lang]=words;}},hyphenate:function(target,lang){var hyphenate,n,i;if(Hyphenator.languages.hasOwnProperty(lang)){if(!Hyphenator.languages[lang].prepared){prepareLanguagesObj(lang);}
hyphenate=function(word){if(urlOrMailRE.test(word)){return hyphenateURL(word);}else{return hyphenateWord(lang,word);}};if(typeof target==='string'||target.constructor===String){return target.replace(Hyphenator.languages[lang].genRegExp,hyphenate);}else if(typeof target==='object'){i=0;while(!!(n=target.childNodes[i++])){if(n.nodeType===3&&n.data.length>=min){n.data=n.data.replace(Hyphenator.languages[lang].genRegExp,hyphenate);}else if(n.nodeType===1){if(n.lang!==''){Hyphenator.hyphenate(n,n.lang);}else{Hyphenator.hyphenate(n,lang);}}}}}else{onError(new Error('Language "'+lang+'" is not loaded.'));}},getRedPatternSet:function(lang){return Hyphenator.languages[lang].redPatSet;},isBookmarklet:function(){return isBookmarklet;},getConfigFromURI:function(){var loc=null,re={},jsArray=document.getElementsByTagName('script'),i,j,l,s,gp,option;for(i=0,l=jsArray.length;i<l;i++){if(!!jsArray[i].getAttribute('src')){loc=jsArray[i].getAttribute('src');}
if(!loc){continue;}else{s=loc.indexOf('Hyphenator.js?');if(s===-1){continue;}
gp=loc.substring(s+14).split('&');for(j=0;j<gp.length;j++){option=gp[j].split('=');if(option[0]==='bm'){continue;}
if(option[1]==='true'){re[option[0]]=true;continue;}
if(option[1]==='false'){re[option[0]]=false;continue;}
if(isFinite(option[1])){re[option[0]]=parseInt(option[1],10);continue;}
if(option[0]==='onhyphenationdonecallback'){re[option[0]]=new Function('',option[1]);continue;}
re[option[0]]=option[1];}
break;}}
return re;},toggleHyphenation:function(){if(Hyphenator.doHyphenation){removeHyphenationFromDocument();Hyphenator.doHyphenation=false;storeConfiguration();toggleBox();}else{hyphenateDocument();Hyphenator.doHyphenation=true;storeConfiguration();toggleBox();}}};}(window));Hyphenator['languages']=Hyphenator.languages;Hyphenator['config']=Hyphenator.config;Hyphenator['run']=Hyphenator.run;Hyphenator['addExceptions']=Hyphenator.addExceptions;Hyphenator['hyphenate']=Hyphenator.hyphenate;Hyphenator['getRedPatternSet']=Hyphenator.getRedPatternSet;Hyphenator['isBookmarklet']=Hyphenator.isBookmarklet;Hyphenator['getConfigFromURI']=Hyphenator.getConfigFromURI;Hyphenator['toggleHyphenation']=Hyphenator.toggleHyphenation;window['Hyphenator']=Hyphenator;if(Hyphenator.isBookmarklet()){Hyphenator.config({displaytogglebox:true,intermediatestate:'visible',doframes:true});Hyphenator.config(Hyphenator.getConfigFromURI());Hyphenator.run();}
Hyphenator.languages['cs']={leftmin:2,rightmin:2,shortestPattern:1,longestPattern:6,specialChars:"ěščřžýáíéúůťď",patterns:{2:"a11f1g1k1n1pu11vy11zé11ňó11š1ť1ú1ž",3:"_a2_b2_c2_d2_e2_g2_h2_i2_j2_k2_l2_m2_o2_p2_r2_s2_t2_u2_v2_z2_č2_é2_í2_ó2_š2_ú2_ž22a_a2da2ga2ia2ka2ra2sa2ta2u2av2aya2ča2ňa2ť2b_b1db1h1bib1j2bkb1m2bn1bob2z1bá1bí2bň2c_1ca2cc1ce1ci2cl2cn1coc2p2ctcy21cá1cí2cň1ců2d_1dad1bd1d1de1did1j2dkd1m2dn1dod1t1dud2v1dy1dá1dé1dě1dí2dň1dů1dý2e_e1ae1be1ee1ie2ke1o2ere1se1te1ue1áe2ňe1ře2šeú12f_f2l2fn2fr2fs2ft2féf2ú2g_2gngo12h_h2bh2c2hd2hkh2mh2rh1č2hňhř2h2ž2i_i1ai1bi1di1hi1ji1li1mi2ni1oi1ri1ti1xi1ái2ďi1éi1ói1ři2ši2ž2j_j2d1jij1j2jkj2m2jn2jp2jz2jď1jí2jž2k_k2dk2e2kf2kkk2l2kn2ks2kčk2ň2l_2lf2lg2lh1li2lj2lk2ll2ln2lp2lv2lz2lň1lů1lý2m_1ma1me2mf1mim2l2mn1mo2mp1mu2mv2mz2mčm2ž2n_2nb2nf2ngn1j2nk2nn2nz2nď2nónů22nž2o_o1ao1cog2o1ho1io1jo1lo1mo2no1oo1to2uo1xo2zo1čo2ňo1ř2p_2pkp2l2pn2pp2ptpá12pč2pš2pťqu22r_r1br1cr1d2rkr1l2rn2rrr1x2rzr1č2ró2rš2s_s2cs2d1se2sf1sis2js2k2sn1sos2p1sr2ss1sus2v1sé1sí2sň2sť1sůs2ž2t_1te2tf2tg1ti2tl2tm2tn1to2tpt2vt2č1té1tě2tř2tš1tů2u_u2b2ufu2ku2mu2nu2pu2ru2su2vu2zu2ču2ďu2ňu2šu2ž2v_2vkv2l2vm2vnv2p2vňwe22x_2xf2xnx1ty2ay2ey2sy2ňy2šyž22z_2zbz2ez2j2zl2ztz2v2zzzá12zč2zňz2řá1bá1dá1já1sá2ňá1řá2š2č_1ča2čb1če1či2čk2čn1čoč2p2čs1ču1čá1čí1čů2ď_1ďa1ďoé2dé2fé2lé2mé2sé2té2šé2žě1cě1lě2vě2zě1řě2šě2ťě2ží1bí1hí1jí1lí1rí1tí2ňí1ří2š2ň_2ňa2ňk2ňmň1só2z2ř_2řc2řdři12řk2řn1řoř2v2řz2řš2š_2šl2šnš2p2štš2vš2ň2ť_2ťk2ťm2ťtú2dú2kú2lú2nú2pú2tú2vú2zú2čú2žů1bů1cůt2ů2vů2zů2žý1bý1dý1hý1jý1lý2ný1rý1tý1uý1ř2ž_2žk2žl2žnž2v2žď2žň2žš",4:"_ch2_ná1_st2_us2_ut2_vy3_vý1_za3_zd2a3daa3dea3dia3doa3dua3dya3dáa3déa3děa3día3důa3dýa3gaa3goa3gua3gáah3va3ina3iva2jda2jmaj2oa3kea3kia3kla3koa3kra3kua3kya3káa3kéa3kóa3kůa3kýap3ta3raa3rea3ria3roa3rua3rya3ráa3róa3růa3rýa3saa3sea3sha3soa3sua3sva3sya3sáa3séa3sía3sůa3taa3tea3tia3toa3tra3tua3tva3tya3táa3téa3těa3tía3tóat1řa3tůa3týa3uja3učav3dav3taz3ka3zpa3čaa3čea3čia3čla3čoa3čua3čáa3čía3čůa3ňoa3ňua3říaú3t3ba_2b1cbe3pbis33bl_3blk2brib2ru2b1tbu2c3by_bys32b1č1bě_3bínb3řab1ří2bš2ce2u2ch_1cha3che2chl2cht1chu1chy1chá2chř2ck2c3lac3léc2tict2nc3tvc2těcuk11c2vda3dd2bad2bá2d1cde1xde2z2d1hd3kv3dl_d1lad3li1dlnd2lud1léd2lů1dmddo1ddo3hdo3pdo1sdo3tdo3čd1red3réd3rýd3tld3třdu3p2durd3ved3vld3vrd3vyd3vád3věd3víd3zbd3zdd3zn2d1č3dějd1řad1ří2dš2d3škd3št3dů_dů3sd2ž2e2are2břed1led3ve1hae1hee1hoe1hre1hue1hye1háe1hýe1jeej1mej1oej1uej3ve3kae3kee3koe3kre3kue3kye3káe3kée3kóe3kře3kůe1lae1lee1loe1lue1lye1láe1lée1líe1mle1mre1mye3máe1měe1míe3mře3můe1mýeo1seo3ze2plepy3e1rae1ree1rie1roer3se1rue1rye1ráe1rée1růe1rýe2ske2sles2me2stet1řeu3beu3deu3keu3meu3neu3peu3reu3teu3veu3zeu3že3vdevy3e3xue3zeez2te3zíe3zře1čte3ňoe3ňue3ňáe3óne3říe3šee3šie3šle3šoe3šíeú3neú3peú3teú3čf3líf1rige2s3gic3gin2g1mgu3mgu3vhe2she2uhe3x2hli2hlý2h2nh3ne2h1th2tě2h2vhyd1hys3ia3dib2li1chid2li1emi1eni1etif1ri2hlih3ni3imi2klik3milu3i3nai3nei3nii3noi3nui3nyi3nái3néi3něi3níi3nůi3nýi2psi1sais3cis1ti1syi3sáit1ri2tvi1umiv3di3zpiz1ri1člič3ti1íci1ími3šei3šiiš3ki3šoi3šui3šái3šíi3žai3žei3žii3žoi3žui3žája3dja3gj1b22j1cj3drj3dáj3důj3efj3ex2j1hj3kv2j1lj3maj3mi2jmíjne3j1obj1odj1ohj1opj1osj2ov2j1rj3sn2j1tj3tlju3pj1usju3tju3vju3zj1už2jv2j3vdj3vnj3zbj3zdj3zkj3znj3zp2j1čj3štj3šť2jú1jú3njú3čjú3ž3kaj3kat3kav3kač3kař2k1c3ket3kl_k3lék3lók3lý2k2mk3mě3kof3kovkr2s2k1tkt2r3kujku3v2k2v3kyn3kác3kár3kářk2ř23ků_1la_2l1b2l1c2l1dle2i1lej1lel3lio2ližl2kl2l1m1loslo3zl2pěls3n2l1t1lá_2l1č1lé_1lík1líř2lš2l3štlý2t2l2ž2m1b2m1cm2dl3me_me3x2mk22mleml3h2mlim3nam3nám3ném3nýmo2kmo2smoú3m2psmp2tmr2s2m1tmu3n2muš3má_má2sm2čemí1c2m2šmš3ť3mů_3mý_3na_na3hnat2na3zna3š2n1c2n1dne1dne1hne2jne3pne3zn3frng1l3nio2n1lno3z2nožn2sa2n1t2nub3ny_3nák2n1č2nív2níž2nš2n3što1bao1beob1lob1ro1buob3zo3béocy3od3bod1lod3vod1řo1e2oe3go2flo3gnoj2o2okaom2no3nao3neo3nio3noo3nuo3nyo3náo3něo3nío3nůo3nýo2pso1rao1reo1rio1roo1ruo1ryo1ráo3réo1růo3rýo1sao1sko1slo1syo3tío3třou3mou3vo3zaoz1bo3zeoz1ho3zioz3joz3koz1loz3mo3zooz3poz3to3zuo3zío3zůoč2ko3ňao3ňoo3ško3šlo3žl2p1c3pečp2kl3pl_pl3hp2nu3podpo3hpo3ppoč2pr2cpro1pr2sprů3p3tupá2c2př_při31ra_2rakr2blrca3r1harh3nr1hor3hur1há1ricr2kl2r1mro3h2r1sr2st2r1tr2thrtu31ru_1ry_ryd2rz3drz3l1rák1rářrč3t3ré_3rý_s2b2s3casch2s3cis3císe3h3sel3semset2se3zs3fo3sfés3fú3sic3sif3sik3sits3jus3ků3sl_3slns2lys1lís2mas2mos2nas2nes2ná2st_2stns2tvs2tás1tísy3csá2d3sáh2s2čs3čis3ťo1ta_1tajt1ao2t1b2t1c3te_2tihtiú32tiž2tk2t2klt2ká3tl_t1le3tlmtlu3t1lyt1lét2mat3níto3b2toj2trč2trý2t1sts2t2t1t1tu_1tuj2tup2tve1ty_3tá_t3či2tčí3tém2těh2těp1tíc1tím2tín2tírt1řut2řát3št1tý_1tým1týř3týšu2atu3bau3beu3biu3bou3buu3báu3bů2u2du3deu3diu3dou3duu3dyu3díu2hlu2inu2jmu3keu3kou3kuu3kyu3kůul1hu3mau3meu3miu3muu3má3umřu3neu3nou3nuu3něu3níu3nůu3pau3peu3piu3puu3pyu3páu3pěu3píu3půu3rau3reu3riu3ruu3rá1urču3růus1lu3sou3syu3sáu3síu3sůu3viu3vuu3zeu3ziuz1lu3zou3zuu3zíu3čau3čeu3čiu3čouč3tu3čuu3čáu3číu3šeu3šiu3šou3šuu3šáu3šíu3žeu3žou3žuu3žáu3ží2v1b2v1cv2ch2v2dv3di3venve2pv2kr2vlovo3bvo2svou3vr2cv1ro2vs2v1sk2v2tvy3cvyp2vy3tvy3čvyš2v2z22v2čv3čáv3čí3vín2vřív2š23výsvý3tv2ž23war3xovy2bly2chy2dry2gry3hny2kly3niy2přyr2vy3say3sey3siy3smy3soy3spys2ty3suy3svy3syy3sáy3séy3síyu3žy3vsy3zby3zdy3zky3zny3zpyč2kyř3by3říy3šey3šiy3škyš1ly3šoy3špy3šuy3šíy3ždza3hza3iza3jza3kzat2za3zza3šz2by2z1c2z2dz3dize3hzet2zev2ze3z2z2fz1ház3jí2z2kz3kyz3kéz3kůz3ký3zl_z2m22zmez3mnz3my2z2nz3noz3nuz3nyz3néz3něz3níz3ný2z2pz3ptz3tř3zu_zu3šz3vi3zy_záh23zápzá3zzáš2z3čl2zš2z3škz3štzú3čzú3žzů3sá2blá2dlád1řá1haá3heáh1láh3ná1hoá1hrá1háá1laá1leá1loá1luá1lyá3léá1líá3myá3méá1měá3míá3mýá1raá1reár2má1roá1ruá3růá2scá2smá2stát3kát1rá1tuá1tyá1tíá3týáz3ká3šeá3ší2č1c3če_če1cč3koč3kuč3ky2č1mč2neč1sk2č2t3čtvč3tí2ď1t3ďujé3dié3doé3foéf1ré2klé3maé3meé3mié3moé3mué3můé3taé3toé3táěd3rě3haě3heěh3ně1hoě3huě3hůě3jaě1jeě1joě3jůě1raě1reě1roěr3sě1ruě1ryě1růěs3kěs3nět1lě1trět3vě1tíě3vaě3veě3vlě3voě3vuě3váěv3čě3zeě3ziěz3ně3zoě3zíě3šeě3šiě3šoě3šuě3šáě3šíěš3ťě3ťoě3žeě3žiě3žoě3žuě3žííb3říd1lí2hlíh3ní2krí1máí3méí1měí1saít3kíz3kí3šeí3šií3šoí3šíňa3d3ňov2ň1tó3zaó3zió3zoó3zy2ř2bře1h2řesřia3ři3hřis2ři3zři3řř2kl2ř1l2ř1m2řou2ř2p2ř1s2ř1t2ř1č2řídří1sř3štšab32š1c2š2kš3kaš3ke3škrš3kyš2laš2liš2lošlá2š2léš2lý2š1m2š1sší3dš3ší2š2ťš3ťoš3ťuš3ťá3ťalú2c2úz3k3účeů1hlů3jdů1leů1myů1měů1raů1s2ů2stů3vaů3voů3věů3zoů3žeů3žiů3žoý1mlý1měý3noý1s2ý2ský3zký3znýš3lža3d3žač2ž1b2ž1c2ž1d3žil3žlo2ž1mžon22ž1t",5:"_a4da_a4de_a4di_a4do_a4dé_a4kl_a4ko_a4kr_a4ku_a4ra_a4re_a4ri_a4ro_a4ry_a4rá_a4sa_a4se_a4so_a4sy_a4ta_a4te_at3l_a4to_a4tr_a4ty_a4ve_cyk3_dez3_d4na_dne4_d4ny_dos4_d4ve_d4vě_d4ví_e4ch_e4ko_es3k_es3t_e4ve_f4ri_h4le_h4ne_i4na_i4ni_i4no_is3l_j4ak_j4se_j4zd_jád4_k4li_k4ly_ne3c_neč4_ne3š_ni2t_n4vp_o4bé_ode3_od3l_o4ka_o4ko_o4na_o4ne_o4ni_o4no_o4nu_o4ny_o4ně_o4ní_o4pe_o4po_o4se_o4sl_ot3v_o4tí_o4tř_o4za_o4zi_o4zo_o4zu_o4šk_o4šl_o4ži_p4ro_p4rý_p4se_pu3b_rej4_re3s_ro4k_s4ch_s4ci_sem4_s4ke_sk4l_s4ká_s4le_s4na_s4ny_s4pe_s4po_s4tá_s4ži_u4ba_u4be_u4bi_u4bo_u4de_u4di_u4do_u4du_u4dí_uh4n_uj4m_u4ko_u4ku_ul4h_u4ma_u4me_u4mi_u4mu_u4ne_u4ni_u4pa_u4pe_u4pi_up4n_u4po_u4pu_u4pá_u4pě_u4pí_u4ra_u4ro_u4rá_u4so_u4st_u4sy_u4sí_u4vi_u4ze_u4če_u4či_u4čí_u4še_u4ši_u4šk_uš4t_u4ší_u4ži_už4n_u4žo_u4ží_v4po_v4zá_v4ži_y4or_y4ve_zar2_zač2_z4di_z4dr_z4ky_z4mn_z4no_z4nu_z4ně_z4ní_z4pe_z4po_z4tř_z4ve_z4vi_č4te_še3t_š4ka_š4ke_š4ky_š4ťo_š4ťá_ú4důaa3t2ab4lýab3riab4sbab2stac4ciad2laa4dlia4dláa4dléad4mead4muado4sad3ria3drža4dužad3voad4úzad4úřae4viafi2aag4faag3roah4liai4reaj4meak4nial4fbal4klal4tzal3žíam4bdam4klam4nuamo3sam4žia4naean4dtaneu4an4scan4sgan4slan4sman2span4svan4tčan4žhao4edao4hmao4tčap4r_a4psoa4př_ar4dwa4rerar4glar4kha4roxar3star2vaar3š2ar4šrarůs3a3sinas3náas3pia4stkas4tmas3tvat4cha4tioat4klat3loat3rea4truat4ráat4thau4gsauj4maus3tav4d_av3loa4vlua4vlíav4tiay4onaz3laaz4léaz3niač4máaře4ka4špla4špyba4brba3kaba4sebe4efbe4etbej4mbeu4rbe2z3beze3bi2b3bist4bi4trbl4blb2lemb2lesb4lánb2lémbo4etbo4jmbo4okbo4trbou3sbo4škb2ralb2ranb4roubroz4b3ru_b3rubb2rán2b1s2bs3trbtáh4bu4enby4smby4tčby4znbé4rcbě3tabí4rcb3ře_bře4scad4lca4escech4ced4lcelo3ce4nsce4ovce4pscer4v4che_ch4lych4mb2ch3n4chtech4u_cik4lc4ketco4atco4mmco4žpctis4ct4lací4plda4jšda4klda4trdch4ldd4hade3hnde3jdde3klde3kvde2nade2ozde3slde4smde4sode2spdes4tde4xtde3zndez3ode3čtde4žpdi4gg4dinddis3kdi4sodj4usd4labd4lakd2loud3lučd4láž2d1lídmýš44dobldo3bydo3bědo3býdod4ndoj4m4dokn4dolydo3mndo4pcdop4ndor2vdos4pdo3ukdo3učdo3z2doz4ndoč4tdo4žp4drand4rapd4rend3rosd3roud3rošdr4scd3rušd4rýv2d1s2ds4kůds4podum3řdu3nadu4pndu3sidu4í_d4vacdy4sudře4kd4řepd4řevd2řítea3dreb4erebez2eb4lie4ch_e4chme3choe2chre3chve4chťed4beed4kved2mae3dmned4říee4thee3xieg4giehno4eh4něej3age3jase3jede3jezej3ine3jisej3moe3jmue4klye4lauel4dvel4zee4mlíemo3kem3žeen4dven4scen4sient3reo3byeod3leo4due4oleeo2steo4třeo4zbeo4zdeoše3epa3te4pniep2noe4pnýep4tlep4tmep4tne4ptuer4a_er4s_er4sne4sage2scee4sinesi4ses4k_es3kyes3kée4slye4sp_es4pee4st_e4stee4tkie4tkre4tlie4tlyet3riet3roet3růet4úneu3cteu4m_eu4r_e4uraeu4rgeu3s2eu4tseve4še3v2ke4vskex4taey4orey4ovez4apez4boez3deez3duez4děez4ejez4elez4erez4esez4ezez4ešezis4ez4itez4leez4náez4něez4pyez4ácez4áhez4čeez4řeeč4tee4čtie4čtíeře4keř4kue4škaeš4láeš4toeúmy4ežíš4fe4infene4fe4uefi4emfi4flfló4rfm4nof4ranf4ras3frekfs4tefu4chga4učghou4gi4ímg4lomg4noig4nosgo4hm3grafgu4elgu4itgu4m_gus4tha4agha4arha4blha4brha3dlha4kehas3tha4ydhe4brhe4idhej4shi4anhi3erhi4ghhi4re4hla_h4ledh3lenh3lobh3loph3lovh3luj2h1ly4hlá_h4lásh3lí_4hlíkh4nedh3nivh4noj3hněd4hovehra4ph4tinh4títhu4chhu3mohu4tňhy4dohy4pshy4zdhř4byhý4blia3g2i4al_ias4tia4tri2b1ri4chžid4gei4dlýig4nei3hl_i4hliih4naijed4ij4meij4miik3leik4ryi4kveik4úřil4bai4lnui4mlai4mlyi4munina3din4cmin4dl3infein4ghin4gpin4gsin4gtin4špio4skiro4sis4chis4k_is3kais3keis3kris3kuis3kvis3kyis3lois3léis3plis3pois4thist3vis3tíit4rhit4rpit4seit4suix4tdič4tlič4toiř4kliř4čeiš4kriš4kviš4toja2b2jac4kja4cqj3aktj3dobj3dokj3dosjd4říjech4jg4raji4chjih3lji4mžj4inajis3kji2zvjod2řj4orajo3svj3ovljpor42j1s2j4semj4si_j4sk_js4kojs4kájs4poju4anju3naju3spju4t_ju4xtju3žijád2rjš4tika4blka4chka3dlka3ka3kami3kaněka2pska4pvka2přkas3tka4učkaš3lka4špke4blke3joke4prke4psk3lejk4libk3lic4klo_k3los2k3lyk3lá_kna4sko3byko4jmko2přko4skko3zá4kroak3robk3rofkr4ú_kuch4ku4fřku4hrku3seku3siku3suku4thk4vrňky2prkyp3řky4znká4plk3řejkš4tila4brlab4sla3kala4nqla4psla4všla4y_la2zmld4nele4adle4auleh3nle3jole4prle4psle4scle4smle4svlet3mle2trle4tčle4ukle4vhle4vkle3xilez3n3lhanli4azli4blli4bvli4dmlind4li4tňli4vrl4katlk4nul4nullo3brlo4idlo4islo3splo3svlo2trlo4třlo4u_loz4dlo4šk2l1s2l4slalst4nl4stílt4ralt4rult4rylu4idlu4j_lu4k_lu4lklu4m_lu4mnlu3prlu3valu3vllu3vylu3vílá4jšlá4všlí4pllí4znl4štýmaj4sma4klma4kr4maldmas3kmat3rma4všmaz3l2m1d2me4gome4ismh4lemid3lmik3rmi4xt3m2klmk4lamk4li4mla_ml4h_ml4scml4sk4mlu_mna4sm4nohm3nosm4noz3množm4nézm3nějmod3rmo2hlmo4s_mot3ř4moutmoza4mo3zřm4plompo4smp4se2m1s2m4stlmu4flmu4n_mu4ndmu4nnmu4nsmu4nšmy4škmálo3mí4rňmš4čina3chna4dona4emna4h_na3jdna3kana3p2na3s2na4s_na3tlna3třnaz4kna4zšna4č_naž4nn4chcnd4hindo4tnd2rend4rind4říne4glnej3tnej3une3klne3kvne4m_ne3s2ne4s_ne4ssne3tlnet4rne3udne3v2ne4v_nez4nne3škne3šťng4lang4leng4lín4grong4vinik4tni4mrni4mž3nisk2nitřno3b2no4bsno3hnno4hsno4irno4mžno3smnot4rno4zdno4šk2n1s2ns3akns4kon4socns3pont4r_nt3runt3ránu4ggná3s2ná4s_nš4ťooang4obe3jobe3sobe3zob4rňobys4o4chlo2chroc4keoc4koo4ct_oct3noc4únode3pode3so4docodos4od3raod3růo3držoe3tioh4neoi4ceo4into4jaro4jmio4jmuo4jmůo4juzok2teol4glol4toom4klona4soo4hřoote2o4ptuopá4to4př_o4raeor4dmor3stor4váorůs3o4saiose4sosi4do4skuosk3vo4skáo4skýos4laos4lios4lýos3moos4muo4st_o4stgo4stmo4stéo4stšo4stýot4klo4tlýoto3sot3root3víot3řiou3běou3děou4flou4ilou4isou4k_ou3kao4uklou3krou3káoup3noupo4ou4s_ou3saou3seou4skou3smou4tvou4vlou4vnouz3do4učkou3žio4vskovy2po2vštoz4d_oz3dáoz3děoz3díozer4oz4koo4zn_oz4pyoz4pěoz4píoz3rooz3ruoz3růo4zutoz3vroz3váozů4soč4kaoři2so4škuo4škyoš4láoš4mooš4tioš4ťuož4mopa4edpa4espa4klpa3sipa4t_pe4alpede4pe4igpe4npperi3pi4krpi4plpl4h_4plo_po1b2po3c2poly3po3m2po4mppo4olpo4p_po4pmpo1s2pos4ppo3t2po4t_po4tnpo3ukpo3učpo3už3po3vpo3z2po4zdpo3čkpo3řípo4šv4pra_prob2pro3ppro3z4pránpse4s2p1skp4sutp4tejp4terp4tevpt4rip4tá_pu4dlpu4trpyt3lpád3lpá4nvpá4slpé4rhpře3hpře3jpře3zpřih4pš4tira4brra4emra4esra4ffra4hlra4hmra4jgra4jšra4nhra3sira4vvra4wlra4y_ra4yora4ďm4ražir3char3chorc4kir4dlardo2sre4adre4aured4rre4etre3klre4mrre2sbres3lret4rre4umr3hl_ri4bbri4dgri4drri4flri4ghri4zmr4miorn4drro4adro3byrod2l3rofyro4h_ro4jbro4kšrom3nro2sbro3svro3tiro3tlro4tčro3vd3rovýroz3droz3nro4zoroz3vro3záro4čprpa3drr4harr4hor4stur4trárt4smr2t3vrt4zuru3seru3sirus3kru3žirych3rys3try4zkry4znry4í_ry4škrád4lrá4džrá3rirš4nírů4m_rů4v_rý4znsa4pfsa4prsas3ks3ce_sch4lsch4nsci4ese4ause4igse4ilsej4mse4kuse3lhse3s2ses4kse4ssse3tkse3třse4urse3čtsi4fl4skacs4kak4skams4kok2skonskos44skotsk4rask4rusk4ry4skvesk4vos3káns4lavs3le_s4leds3lems3lens3lets4libs3ly_s4meks3nats3ne_sn4tls3ná_s4nídsob4lso3brso4skso4tvsou3hsou3ssouz4so4šks4polss4sr4sta_s3tajs2tanst4at4stecs4tepst4er2stil4stičst3lo4sto_4str_4strnst4ve3ství4sty_s4tyl3styš4stá_s3tář4stě_s4těd3stěhs2těrs2těž2stí_su4basu4bosuma4su3vesá2klta2blt2a3dta4jfta4jg4talt4tand3taně2tarktast4ta4čkte4akte4flte4inteob4tep3lters4te4trte4ucte4urte4utti4grti3kltin4gti4plti3slti4tr2titutiz4r4tizít4kalt4kattk4latk4li4tkně4tla_tles3t3lo_t4loutlu4sto4astob4lto3drto4hmto4irtol4sto4ol4top_4topt4topu2torn2toupt4reat4reftre4ttrip4t4ritt4rogt3rolt4rou4trunt4rus4trášt3růmt3růvts4kott4chtt4ritu4fftu4lktu4r_tu3rytu4s_tu4ť_tu3ži2t3vit4višt4výcty4gřty2laty4řety4řhty4řjty4řoty4řrty4řútá4flté2bl2těnn4tíc_4tícet4řebt2řelt2řict3řiltř4ti3třábtří4stš4tiubs4tu3bí_uc4tíu3druue4fauh3láuh3nou3ka_uk4ajuk4aluk4atuk3lauk3leuk4á_ul4faul4píum4plum4ruun4dlun4žru3pln2u3rou3ry_us3kyus3káus3kéus3kýus2lou4steu4styu4stéu4stěu3střu4stšu4stýu3su_u4trou4tráuš4kluš3tíva3dlva4jťva4klv4dalv4děkv4děčve3jdve3psvep3řves3lve4smves4pvi4chvide2vi4drvi4etvi4krvi2tr4vle_4vlemv4nadvo4icvo4javo4jbvo4jdvo4jjvo4jmvo4jřvo4třvous2vr2dl4vrnyvr4stv3stvvy3d2vy3s2vy4snvys4tvyč4kvy4š_vy4šmvy4ššvy4žlvz4novz4névz4něvz4nívá3riv4čírvě4cmvíce3v3řínvše3s3vý3zwa4fdwa4rexand4xisk4xt4raxy4smyb3riy4chry2d1lyd4láyd4y_yh4neyj4mayj4meyk3layk4lyym4klyna4sype4ryp4siyp4táys3luys3teyst4ryt4meyvě4tyz4něyz4níyz4poyřk4nyř4čezab2lza4bsza4dkza3dlza4dnza4jkza4ktzal4kzam4nza3p2za3s2za3tlzat4rza4utzaz4nza4zšza4č_zaš4kza4šszban4zbys4zd4rezd4víze3p2ze3s2zes4pze3vnze4z_z4inez3ka_zlik3z3ly_z4měn3znakz4nalz3ne_z3nicz4nělz4nítz4nívzo4trzo4škz4pát3zrak2z1s2z4trázu3mozu3mězu3mízva4dz3vařzvik4zv4něz3vodz3vojz4vonzv4roz4vánz4věsz3víjzá3s2zřej3z3řezz3řešzš4ka2z2ú1áb4ryá4bř_á3choádo4sá3hl_á4jmuáj4můá4kliák4niáne4vá2s3kás4k_ás4klás4kná2slaás4lyás4poáv4siáv4síáz3niáz4viář4keář4kůča4brčes3kč3ka_čs4lačs4srčt4la4čtěnčís3lďs4te4ére_ě3hl_ěh3loě4kléě3k2těra3děrs4tět1a3ět4acět3raět3říěš4ťsí3choích4tíjed4íj4můí2s3kís4klís4knís4l_ís3leís4lnísáh2íz3daíz3deí3znařa4plřa4ďmře3chře3jdře3klře3kvřeo4rře3p2ře4p_ře4pkře4pčřer4vře2spře4srře3tlřet4řře3zdře3zk4řezlře3čtři4h_ři4hnři4jďři4l_ři4lbřil2n4řineři4v_ři4vkři4vnřič4tři4š_řk4lařk4liřk4lyřk4nořs4tořá4plřá2slří4křřš4tiša4vlšej4dšep3tši4mr4škovšk4roš3ku_š3livšmi4dš4tipšt4kašt4klš4těkš2těsš4těvš4típťáč4kúj4maút4koúře4zúš4tiůr4vaůr4vyůs3teů3tklý3choýd4laýt4kuýt4kyý4vliý4zvuýč4něža4tvže2b3žeh3nže4mlže4zgži4dlži4jmži2vlžk4niž4lic2ž1s2žá4bržá4nrží4znžš4tižš4tě",6:"_ale3x_as3t3_je4dl_kří3d_le4gr_li3kv_moud3_na3č4_nář4k_od3rá_os4to_os4tě_ot3rá_ově4t_oz3do_pa4re_pa3tř_po3č4_roze3_roz3r_ru4dl_se3pn_va4dl_zao3sab3lona3d3ra3a3dvaa4nameane4skao4střas4tatat3ronat3rova4tří_ba4chr4chalgcien4c4dbat_3dch4nde4bredej4mode3strd3lou_4doboj4do4dd4do4djdomoh44do4čn3drobndře4pne3chl_eilus3ej3eleeju3steoch3repoč3te4s4knes3ku_e4s3lies3tižes4toles3táneu4rase4u4t_eu4traevy4čkevě4trezaos3ez3dovez4ed2eč4kateštíh4ha4dlahatos44h3lo_3hodinho3strhos4tě4hovna4hovny4hovná4hovněhy2t3rid4lo_ik3lo_ilič4nis3ko_i3slavis4talis4tatié4re_jbyst3jez3díjit4rojmou3dj1o3z2jpo4zvjpříz4j4s4kůj4s4mej4sou_j4soucj4s4teka2p3lka2p3rkast3r4k3la_4k3li_ko2t3vkous3k4la3silech3t4lejšk4lenchlepa3dlepo4slet4lilo3střma4tramet3remezi3smys3lonam4nene3h4nne4krones4le4nestino4skyno3strnst4rant4lemob3řezodej4modo4tkod4ranofrek4oje4dlo4jmovont4raopoč3topro4sopřej4o4s3keos4toros3trůoze3d2pat4ripes3t3pe4tra4p3la_4p3li_po3drupo3drápost4rpoč3tepra3stpro3t4pře3t4pře3č2rast4rre3kviretis4ric4kurna4všro3d4rromy4sropát4ro4skvro4skyrově4trs3tvěrs3tvý3rvanírys3kyrůs3ta3schopser4vase4střsig4nosi3ste4s3la_s4liči4s3lo_spro4ss4teros4tichs4tink4stit_s4tona4stou_4strams4trik4strács3třejsych3rsy4nesta3str4tenémtes3tatis4tr4t2kant3rant4tric_tro4sk4trouh4troň_4t4ružt3rálnt4vinntě3d4ltřeh3nupe2r3ve3dleve3stave3t4řve2z3m2v3la_vrst3vvy4dravě3t4aví4hatv3ští_y3klopymané4z4doba4zerotzlhos4ztros3zá4kl_ác3ti3ázni4cč4tenýě4trají3t3řeí3z3nií3zněnře4dobře4kříře3skaře3skořes3poře3staře3stuře3stáře3stř3ři4t_š3k3li4š3kouůs3tánýpo3č4",7:"_dneš4k_mi3st4_no4s3t_os3t3r_polk4la4stru_b4roditckte4rýdob4ratdos4tivenitos4epro4zře4strouevyjad4evypá4t4kličkamš4ťan_nte4r3aonář4kaopře4jmovi4dlapodbě4hpod4nes4rčitý_se4strase4stru4stupnitac4tvovrs4tvězdně4níz4dobnýádos4tič4tené_č4tový_ů4jmový"}};Hyphenator.config({selectorfunction:function(){return $$('#dis-module-x div p, table.adminTable p');},persistentconfig:true});Hyphenator.run();

