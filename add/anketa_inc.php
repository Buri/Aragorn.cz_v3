<?php
$diskuzeID = $dItem->id;
$jeAnketa = mysql_query("SELECT * FROM 3_ankety WHERE dis = $diskuzeID AND aktiv = 1");
if (mysql_num_rows($jeAnketa) != "1") {
}
else {
	echo "<tr><td colspan='2'><div>Anketa:</div>\n";
	$moznosti = array();
	$anketa = mysql_fetch_object($jeAnketa);
	mysql_free_result($jeAnketa);
	$moznosti = explode(">", stripslashes($anketa->odpoved));
	
echo "
<script type='text/javascript'>
function anketa_hlasovat(hlas,ida) {
	if (!send_xmlhttprequest(anketa_obsluha, 'GET', '/anketa_jsx.php?anketa='+ida+'&volba=' + hlas)) { return false; }
	document.getElementById('pocet' + hlas).innerHTML++;
	for (var key in document.getElementById('anketa').getElementsByTagName('td')) {
		var val = document.getElementById('anketa').getElementsByTagName('td')[key];
		if (val.className == 'odpoved') { val.innerHTML = val.firstChild.innerHTML; } 
	}
	document.getElementById('stav-anketa').innerHTML = 'Ukládá se.';
	return true;
}
function anketa_obsluha(xmlhttp) {
	if (xmlhttp.readyState == 4) {
		var odpovedi = xmlhttp.responseXML.getElementsByTagName('odpoved');
		for (var i=0; i < odpovedi.length; i++) {
			document.getElementById(odpovedi[i].getAttribute('id')).innerHTML = odpovedi[i].firstChild.data;
		}
		document.getElementById('stav-anketa').innerHTML = 'Hlasování uloženo.';
		document.getElementById('hlasovalo-anketa').innerHTML = xmlhttp.responseXML.getElementsByTagName('odpovedelo')[0].firstChild.data;
 	}
}
</script>
";

	echo "<table id='anketa' class='anketa text' cellspacing='0' cellpadding='0'>
	<tr><td colspan='2' class='otazka'>$anketa->otazka</td></tr>\n";
	$pocty = mysql_query("SELECT count(*) AS pocet, hlas FROM 3_ankety_data WHERE ank_id = '$anketa->id' GROUP BY hlas ORDER BY hlas ASC");
	$hlasy = array_fill(0, count($moznosti), "0");
	$hlasyAll = 0;
	if (mysql_num_rows($pocty)>0) {
		while ($hlasOne = mysql_fetch_object($pocty)) {
			$hlasy[$hlasOne->hlas] = $hlasOne->pocet;
			$hlasyAll = $hlasyAll + $hlasOne->pocet;
		}
	}
	$hlasoval = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_ankety_data WHERE uid = '$_SESSION[uid]' AND ank_id = $anketa->id"));
	for ($i=0;$i<count($moznosti);$i++) {
		$odpoved = $moznosti[$i];
		$procenta = (($hlasyAll > 0) ? round($hlasy[$i]/$hlasyAll*100,1) : 0);
		if ($hlasoval[0]==0 && $LogedIn && $AllowedTo != "nothing" && !$_SESSION['novacek']) {
			$odpoved = "<a href='?akce=anketa-hlasovat&amp;anketa=$anketa->id&amp;volba=$i' onclick='return !anketa_hlasovat($i,$anketa->id);'>$odpoved</a>";
		}
		echo "<tr><td class='odpoved'>$odpoved</td><td id='pocet$i'>$hlasy[$i]</td></tr>\n";
	}
	$konc = "ů";
	if ($hlasyAll == 1) {
		$konc = "";
	}
	elseif ($hlasyAll > 1 && $hlasyAll < 5) {
		$konc = "y";
	}
	if ($LogedIn && $AllowedTo != "nothing") {
		echo "<tr class='anketa-bottom'><td><span id='stav-anketa'>".($hlasoval[0]>0?"Váš hlas již byl zaznamenán.":($_SESSION['novacek'] ? 'Hlasovat lze jen s účtem starším 14 dní.':"Hlasujte"))."</span></td><td><span id='hlasovalo-anketa'>".$hlasyAll." hlas".$konc."</span> celkem</td></tr>\n";
	}
	else {
		echo "<tr class='anketa-bottom'><td colspan='2'><span id='hlasovalo-anketa'>".$hlasyAll."</span> celkem</td></tr>\n";
	}
	echo "</table>
	</td></tr>\n";
}
?>
