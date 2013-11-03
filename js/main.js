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
