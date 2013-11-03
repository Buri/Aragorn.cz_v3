<?php

$time = time();
$ok = false;
$PravaMax = false;
$microOne = (float)microtime();

mb_internal_encoding("UTF-8");

//cesta
$inc = "http://".$_SERVER['HTTP_HOST'];
$zalozkyOmezeniCount = 20;

require "../db/conn.php"; //spojeni s db

if (!function_exists("_htmlspec")) { /* NEMAM SPOJENI */
	function _htmlspec($a) {
	  return htmlspecialchars($a, ENT_QUOTES, "UTF-8");
	}
	echo "<style type='text/css'>body{margin:0 !important;}</style><big style='z-index: 50000; font-size: 200%; width: 100%; color: red; background-color: black; padding: 10px 0; position: absolute; top: 50px; left:0 display: block; text-align: center'>Aragorn.cz se nepripojil k DB a proto neni funkcni!</big>";
}
else { /* ALL NORMAL */
	//start ses. - musi byt az po ob_start()
	session_set_cookie_params(3600);
	session_start();
	require "../add/funkce.php";	//vlozeni fci
	require "../add/rewrite.php";	//vlozeni rewrite prevodu
	require "../add/auth.php";	//autorizace
	require "../add/check_for_ban.php";	//ban?
	if (isSet($_GET['akce'])){
	  include "../akce/akce.php";	//akce
	}
}

if (isset($_GET['akce']) && $LogedIn && isset($_SESSION['login']) && ($_SESSION['login'] == "apophis" || $_SESSION['login'] == "Saltzhornia")) {
	if($_SESSION['lvl']>=3||$_SESSION['login']=="apophis") $PravaMax = true;
	if ($_GET['akce'] == "kalendar-insert") {
		$weekSql = $weekSql2 = "";
		$cs = explode(":",$_POST['novy_cas']);
		if (count($cs)==1) $cs = explode("-",$_POST['novy_cas']);
		if (count($cs)==1) $cs = explode("/",$_POST['novy_cas']);
		if (count($cs)==2) $cs[2] = 0;
		switch($_POST['tydne_typ']) {
			case "1":
			case "2":
				$dt = explode("/",$_POST['nove_datum']);
				if (count($dt)==1) $dt = explode(".",$_POST['nove_datum']);
				if (count($dt)==1) $dt = explode("-",$_POST['nove_datum']);
				if (count($dt)==3 && count($cs)==3 && $_POST['nazev_akce'] != "" && $_POST['popis_akce'] != "") {
					if ($cs[2]>=0 && $cs[2]<=59 && $cs[1]>=0 && $cs[1]<=59 && $cs[0]>=0 && $cs[0]<=23 && ctype_digit($cs[2]) && ctype_digit($cs[1]) && ctype_digit($cs[0]) && ctype_digit($dt[0]) && ctype_digit($dt[1]) && ctype_digit($dt[2]) && $dt[2]>1970 && $dt[1] > 0 && $dt[1] <= 12 && $dt[0] > 0 && $dt[0] <= 31 && $dt[2] < date("Y")+2) {
						if ($dt[0] <= date("t",mktime(1,1,1,$dt[1],5,$dt[2]))) {
							$tm = mktime($cs[0],$cs[1],$cs[2],$dt[1],$dt[0],$dt[2]);
							$weekSql = ", weekly"; $weekSql2 = ", '".$_POST['tydne_typ']."'";
							$ok = true;
						}
					}
				}
			break;
			default:
				if (ctype_digit($_POST['mesicne']) && $_POST['mesicne'] > 0 && $_POST['mesicne'] <= 31) {
					if (count($cs) == 3 && $_POST['nazev_akce'] != "" && $_POST['popis_akce'] != "" && $cs[2]>=0 && $cs[2]<=59 && $cs[1]>=0 && $cs[1]<=59 && $cs[0]>=0 && $cs[0]<=23 && ctype_digit($cs[2]) && ctype_digit($cs[1]) && ctype_digit($cs[0])) {
						$tm = mktime($cs[0],$cs[1],$cs[2]);
						$weekSql = ", monthly"; $weekSql2 = ", '".$_POST['mesicne']."'";
						$ok = true;
					}
				}
				elseif (isset($_POST['rocne']) && $PravaMax) {
					$yy = explode("/",$_POST['rocne']);
					if (count($yy)==1) $yy = explode(".",$_POST['rocne']);
					if (count($yy)==1) $yy = explode("-",$_POST['rocne']);
					if (count($yy)==2 && $yy[0]>0 && $yy[0]<=31 && $yy[1]>0 && $yy[1]<=12 && ctype_digit($yy[0]) && ctype_digit($yy[1])) {
						if ($yy[0]<10) $yy = $yy[1]."0".$yy[0];
						else $yy = $yy[1]."".$yy[0];
						$tm = mktime($cs[0],$cs[1],$cs[2]);
						$weekSql = ", yearly"; $weekSql2 = ", '".addslashes($yy)."'";
						$ok = true;
					}
				}
				else {
					$dt = explode("/",$_POST['nove_datum']);
					if (count($dt)==1) $dt = explode(".",$_POST['nove_datum']);
					if (count($dt)==1) $dt = explode("-",$_POST['nove_datum']);
					if (count($dt)==3 && count($cs) == 3 && $_POST['nazev_akce'] != "" && $_POST['popis_akce'] != "") {
						if ($cs[2]>=0 && $cs[2]<=59 && $cs[1]>=0 && $cs[1]<=59 && $cs[0]>=0 && $cs[0]<=23 && ctype_digit($cs[2]) && ctype_digit($cs[1]) && ctype_digit($cs[0]) && ctype_digit($dt[0]) && ctype_digit($dt[1]) && ctype_digit($dt[2]) && $dt[2]>1970 && $dt[1] > 0 && $dt[1] <= 12 && $dt[0] > 0 && $dt[0] <= 31 && $dt[2] < date("Y")+2) {
							if ($dt[0] <= date("t",mktime(1,1,1,$dt[1],5,$dt[2]))) {
								$tm = mktime($cs[0],$cs[1],$cs[2],$dt[1],$dt[0],$dt[2]);
								$ok = true;
								$weekSql = $weekSql2 = "";
							}
						}
					}
				}
			break;
		}

		if ($ok) {
			$publicly = "0";
			$UserId = $_SESSION['uid'];
			if ($PravaMax) {
				if (isset($_POST['verejne']) && $_POST['verejne'] == "on") $publicly = "1";
				if (isset($_POST['systemova']) && $_POST['systemova'] == "on") $UserId = "0";
			}
			if ($UserId == "0") $publicly = "1";
			$nazev = addslashes($_POST['nazev_akce']);
			$text = addslashes($_POST['popis_akce']);
			$sql = "INSERT INTO 3_kalendar (uid, public, timestamp".$weekSql.", nazev ,text) VALUES ($UserId,$publicly,$tm".$weekSql2.",'".$nazev."','".$text."')";
			mysql_query($sql);
			header("Location: $inc/add/kalendar.php?ok=1");
			exit;
		}
		else {
			header("Location: $inc/add/kalendar.php");
			exit;
		}
	}
}

$form_for_new = "";
if ($LogedIn && isset($_SESSION['login']) && ($_SESSION['login_rew'] == "apophis" || $_SESSION['login_rew'] == "saltzhornia")) $form_for_new = "<div id='inserter' class='hide'>\n<form action='/add/kalendar.php?akce=kalendar-insert' name='formular' class='f' method='post'>
<fieldset><legend>Nová akce</legend>
	<p><label for='nazev_akce'><span>Název</span><input id='nazev_akce' name='nazev_akce' type='text' maxlength='40' /></label></p>
	<p><label for='popis_akce'><span>Popis</span><input id='popis_akce' name='popis_akce' type='text' maxlength='150' /></label></p>
	<p><label for='novy_cas'><span>Čas</span><input id='novy_cas' name='novy_cas' type='text' maxlength='8' /></label></p>
	<p><label for='nove_datum'><span>Datum</span><input id='nove_datum' name='nove_datum' type='text' maxlength='10' /></label></p>
	".(($_SESSION['lvl']>=3||$_SESSION['login']=="Saltzhornia")?"<p><label for='systemova'><span>Systémová akce</span><input id='systemova' name='systemova' type='checkbox' value='on' /></label></p>":"")."
	<p><label for='verejne'><span>Veřejná</span><input id='verejne' name='verejne' type='checkbox' value='on' /></label></p>
	<p><label for='tydne_typ'><span>a) Nastavení týdne</span><select id='tydne_typ' name='tydne_typ'>
		<option value='0' selected> - - - - - </option>
		<option value='1'>Každý týden</option>
		<option value='2'>Jednou za 2 týdny</option>
		</select></label></p>
	<p><label for='mesicne'><span>b) Den měsíce</span><input type='text' maxlength='2' id='mesicne' name='mesicne' /></label></p>
	".(($_SESSION['lvl']>=3||$_SESSION['login']=="apophis")?"<p><label for='rocne'><span>c) Roční</span><input type='text' maxlength='5' id='rocne' name='rocne' value='DD/MM' /></label></p>":"")."
	<p><input type='submit' value='Vytvořit akci' /></p>
</fieldset>
</form>
</div><a href='#' onclick='$(\"inserter\").toggleClass(\"hide\");return false;'>Přidat akci</a>
";


class Aragorn_Kalendar {
	var $r;
	var $titles;

	function Aragorn_Kalendar(){
		$this->titles = array();
		$this->r = "";
	}
	
	function cleaner() {
		$this->r = "";
	}

	function printer() {
		echo $this->r;
	}

	function showDay(){
		return $this->titles;
	}

	function prepare($thisYear,$thisMonth,$thisDay,$public,$showWeek,$monthMinus,$monthPlus,$justOneDay,$PrintScript) {
		global $LogedIn,$_SESSION;
		// variables
		$time = time();
		$todaysDate = date("j-n-Y");
		$aSude = $aLiche = $aWeekly = $aMonthly = $aYearly = $aDay = array();
		$tSude = $tLiche = $tWeekly = $tMonthly = $tYearly = $tDay = array();
		$aLicheIds = $aSudeIds = $aWeeklyIds = $aMonthlyIds = $aYearlyIds = $aDayIds = array();
		$strToday = "dtm-now t"; $strPast = "m"; $strFuture = "p"; $strNow = "t";

		// choose type of view
		$r = $publicSql = ""; $uidS = 0;
		if (!$public) $publicSql = " AND `public`=1";
		if (isset($_SESSION['uid']) && $_SESSION['uid']>0) $uidS = $_SESSION['uid'];

		$timeBack = mktime(0,0,1,$thisMonth-$monthMinus,1,$thisYear);
		$timeNext = mktime(23,59,59,$thisMonth+$monthPlus+1,0,$thisYear);
		if ($justOneDay) {
			$timeBack = mktime(0,0,1,$thisMonth,$thisDay,$thisYear);
			$timeNext = mktime(23,59,59,$thisMonth,$thisDay,$thisYear);
		}
		$sel_text = "`id`,`weekly`,`monthly`,`yearly`,`nazev`,`timestamp`,`uid`";
		$monthlySql = "IS NOT NULL";
		if ($justOneDay) {
			$sel_text .= ",text";
			$monthlySql = "=$thisDay";
		}
		$yearlyMin = date("nd",$timeBack);
		$yearlyMax = date("nd",$timeNext);
		$spoj = "AND";
		if ($yearlyMin > $yearlyMax) {
			$spoj = "OR";
		}
		$yearly = "`yearly` >= $yearlyMin $spoj `yearly` <= $yearlyMax";

		$sql = "SELECT * FROM 3_kalendar WHERE ((`uid` = 0) OR (`uid` = $uidS $publicSql)) AND ((`timestamp` <= $timeNext AND `timestamp` >= $timeBack) OR (`weekly` IS NOT NULL) OR (`monthly` $monthlySql) OR ($yearly))";

		$kalendarS = mysql_query($sql);
		if (mysql_num_rows($kalendarS)>0) {
			while ($w = mysql_fetch_object($kalendarS)) {
				$strCas = date("H:i:s",$w->timestamp);
				if ($w->weekly != "") {
					$weekDay = date("w",$w->timestamp);
					if($w->weekly == "1") { $aWeeklyIds[$w->id] = $w; $aWeekly[$weekDay] = 1; $tWeekly[$weekDay][] = $strCas." - ".htmlspecialchars($w->nazev,ENT_COMPAT,"UTF-8");
					}
					else {
						$CisloTydne = date("W",$w->timestamp);
						if ($CisloTydne % 2 == 1) { $aLicheIds[$w->id] = $w; $aLiche[$weekDay] = 1; $tLiche[$weekDay][] = $strCas." - ".htmlspecialchars($w->nazev,ENT_COMPAT,"UTF-8");
						}
						else { $aSudeIds[$w->id] = $w; $aSude[$weekDay] = 1; $tSude[$weekDay][] = $strCas." - ".htmlspecialchars($w->nazev,ENT_COMPAT,"UTF-8");
						}
					}
				}elseif ($w->yearly != "") { $aYearlyIds[$w->id] = $w; $aYearly[$w->yearly] = 1; $tYearly[$w->yearly][] = $strCas." - ".htmlspecialchars($w->nazev,ENT_COMPAT,"UTF-8");
				}elseif ($w->monthly != "") { $aMonthlyIds[$w->id] = $w; $aMonthly[$w->monthly] = 1; $tMonthly[$w->monthly][] = $strCas." - ".htmlspecialchars($w->nazev,ENT_COMPAT,"UTF-8");
				}else { $aDayIds[$w->id] = $w; $aDay[date("j-n-Y",$w->timestamp)] = 1; $tDay[date("j-n-Y",$w->timestamp)][] = $strCas." - ".htmlspecialchars($w->nazev,ENT_COMPAT,"UTF-8");
				}
			}
			mysql_free_result($kalendarS);

			$m=(-1)*$monthMinus;
			$days = array();
			for($a=$m;$a<=$monthPlus;$a++){
				$days[$a]=date("t",mktime(1,1,1,$thisMonth+$a,3,$thisYear));
			}
			$WeekShift = 0;
			if (date("w",mktime(1,1,1,1,1,$thisYear))==0) {
				$WeekShift = 1;
			}

			$d=1;
			$tbl_id = "dtm-tbl-small";
			if ($PrintScript) $tbl_id = "dtm-tbl";
			$r .= "<table border='1' id='$tbl_id'>\n<thead><tr>".($showWeek?"<th>týden</th>":"")."<th>Po</th><th>Út</th><th>St</th><th>Čt</th><th>Pá</th><th>So</th><th>Ne</th></tr></thead>\n<tbody>\n";
			while(true){
				$classAdd = $className = ""; $titles = false;
				$aDtm = getdate(mktime(1,1,1,$thisMonth+$m,$d,$thisYear));
				$sDtm = $aDtm['mday']."/".$aDtm['mon'];
				$ids = array();

				if ((date("W",$aDtm[0])+$WeekShift) % 2 == 0) {
					if (isset($aSude[$aDtm['wday']])) {
						$titles = true;
						$ids = array_merge($ids,$aSudeIds);
						$classAdd .= " s".$aDtm['wday'];
					}
				}
				else {
					if (isset($aLiche[$aDtm['wday']])) {
						$titles = true;
						$classAdd .= " l".$aDtm['wday'];
						$ids = array_merge($ids,$aLicheIds);
					}
				}

				if (isset($aWeekly[$aDtm['wday']])) {
					$titles = true;
					$classAdd .= " w".$aDtm['wday'];
					$ids = array_merge($ids,$aWeeklyIds);
				}

				if (isset($aMonthly[$aDtm['mday']])) {
					$titles = true;
					$classAdd .= " m".$aDtm['mday'];
					$ids = array_merge($ids,$aMonthlyIds);
				}

				$yday = $aDtm['mday'];
				if ($yday<10) $yday = "0".$yday;
				$yday = (int)($aDtm['mon']."".$yday);
				if (isset($aYearly[$yday])) {
					$titles = true;
					$classAdd .= " y".$yday;
					$ids = array_merge($ids,$aYearlyIds);
				}

				if (isset($aDay["$aDtm[mday]-$aDtm[mon]-$aDtm[year]"])) {
					$titles = true;
					$classAdd .= " d".$aDtm['mday']."-".$aDtm['mon']."-".$aDtm['year'];
					if ($aDtm['mday'] == $thisDay && $aDtm['mon'] == $thisMonth && $aDtm['year'] == $thisYear) {
						$ids = array_merge($ids,$aDayIds);
					}
				}

				if ($aDtm['mday']==$thisDay && $aDtm['mon']==$thisMonth && $aDtm['year']==$thisYear) {
					$this->titles = $ids;
				}

				if ($aDtm['mday']."-".$aDtm['mon']."-".$aDtm['year']==$todaysDate) $className = $strToday;
				elseif ($aDtm['mon'] == $thisMonth) $className = $strNow;
				elseif (($aDtm['mon']>$thisMonth && $aDtm['year']==$thisYear) || $aDtm['year']>$thisYear) $className = $strFuture; // budoucnost - plus
				elseif (($aDtm['mon']<$thisMonth && $aDtm['year']==$thisYear) || $aDtm['year']<$thisYear) $className = $strPast; // minulost - minus
				else $className = $strNow;

        $addToUrl = "";
				if (!$PrintScript) {
					$addToUrl = "/add/kalendar.php";
				  if ($className != $strToday) {
				    $className = "";
					}
					else {
				    $className = " class='".$className."'";
					}
				}
				else {
			    $className = " class='".$className.$classAdd."'";
				}

				if ($titles) $sDtm = "<a href=\"$addToUrl?view=$aDtm[year]-$aDtm[mon]-$aDtm[mday]\">".$sDtm."</a>";

				if ($m==-1*$monthMinus && $d==1 && ($aDtm['wday'] > 1 || $aDtm['wday'] == 0)) {
					if ($aDtm['wday'] == 0) $r .= "<tr>".($showWeek?"<td>".(date("W",$aDtm[0])+$WeekShift).".</td>":"")."<td colspan='6'></td><td".$className.">$sDtm</td>";
					else $r .= "<tr>".($showWeek?"<td>".(date("W",$aDtm[0])+$WeekShift).".</td>":"")."<td colspan='".($aDtm['wday']-1)."'></td><td".$className.">$sDtm</td>";
				}else {
					if ($aDtm['wday'] == 1) $r .= "<tr>".($showWeek?"<td>".(date("W",$aDtm[0])+$WeekShift).".</td>":"")."";
					$r .= "<td".$className.">$sDtm</td>";
				}

				if ($aDtm['wday'] == 0)$r .= "</tr>\n";
				if ($m==$monthPlus && $aDtm['mday'] == $days[$m]) {
					if ($aDtm['wday'] != 0) $r .= "<td colspan='".(7-$aDtm['wday'])."'></td></tr>\n";
					break;
				}elseif ($aDtm['mday'] == $days[$m]) {
					$m++;
					$d = 1;
					continue;
				}
				$d++;
			}
			$r .= "</tbody></table>\n";
			if ($PrintScript) {
				$r .= "
<script type='text/javascript'>
	func"."tion TitleTize(v,k,t){
		switch(t){
			case 'y': case 'm': case 'w': case 'l': case 's': case 'd':
				v = '<p class=\"a-'+t+'-p\">'+v+'</p>';
				\$\$('#".$tbl_id." .'+k+' a').each(function(el){if(!el.getProperty('title')){el.setProperty('title',v)}else{el.setProperty('title',el.getProperty('title')+v)}});
			break;
		}
	}
	fun"."ction Maketize(){\n";
				$art = array("Yearly","Monthly","Weekly","Liche","Sude","Day");
				foreach($art as $d) {
					$nzv = "t".$d;
					if (count($$nzv)>0) {
						$a = array();
						$r .= "		var ".strtolower($d)." = {";
						foreach($$nzv as $k=>$v) {
							$a[] = "'".strtolower($d[0]).$k."':\"".join("<br />",$v)."\"";
						}
						$r .= join(",",$a)."};\n";
						$r .= "		\$each(".strtolower($d).",function(v,k){TitleTize(v,k,'".strtolower($d[0])."')});\n";
					}
					else $r .= "		var ".strtolower($d)." = []\n";
				}
				$r .= "		var myTips = new Tips(\$\$('#dtm-tbl td a'), {maxTitleChars: 400,fixed:true,offsets:{x:20,y:20},showDelay:0,hideDelay:0});
	}
	window.onload=function(){Maketize()};
</script>\n";
			}
		}
		$this->r = $r;
	}

}



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>jen tak...</title>
<script charset="utf-8" src="/js/mootools.js" type="text/javascript"></script>
<style type='text/css'>
body { font-family: Arial, lucida, sans-serif; }
#dtm-tbl { border-collapse: collapse; float: left; margin: 10px; }
#dtm-tbl-small { border-collapse: collapse; margin: 5px; font-size: 80%; }
#dtm-tbl-small td, #dtm-tbl-small th { font-size: 80%; padding: 1px; text-align: center; vertical-align: middle; }
#dtm-tbl thead th { font-variant: small-caps; background-color: #D3D3D3; }
#dtm-tbl td { text-align: center; vertical-align: middle; width: 3em; line-height: 2.5; letter-spacing: 1px; font-size: 80%; }
#dtm-tbl .m { background-color: #C7EDFF; color: #81CCF0; }
#dtm-tbl .r-m, .dtm-tbl .r-p, .dtm-tbl .r-t { }
#dtm-tbl .p { background-color: #FEEED8; color: #F1CA8B; }
#dtm-tbl .t { background-color: #ADED9C; color: #2A7B00; font-weight: bold; }
#dtm-tbl .dtm-now { font-weight:bold; background-color: #4EC824 !important; color: #CCFFBF !important; }
#dtm-tbl-small .dtm-now { font-weight:bold; background-color: #000000 !important; color: #FFFFFF !important; }
.tool-tip { position: absolute; margin: 1.5em 0 0 0; background-color: #000; color: #95513C; font-size: 70%; padding: 1px; }
.tool-title { display: none; }
.hide {display:none;}
.tool-text p { padding: 5px; margin: 0; display: block; line-height: 1.1; }
.flr { float: left; padding: 5px; }
.flr table { margin: 5px 0; padding: 5px; background-color: #D3D3D3; border-collapse: collapse; border: 1px solid #000; }
.flr table td { padding: 5px; font-size: 90%; vertical-align: top; background-color: #808080; color: white; font-weight: bold; }
.flr .dtm-text { background-color: #F0F0F0; color: black; font-weight: normal; }
.flr h1, .flr h2, .flr h3, .flr h4 { margin: 0; }
#dhtmltooltip { visibility: hidden; position: absolute; background-color: #D3D3D3; border: 1px solid #808080; color: black; }
.a-w-p { background-color: red; color: white; } /* weekly */
.a-m-p { background-color: skyblue; color: navy; } /* monthly */
.a-y-p { background-color: #D1F0D6; color: #1E6D48; } /* yearly */
.a-d-p { background-color: green; color: lime; } /* daily */
.a-l-p { background-color: #FFC0CB; color: #800080; } /* liche */
.a-s-p { background-color: #800080; color: #FFC0CB; } /* sude */
.clearer { padding: 0; margin: 0; line-height: 1; clear: both; }
body { background-color: #f8f8f8; color: #333; padding: 10px; }
p a { text-decoration: none; color: #33AA33; font-weight: bold; background-color: #CCFFCC; padding: 1px 3px; }
p { display: inline; margin: 2px; border: 1px solid #999; padding: 3px; }
table { clear: both; }
form { clear: both; margin-top: 10px; }
form fieldset { border: 1px dashed #000; }
form p { display: block; margin: 0; padding: 3px 5px; border: none; }
form input { background-color: #E7E7E7; color: #444444; padding: 2px 3px; border: 1px solid #AAAAAA; }
form label span { margin-right: 5px; }
</style>
</head>
<body>
<div id="dhtmltooltip"></div>
<?php
$dneska = getdate();
echo "<p>";
if (isset($_GET['m'])) {
	$posunM = (int)$_GET['m'];
	if ($posunM > -13 && $posunM < 13) {
		if ($posunM != 0) {
			$dneska = getdate(mktime(1,1,1,$dneska['mon']+$posunM));
			echo "<a href='?m=".($posunM-1)."' title='O další měsíc zpět'>&laquo;</a> | <a href='?' title='Návrat k dnešnímu datu'>měsíc</a> | <a href='?m=".($posunM+1)."' title='O další měsíc vpřed'>&raquo;</a>";
		}
		else {
			echo "<a href='?m=-1' title='O jeden měsíc zpět'>&lt;</a> | měsíc | <a href='?m=1' title='O jeden měsíc vpřed'>&raquo;</a>";
		}
	}
	elseif ($posunM <= -13) {
		echo "&laquo; | <a href='?' title='Návrat k dnešnímu datu'>měsíc</a> | <a href='?m=-12' title='O další měsíc vpřed'>&raquo;</a>";
	}
	else {
		echo "<a href='?m=12' title='O další měsíc zpět'>&lt;</a> | <a href='?' title='Návrat k dnešnímu datu'>měsíc</a> | &raquo;";
	}
}
else {
	echo "<a href='?m=-1'>&laquo;</a> | měsíc | <a href='?m=1'>&raquo;</a>";
}
echo "</p>\n";

$kalendar = new Aragorn_Kalendar;
$kalendar->prepare($dneska['year'],$dneska['mon'],$dneska['mday'],true,true,1,1,false,true);
$kalendar->printer();
unset($kalendar);

if (isset($_GET['view'])) {
	echo "\n<div class='flr'>\n";
	$dtm = explode("-",$_GET['view']);
	if (count($dtm)==3) {
		if (ctype_digit($dtm[0]) && ctype_digit($dtm[1]) && ctype_digit($dtm[2]) && $dtm[0]>1970 && $dtm[1] > 0 && $dtm[1] <= 12 && $dtm[2] > 0 && $dtm[2] <= 31 && $dtm[0] < $dneska['year']+2) {
			if ($dtm[2] <= date("t",mktime(1,1,1,$dtm[1],5,$dtm[0]))) {
				$oneDay = new Aragorn_Kalendar;
				$oneDay->prepare($dtm[0],$dtm[1],$dtm[2],true,false,0,0,true,false);
				$oneDay->cleaner();
				$ac = $oneDay->titles;
				echo "<h4>".$dtm[2].".".$dtm[1].".".$dtm[0]."</h4>";
				for ($i=0;$i<count($ac);$i++) {
?>
<table border="0" cellspacing="2" cellpadding="0">
<tr><td>Akce:</td><td><?php echo $ac[$i]->nazev;?></td></tr>
<tr><td>Čas:</td><td><?php echo date("H:i:s",$ac[$i]->timestamp);?></td></tr>
<td class='dtm-text' colspan="2"><?php echo $ac[$i]->text;?></td></tr>
</table>
<?php
				}
			}
		}
	}
	echo "</div>\n";
}

$dneska = getdate();
$smallKal = new Aragorn_Kalendar;
$smallKal->prepare($dneska['year'],$dneska['mon'],$dneska['mday'],true,false,0,0,false,true);
$smallKal->printer();
//unset($smallKal);

if ($ok == true) {
	echo "<div class='clearer'></div>\n";
	echo "<div style='color:green;font-weight:bold;'>uloženo</div>\n";
}

echo $form_for_new;

$microTwo = (float)microtime();
echo (abs($microOne-$microTwo))."s";

?>
</body>
</html>