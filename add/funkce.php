<?php
$time = time(); //aktualni cas
$postPC = 20; //zobrazenych na strance u posty
$usersPC = 50; //zobrazenych na strance u uzivatelu
$clankyPC = 20; //zobrazenych na strance u clanku
$galeriePC = 15; //zobrazenych na strance u galerie
$hernaPC = 15; //zobrazenych na strance u herny
$commPC = 20; //komentaru ci hernich prispevku na stranku
$adminBlogPC = 3; //blog spotu na stranku
$ajaxTimeout = 60*15; //timeout u ajax chatu na vyhozeni
$runBookmarksList = $runBookmarks = $hFound = $cFound= $dFound = false;
$splitMetaFromText = 1;
$cisloUctuAragornu = "670100-2203300272 / 6210 (mBank)";
$GLOBAL_description = "";
$changeToXHTML = array("b>"=>"strong>","<i>"=>"<em>","</i>"=>"</em>");
$ajaxed = false;
$doingLogin = false;

/*
//////////// april /////////////
*/

$lastRandomNumber = 0;

if (isset($_SESSION["uid"]) && isset($_SESSION["login"]) && isset($_SESSION["chut"]) && $_SESSION["chut"] && $time < mktime(23, 59, 59, 4, 1, 2012) && $time > mktime(0, 0, 1, 4, 1, 2012)) {
	function vypatlavac($whata) {
		$slova = Array("v"=>"w","!"=>"!!!","ú"=>"uuu","kw"=>"q","ů"=>"uuu","č"=>"cz","j"=>"y","š"=>"sh","ž"=>"zh","á"=>"aaa","é"=>"eee","eyt"=>"8","ř"=>"rz","í"=>"iii","ý"=>"yyy","ó"=>"ooo","ě"=>"e","ť"=>"t","ň"=>"n","ö"=>"o","ü"=>"u","ľ"=>"l","ŕ"=>"rrr","ĺ"=>"l","ô"=>"oo","ł"=>"l","ş"=>"s","ç"=>"c","ü"=>"u","ć"=>"c","ś"=>"s","ź"=>"z","ń"=>"n",". "=>" LOL. ",", "=>" woe, "," woe, "=>" chD, ","to yo"=>"tj","to ano"=>"tj","srdce"=>"srdiiiczkooo","spaaat"=>"hajat","spinkat"=>"hajinkat","spinka"=>"hajinka","spinkaaa"=>"hayinkaaa","howno"=>"howiiinkooo","polibek"=>"muckaaaniii","liiibaaaniii"=>"mucinkaaaniii","dobryyy"=>"good","prdel "=>"kakaaaczek ","prdel,"=>"kakaaaczek,","prdel."=>"kakaaaczek.","prdel!"=>"kakaaaczek!","do prdele"=>"do kakaaaczka","v prdeli"=>"v kakaaaczku","prdeliii"=>"kakaaaczkem","dobry"=>"good","piwo"=>"piiivo","mimochodem"=>"btw","diiik."=>"thx.","diiiky."=>"thx.","diiik!"=>"thx!!!","diiiky!"=>"thx!!!","diiik,"=>"thx,","diiiky,"=>"thx,","dekuju"=>"thx","dekuji."=>"thx.","mrdka"=>"mrdaaanek","mrdky"=>"mrdaaanky","mrdkou"=>"mrdaaanekm","mrdkami"=>"mrdaaankama","mrdkma"=>"mrdaaankama","kraaaw"=>"klawisht","koza"=>"koziczka","kozy"=>"koziczky","kozataaa"=>"koziczkataaa"," moc "=>" mocinky "," uuuplne "=>" upe "," uplne "=>" upe ","wole "=>"woe "," ano"=>" jj","newiiim"=>"nwm","newim"=>"nwm"," ty vole"=>" twe"," ty woe"=>" twe","milaaacz"=>"milaaash","miluy"=>"lowiiiskuy","milov"=>"lowiiiskow","neylepshiii "=>"best ","promin "=>"sry ","prominte"=>"soracz","ď"=>"d","smrt "=>"death ","kurva"=>"kua","protoze"=>"ptz","protozhe"=>"ptz","kurwa"=>"kua","prosim"=>"pllls ","prosiiim"=>"pls ","pawou"=>"pabou","huste"=>"cool","husteee"=>"cool","hustyyy"=>"cool"," oka "=>" kukucz "," oka,"=>" kukucz,"," oka."=>" kukucz."," oka!"=>" kukucz!","koczk"=>"koshisht","prase"=>"prasaaatko","sran"=>"kakan","seru"=>"kakaaam","spaaat"=>"dadynkat","spi "=>"dadynkej ","draaat"=>"dlaaat","czay "=>"czayiczek ","puuuydu"=>"pudu","boliii"=>"bolinkaaa","bill "=>"billiiishek ","bolest."=>"bebiiiczkooo.","bolest,"=>"bebiii,","bolest!"=>"bebiii!!!","bolestiw"=>"bebiiiczkow","ale "=>"ae ","ale "=>"aue ","wolat"=>"telefooonowat","kunda"=>"kundiczkaaa","czuuuraaak"=>"czuuulaaaczek","moye"=>"moe","twoye"=>"twoe","kamaraaad"=>"kaaamosh","tedy "=>"teda ","peysek"=>"pesaaaczek","aaaczci"=>"aaashci","trochu"=>"kapishtu","troshku"=>"kapishtu","trocha"=>"kapishta","troshka"=>"kapishta","polshtaaarz "=>"bucliiik ","polshtaaarzo"=>"bucliiiko","polshtaaarze "=>"bucliiiky ","polshtaaarzem "=>"bucliiikem ","polshtaaarzi"=>"bucliiiku","polshtaaarzema"=>"bucliiikama","polshtaaarzemi"=>"bucliiikama","perzin"=>"perzink"," ucho"=>" oushko"," ushi"=>" oushka"," ushat"=>" oushkat"," ucha"=>" ouszka","ruuuzhow"=>"ruuuzhowouck","slowniiik "=>"slowniiiczek ","slowniiiku"=>"slowniiiczku","slowniiikuuu"=>"slowniiiczkuuu","slowniiiky"=>"slowniiiczky","slowniiikem"=>"slowniiiczkem","slowniiikama"=>"slowniiiczkama","hezk"=>"klaaasnoushk","eugeot "=>"ezhotek ","rabant"=>"laaabik","kraaaw"=>"klawishk","yenom"=>"enom","pouze"=>"enom","zhaaarowk"=>"zhaaarowiczk","zhaaarziwk"=>"zhaaarziweczk","wyyyboyk"=>"wyyyboycziczk","ymenuyi se"=>"nadaaaway mi","ymenuyu se"=>"nadaaaway mi","ymenuyiii se"=>"nadaaaway yim","ymenuyou se"=>"nadaaaway yim","ahoy"=>"ayoy","hlawa"=>"hlawiczka","hlawo"=>"hlawiczko","hlawy"=>"hlawiczky","x"=>"xxx","hahaha"=>"hhh","ch"=>"x","to ye"=>"toe","nikdy"=>"nigdy","neniii"=>"neeeni","co ye"=>"coe","t "=>"th ");
		$smajl = Array(" :-***"," X_x"," =("," =)"," ;-*"," O_o"," ^_^"," <3"," xD"," :-/"," </3");
		$slint = Array(" *MuUuUcK*"," *LoWe*"," *KiSs*"," Emo Ye BeSt!!!!!"," UmIiIrAaAm, ZhiWOT jE Na hOwNo!!!"," ToKiO HoTeL RuLezZz!!!"," BiLlIiIsHeK Ye BeSt!"," FsHeCkY WaAaS LoWiIiSkUyU!"," YsEm UpE DaAaRk A IiIwL!!!"," Toe WoDWazZz Woe!!!"," WoE NeWiIiIiIiIiSh!!!!!!!!!!"," i hATe EWeRyOnE!!!"," NeMaAa NeKdO ZhIlEtKu?"," SeSh hUstEy!!! mEgA WoE!"," MrTe Te MuCiNkAaAm DiiiVenKooOoO!"," MTMMMMMR"," BoLiNkAaA Me SrDiIiIiIcZkOoOoO </3 :'("," <3 :-***"," loWiIisKuYu EmO!!! :-**"," YaAa Se PoDrZiIiZnU!!! :(((("," SmUtNiIiIiIiIiIiM!!!!!!!!!!!! :(((((((("," NiKdO Me NeMaAa LaAaAaAaD!!!!!! :((((((");
		$whata = strtr($whata, $slova);
		$whata .= " ".$smajl[mt_rand(0, count($smajl)-1)];
		$whata .= " ".$slint[mt_rand(0, count($slint)-1)];
		return $whata;
	}

	$aprilovyZertikArray = array(
 " Na nic se neohlížet. " ,
 " Kdo pozdě chodí, sám sobě škodí. " ,
 " Dopijem a půjdem. " ,
 " Dneska mi to ale zapaluje. " ,
 " Láska hory přenáší. " ,
 " Můj dům, můj hrad. " ,
 " Jak si kdo ustele, tak si lehne. " ,
 " ... stanu se ještě menším, až budu nejmenším na celém světe ... " ,
 " Jak se do lesa volá, tak se z lesa ozývá. " ,
 " Království za koně. " ,
 " Sejde z očí, sejde z mysli. " ,
 " Nechte maličkých přijíti ke mě. " ,
 " Čistota - půl zdraví. " ,
 " Kdo po tobě kamenem, ty po něm chlebem. " ,
 " Darovanému koni na zuby nehleď. " ,
 " Všude dobře, doma nejlépe. " ,
 " Veni, vidi, vici.",
 " Chudoba cti netratí. " ,
 " Kniha přítel člověka. " ,
 " Až naprší a uschne. " ,
 " Nezůstal kámen na kameni. " ,
 " Nehas, co tě nepálí. " ,
 " Nemá to hlavu ani patu. " ,
 " Neztrácej hlavu! " ,
 " Toho bohdá nebude, aby český král z boje utíkal. " ,
 " Není důležité vyhrát, ale zúčastnit se. ", 
 " Jsme jedné krve, ty i já. (hrabě Drákula) ",
 " To je jinčí kafe. (Maryša) ",
 " Mluviti stříbro, mlčeti zlato. (Jan Chrysostomos) ",
 " Není všechno zlato, co se trpytí. (Přemysl Otakar II.) ",
 " Alea iacta est. (Kostky jsou vrženy.) (Rubik) ",
 " Lidé bděte. (Šípková Růženka) ",
 " Nechlub se cizím peřím. (Ikarus) ",
 " Nikdo za nic nemůže. (Odysseus) ",
 " Já na bráchu, brácha na mě. (Kain) ",
 " Nemám čas. (Chronos) ",
 " Jablko nepadá daleko od stromu. (Newton) ",
 " Příjdu hned. (Mesiáš) ",
 " Nenech se převézt. (Charon) ",
 " Na groš nekoukej. (Jidáš) ",
 " Bylo nás pět. (d'Artagnan) ",
 " Jako když hrách na stěnu hází. (Jánošík) ",
 " To není Ono. (John Lennon) ",
 " Vzít věci do vlastních rukou. (Othello) ",
 " Jako vejce vejci. (Kolumbus) ",
 " Konecně se mi rozsvítilo. (T. A. Edison) ",
 " Už mi nevolej. (Bell) ",
 " Lež má krátké nohy. (Meresjev) ",
 " Nebuď labuť. (Léda) ",
 " Dies irae. (Dny hněvu.) (John Reed) ",
 " Opravdová krása není vidět. (Quasimodo) ",
 " Stěhovat se je horší než vyhořet. (Herostrates) ",
 " Do třetice všeho dobrého. (Václav III.) ",
 " Za blbost se platí. (Didus ineptus) ",
 " Jezte lušteniny, jsou zdravé. (Jacob) ",
 " Na to vem jed. (krédo rodiny Borgiů)",
 " To je hlína. (Golem) ",
 " Malý ryby, taky ryby. (Jonáš) ",
 " Konečně sami. (Pátek) ",
 " Za svou pravdou stát. (Sedící býk) ",
 " Země, země. (Jan Bezzemek) ",
 " Happy end. (Sofokles) ",
 " Život je pohádka. (H. Ch. Andersen) ",
 " Jez do polosyta, pij do polopita. (Otesánek) ",
 " Vím, že nic nevím. (Děd Vševěd) ",
 " Neváhej! (Hamlet) ",
 " Dočkej času, jako husa klasu. (Ošklivé kačátko) ",
 " Ty jsi ta jedna jediná. (Adam) ",
 " Nevidět si na špičku vlastního nosu. (Cyrano) ",
 " Nemáte oheň? (Prometheus) ",
 " Pro Boha živého! (Nietzsche) ",
 " Hlava se mi točí. (Galileo) ",
 " Natáh' se jak dlouhý, tak široký. (Bystrozraký) ",
 " My v tom prsty nemáme. (Jezinky)",
 " Nemá cenu plakat nad rozlitým mlékem. (Smetana) ",
 " Zde by měly kvést růže. (Blbost) ",
 " Pro Krista Pána. (Ježíš) ",
 " Opakovaní je matkou modrosti. (Ozvěna) ",
 " Kout pikle. (Lešetínský kovář) ",
 " Egalité (Rovnost) (Caesar) ",
 " Fraternité (Bratrství) (Boleslav) ",
 " Liberté (Svoboda) (Stalin) ",
 " Já si myji ruce. (Lady Macbeth) ",
 " Mít velké oči. (Červená Karkulka) ",
 " Proč bychom se netěšili, když nám Pán Bůh zdraví dá. (Job) ",
 " Hořím touhou. (Fénix) ",
 " Na to se musí jít od lesa. (Robin Hood) ",
 " Nic lidského mi není cizí. (Ďábel) ",
 " Nenechat se doběhnout. (Zátopek) ",
 " Co na srdci, to na jazyku. (Smrt) ",
 " Upaluj. (Giordano Bruno) ",
 " Kdo neumí, učí. (J. A. Komenský) ",
 " Být na ráně. (Kennedy) ",
 " Už se mi zapalujou lejtka. (Jana z Arku) ",
 " Jdi mi k šípku. (Růženka) ",
 " Melu pátou přes devátou. (Beethoven) ",
 " Hrome! (Prokop Diviš) ",
 " Z nás si nikdo střílet nebude. (Smith & Wesson) ",
 " Ztrácím niť. (Ariadna) "
/*
	" SeSh hUstEy!!! mEgA WoE!",
	" Polej mě sirupem!",
	" NeMaAa NeKdO ZhIlEtKu?", " ŽrááádýÍýÍýÍýLkoOoOoO!!!! ",
	" Miluju med, všude!",
	" UmIiIrAaAm !!!",
	" Chci to!",
	" ZhiWOT jE Na hOwNo!!!",
	" Má mě vůbec někdo rád?",
	" ToKiO HoTeL RuLezZz!!!",
	" Popros... a svléknu se!", " Dneska ne! NE! NÉÉÉÉÉ!",
	" BiLlIiIsHeK Ye BeSt!",
	" Kolik mi je vlastně let???",
	" FsHeCkY WaAaS LoWiIiSkUyU!", " Chci tě líbat, hladit a laskat!",
	" Jdu do sprchy ;-)",
	" YsEm UpE DaAaRk A IiIwL!!!",
	" Miluju bublinky",
	" Toe WoDWazZz Woe!!!",
	" Budliky budliky :o)",
	" *MuUuUcK*",
	" Lády dády dády dá!!!",
	" *LoWe*",
	" Hoooořřřříííííímmmmm",
	" WoE NeWiIiIiIiIiSh!!!!!!",
	" Zapalujou se mi lýtka!",
	" i hATe EWeRyOnE!!!",
	" Pověz mi, jak chutnáš?",
	" *KiSs*",
	" Emo Ye BeSt!!!!!",
	" MrTe Te MuCiNkAaAm!",
	" MTMMMMMR",
	" BoLiNkAaA Me SrDiIiIcZkOoO &lt;/3 :&apos;(",
	" &lt;3 :-***",
	" loWiIisKuYu EmO!!! :-**",
	" YaAa Se PoDrZiIiZnU!!! :((((",
	" SmUtNiIiIiIiM!!!!!!!! :(((((",
	" NiKdO Me NeMaAa LaAaAaAaD!!!! :(((("
*/
	);
	
	$aprilovyZertikArrayLength = count($aprilovyZertikArray);
	function aprilovyZertik($t = 0) {
		global $aprilovyZertikArray, $aprilovyZertikArrayLength, $lastRandomNumber;
		$s = mt_rand(0, $aprilovyZertikArrayLength - 1);
		while ($s == $t) {
			$s = mt_rand(0, $aprilovyZertikArrayLength - 1);
		}
		$lastRandomNumber = $s;
		return "<em> ~ ".$aprilovyZertikArray[$s]."</em>";
	}
}
else {
	function aprilovyZertik($q) {
		return "";
	}
	function vypatlavac($s) {
	  return $s;
	}
}

/*
///////////// april ////////////////
*/


if (isset($_SESSION["uid"]) && isset($_SESSION["lvl"])) {
	$zalozkyOmezeniCount = ($_SESSION['lvl'] > 1) ? 999 : 20;
}
else {
	$zalozkyOmezeniCount = 20;
}

	$infoIDunique = 0;

//nezaporny index
if (isSet($_GET['index']) && $_GET['index'] < 1){
		$_GET['index'] = 1;
}

function nl2p($text){

	// Return if there are no line breaks.
	if (!strstr($text, "\n")) {
		return $text;
	}
	if (stripos($text, "</p>") !== false) {
		return $text;
	}
	// put all text into <p> tags
	$text = '<p>' . $text . '</p>';
	// replace all newline characters with paragraph
	// ending and starting tags
	$text = str_ireplace(array("\n\n\r\r", "\n\n", "\n\r\n", "\r\n\r", "\n", "\r\r", "\r"), array("</p><p>", "</p><p>", "</p><p>", "</p><p>", "<br />", "<br />", ""), $text);
	// remove empty paragraph tags & any cariage return characters
	$text = str_ireplace(array('<br /><br />', "<br />\n<br />", "<br />\r<br />"), '<br />', $text);

	$text = str_ireplace(array("<BR>", "<HR>", "<BR >", "  "), array("<br />", "<hr />", "<br />", " "), $text);
	$text = str_ireplace(array('<p> </p>', '<p></p>', " \r", " \n"), '', $text);
	
	return $text;

}

function je_text_utf8($Str) {
	for ($i=0; $i<strlen($Str); $i++) {
		if (ord($Str[$i]) < 0x80) continue;
		elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n=1;
		elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n=2;
		elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n=3;
		elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n=4;
		elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n=5;
		else return false;
		for ($j=0; $j<$n; $j++) {
			if ((++$i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80))
			return false;
		}
	}
	return true;
}

function do_seo_advanced($string="") {
// na mala pismena nejdrive, proto musi byt MultiByteString
mb_internal_encoding("UTF-8");
$string = mb_strtolower($string);
	if (je_text_utf8($string)) {
		$chars = array(
		// Hlavni znak . diakritika
		chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
		chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
		chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
		chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
		chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
		chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
		chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
		chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
		chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
		chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
		chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
		chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
		chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
		chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
		chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
		chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
		chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
		chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
		chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
		chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
		chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
		chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
		chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
		chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
		chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
		chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
		chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
		chr(195).chr(191) => 'y',
		// Latin Extended-A
		chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
		chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
		chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
		chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
		chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
		chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
		chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
		chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
		chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
		chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
		chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
		chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
		chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
		chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
		chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
		chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
		chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
		chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
		chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
		chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
		chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
		chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
		chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
		chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
		chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
		chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
		chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
		chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
		chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
		chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
		chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
		chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
		chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
		chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
		chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
		chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
		chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
		chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
		chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
		chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
		chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
		chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
		chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
		chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
		chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
		chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
		chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
		chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
		chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
		chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
		chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
		chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
		chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
		chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
		chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
		chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
		chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
		chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
		chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
		chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
		chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
		chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
		chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
		chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
		// znak Euro
		chr(226).chr(130).chr(172) => 'E');
		
		$string = strtr($string, $chars);
	} else {
		// ISO-8859-1 pokud neni UTF-8
		$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
			.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
			.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
			.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
			.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
			.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
			.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
			.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
			.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
			.chr(252).chr(253).chr(255);

		$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

		$string = strtr($string, $chars['in'], $chars['out']);
		$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
		$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
		$string = str_replace($double_chars['in'], $double_chars['out'], $string);
	}

$co = array(
" ", "/", "\\", "&", "?", "!", "@", "\$", "%", "^",
"*", "(", ")",	"+", "~", ";", "'", "\"", ">", "<",
"|", ",", ":",	"=", "´", "§", "[", "]",	"{", "}",
"¨", "`", "_",	"„", "“", "…", ".", "°");
$naco = array(
"-", "-", "-",	"-", "-", "-", "-", "-",	"-", "-",
"-", "-", "-",	"-", "-", "-", "-", "-",	"-", "-",
"-", "-", "-",	"-", "-", "-", "-", "-",	"-", "-",
"-", "-", "-",	"-", "-", "-", "-", "-");

	$string = str_replace($co,$naco,$string);
	$string = ereg_replace("[^[:alnum:]\.]","-",$string);

$diak ="ěščřžýáíéťňďúůóöüđàČŘŽÝÁÍÉŤŇĎÑŮÓÖÜ ëä";
$diak.="\x97\x96\x91\x92\x84\x93\x94\xAB\xBB";
$ascii="escrzyaietnduuoouESCRZYAIETNDUUOOU-ea";
$ascii.="\x2D\x2D\x27\x27\x22\x22\x22\x22\x22";
$string = StrTr($string,$diak,$ascii);

	$string = ereg_replace("-{1,}","-",$string);

	while ($string[strlen($string)-1]=="-" && (strlen($string)>3)) {
		$string = substr($string,0,-1);
	}
	while ($string[0]=="-" && (strlen($string)>3)) {
		$string = substr($string,1);
	}
return trim($string);
}

//definice hlavicek mailu
	$headers = "From: info@aragorn.cz\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\n";
	$headers .= "Content-Transfer-Encoding: 8bit\n";
	$headers .= "Return-Path: info@aragorn.cz\n";

function timer_this() {
	return microtime(true);
/*	$mtime = explode(' ', $mtime);
	$time_end = $mtime[1] + $mtime[0];
	return $time_end;
*/
}

$timer_start = timer_this();

//vypise warning
function info($text,$ajax=false){
	global $infoIDunique,$LogedIn;
	$odkaz = "";
	if ($LogedIn) $odkaz = "<a rel=\"nofollow\" href=\"#\" onclick=\"hide('inf-$infoIDunique');return false;\" class='permalink2' title='Zavřít'>Zavřít</a>";
	if (!$ajax) echo "<p class='info' id='inf-$infoIDunique'><span class='war' title='Varování'></span>$text".$odkaz."</p>";
	$infoIDunique++;
}
//vypise ok
function ok($text,$ajax=false){
	global $infoIDunique,$LogedIn;
	$odkaz = "";
	if ($LogedIn) $odkaz = "<a rel=\"nofollow\" href=\"#\" onclick=\"hide('inf-$infoIDunique');return false;\" class='permalink2' title='Zavřít'>Zavřít</a>";
	if (!$ajax) echo "<p class='info' id='inf-$infoIDunique'><span class='ok' title='Ok'></span>$text".$odkaz."</p>";
	$infoIDunique++;
}
//vypise info
function inf($text,$ajax=false){
	global $infoIDunique,$LogedIn;
	$odkaz = "";
	if ($LogedIn) $odkaz = "<a rel=\"nofollow\" href=\"#\" onclick=\"hide('inf-$infoIDunique');return false;\" class='permalink2' title='Zavřít'>Zavřít</a>";
	if (!$ajax) echo "<p class='info' id='inf-$infoIDunique'><span class='inf' title='Ok'></span>$text".$odkaz."</p>";
	$infoIDunique++;
}

if (isset($_POST['sekce'])) {
	$sekce = $_POST['sekce'];
}
if (isset($_GET['sekce'])) {
	$sekce = $_GET['sekce'];
}

//strankovani
function make_pages($pocet_polozek, $delka_stranky, $index, $onpage="0"){
global $ajaxed,$link,$slink,$sslink,$usersPC,$clankyPC,$postPC,$commPC,$adminBlogPC,$hernaPC,$galeriePC,$sid,$id,$dFound,$cFound,$hFound;

if ($ajaxed) return;

switch ($link) {
	case "herna":
	case "diskuze":
	case "galerie":
	case "clanky":
		if ($hFound==true || $dFound==true || $cFound==true) {
//			echo $delka_stranky."-".$onpage.":: ";
//			$onpage is SET !!!
		}else {
			switch ($link) {
				case "herna":
					$delka_stranky = $hernaPC;
				break;
				case "galerie":
					$delka_stranky = $galeriePC;
				break;
				case "clanky":
					$delka_stranky = $clankyPC;
				break;
				default:
					$delka_stranky = $commPC;
				break;
			}
		}
	break;
	case "uzivatele":
	break;
	case "posta":
	break;
	default:
	break;
}

$pocet_stranek = ceil($pocet_polozek/$delka_stranky);

$sekce = "";
if (isset($_POST['sekce'])) {
	$sekce = $_POST['sekce'];
}
if (isset($_GET['sekce'])) {
	$sekce = $_GET['sekce'];
}

$podle = "";
if (isset($_POST['podle'])) {
	$podle = $_POST['podle'];
}
if (isset($_GET['podle'])) {
	$podle = $_GET['podle'];
}

$search = "";
if (isset($_GET['search'])) {
	$search = $_GET['search'];
}

$aU = array();

if (strlen($sekce)>0) $aU[] = "sekce=".urlencode($sekce);

if (strlen($podle)>0) $aU[] = "podle=".urlencode($podle);

if (strlen($search)>0) $aU[] = "search=".urlencode($search);

$aU = join("&amp;",$aU);

$index = (int)$index;

if ($index <= 1){
	$index = 1;
}elseif($index > $pocet_stranek){
	$index = $pocet_stranek;
}

if (strlen($slink) > 0){
	$addUri = "$link/$slink";
	$addCom = "#kom";
	$add2 = "2";
}else{
	$addUri = "$link";
	$addCom = "";
	$add2 = "";
}

if ($link == 'posta' && $slink == 'konverzace' && strlen($sslink) > 0){
	$addUri = "$link/$slink/$sslink";
	$addCom = "#kom";
	$add2 = "2";
}

//pro admin blog
if (strlen($addUri) < 1){
	$linkReplacement = "/";
	$addCom = "#kom";
	$add2 = "2";
}else{
	$linkReplacement = "/$addUri/";
}

$aUw = $otaznik = "";
if ($aU != "") {
	$aUw = "&amp;".$aU;
	$otaznik = "?";
}

if ($index>1 && $pocet_stranek > 3) {
	if (($index)>1) {
		$exp[] = "<a href='".$linkReplacement.$otaznik.$aU.$addCom."' title='První stránka'>|&lArr;</a>";
	}
	if (($index > 2)) {
		$exp[] = "<a href='".$linkReplacement."?index=".($index-1).$aUw.$addCom.$add2."' title='Předchozí (".($index-1).".) stránka'>&lArr;</a>";
	}
	else $exp[] = "<a href='".$linkReplacement.$otaznik.$aU.$addCom.$add2."' title='Předchozí (".($index-1).".) stránka'>&lArr;</a>";
}

if ( $index > 5 && $pocet_stranek > 3){
	$exp[] = "<a href='".$linkReplacement.$otaznik.$aU.$addCom.$add2."'>1</a> ... ";
}
elseif ( $index > 4 && $pocet_stranek > 3){
	$exp[] = "<a href='".$linkReplacement.$otaznik.$aU.$addCom.$add2."'>1</a>";
}

for( $i=3;$i>0;$i--){
	if( $index > $i ){
		$iU = $index-$i;
		if ($iU > 1) $exp[] = "<a href='".$linkReplacement."?index=".$iU.$aUw.$addCom.$add2."'>$iU</a>";
		else $exp[] = "<a href='".$linkReplacement.$otaznik.$aU.$addCom.$add2."'>$iU</a>";
	}
}
if ($index > 1) $exp[] = "<a href='".$linkReplacement."?index=".$index.$aUw.$addCom."' class='strankovaniMarked'>$index</a>";
else $exp[] = "<a href='".$linkReplacement.$otaznik.$aU.$addCom."' class='strankovaniMarked'>$index</a>";

for ($i=1;$i<4;$i++){
	if( ($index+$i) <= $pocet_stranek){
		$iU = $index+$i;
		if ($iU > 1) $exp[] = "<a href='".$linkReplacement."?index=".$iU.$aUw.$addCom."'>$iU</a>";
		else $exp[] = "<a href='".$linkReplacement.$otaznik.$aU.$addCom."'>$iU</a>";
	}
}

if ( ($index+5) <= $pocet_stranek && $pocet_stranek > 3){
	$exp[] = "... <a href='".$linkReplacement."?index=".$pocet_stranek.$aUw.$addCom."'>$pocet_stranek</a>";
}
elseif ( ($index+4) <= $pocet_stranek && $pocet_stranek > 3){
	$exp[] = "<a href='".$linkReplacement."?index=".$pocet_stranek.$aUw.$addCom."'>$pocet_stranek</a>";
}

if ($index<$pocet_stranek && $pocet_stranek > 3) {
	$exp[] = "<a href='".$linkReplacement."?index=".($index+1).$aUw.$addCom."' title='Další (".($index+1).".) stránka'>&rArr;</a>";
	if ($index < $pocet_stranek) {
		$exp[] = "<a href='".$linkReplacement."?index=".$pocet_stranek.$aUw.$addCom."' title='Poslední stránka'>&rArr;|</a>";
	}
}

if (count ($exp) > 0){
	$exp = join(" ",$exp);
}else{
	$exp = "";
}

return $exp;
}

//testovani formatu grafiky
function format_test($ico){
	if ($ico!=="image/jpg" && $ico!=="image/gif" && $ico!=="image/jpeg" && $ico!=="image/pjpeg" && $ico!=="image/png"){
		return 1;
	}else{
		return 0;
	}
}
//rozmery ikony
function ico_size($ico){
$size = getimagesize($ico);
	if ($size[0] > 50 || $size[0] < 40 || $size[1] < 40 || $size[1] > 70) return 1;
	else return 0;
}
//velikost ikony
function ico_dat($ico){
	if ($ico > 16384) return 1;
	else return 0;
}
//detekce nevhodnych znaku u registrace
function bl($str){
	if (!preg_match('/["\'\|;:<>\/\\#!@$%^&*\(\)_+]/',$str)) return 0;
	else return 1;
}

//zformatuje text pri vstupu (diskuze, posta...)
function editor($str){
	$str = _htmlspec($str);
	$str = ereg_replace("{ *i *}([^{}]*){ */ *}","<i>\\1</i>", $str);
	$str = ereg_replace("{ *b *}([^{}]*){ */ *}","<b>\\1</b>", $str);
	$str = ereg_replace("{ *u *}([^{}]*){ */ *}","<u>\\1</u>", $str);
  $str = ereg_replace("{ *castle *}([^{}]*)###([^{}]*)@@@([^{}]*){ */ *}","<a href='\\1' title='\\3'><img src='\\2' alt='\\3' /></a><a href='\\1' title='Zobrazit celou pozvánku.'>Chceš-li zvěděti více, zde si můžež celou pozvánku prohlédnouti.</a>", $str);
	$str = ereg_replace("{ *spoiler *}([^{}]*){ */ *}","<span class='spoiler'>\\1</span>", $str);
	$str = ereg_replace("{ *color1 *}([^{}]*){ */ *}","<span class='hlight1'>\\1</span>", $str);
	$str = ereg_replace("{ *color2 *}([^{}]*){ */ *}","<span class='hlight2'>\\1</span>", $str);
	$str = ereg_replace("{ *color3 *}([^{}]*){ */ *}","<span class='hlight3'>\\1</span>", $str);
	$str = ereg_replace("{ *link *}http://www.aragorn.cz([^{}]*){ */ *}","<a href='http://www.aragorn.cz\\1' class='permalink2' target='_blank' title='Vnitřní odkaz :: http://www.aragorn.cz\\1'>http://www.aragorn.cz\\1</a>", $str);
	$str = ereg_replace("{ *link *}http://aragorn.cz([^{}]*){ */ *}","<a href='http://www.aragorn.cz\\1' class='permalink2' target='_blank' title='Vnitřní odkaz :: http://www.aragorn.cz\\1'>http://www.aragorn.cz\\1</a>", $str);
	$str = ereg_replace("{ *link *}www.aragorn.cz([^{}]*){ */ *}","<a href='http://www.aragorn.cz\\1' class='permalink2' target='_blank' title='Vnitřní odkaz :: http://www.aragorn.cz\\1'>http://www.aragorn.cz\\1</a>", $str);
	$str = ereg_replace("{ *link *}aragorn.cz([^{}]*){ */ *}","<a href='http://www.aragorn.cz\\1' class='permalink2' target='_blank' title='Vnitřní odkaz :: http://www.aragorn.cz\\1'>http://www.aragorn.cz\\1</a>", $str);
	$str = ereg_replace("{ *link *}https://([^{}]*){ */ *}","<a href='https://\\1' class='permalink2' target='_blank' title='Externí odkaz :: https://\\1'>https://\\1</a>", $str);
	$str = ereg_replace("{ *link *}http://([^{}]*){ */ *}","<a href='http://\\1' class='permalink2' target='_blank' title='Externí odkaz :: http://\\1'>http://\\1</a>", $str);
	$str = ereg_replace("{ *link *}([^{}]*){ */ *}","<a href='http://\\1' class='permalink2' target='_blank' title='Externí odkaz :: http://\\1'>http://\\1</a>", $str);
	return $str;
}

function odhtml($text) {
	$search = array ("'([\r\n])[\s]+'","'&(quot|#34);'i","'&(lt|#60);'i","'&(gt|#62);'i","'&#039;'i","'&(amp|#38);'i");
	$replace = array ("\\1","\"","<",">","'","&");
	$text = preg_replace($search, $replace, $text);
	$text = str_replace("<!--", "&lt;!--", $text);
	$text = str_replace("-->", "--&gt;", $text);
	return $text;
}

//vyplivnuti textu (diskuze, posta...)
function spit($str, $var){
	$str = trim($str);
	if ($var > 0){ //pro diskuze apod
		$str = ereg_replace("(\r\n){2,}","\r\r", $str);
		$str = ereg_replace("(\r){2,}","\r\r", $str);
		$str = ereg_replace("(\n){2,}","\r\r", $str);
		return str_replace(array("\r","\n"), "", nl2br(stripslashes($str)));
	}else{ //zakazani blank znaku
//		$str = ereg_replace("(\r\n)+", "", $str);
		return nl2p(stripslashes($str));
	}
}

function sl($lvl,$t=1){
	if ($lvl == 2){ //bonus
		$lv = " class='star bonus$t'";
	}elseif ($lvl >= 3){ //admin
		$lv = " class='star admin$t'";
	}else{
		$lv = "";
	}
return $lv;

}

//prevedeni data do textove podoby
function sd($dm){
	return sdh($dm,false); 
}

//prevedeni data do textove podoby s hodinami a min.
function sdh($dm,$tm=true){
global $sdh_mesice,$sdh_result_h;

$sdh_resultf = explode(" ", date("j n Y H:i", $dm));
$mesic = (int)($sdh_resultf[1]);

$sdh_resultf[1] = $sdh_mesice[$mesic];


if (!$tm)
	$sdh_resultf[3]="";
else
	$sdh_resultf[3] = ' '.$sdh_resultf[3];

return "$sdh_resultf[0]. $sdh_resultf[1] $sdh_resultf[2]".$sdh_resultf[3];
}

$sdh_result_h = array();

$sdh_mesice = array (1 => "ledna",
2 => "února", 3 => "března", 4 => "dubna", 5 => "května",
6 => "června", 7 => "července", 8 => "srpna", 9 => "září",
10 => "října", 11 => "listopadu", 12 => "prosince");


//zobrazovani stars
function rating($hodnoceni, $hodnotilo){
	if ($hodnoceni > 0) $w = round($hodnoceni / $hodnotilo * 17);
	else $w = 0;

	if($w < 1) $w = "Zatím nehodnoceno";
	else $w = "<span title='hodnoceno ".$hodnotilo."&times; ~ &#216; ".round($hodnoceni / $hodnotilo,1)."' class='rating helper'><span style='width: ".$w."px'></span></span>";

	return $w;
}

//pro indikaci navstiveni diskuze
function visitedVerify($id, $sid, $tm=0,$lastid=0,$where="id"){
global $LogedIn, $time, $spojeni, $vT;

if ($tm==0) $tm=$time;

if ($where=="id" && $lastid==0) $lastid = intval($vT);
$lastid = intval($lastid);

if ($LogedIn == true){
	$iC = mysql_fetch_row( mysql_query("SELECT count(*) FROM 3_visited_$sid WHERE uid = $_SESSION[uid] AND aid = $id"));
		if ($iC[0] > 0){
			if ($sid != 4) {
				mysql_query ("UPDATE 3_visited_$sid AS v SET v.time = '$tm', v.lastid = '$lastid', v.news = (SELECT COUNT(*) FROM 3_comm_$sid AS c WHERE c.aid = '$id' AND c.id > $lastid) WHERE v.uid = $_SESSION[uid] AND v.aid = $id");
			}
			else {
				if ($_SESSION['uid'] == 2 || $_SESSION['uid'] == 1990) {
					mysql_query ("UPDATE 3_visited_$sid SET time='$tm', lastid='$lastid', news=0 WHERE uid = $_SESSION[uid] AND aid = $id");
				}
				else {
					mysql_query ("UPDATE 3_visited_$sid SET time='$tm', lastid='$lastid' WHERE uid = $_SESSION[uid] AND aid = $id");
				}
			}
		}else{
			mysql_query ("INSERT INTO 3_visited_$sid (uid, time, aid, lastid) VALUES ('$_SESSION[uid]', '$tm', '$id', '$lastid')");
		}
	}
}

//vraci cas posledni navstevy
function visitedGetTime($aid, $sid){
global $LogedIn, $spojeni;
	if ($LogedIn == true){
		$iC = mysql_fetch_row(mysql_query("SELECT time FROM 3_visited_$sid WHERE uid = $_SESSION[uid] AND aid = $aid") );
		if ($iC[0] > 0) return $iC[0];
	}
	return 0;
}

//vraci id prispevku posledni navstevy
function visitedGetId($aid, $sid){
global $LogedIn, $spojeni;
	if ($LogedIn == true){
		$iC = mysql_fetch_row( mysql_query("SELECT lastid FROM 3_visited_$sid WHERE uid = $_SESSION[uid] AND aid = $aid") );
		if ($iC[0] > 0) return $iC[0];
	}
	return 0;
}

//pocet komentaru
function getComm($aid, $sid,$override=false,$unread=0,$all=0,$vuid=0){
global $LogedIn,$spojeni,$link;

$Iid = $aid;
switch ($link) {
	case "diskuze":
		if (isset($_GET['oblast'])) {
			if (ctype_digit($_GET['oblast'])) $Iid = $Iid."&amp;o=".$_GET['oblast'];
		}
	break;
	case "herna":
		if (isset($_GET['sekce'])) {
			if ($_GET['sekce'] != "") $Iid .= "&amp;c=".$_GET['sekce'];
		}
		if (isset($_GET['podle'])) {
			if ($_GET['podle'] != "") $Iid .= "&amp;p=".$_GET['podle'];
		}
	break;
	case "clanky":
		if (isset($_GET['sekce'])) {
			if ($_GET['sekce'] != "") $Iid .= "&amp;c=".$_GET['sekce'];
		}
	break;
	case "galerie":
	break;
	default:
		$Iid .= "&amp;s=$sid";
	break;
}

if (isset($_GET['index'])) {
	if (ctype_digit($_GET['index']) && $_GET['index'] != "" && $_GET['index'] != "0") {
		$Iid .= "&amp;i=".$_GET['index'];
	}
}

$themes = array("","komentáře","komentáře","diskuzi","tuto jeskyni");
if ($sid == 4) {
	if ($override) {
		if ($LogedIn == true && $vuid > 0) {
			if($unread > 0){
				$c = "<a href='?akce=dv&amp;a=$Iid' title='Přestat sledovat $themes[$sid]'><span class='c-u'>$unread</span> / $all</a>";
			}else{
				$c = "<a href='?akce=dv&amp;a=$Iid' title='Přestat sledovat $themes[$sid]'>".$all."</a>";
			}
		}
		else {
			$c = $all;
		}
	}
	else {
		if ($LogedIn == true){
			$cS = mysql_query("SELECT count(*) FROM 3_comm_$sid WHERE aid = $aid AND (whispering = '' OR whispering IS NULL OR whispering LIKE '%#$_SESSION[uid]#%' OR uid = $_SESSION[uid])");
			$c = mysql_fetch_row($cS);
			$aS = mysql_query("SELECT time,lastid FROM 3_visited_$sid WHERE uid = $_SESSION[uid] AND aid = $aid");
			$a = mysql_fetch_row($aS);
			if ($a[0] > 0 || $a[1] > 0){
	
				if ($a[1]>0) $where = "id > $a[1]";
				else $where = "time > $a[0] AND id > $a[1]";
	
				$bS = mysql_query ("SELECT count(*) FROM 3_comm_$sid WHERE $where AND aid = $aid AND (whispering LIKE '%#$_SESSION[uid]#%' OR whispering = '' OR whispering IS NULL OR uid = $_SESSION[uid])");
				$b = mysql_fetch_row ($bS);
				if($b[0] > 0){
					$c = "<a href='?akce=dv&amp;a=$Iid' title='Přestat sledovat $themes[$sid]'><span class='c-u'>$b[0]</span> / $c[0]</a>";
				}else{
					$c = "<a href='?akce=dv&amp;a=$Iid' title='Přestat sledovat $themes[$sid]'>".$c[0]."</a>";
				}
			}else{
				$c = $c[0];
			}
		}else{
			$cS = mysql_query ("SELECT count(*) FROM 3_comm_$sid WHERE aid = $aid AND (whispering = '' OR whispering IS NULL)");
			$c = mysql_fetch_row ($cS);
			$c = $c[0];
		}
	}
}
else {
	if ($override) {
		if ($LogedIn == true && $vuid > 0) {
			if($unread > 0){
				$c = "<a href='?akce=dv&amp;a=$Iid' title='Přestat sledovat $themes[$sid]'><span class='c-u'>$unread</span> / $all</a>";
			}else{
				$c = "<a href='?akce=dv&amp;a=$Iid' title='Přestat sledovat $themes[$sid]'>".$all."</a>";
			}
		}
		else {
			$c = $all;
		}
	}
	else {
		$cS = mysql_query ("SELECT count(*) FROM 3_comm_$sid WHERE aid = $aid"); 
		$c = mysql_fetch_row ( $cS );
		if ($LogedIn == true){
			$aS = mysql_query ("SELECT time,lastid FROM 3_visited_$sid WHERE uid = $_SESSION[uid] AND aid = $aid"); 
			$a = mysql_fetch_row ( $aS );
			if ($a[0] > 0 || $a[1] > 0){
				if ($a[1]>0)
					$where = "id > $a[1]";
				else
					$where = "id > $a[1]";
	
				$bS = mysql_query ("SELECT count(*) FROM 3_comm_$sid WHERE $where AND aid = $aid"); 
				$b = mysql_fetch_row ($bS);
				if($b[0] > 0){
					$c = "<a href='?akce=dv&amp;a=$Iid' title='Přestat sledovat $themes[$sid]'><span class='c-u'>$b[0]</span> / $c[0]</a>";
				}else{
					$c = "<a href='?akce=dv&amp;a=$Iid' title='Přestat sledovat $themes[$sid]'>".$c[0]."</a>";
				}
			}else{
				$c = $c[0];
			}
		}else{
			$c = $c[0];
		}
	}
}
return $c;
}


function getHighlight2($count, $visited){

			if($visited > 0){
				$c = "<span class='c-u'>$visited</span> / $count";
			}else{
				$c = $count;
			}

return $c;
}

function getHighlight($count){

		if($count > 0){
			$c = "<span class='c-ub'>($count)</span>";
		}else{
			$c = "";
		}

return $c;
}

//checkovani zalozek
function chBook(){
global $LogedIn, $link, $slink, $id, $sid, $spojeni;
$string = "";
	if ($LogedIn == true){
		$bS = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_visited_$sid WHERE uid = $_SESSION[uid] AND aid = $id AND bookmark = '1'")); 
		if ($bS[0] > 0){
			$string = "<span class='hide'> | </span><a href='/$link/$slink/?akce=rem-bookmark' title='Odebrat záložku' class='permalink'>Odebrat záložku</a>";
		}else{
			$string = "<span class='hide'> | </span><a href='/$link/$slink/?akce=add-bookmark' title='Vytvořit záložku' class='permalink'>Vytvořit záložku</a>";
		}
	}
	return $string;
}

//odeslani system posty
function sysPost($komu, $text){
	global $AragornCache;

	$time = time();
	$r = 0;
	$hash = addslashes(md5($text));
	$sql = "";
	$messId = 0;
	mysql_query("LOCK TABLES 3_post_text WRITE, 3_post_new WRITE");
	$jeHashS = mysql_query("SELECT id,content FROM 3_post_text WHERE hash = '$hash' ORDER BY id ASC");
	if ($jeHashS && mysql_num_rows($jeHashS)>0){
		while($jeHash = mysql_fetch_row($jeHashS)) {
			if ($jeHash[1] == $text) {
				$messId = $jeHash[0];
				break;
			}
		}
	}
	if ($messId == 0) {
		$text = addslashes($text);
		mysql_query("INSERT INTO 3_post_text (content, hash) VALUES ('$text','$hash')");
		$messId = mysql_insert_id();
	}
	if (is_array($komu)) {
		$sqlI = array();
		for ($i=0;$i<count($komu);$i++) {
			$sqlI[] = "('$messId','$komu[$i]','0','3','0','$time')";
			if (isset($AragornCache)) {
				$AragornCache->delVal("post-unread:$komu[$i]");
			}
		}
		mysql_query('INSERT INTO 3_post_new (mid,tid,fid,stavfrom,stavto,cas) VALUES '.join(',',$sqlI));
		$r = mysql_affected_rows();
	}
	else {
		mysql_query("INSERT INTO 3_post_new (mid, tid, fid, stavfrom, stavto, cas) VALUES ('$messId', '$komu', '0', '3', '0', '$time')");
		$r = mysql_affected_rows();
		if (isset($AragornCache)) {
			$AragornCache->delVal("post-unread:$komu");
		}
	}
	mysql_query("UNLOCK TABLES");
	return $r;
}

function hmac_md5($key, $data) {
	$blocksize = 64;
	if (strlen($key) > $blocksize) {
		$key = pack("H*", md5($key));
	}
	$key = str_pad($key, $blocksize, chr(0x00));
	$k_ipad = $key ^ str_repeat(chr(0x36), $blocksize);
	$k_opad = $key ^ str_repeat(chr(0x5c), $blocksize);
	return md5($k_opad . pack("H*", md5($k_ipad . $data)));
}

//odpocet
function countdown($navrat){
$navrat = date("i:s", $navrat);
$navrat = explode (":", $navrat);

if ($navrat[0][0] == 0){
	$min = $navrat[0][1];
}else{
	$min = $navrat[0];
}
if ($navrat[1][0]==0){
	$sec = $navrat[1][1];
}else{
	$sec = $navrat[1];
}

return "$min min. a $sec sec.";
}

//ajax vlozeni zpravy
function ajaxChatInsert($mes, $fid, $fname, $tid, $tname, $room, $color, $time, $admin, $sys){

	//serializace rozmrda diakritiku u UTF, takze nutny zakodovat
	if ($admin==false){
		$mes = _htmlspec($mes);
	}
	if ($sys==false) {
		include "../chat/smileyadd.php";
		$mes = addsmileys($mes,$admin);
	}
	$text = base64_encode($mes);

	//a tady uz serializujeme, homies
	$serialized = serialize(array("fname" => base64_encode($fname), "tname" => base64_encode($tname),
"type" => 0, "special" => 0, "color" => $color, "text" => $text));

		mysql_query ("INSERT INTO 3_ajax_chat (room, fid, tid, time, serialized) VALUES ($room, $fid, $tid, $time, '$serialized')");

}

//zpravu vlozi system
function ajaxChatInsertSystem($text, $rid, $name = "Systém"){
global $time;
	ajaxChatInsert($text, 0, $name, 0, 0, $rid, 'white', $time, true, true);

}

//zpravu vlozi system
function ajaxChatInsertSystemWhisper($text, $rid, $tid, $tname){
global $time;

	ajaxChatInsert($text, 0, 'Systém', $tid, $tname, $rid, 'white', $time, true, true);

}

//refreshne usery v zahlavi ajax chatu
function ajaxRefreshOccupants($id, $mode){
global $time;
	$aU = mysql_query("SELECT u.login, c.uid, u.ico, c.timestamp FROM 3_users AS u, 3_chat_users AS c WHERE u.id = c.uid AND c.rid = '$id' AND c.odesel = '0' ORDER BY u.login_rew ASC");
	$ret = "";
if ($mode < 1){
	$ret .= "<aj>\n";
}
	$a = array();
	while ($oU = mysql_fetch_object($aU)){
		if ($mode > 0){
		 $a[] = _htmlspec($oU->login);
		 $ret .= "<img src=\"http://s1.aragorn.cz/i/$oU->ico\" height=\"35\" width=\"35\" class=\"oI\" alt=\"ikonka ~ "._htmlspec($oU->login)."\" title=\"ikonka ~ "._htmlspec($oU->login)."\" onmouseover=\"ddrivetip('<img src=\'http://s1.aragorn.cz/i/$oU->ico\' alt=\'".addslashes($oU->login)."\' style=\'padding: 3px\' /><div class=\'dhtmlDiv\'>".addslashes($oU->login)."<br />".countdown($time-($oU->timestamp))."</div>');\" onmouseout='hidedrivetip();' onclick='whisperTo(\"".addslashes($oU->login)."\");' />";
		}else{
		 $ret .= "<oc i='$oU->ico' uid='$oU->uid' t='".countdown($time-$oU->timestamp)."'><![CDATA[$oU->login]]></oc>\n";
		}
	}

if ($mode < 1){
	$ret .= "</aj>\n";
}
else {
	$ret .= "\n		<script type='text/javascript'>var G_occupants = new Array('".join("','",$a)."');</script>\n";
}

	return $ret;
}

function write_advert(){
	$time = time();
	$counter = 0;
	$lastTime = $time-60*10;
	$aU = mysql_query("SELECT * FROM 3_chat_advert WHERE active = 1 AND last < $lastTime ORDER BY last ASC");
	while ($oU = mysql_fetch_object($aU)){
		$naposledy = ($time-($oU->cykle * 60));
		$counter++;
		if ($oU->last <= $naposledy){
			mysql_query ("UPDATE 3_chat_advert SET last = ".time()." WHERE id = ".$oU->id);
			$rid = 1;
			ajaxChatInsertSystem($oU->text, $rid);
			break;
		}
	}
}

function getUserIP() {
	$ip = array();
	if (getenv("HTTP_X_REAL_IP") != "") {
		$ip[] = getenv("HTTP_X_REAL_IP");
		$ip[] = gethostbyaddr($ip[0]);
	}
	if (getenv("HTTP_CLIENT_IP") != "" && count($ip) < 1) {
		$ip[] = getenv("HTTP_CLIENT_IP");
		$ip[] = gethostbyaddr(getenv("HTTP_CLIENT_IP"));
	}
	if (getenv("HTTP_X_FORWARDED_FOR") != "" && count($ip) < 1) {
		$ip[] = getenv("HTTP_X_FORWARDED_FOR");
		$ip[] = gethostbyaddr(getenv("REMOTE_ADDR"));
	}
	if (getenv("REMOTE_ADDR") != "" && count($ip) < 1) {
		$ip[] = getenv("REMOTE_ADDR");
	}
	$ip = array_unique($ip);
	$ip = array_filter($ip);
	$ip = join("@", $ip);
	return $ip;
}

function addOneVisited($sid,$aid,$whis=false){
	if ($sid != "4") {
		mysql_query("UPDATE 3_visited_$sid SET news = news+1 WHERE aid = $aid");
	}
	else recountVisited($sid,$aid,$whis);
}

function removeManyVisited($sid,$aid,$howMuch=1,$whis=false){
	if ($sid != "4") {
		mysql_query("UPDATE 3_visited_$sid SET news = news-$howMuch WHERE aid = $aid");
	}
	else recountVisited($sid,$aid);
}

function recountVisited($sid,$aid,$whis=false,$users=false){
	if ($whis) {
		$whis = join(",", explode('#', trim($whis, ' #')));
		mysql_query("UPDATE 3_visited_4 AS v SET v.news = v.news+1 WHERE v.uid IN ($whis) AND v.aid = '$aid'");
	}
	else {
		if ($sid != "4") {
			mysql_query("UPDATE 3_visited_$sid AS v SET v.news = (SELECT COUNT(*) FROM 3_comm_$sid AS c WHERE c.aid = '$aid' AND c.id > v.lastid) WHERE v.aid = '$aid'");
		}
		else {
			mysql_query("UPDATE 3_visited_4 AS v SET v.news = v.news+1 WHERE v.aid = '$aid'");
		}
	}
}

function roz_types($t) {

	$a = array('Fantasy'=>'Fantasy','Sci-fi'=>'Sci-fi',0=>'0',1=>'1','0'=>'0','1'=>'Fantasy','2'=>'Sci-fi');

	if (isset($a[$t]))
		return $a[$t];

	return $a['Fantasy'];
}

//echo "ok";

function _check_num($r,$x){
	$r = str_rot13($r);
	$r = base_convert($r,35,10);
	if(strlen($r)>(2*strlen($x)))return true;
	return false;
}

function _encode_num($r,$x){
	$n = mt_rand(pow(10,strlen($x)-1),pow(10,strlen($x))-1);
	$r = base_convert($n.$r.$n,10,35);
	$r = str_rot13($r);
	return $r;
}

function _decode_num($r,$x){
	$r = str_rot13($r);
	$r = base_convert($r,35,10);
	$r = substr($r, strlen($x), -strlen($x));
	return $r;
}

function _postolka_read($n){
	global $AragornCache;
	@mysql_query("UPDATE 3_post_new SET stavto='1' WHERE stavto!='3' AND id='$n->id' AND tid='$_SESSION[uid]'");

	$AragornCache->delVal("post-unread:$_SESSION[uid]");

	if ($n->parent > 0) {
	  $parentS = mysql_query("SELECT * FROM 3_post_new WHERE id='$n->parent'");
	  if ($parentS && mysql_num_rows($parentS)>0) {
			$parent = mysql_fetch_object($parentS);
			mysql_free_result($parentS);
			if ($parent->whis != "") {
				$users = explode(",",$parent->whis);
				$pozice = array_search($_SESSION['uid'], $users);
				$whisNew = substr_replace($parent->whisstav,'1',$pozice,1);
				mysql_query("UPDATE 3_post_new SET whisstav='$whisNew' WHERE id='$n->parent'");
			}
		}
	}
}

function remove_HTML($s , $keep = '' , $expand = 'script|style|noframes|select|option'){
	/**///prep the string
	$s = ' ' . $s;
	$k = array();
	/**///initialize keep tag logic
	if(strlen($keep) > 0){
		$k = explode('|',$keep);
		for($i=0,$j=count($k);$i<$j;$i++){
			$s = str_ireplace('<' . $k[$i],'[[[{(' . $k[$i],$s);
			$s = str_ireplace('</' . $k[$i],'[[[{(/' . $k[$i],$s);
		}
	}
	
	//begin removal
	/**///remove comment blocks
	while(stripos($s,'<!--') > 0){
		$pos[1] = stripos($s,'<!--');
		$pos[2] = stripos($s,'-->', $pos[1]);
		$len[1] = $pos[2] - $pos[1] + 3;
		$x = substr($s,$pos[1],$len[1]);
		$s = str_ireplace($x,'',$s);
	}
	
	/**///remove tags with content between them
	if(strlen($expand) > 0){
		$e = explode('|',$expand);
		for($i=0,$j=count($e);$i<$j;$i++){
			while(stripos($s,'<' . $e[$i]) > 0){
				$len[1] = strlen('<' . $e[$i]);
				$pos[1] = stripos($s,'<' . $e[$i]);
				$pos[2] = stripos($s,$e[$i] . '>', $pos[1] + $len[1]);
				$len[2] = $pos[2] - $pos[1] + $len[1];
				$x = substr($s,$pos[1],$len[2]);
				$s = str_ireplace($x,'',$s);
			}
		}
	}
	
	/**///remove remaining tags
	while(stripos($s,'<') > 0){
		$pos[1] = stripos($s,'<');
		$pos[2] = stripos($s,'>', $pos[1]);
		$len[1] = $pos[2] - $pos[1] + 1;
		$x = substr($s,$pos[1],$len[1]);
		$s = str_ireplace($x,'',$s);
	}
	
	/**///finalize keep tag
	for($i=0,$j=count($k);$i<$j;$i++){
		$s = str_ireplace('[[[{(' . $k[$i],'<' . $k[$i],$s);
		$s = str_ireplace('[[[{(/' . $k[$i],'</' . $k[$i],$s);
	}
	
	return trim($s);
}

function strip_only($str, $tags) {
	if(!is_array($tags)) {
		$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
		if(end($tags) == '') array_pop($tags);
	}
	foreach($tags as $tag) $str = preg_replace('#</?'.$tag.'[^>]*>#is', '', $str);
	return $str;
}
?>