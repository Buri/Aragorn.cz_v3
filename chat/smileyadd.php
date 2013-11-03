<?php

mb_internal_encoding("UTF-8");

function addsmileys($text,$isadmin=false) {
$cnt = 0;
$smile = array(

":\)"=>"smile.gif",
":o\)"=>"smile.gif",
":-\)"=>"smile.gif",

"=\)"=>"smile2.gif",
"=-\)"=>"smile2.gif",
"=o\)"=>"smile2.gif",

":angry:"=>"angry.gif",
"\]:O"=>"angry.gif",
"\]:-O"=>"angry.gif",

":oops:"=>"ashamed.gif",

":D"=>"grin.gif",
":-D"=>"grin.gif",
"=D"=>"grin.gif",

":cry:"=>"cry.gif",
":,\("=>"cry.gif",
":,\|"=>"cry.gif",
":.\|"=>"cry.gif",

":-\/"=>"dontknow.gif",
":\/"=>"dontknow.gif",

":x"=>"quiet.gif",
":-X"=>"quiet.gif",

":censored:"=>"censored.gif",

":\("=>"sad.gif",
":-\("=>"sad.gif",
":-\["=>"sad.gif",

":P"=>"tongue.gif",
":-P"=>"tongue.gif",
":oP"=>"tongue.gif",

":wow:"=>"wtf.gif",
":wtf:"=>"wtf.gif",
"8-O"=>"wtf.gif",
":-O"=>"wtf.gif",
":O"=>"wtf.gif",

":tongue:"=>"tongue2.gif",
"\]:P"=>"tongue2.gif",
"\]:-P"=>"tongue2.gif",

";\)"=>"wink.gif",
";-\)"=>"wink.gif",
";D"=>"wink.gif",
";D"=>"wink.gif",

":green:"=>"green.gif",
":red:"=>"red.gif",
":blue:"=>"blue.gif",

":vamp:"=>"vamp.gif",
":vampire:"=>"vamp.gif",
":twisted:"=>"twisted.gif",
":mad:"=>"ai.gif",

":yes:"=>"yes.gif",
":ok:"=>"yes.gif",
":no:"=>"no.gif",
":nope:"=>"no.gif",

":sun:"=>"sun.gif",				/* SPECIALities 4 ALL  */
":moon:"=>"moon.gif",
":duck:"=>"duck.gif",
":angel:"=>"angel.gif",
"O:-\)"=>"angel.gif",
"O:-D"=>"angel.gif",
":redstar:"=>"red-star.gif",
":flower:"=>"flower.gif",
":flower2:"=>"flower2.gif",
":kiss:"=>"kiss.gif",
":coffee:"=>"coffee.gif",
":coffee2:"=>"coffee2.gif",
":touchme:"=>"vetvicka.gif",
":cccp:"=>"cccp.gif",
":czech:"=>"czech.gif",
":naistar:"=>"star.gif",
":bighug:"=>"hug2.gif",
":hug:"=>"hug.gif"

);

$smile_plus = array(		/* ADMINS ONLY */
":metal4ever:"=>"metal.gif",
":kill:"=>"kill.gif",
":selfkill:"=>"self_killer.gif",
":bagr4gran:"=>"bagr2.gif",
":wall:"=>"wall.gif",
":regretful:"=>"regretful.gif",
":mash:"=>"helicopter.png",
":4077:"=>"helicopter.png",
":shot4admin:"=>"kill.gif",
":car:"=>"car2.gif",
":radio:"=>"radio.gif",
":auto:"=>"car2.gif",
":spank:"=>"spank.gif",
":guitar:"=>"guitar.gif",
":radio:"=>"radio.gif",
":tank:"=>"tank.gif",
":rabbit:"=>"rabbit.gif",
":witch:"=>"witch.gif",
":doctorwho:"=>"dw.gif",
);

while (list($title,$src)=each($smile)){
	$src_final = "<img alt='".$src."' src='/chat/smile/".$src."' /> ";
	if ($cnt>3) {
		break;
		$text = preg_replace("#\*$title\*#ui",stripslashes($title),$text);
	}
	else {
		$textX = $text;
		$text = preg_replace("#\*$title\*#ui",$src_final,$text,3);
		$text = preg_replace("#\*$title\*#ui",stripslashes($title),$text);
		if ($text !== $textX) {
			$cnt++;
		}
	}
}

if ($isadmin) {
	while (list($title,$src)=each($smile_plus)){
		$src_final = "<img alt='".$src."' src='/chat/smile/".$src."' /> ";
		if ($cnt>3) {
			break;
		}
		else {
			$textX = $text;
			$text = preg_replace("#\*$title\*#ui",$src_final,$text,3);
			$text = preg_replace("#\*$title\*#ui","",$text);
			if ($text !== $textX) {
				$cnt++;
			}
		}
	}
}

$text = preg_replace('#(http://|https://|ftp://|(www\.))([\w\-]*\.[\w\-\.]*([/?][^\s]*)?)#e',"'<a target=\"_blank\" title=\"\\2\\3\" href=\"'.('\\1'=='www.'?'http://':'\\1').'\\2\\3\">'.((mb_strlen('\\2\\3')>43)?(mb_substr('\\2\\3',0,40).'&hellip;'):'\\2\\3').'</a>'",$text);

return $text;
}

?>
