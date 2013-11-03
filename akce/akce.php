<?php
//vetveni akci
switch ($_GET['akce']){

//registrace uzivatele (registrace)
case "reg":
  include "./akce/registrace/registrace.php";
break;

//ulozeni ikonky (nastaveni)
case "nastaveni-ico":
  include "./akce/nastaveni/ico.php";
break;

//ulozeni noveho hesla (nastaveni)
case "nastaveni-heslo":
  include "./akce/nastaveni/heslo.php";
break;

//zmena mailu (nastaveni)
case "nastaveni-mail":
  include "./akce/nastaveni/mail.php";
break;

//zmena os nastaveni (nastaveni)
case "nastaveni-os":
  include "./akce/nastaveni/os.php";
break;

case "nastaveni-rozcesti":
	include "./akce/nastaveni/rozcesti.php";
break;

//zmena podpisu (nastaveni)
case "nastaveni-podpis":
  include "./akce/nastaveni/podpis.php";
break;

//zmena chat (nastaveni)
case "nastaveni-chat":
  include "./akce/nastaveni/chat.php";
break;

//bonus (nastaveni)
case "nastaveni-bonus":
  include "./akce/nastaveni/bonus.php";
break;

//odstraneni navstivenych z visited
case "nastaveni-attend":
  include "./akce/nastaveni/attend.php";
break;

//title (nastaveni)
case "nastaveni-title":
  include "./akce/nastaveni/title.php";
break;

//some funny things
case "nastaveni-system-specialities":
	include "./akce/nastaveni/json-settings.php";
break;

//pretizeni stylu z JS
case "nastaveni-style":
	include "./akce/nastaveni/style.php";
break;

//odeslani interni posty (posta)
case "posta-send":
  include "./akce/posta/send.php";
break;

//vymaz vybranych zprav (posta)
case "posta-delete":
  include "./akce/posta/delete.php";
break;

//vymaz vsech zprav (posta)
case "posta-deleteall":
  include "./akce/posta/delete_all.php";
break;

//galerie
case "galerie-new":
  include "./akce/galerie/galerie.php";
break;

//diskuze - odeslani noveho tematu ke schvaleni
case "diskuze-new":
  include "./akce/diskuze/d-new.php";
break;

//diskuze - zmena prav pro cteni, psani a zakazovani
case "diskuze-prava":
  include "./akce/diskuze/d-prava.php";
break;

//diskuze - zmena vlastnictvi
case "diskuze-vlastnictvi":
  include "./akce/diskuze/d-owner.php";
break;

case "diskuze-obecne":
  include "./akce/diskuze/d-obecne.php";
break;

//diskuze - smazani tematu
case "diskuze-smazat":
  include "./akce/diskuze/d-smazat.php";
break;

//diskuze - zmena moderatoru diskuze
case "diskuze-spravci":
	include "./akce/diskuze/d-spravci.php";
break;

//diskuze - administrace TOP
case "diskuze-administrace":
	include "./akce/diskuze/d-admin.php";
break;

//diskuze - administrace TOP
case "diskuze-administrace2":
	include "./akce/diskuze/d-oblasti.php";
break;

//novy clanek
case "clanky-new":
  include "./akce/clanky/clanky.php";
break;

//edit vraceneho (!) clanku
case "clanek-edit":
  include "./akce/clanky/c-edit.php";
break;

//hodnoceni (clanky, galerka)
case "rating":
  include "./akce/hodnoceni/rating.php";
break;

//postnuti prispevku do diskuze/kommentare/herna/galerie
case "post-comm":
  include "./akce/comm/comm.php";
break;

//vymaz prispevku
case "comm-delete":
  include "./akce/comm/delete.php";
break; 

//pridani/odebrani uzivatele z pratel
case "friends":
  include "./akce/friends/friends.php";
break; 

//pridani/odebrani uzivatele z pratel
case "search-user":
  include "./akce/users/search.php";
break; 

//pridani zalozky
case "add-bookmark":
  include "./akce/bookm/add.php";
break; 

//odebrani zalozky
case "rem-bookmark":
  include "./akce/bookm/rem.php";
break; 

//nova mistnost u chatu
case "chat-create":
  include "./akce/chat/create.php";
break;

//admin chat uprava
case "chat-adjust":
  include "./akce/chat/adjust.php";
break;

//chat vstup do mistnosti
case "chat-enter":
  include "./akce/chat/enter.php";
break;

//komentare u uzivatelu
case "u-comm":
  include "./akce/u_comm/post.php";
break;

//zalozeni jeskyne
case "herna-new":
  include "./akce/herna/h-new.php";
break; 
//filtrovani herny
case "herna-filter":
  include "./akce/herna/h-filter.php";
break;
//zalozeni postavy
case "herna-reg":
  include "./akce/herna/h-reg.php";
break; 
//editace postavy
case "postava-edit":
  include "./akce/herna/h-edit.php";
break; 
//zabiti postavy
case "postava-kill":
  include "./akce/herna/h-kill.php";
break; 
//level up postavy
case "postava-level-up":
  include "./akce/herna/h-level.php";
break; 
//level down postavy
case "postava-level-down":
  include "./akce/herna/h-level.php";
break; 
//schvaleni/odmitnuti postavy
case "pj-schvalovani":
  include "./akce/herna/h-pj-schval.php";
break; 
//editace jeskyne - easy
case "pj-obecne":
  include "./akce/herna/h-pj-obecne.php";
break;
//smazani jeskyne se vsim vsudy
case "pj-delete":
  include "./akce/herna/h-pj-delete.php";
break;
//nahrani ikonky PJe 
case "pj-ico":
  include "./akce/herna/h-pj-ico.php";
break; 
//predani jeskyne
case "pj-vlastnik":
  include "./akce/herna/h-pj-vlastnik.php";
break;
//pomocni PJs
case "pj-helper":
  include "./akce/herna/h-pj-helper.php";
break;
//kostka k6 / k10 / k%
case "k6":
case "k10":
case "k20":
case "k100":
case "4k6":
case "2k6plus":
case "XkY":
	$akce = $_GET['akce'];
  include "./akce/herna/h-kostka.php";
break; 
//obchod - neni toho malo, je to vsechno skoro v jednom
case "obchod":
	include "./akce/herna/h-obchod.php";
break;
//anketa - ovladani, mazani, zakladani
case "anketa":
	include "./akce/anketa/ank_all.php";
break;
case "anketa-hlasovat":
	include "./akce/anketa/ank_hlas.php";
break;

case "inv":
	include "./akce/inventar/inv-del.php";
break;
case "nakup":
	include "./akce/inventar/inv-nakup.php";
break;
case "kouzla":
	include "./akce/inventar/inv-kouzla.php";
break;
case "item":
	include "./akce/inventar/inv-item.php";
break;
case "map":
	include "./akce/herna/h-map.php";
break;

case "cave-chat-enter":
case "cave-enter":
	include "./akce/chat/cave-enter.php";
break;

case "poznamky":
	include "./akce/users/notes.php";
break;

case "dv":
	include "./akce/nastaveni/view-del.php";
break;

case "jeskyne-export":
	include "./akce/herna/h-export.php";
break;

case "diskuze-export":
	include "./akce/diskuze/d-export.php";
break;

case "jeskyne-clear":
	include "./akce/herna/h-clear.php";
break;

case "diskuze-clear":
	include "./akce/diskuze/d-clear.php";
break;

//odeslani postolky
case "posta-send-new":
  include "./akce/posta/new-send.php";
break;

case "postolka-delete": //vymaz vybranych zprav
case "postolka-delete-in": //vymaz vsech ToMe zprav
case "postolka-delete-out": //vymaz vsech FromMe zprav
  include "./akce/posta/new-delete.php";
break;

//administrace prav pro clankz/galerii
case "administrace-dila":
  include "./akce/comm/prava-set.php";
break;

}
?>
