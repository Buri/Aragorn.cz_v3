<!--
function MM_findObj(theObj, theDoc) {
	var p,i,foundObj;if(!theDoc)theDoc=document;if((p=theObj.indexOf("?"))>0&&parent.frames.length){theDoc=parent.frames[theObj.substring(p+1)].document;theObj=theObj.substring(0,p);}if(!(foundObj=theDoc[theObj])&&theDoc.all){foundObj=theDoc.all[theObj];}for(i=0;!foundObj&&i<theDoc.forms.length;i++){foundObj=theDoc.forms[i][theObj];}for(i=0;!foundObj&&theDoc.layers&&i<theDoc.layers.length;i++){foundObj=findObj(theObj,theDoc.layers[i].document);}if(!foundObj&&document.getElementById)foundObj=document.getElementById(theObj);return foundObj;
}
var MAP_ACTIONS=" onmousedown='dragmestart(event)' onmouseup='dragmeend(event)' onclick='clickme(event)'";var shownMap=false;var povrchyHidden=true;var posunASCII1=97;var posunASCII2=65;var oddelovac=",";var dragingIn=false;var x,y;var mapSrc=new Array();var rozmerX=20;var rozmerY=20;var abeceda="";var BDS0=0;var xaxis0=0;var BDS1=0;var xaxus1=0;var BDE0=0;var yaxis0=0;var BDE1=0;var yaxis1=0;
for(x=0;x<=25;x++)abeceda+=String.fromCharCode(posunASCII1+x);
for(x=0;x<=25;x++)abeceda+=String.fromCharCode(posunASCII2+x);
for(x=0;x<=9;x++)abeceda+=String.fromCharCode(48+x);
function mapInit() {
	window.status='Nacitam data...';var mapText=MM_findObj("mapEdSource");var mapTt=mapText.value; if(mapTt.length>=5){rozmerX=1;rozmerY=1;mapSrc="";mapSrc=new Array();mapT = mapTt.split("|");if(!isNaN(parseInt(mapT[1]))&&!isNaN(parseInt(mapT[0]))){rozmerX=parseInt(mapT[1]);rozmerY=parseInt(mapT[0]);}else{rozmerX=rozmerY=0;}mapRow="";mapRow=mapT[2].split(oddelovac);if((rozmerX>0)&&(rozmerY>0)&&(mapRow.length<=rozmerY)){for(y=0; y<rozmerY;y++){mapSrc[y]=new Array();for(x=0;x<rozmerX;x++){if(x>(mapRow[y].length-1)){mapSrc[y][x]="a";}else{if(abeceda.indexOf(mapRow[y].charAt(x))>=0){mapSrc[y][x]=mapRow[y].charAt(x);}else{mapSrc[y][x]="a";}}}}}else{baseMap();}}else{baseMap();};
}
function checkJS() {
	ov=MM_findObj("check4js");ov.innerHTML="";ov.style.display="none";ov.style.visibility="hidden";
}
function mapLoaded() {
	ov=MM_findObj("map-loaded");ov.innerHTML="";ov.style.display="none";ov.style.visibility="hidden";
}
function zeroise() {
	BDS0=0;BDS1=0;BDE0=0;BDE1=0;xaxis0=0;xaxis1=0;yaxis0=0;yaxis1=0;
}
function mapDefinition(pismeno) {
	var cislo=abeceda.indexOf(pismeno);if(cislo<0){return 0;}else return cislo;
}
function baseMap() {
rozmerX=rozmerY=20;for(y=0;y<rozmerY;y++){mapSrc[y]=new Array();for(x=0;x<rozmerX;x++){mapSrc[y][x]="a";}}
}
function MapEdMapaClick(ax,ay) {
var rbutton=MM_findObj("mapEdPovrchRadio");for(i=0;i<rbutton.length;i++){if(rbutton[i].checked){hodnota=rbutton[i].value;break;}}mapSrc[ay-1][ax-1]=abeceda.charAt(hodnota);MM_findObj("MapEdX"+ax+"Y"+ay).style.backgroundPosition=-15*hodnota+"px 0px";
}
function mapEdPovrchyTypyShow() {
	if(povrchyHidden){povrchyHidden=false;obsah="<div class='mapEdTypyTable'>";obsah+="<label for='mapEdPovrchRadio0'><input id='mapEdPovrchRadio0' class='mapEdPRadio' name='mapEdPovrchRadio' type='radio' value='0' checked />" + "<span style=\"background: url("+MAP_POVRCH_SRC+") no-repeat 0px 0px;\"></span></label><br />";for(y=1;y<FIRST_COL;y++){obsah+="<label for='mapEdPovrchRadio"+y+"'><input id='mapEdPovrchRadio"+y+"' name='mapEdPovrchRadio' class='mapEdPRadio' type='radio' value='"+y+"' />"+"<span style=\"background: url("+MAP_POVRCH_SRC+") no-repeat -"+y*15+"px 0px;\"></span></label><br />";}obsah+="</div><div class='mapEdTypyTable'>";for(y=FIRST_COL;y<ALL_COL;y++){obsah+="<label for='mapEdPovrchRadio"+y+"'><"+"input id='mapEdPovrchRadio"+y+"' name='mapEdPovrchRadio' class='mapEdPRadio' type='radio' value='"+y+"' />"+"<span style=\"background: url("+MAP_POVRCH_SRC+") no-repeat -"+y*15+"px 0px;\"></span></label><br />";}obsah+="</div><div id='mapEdOvladani'></div>";MM_findObj("mapEdPovrchy").innerHTML=obsah;}
}
function clickme(e) {
	var targ;if(!e)var e=window.event;if(e.target)targ=e.target;else if(e.srcElement)targ=e.srcElement;if(targ.nodeType==3)targ=targ.parentNode;var tname;tname=targ.tagName;if(tname=="TD"){tname=targ.getAttribute("id");var bunka_xx=tname.indexOf("X");var bunka_yy=tname.indexOf("Y");var bunka_x=tname.substring(bunka_xx+1,bunka_yy);var bunka_y=tname.substring(bunka_yy+1,tname.length);MapEdMapaClick(bunka_x,bunka_y);}
}
function dragmestart(e) {
	zeroise();if(!dragingIn){var targ;if(!e)var e=window.event;if(e.target)targ=e.target;else if(e.srcElement)targ=e.srcElement;if(targ.nodeType==3)targ=targ.parentNode;var tname;tname=targ.tagName;if(tname=="TD"){tname=targ.getAttribute("id");var bunka_xx=tname.indexOf("X");var bunka_yy=tname.indexOf("Y");var bunka_x=tname.substring(bunka_xx+1,bunka_yy);var bunka_y=tname.substring(bunka_yy+1,tname.length);BDS0=bunka_x;BDS1=bunka_y;BDE0=0;BDE1=0;dragingIn=true;}}else{dragingIn=false;}
}
function dragmeend(e) {
	if(dragingIn&&BDS0>0&&BDS1>0){var targ;if(!e)var e=window.event;if(e.target)targ=e.target;else if(e.srcElement)targ=e.srcElement;if(targ.nodeType==3)targ=targ.parentNode;var tname;tname=targ.tagName;if(tname=="TD"){tname=targ.getAttribute("id");var bunka_xx=tname.indexOf("X");var bunka_yy=tname.indexOf("Y");var bunka_x=tname.substring(bunka_xx+1,bunka_yy);var bunka_y=tname.substring(bunka_yy+1,tname.length);BDE0=bunka_x;BDE1=bunka_y;}if(BDS0>0&&BDE0>0&&BDS1>0&&BDE1>0){xaxis0=0;xaxis1=0;yaxis0=0;yaxis1=0;xaxis0=parseInt(BDS0);xaxis1=parseInt(BDE0);yaxis0=parseInt(BDS1);yaxis1=parseInt(BDE1);if(xaxis0>0&&xaxis1>0&&yaxis0>0&&yaxis1>0){var pismenko;var rbutton=MM_findObj("mapEdPovrchRadio");for(var i=0;i<rbutton.length;i++){if(rbutton[i].checked){var hodnota=rbutton[i].value;break;}}pismenko=abeceda.charAt(hodnota);var rozdily,rozdilx;if(yaxis0>yaxis1){rozdily=yaxis0;yaxis0=yaxis1;yaxis1=rozdily;}if(xaxis0>xaxis1){rozdilx=xaxis0;xaxis0=xaxis1;xaxis1=rozdilx;}tbdy=document.getElementById(MAP_NET+'-body');for(y=yaxis0;y<=yaxis1;y++){for(x=xaxis0;x<=xaxis1;x++){mapSrc[y-1][x-1]=pismenko;tbdy.childNodes[y-1].childNodes[x-1].style.backgroundPosition=-15*hodnota+"px 0px";}}}}zeroise();dragingIn=false;document.body.focus();}else{dragingIn=false;zeroise();}return false;
}
function SaveLoadLite() {
if(!povrchyHidden){MM_findObj("mapEdOvladani").innerHTML="<a href='#' onclick='VygenerujMapu();return false;'>Dočasně ulož</a> <a href='#' onclick=\"mapInit();VykresliSit('mapEdPJ');VykresliMapu();return false;\">Vrať změny</a>";}
}
function VykresliSit(komu) {
	window.status="Kreslim ctvercovou sit ...";var obj=MM_findObj(komu);var obsah="<table id='"+MAP_NET+"' class='mapEdMapa' border='0' cellspacing='1' cellpadding='0'><tbody id='"+MAP_NET+"-body'"+MAP_ACTIONS+">";obsah+="</tbody><"+"/table>";obj.innerHTML=obsah;tbdy=document.getElementById(MAP_NET+"-body");for(y=1;y<=rozmerY;y++){r=document.createElement("tr");r.style.height="15px";r.className="mapEdRow";for(x=1;x<=rozmerX;x++){b=document.createElement("td");b.id="MapEdX"+x+"Y"+y;r.appendChild(b);}tbdy.appendChild(r);}shownMap=true;window.status="";
}
function VykresliMapu() {
	window.status='Zakresluji dlazdice ...';tbdy=document.getElementById(MAP_NET+"-body");for(y=1;y<=rozmerY;y++){for(x=1;x<=rozmerX;x++){tbdy.childNodes[y-1].childNodes[x-1].style.backgroundPosition=-15*mapDefinition(mapSrc[y-1][x-1])+"px 0px";}}window.status='';SaveLoadLite();
}
function VygenerujMapu() {
	if(!shownMap){return false;}else{var mapTXT="";for(y=0;y<rozmerY;y++){for(x=0;x<rozmerX;x++){mapTXT+=mapSrc[y][x];}if(y<rozmerY-1){mapTXT=mapTXT+oddelovac;}}MM_findObj("mapEdSource").value=rozmerY+"|"+rozmerX+"|"+mapTXT;SaveLoadLite();}
}
function Pridej(co,komu) {
	if(shownMap){tbdy=document.getElementById(MAP_NET+'-body');if(co=="col"){if(rozmerX==50){alert('Maximální počet sloupečků mapy je 50.');}else{for(y=0;y<rozmerY;y++){c=document.createElement("td");c.id="MapEdX"+(1+rozmerX)+"Y"+(1+y);mapSrc[y][rozmerX]="a";tbdy.childNodes[y].appendChild(c);}rozmerX++;}}else{if(rozmerY==30){alert('Maximální počet řádků mapy je 30.');}else{mapSrc[rozmerY]=new Array();a=document.createElement("tr");a.style.height="15px";a.className="MapEdRow";for(x=0;x<rozmerX;x++){mapSrc[rozmerY][x]="a";c=document.createElement("td");c.id="MapEdX"+(x+1)+"Y"+(rozmerY+1);a.appendChild(c);}tbdy.appendChild(a);rozmerY++;}}}else{return false;}
}
function Uber(co,komu) {
	if(shownMap){var tabulka=(komu);if(co=="row"){if(rozmerY<6){alert("Nejmenší počet řádků je 5.");}else{tabulka.deleteRow(rozmerY-1);mapSrc.pop();rozmerY--;}}else if(co=="col"){if(rozmerX<6){alert("Nejmenší počet sloupců je 5.");}else{for(y=0;y<rozmerY;y++){mapSrc[y].pop();tabulka.rows[y].deleteCell(rozmerX-1);}rozmerX--;}}}else{return false;}
}
function Posun(kam) {
	if(shownMap){tbdy=document.getElementById(MAP_NET+'-body');if(kam=="right"){for(y=0;y<rozmerY;y++){for(x=rozmerX-1;x>0;x--){mapSrc[y][x]=mapSrc[y][x-1];tbdy.childNodes[y].childNodes[x].style.backgroundPosition=tbdy.childNodes[y].childNodes[x-1].style.backgroundPosition;}tbdy.childNodes[y].childNodes[0].style.backgroundPosition="0px 0px";mapSrc[y][0]="a";}}else if(kam=="down"){for(x=0;x<rozmerX;x++){for(y=rozmerY-1;y>0;y--){mapSrc[y][x]=mapSrc[y-1][x];tbdy.childNodes[y].childNodes[x].style.backgroundPosition=tbdy.childNodes[y-1].childNodes[x].style.backgroundPosition;}tbdy.childNodes[0].childNodes[x].style.backgroundPosition="0px 0px";mapSrc[0][x]="a";}}else if(kam=="left"){for(y=0;y<rozmerY;y++){for(x=1;x<rozmerX;x++){mapSrc[y][x-1]=mapSrc[y][x];tbdy.childNodes[y].childNodes[x-1].style.backgroundPosition=tbdy.childNodes[y].childNodes[x].style.backgroundPosition;}tbdy.childNodes[y].childNodes[rozmerX-1].style.backgroundPosition="0px 0px";mapSrc[y][rozmerX-1]="a";}}else if(kam=="up"){for(x=0;x<rozmerX;x++){for(y=1;y<rozmerY;y++){mapSrc[y-1][x]=mapSrc[y][x];tbdy.childNodes[y-1].childNodes[x].style.backgroundPosition=tbdy.childNodes[y].childNodes[x].style.backgroundPosition;}tbdy.childNodes[rozmerY-1].childNodes[x].style.backgroundPosition="0px 0px";mapSrc[rozmerY-1][x]="a";}}}else{return false;}
}
//-->
