<?php

$menuLinks['ankety'] = "Ankety";
$requireRights['ankety'] = true;

function ankety_head($rub) {
	global $menuLinks;
	Header ("Location: /rs/$rub/");
	exit;
}

function ankety_body($rub) {

	$anketa = 0;

	if (!isSet($_GET['index'])){
	  $index = 1;
	}else{
	  $index = (int) ($_GET['index']);
	}

	$from = ($index - 1) * ARTICLE_COUNT; //od kolikate polozky zobrazit

	if (!isset($_GET['diskuze'])) {
		$_GET['diskuze'] = 0;
	}
	if ($_GET['diskuze'] !== 0) {
		$diskuze = addslashes($_GET['diskuze']);
		$moznosti = array();
		$jeAnketa = mysql_query("SELECT a.*, t.nazev AS diskuzenazev FROM 3_ankety AS a, 3_diskuze_topics AS t WHERE a.dis = t.id AND t.nazev_rew = '$diskuze' ORDER BY a.id DESC LIMIT 1");
		if (mysql_num_rows($jeAnketa) > 0) {
			$anketaO = mysql_fetch_object($jeAnketa);
			if ($anketaO->aktiv == 0) {
				$anketa = -1;
			}
			else {
				$anketa = $anketaO->id;
				mysql_free_result($jeAnketa);
				$moznosti = explode(">", $anketaO->odpoved);
				$hlasy = array();
				foreach ($moznosti as $k=>$val) {
					$hlasy[$k] = array();
				}
			}
		}
	}

	if ($anketa > 0) {
		echo "<h2>Jak kdo hlasoval v aktuálně aktivní anketě diskuze $anketaO->diskuzenazev</h2>
		<p>Otázka: <strong>$anketaO->otazka</strong></p>\n";

		$pocty = mysql_query("SELECT u.login, a.hlas, u.ip FROM 3_ankety_data AS a LEFT JOIN 3_users AS u ON u.id = a.uid WHERE a.ank_id = '$anketa' ORDER BY 1 ASC");
		$ips = $numberToIp = array();
		$hlasyAll = 0;
		$c = 0;
		if (mysql_num_rows($pocty)>0) {
			while ($hlasOne = mysql_fetch_object($pocty)) {
				$hlasy[$hlasOne->hlas][] = $hlasOne->login . ' ... ' . $hlasOne->ip;
				$numberToIp[$c] = $hlasOne->ip;
				if (isset($ips[$hlasOne->ip])) {
					$ips[$hlasOne->ip]++;
				}
				else {
					$ips[$hlasOne->ip] = 1;
				}
				$c++;
			}
			mysql_free_result($pocty);
			echo "<table width='80%'>\n";
			for ($i=0,$count=count($hlasy);$i<$count;$i++) {
				$q = $hlasy[$i];
				foreach($q as $k => $v) {
					if ($ips[$numberToIp[$k]] > 1) {
						$hlasy[$i][$k] .= ' <b>multi</b>';
					}
				}
				echo "<tr><td width='40%'><b>$moznosti[$i]</b></td><td>";
				if (count($hlasy[$i]) > 0) {
					echo join(", ",$hlasy[$i]);
				}
				else {
					echo "<em>nikdo nehlasoval</em>\n";
				}
				echo "</td></tr>\n";
			}
			echo "</table>\n";
		}
		else {
			echo "<p class='error'>V této anketě ještě nikdo nehlasoval.</p>\n";
		}

		echo "<p><a href='/rs/ankety/'>Zpět na výpis</a></p>\n";

	}
	elseif ($anketa == -1) {
		echo "<h2>V diskuzi <em>$anketaO->diskuzenazev</em> není aktivní anketa. Žádné průběžné náhledy či výsledky.</h2>";
	}
	else {
		$artc = array_shift(mysql_fetch_row(mysql_query("SELECT count(t.id) FROM 3_ankety AS a, 3_diskuze_topics AS t WHERE t.id = a.dis AND a.aktiv = '1'")));

		if ($artc > 0) {

		  echo "<p>".make_pages($artc, ARTICLE_COUNT, $index)."</p>";

			$i = 1;
			$aktivniAnkety = mysql_query("SELECT t.nazev_rew, t.nazev, a.otazka FROM 3_ankety AS a, 3_diskuze_topics AS t WHERE t.id = a.dis AND a.aktiv = '1' ORDER BY 1 ASC LIMIT $from, " . ARTICLE_COUNT);

			if (mysql_num_rows($aktivniAnkety) > 0) {
				echo "<table class='list autolayout'><thead><tr><th width='60%'>Diskuze</th><th>Otázka</th></tr></thead><tbody>\n";
				while ($aktivniAnketa = mysql_fetch_row($aktivniAnkety)) {
				  echo "<tr class='bg".(($i % 2 == 0)? 1 : 2)."'>\n";
					echo "<td><a href='/diskuze/$aktivniAnketa[0]/'>$aktivniAnketa[1]</a></td>\n";
					echo "<td><a href='?diskuze=$aktivniAnketa[0]'>$aktivniAnketa[2]</a></td>";
					echo "</tr>";
					$i++;
				}
				echo "</tbody></table>\n";
			}

		  echo "<p>".make_pages($count, ARTICLE_COUNT, $index)."</p>";
		}
		else {
			
		}
	}

// END BODY FUNC
}
?>
