<h2 class='h2-head'><a href='/admins/' title='Administrátoři Aragorn.cz'><?php echo $itIsApril ? "Těžkooděnci" : "Admins a spol."; ?></a></h2>
<h3><a href='/admins/' title='Administrátoři a další lidé s funkcí na Aragorn.cz'>Výpis podle oboru funkcí</a></h3>
<?php

$admins = mysql_query ("SELECT login_rew, ico, timestamp, id, login FROM 3_users WHERE level > 2 OR id IN(2, 1896, 6085, 4, 5491, 5163, 1192, 3048, 384, 226, 25759) ORDER BY id");
$onOff = $ico = $userId2LoginRew = $userId2Login = array();

while ($aI = mysql_fetch_object($admins)){
  $onOff[$aI->login_rew] = $aI->timestamp;
  $ico[$aI->login_rew] = $aI->ico;
  $userId2LoginRew[$aI->id] = $aI->login_rew;
  $userId2Login[$aI->id] = $aI->login;
}

function postuj($t,$t_r,$pov) {
	global $ico,$onOff;
	if ($onOff[$t_r] > 0){
		$stav = "online";
	}
	else $stav = "offline";
	echo "<tr><td><a href='/uzivatele/$t_r/' title='$t &raquo; profil'>$t</a></td><td><a href='/uzivatele/$t_r/' title='Profil $t'><img src='http://s1.aragorn.cz/i/".$ico[$t_r]."' title='$t &raquo; profil' alt='$t &raquo; ikonka' /></a></td><td>".$pov."</td><td><span class='$stav'></span></td></tr>\n";
}

?>
<table width='100%' class='tb' cellspacing='0'>
<tr><td colspan="4"><p class="text"><strong>Design / Vývoj / Programování / Bonusy</strong></p></td></tr>
<?php
postuj("ixiik", "ixiik", "Vize Aragorn.cz<br />Admin chatu<br />Bonusy");
postuj("Buri the Great", "buri-the-great", "Programování A4<br />Chat");
?>
<tr><td colspan="4"><p class="text"><strong>Diskuze / Herna / Chat</strong></p></td></tr>
<?php
postuj("Dart", "dart", "Diskuze<br />PJ-Master,<br />Herna,<br /> Výuka PJů");
postuj("Layla", "layla", "Schvalování a mazání jeskyní");
postuj("sashiko", "sashiko", "Schvalování a mazání jeskyní");
?>
<tr><td colspan="4"><p class="text"><strong>Redaktoři</strong></p></td></tr>
<?php
postuj("Amthauer", "amthauer", "Poezie, Próza<br /> Děvče na všechno :-)");
postuj("Nefrete", "nefrete", "Próza<br /> Rozhovory");
postuj("rafaela", "rafaela", "Poezie, Próza");
?>
<tr><td colspan="4"><p class="text"><strong>Galerie</strong></p></td></tr>
<?php
postuj("Grom", "grom", "Galerie");
postuj("jola", "jola", "Galerie");
postuj("Niam", "niam", "Galerie<br /> Gumová kachnička");
?>
<tr><td colspan="4"><p class="text"><strong>Všehochuť</strong></p></td></tr>
<?php
postuj('Enca', 'enca', 'Chat');
postuj("Scout", "scout", "Event&nbsp;manager<br />Pořadatel Ara-Ofi-Akcí");
?>

<tr><td colspan="4"><p class="text">Doktorát z administrace na Aragorn.cz = <strong>RIP bývalí admini</strong></p></td></tr>
<?php
postuj("apophis", "apophis", "Neadmin<br />Programování, Vývoj");
postuj($userId2Login[6085], $userId2LoginRew[6085], "Neadmin<br /> bývalý programátor<br /> Rozvoj metalu a tvrdé hudby");
postuj("Gran", "gran", "Neadmin<br /> Pořadatel AraLARPů");
postuj("Laethé", "laethe", "Neadminka<br />Povídky, Próza<br /> Články všeobecně");
postuj("Annox", "annox", "Neadmin<br />Poezie, Povídky");
postuj("Mikymauz", "mikymauz", "Neadmin<br />Chat");
postuj("Yakaman", "yakaman", "Neadmin<br />Próza");
postuj("jilm", "jilm", "Neadmin<br />Vize Aragorn.cz<br />Manažer");
postuj("hater", "hater", "Neadmin<br />Redaktor a budižkničemu");
postuj("Indyján", "indyjan", "Neadmin<br />Herna");
postuj("Saltzhornia", "saltzhornia", "Neadmin<br />Diskuze a Herna");

?>
</table>
