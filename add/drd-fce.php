<?php
function level_postavy($povolani,$zk){
	$level = 1;
	$povolani = floor($povolani/2);
	switch ($povolani) {
		case 0: // valecnik
		$zkk = array (0,449,899,1824,3674,7399,14999,29999,54999,79999,104999,129999,154999,179999,204999,229999,254999,279999,304999,329999,354999,379999,404999,429999,454999,479999,504999,529999,554999,579999,604999,629999,654999,679999,704999,729999);
		break;
		case 1: // hranicar
		$zkk = array (0,524,1049,2074,4124,8224,16349,32499,57499,82499,107499,132499,157499,182499,207499,232499,257499,282499,307499,332499,357499,382499,407499,432499,457499,482499,507499,532499,557499,582499,607499,632499,657499,682499,707499,732499);
		break;
		case 2: // alchymista
		$zkk = array (0,574,1149,2299,4649,9324,18699,37499,57499,82499,112499,137499,162499,187499,212499,237499,262499,287499,312499,337499,362499,387499,412499,437499,462499,487499,512499,537499,562499,587499,612499,637499,662499,687499,712499,737499);
		break;
		case 3: //kouzelnik
		$zkk = array (0,609,1249,2574,5249,10749,21999,44999,69999,94999,119999,144999,169999,194999,219999,244999,269999,294999,319999,344999,369999,394999,419999,449999,469999,494999,519999,544999,569999,594999,619999,644999,669999,694999,719999,744999);
		break;
		case 4: //zlodej
		$zkk = array (0,324,729,1574,3449,7449,16149,34999,59999,84999,109999,134999,159999,184999,209999,234999,259999,284999,309999,334999,359999,384999,409999,434999,459999,484999,509999,534999,559999,584999,609999,634999,659999,684999,709999,734999);
		break;
	}
	while ($zk > $zkk[$level] && $level < 36) {
		$level++;
	}
	return $level;
}

//urci, jake rasy je postava
function rasa_postavy($race){
	switch ($race){
		case 0:
			$r="hobit";
		break;
		case 1:
			$r="kudůk";
		break;
		case 2:
			$r="trpaslík";
		break;
		case 3:
			$r="elf";
		break;
		case 4:
			$r="člověk";
		break;
		case 5:
			$r="barbar";
		break;
		case 6:
			$r="kroll";
		break;
	}
	return $r;
}

// povolani posilat primo cislo z tabulky (delene 2 hodi pro 0 a 1 spravne, protoze 0/2=0 a 1/2=0 zb.1)
function povolani_postavy($a){
	$a = floor($a/2);
	switch($a){
		case 0:
			$a="válečník";
		break;
		case 1:
			$a="hraničář";
		break;
		case 2:
			$a="alchymista";
		break;
		case 3:
			$a="kouzelník";
		break;
		case 4:
			$a="zloděj";
		break;
	}
return $a;
}

// subpovolani - posilat primo cislo povolani z tabulky
function subpovolani_postavy($a) {
	switch ($a){
		case 0:
			$a="bojovník";
		break;
		case 1:
			$a="šermíř";
		break;
		case 2:
			$a="druid";
		break;
		case 3:
			$a="chodec";
		break;
		case 4:
			$a="theurg";
		break;
		case 5:
			$a="pyrofor";
		break;
		case 6:
			$a="mág";
		break;
		case 7:
			$a="čaroděj";
		break;
		case 8:
			$a="lupič";
		break;
		case 9:
			$a="sicco";
		break;
	}
	return $a;
}

//povolani, rasy a jejich atributy
function zakladni_atributy($povolani, $rasa){
	$povolani = floor($povolani/2);
	$magy = 0; $schopnost = "";
	switch ($rasa){
		case 0:		//hobit
			$sila = 2 + mt_rand(1,6); $obratnost = 10 + mt_rand(1,6); $odolnost = 7 + mt_rand(1,6); $inteligence = 9 + mt_rand(1,6); $charisma = 6 + mt_rand(1,6) + mt_rand(1,6);
			$oprava = array(-5, 2, 0, -2, 3);
			$schopnost="Vycítí živého tvora na 12 sáhů.";
			$vyska = mt_rand(70,120); $vaha = mt_rand(800,1200); $velikost="A";
		break;
		case 1:		//kuduk
			$sila = 4 + mt_rand(1,6); $obratnost = 9 + mt_rand(1,6); $odolnost = 9 + mt_rand(1,6); $inteligence = 8 + mt_rand(1,6); $charisma = 6 + mt_rand(1,6);
			$oprava = array(-3, 1, 1, -2, 0);
			$vyska = mt_rand(90,130); $vaha = mt_rand(1000,1500); $velikost="A";
		break;
		case 2:		//trpaslik
			$sila = 6 + mt_rand(1,6); $obratnost = 6 + mt_rand(1,6); $odolnost = 11 + mt_rand(1,6); $inteligence = 7 + mt_rand(1,6); $charisma = 6 + mt_rand(1,6);
			$oprava = array(1, -2, 3, -3, -2);
			$schopnost="Infravidění na 20 sáhů.";
			$vyska = mt_rand(110,140); $vaha = mt_rand(1100,2000); $velikost="A";
		break;
		case 3:		//elf
			$sila = 5 + mt_rand(1,6); $obratnost = 9 + mt_rand(1,6); $odolnost = 5 + mt_rand(1,6); $inteligence = 11 + mt_rand(1,6); $charisma = 6 + mt_rand(1,6) + mt_rand(1,6);
			$oprava = array(0, 1, -4, 2, 2);
			$vyska = mt_rand(145,180); $vaha = mt_rand(1000,1700); $velikost="B";
		break;
		case 4:		//clovek
			$sila = 4 + mt_rand(1,6) + mt_rand(1,6); $obratnost = 8 + mt_rand(1,6); $odolnost = 8 + mt_rand(1,6); $inteligence = 9 + mt_rand(1,6); $charisma = -1 + mt_rand(1,6) + mt_rand(1,6) + mt_rand(1,6);
			$oprava = array(0, 0, 0, 0, 0);
			$vyska = mt_rand(165,210); $vaha = mt_rand(1300,2300); $velikost="B";
		break;
		case 5:		//barbar
			$sila = 9 + mt_rand(1,6); $obratnost = 7 + mt_rand(1,6); $odolnost = 10 + mt_rand(1,6); $inteligence = 5 + mt_rand(1,6); $charisma = mt_rand(1,6) + mt_rand(1,6) + mt_rand(1,6) - 2;
			$oprava = array(1, -1, 1, 0, -2);
			$vyska = mt_rand(175,220); $vaha = mt_rand(1500,2800); $velikost="B";
		break;
		case 6:		//kroll
			$sila = 10 + mt_rand(1,6); $obratnost = 4 + mt_rand(1,6); $odolnost = 12 + mt_rand(1,6); $inteligence = 1 + mt_rand(1,6); $charisma = mt_rand(1,6) + mt_rand(1,6) - 1;
			$oprava = array(3, -4, 3, -6, -5);
			$schopnost="Ultrasluch na 50 sáhů.";
			$vyska = mt_rand(180,245); $vaha = mt_rand(2000,4000); $velikost="C";
		break;
	}
	switch ($povolani){
		case 0:		//valecnik
			$sila = 12 + mt_rand(1,6) + $oprava[0]; $odolnost = 12 + mt_rand(1,6) + $oprava[2]; $zivoty = 10;
		break;
		case 1:		//hranicar
			$sila = 10 + mt_rand(1,6) + $oprava[0]; $inteligence = 11 + mt_rand(1,6) + $oprava[3]; $zivoty = 8;
		break;
		case 2:		//alchymista
			$obratnost = 12 + mt_rand(1,6) + $oprava[1]; $odolnost = 11 + mt_rand(1,6) + $oprava[2]; $zivoty = 7;
			if ($obratnost > 7 && $obratnost < 12) { $magy = 7; }
			elseif ($obratnost > 11 && $obratnost < 17) { $magy = 8; }
			else{ $magy = 9; }
		break;
		case 3:		//kouzelnik
			$inteligence = 13 + mt_rand(1,6) + $oprava[3]; $charisma = 12 + mt_rand(1,6) + $oprava[4]; $zivoty = 6;
			if ($inteligence > 7 && $inteligence < 12) { $magy = 7; }
			elseif ($inteligence > 11 && $inteligence < 18) { $magy = 8; }
			else { $magy = 9; }
		break;
		case 4:		//zlodej
			$obratnost = 13 + mt_rand(1,6) + $oprava[1]; $charisma = 11 + mt_rand(1,6) + $oprava[4]; $zivoty = 6;
		break;
	}
	$zivoty = $zivoty + get_bonus($odolnost, 1);
	$jj = array("sila"=>$sila, "obratnost"=>$obratnost, "odolnost"=>$odolnost, "inteligence"=>$inteligence, "charisma"=>$charisma,
						"zivoty"=>$zivoty, "schopnost"=>$schopnost, "magy"=>$magy, "vyska"=>$vyska, "vaha"=>$vaha, "velikost"=>$velikost);
return $jj;
}

function get_bonus($val, $typ){
	if ($val<1) {
		$vb=1;
	}
	elseif ($val < 10) {
		$vb = -1*floor((11-$val)/2);
	}
	elseif ($val > 12) {
		$vb = floor(($val-11)/2);
	}
	else { // 10..11..12
		$vb = 0;
	}
	
	if ($typ>0){
		return $vb;
	}
	else {
		if ($vb<0) {
			$b = "<span class='hnegative'>$vb</span>";
		}
		elseif ($vb>0) {
			$b = "<span class='hpositive'>+$vb</span>";
		}
		else {
			$b = "$vb";
		}
		return $b;
	}
}

//vrati max_magy podle povolani
function get_magy($povolani,$uroven,$obratnost,$inteligence){
	$m = 0;
	$i = $inteligence;
	$o = $obratnost;
	$pov = floor($povolani/2);
	$pov2 = $povolani;
	$l = $uroven;
	switch($pov2){
		case 4: //pyrofor
		case 5: //theurg
			$hr1=array(0,0,0,0,0,0,184,256,360,490);
			$hr2=array(0,0,0,0,0,0,201,280,394,525);
			$hr3=array(0,0,0,0,0,0,218,303,426,568);
			$hr4=array(0,0,0,0,0,0,230,320,450,600);
			$hr5=array(0,0,0,0,0,0,242,337,474,632);
			$hr6=array(0,0,0,0,0,0,529,360,506,675);
			$hr7=array(0,0,0,0,0,0,276,384,540,710);
		break;
		case 2: //druid
			$hr1=array(0,0,0,0,0,0,18,22,26,30,35,40,46,50,56,62,66,72,79,85,91,98,105,112,120,127,135,143,152,160,169,177,186,195,205,214,224);
			$hr2=array(0,0,0,0,0,0,19,23,27,31,36,40,46,50,57,62,70,76,83,89,96,103,111,118,127,134,143,151,160,169,178,187,197,206,216,227,237);
			$hr3=array(0,0,0,0,0,0,19,23,27,31,36,41,47,51,59,64,73,79,86,93,100,108,115,123,132,140,148,157,167,175,185,195,205,214,225,236,246);
			$hr4=array(0,0,0,0,0,0,20,24,29,34,39,43,50,55,62,69,75,82,89,96,103,111,119,127,136,144,153,162,172,181,191,201,211,221,232,243,254);
			$hr5=array(0,0,0,0,0,0,22,25,30,35,40,44,50,55,63,69,77,85,92,99,106,114,132,131,140,148,158,167,177,187,197,207,217,228,239,250,262);
			$hr6=array(0,0,0,0,0,0,23,26,31,36,42,45,53,58,66,71,80,88,95,103,110,119,127,136,145,154,163,173,184,193,204,215,225,236,248,259,271);
			$hr7=array(0,0,0,0,0,0,24,26,32,39,44,48,55,61,70,76,84,92,99,107,115,124,133,142,152,161,171,181,192,202,213,225,236,247,259,272,284);
		break;
		case 3: //chodec
			$hr1=array(0,0,0,0,0,0,15,18,21,24,27,31,34,37,40,44,46,49,53,56,60,64,67,71,74,78,81,85,89,93,96,101,104,109,112,116,121);
			$hr2=array(0,0,0,0,0,0,16,19,22,25,28,31,34,37,40,44,48,52,56,60,63,67,71,75,78,82,86,89,94,98,102,106,110,115,118,123,128);
			$hr3=array(0,0,0,0,0,0,16,19,22,25,28,31,34,38,42,46,50,54,58,62,66,70,74,78,81,85,89,93,98,102,106,110,114,119,123,128,133);
			$hr4=array(0,0,0,0,0,0,17,20,23,27,30,33,37,41,45,49,52,56,60,64,68,72,76,80,84,88,92,96,101,105,109,114,118,123,127,132,137);
			$hr5=array(0,0,0,0,0,0,18,21,24,28,31,34,37,41,45,49,54,58,62,66,70,74,78,82,89,91,95,99,104,108,112,118,122,127,131,136,141);
			$hr6=array(0,0,0,0,0,0,19,22,25,29,32,35,39,43,47,51,56,60,64,68,73,77,81,85,90,94,98,103,108,112,116,122,126,131,136,141,146);
			$hr7=array(0,0,0,0,0,0,20,22,25,31,34,37,41,45,50,54,58,63,67,72,76,80,85,89,94,98,103,107,113,117,122,127,132,137,142,148,153);
		break;
		case 6: //mag
		case 7: //carodej
			$hr1=array(0,0,0,0,0,0,19,21,24,26,27,29,30,33,36,39,43,46,50,53,57,61,65,69,73,78,82,87,92,97,103,107,113,118,124,130,136);
			$hr2=array(0,0,0,0,0,0,23,26,29,32,35,37,38,42,45,49,54,57,62,66,71,76,81,86,71,97,103,109,115,121,128,134,140,147,15,162,169);
			$hr3=array(0,0,0,0,0,0,26,29,33,36,39,42,44,49,53,57,63,67,71,78,83,89,94,101,107,114,120,127,134,141,149,156,164,172,181,189,198);
			$hr4=array(0,0,0,0,0,0,28,32,36,40,44,47,50,55,60,65,71,76,82,88,94,101,107,114,121,129,136,144,125,160,169,177,186,195,205,214,224);
			$hr5=array(0,0,0,0,0,0,30,35,39,44,48,52,56,61,67,73,79,85,92,98,105,113,120,127,135,144,152,161,170,179,189,198,208,218,229,239,250);
			$hr6=array(0,0,0,0,0,0,33,38,43,48,53,57,62,68,75,81,88,95,102,110,117,126,133,142,151,161,169,179,189,199,210,220,232,243,255,266,279);
			$hr7=array(0,0,0,0,0,0,37,43,48,54,60,65,70,77,84,91,99,106,114,123,131,141,149,159,169,180,190,201,212,223,235,247,259,272,286,298,312);
		break;
	}
	switch($pov){
		case 1: //hranicar
			if ($i<8){switch($l){case 1: $m=0;break; case 2: $m=3;break; case 3: $m=6;break; case 4: $m=10;break; case 5: $m=12;break;default:$m=$hr1[$l];break;}
			}elseif($i>7 && $i<10){switch($l){case 1: $m=0;break; case 2: $m=3;break; case 3: $m=6;break; case 4: $m=10;break; case 5: $m=13;break;default:$m=$hr2[$l];break;}
			}elseif($i>9 && $i<12){switch($l){case 1: $m=0;break; case 2: $m=3;break; case 3: $m=7;break; case 4: $m=11;break; case 5: $m=13;break;default:$m=$hr3[$l];break;}
			}elseif($i>11 && $i<14){switch($l){case 1: $m=0;break; case 2: $m=3;break; case 3: $m=7;break; case 4: $m=11;break; case 5: $m=14;break;default:$m=$hr4[$l];break;}
			}elseif($i>13 && $i<16){switch($l){case 1: $m=0;break; case 2: $m=3;break; case 3: $m=7;break; case 4: $m=11;break; case 5: $m=15;break;default:$m=$hr5[$l];break;}
			}elseif($i>15 && $i<18){switch($l){case 1: $m=0;break; case 2: $m=3;break; case 3: $m=8;break; case 4: $m=12;break; case 5: $m=15;break;default:$m=$hr6[$l];break;}
			}elseif($i>17){switch($l){case 1: $m=0;break; case 2: $m=3;break; case 3: $m=8;break; case 4: $m=13;break; case 5: $m=16;break;default:$m=$hr7[$l];break;}
			}
		break;
		case 2: //alchymista
			if ($l>9){if($o<10){$m=$hr1[9];}elseif($o<13){$m=$hr2[9];}elseif($o<15){$m=$hr3[9];}elseif($o<17){$m=$hr4[9];}elseif($o<19){$m=$hr5[9];}elseif($o>18){$m=$hr6[9];}
			}elseif($o<10){switch($l){case 1: $m=7;break; case 2: $m=15;break; case 3: $m=31;break; case 4: $m=62;break; case 5: $m=126;break;default:$m=$hr1[$l];break;}
			}elseif($o>9 && $o<12){switch($l){case 1: $m=7;break; case 2: $m=16;break; case 3: $m=35;break; case 4: $m=70;break; case 5: $m=131;break;default:$m=$hr2[$l];break;}
			}elseif($o>11 && $o<14){switch($l){case 1: $m=8;break; case 2: $m=17;break; case 3: $m=38;break; case 4: $m=76;break; case 5: $m=142;break;default:$m=$hr3[$l];break;}
			}elseif($o>13 && $o<15){switch($l){case 1: $m=8;break; case 2: $m=18;break; case 3: $m=40;break; case 4: $m=80;break; case 5: $m=150;break;default:$m=$hr4[$l];break;}
			}elseif($o>14 && $o<17){switch($l){case 1: $m=8;break; case 2: $m=19;break; case 3: $m=42;break; case 4: $m=84;break; case 5: $m=158;break;default:$m=$hr5[$l];break;}
			}elseif($o>16 && $o<19){switch($l){case 1: $m=9;break; case 2: $m=20;break; case 3: $m=45;break; case 4: $m=90;break; case 5: $m=169;break;default:$m=$hr6[$l];break;}
			}elseif($o>18){switch($l){case 1: $m=9;break; case 2: $m=21;break; case 3: $m=49;break; case 4: $m=98;break; case 5: $m=184;break;default:$m=$hr7[$l];break;}
			}
		break;
		case 3: //kouzelnik
			if ($i<10){switch($l){case 1: $m=7;break; case 2: $m=10;break; case 3: $m=12;break; case 4: $m=14;break; case 5: $m=17;break;default:$m=$hr1[$l];break;}
			}elseif($i>9 && $i<12){switch($l){case 1: $m=7;break; case 2: $m=11;break; case 3: $m=14;break; case 4: $m=17;break; case 5: $m=20;break;default:$m=$hr2[$l];break;}
			}elseif($i>11 && $i<14){switch($l){case 1: $m=8;break; case 2: $m=12;break; case 3: $m=15;break; case 4: $m=19;break; case 5: $m=22;break;default:$m=$hr3[$l];break;}
			}elseif($i>13 && $i<16){switch($l){case 1: $m=8;break; case 2: $m=12;break; case 3: $m=16;break; case 4: $m=20;break; case 5: $m=24;break;default:$m=$hr4[$l];break;}
			}elseif($i>15 && $i<18){switch($l){case 1: $m=8;break; case 2: $m=12;break; case 3: $m=17;break; case 4: $m=21;break; case 5: $m=26;break;default:$m=$hr5[$l];break;}
			}elseif($i>17 && $i<20){switch($l){case 1: $m=9; break; case 2: $m=13; break; case 3: $m=18; break; case 4: $m=23; break; case 5: $m=28; break;default:$m=$hr6[$l];break;}
			}elseif($i>19) {switch($l) {case 1: $m=9; break; case 2: $m=14; break; case 3: $m=20; break; case 4: $m=26; break; case 5: $m=31; break;default:$m=$hr7[$l];break;}
			}
		break;
	}
	return $m;
}

//prida jeden level ... pouze JEDEN !!! a to az na pozadani, jinak NE !!!
function level_up($id,$uroven,$zkusenosti,$povolani,$odolnost){
	global $postava;
	$life = $uroven_navic = 0;
	$pastL = $uroven;
	$newL = level_postavy($povolani,$zkusenosti);
	$rozdil = $newL - $pastL;
	$magyDo = false;
if ($rozdil>0){
	$uroven_navic = 1;
	$life=0;
	//pocet zivotu up
	if (is_object($postava)) {
		$magy_max = get_magy($postava->povolani,$postava->uroven+1,$postava->obratnost,$postava->inteligence);
		$magyDo = true;
		$povolani = $postava->povolani;
	}
	switch(floor($povolani/2)){
		case 0:
			$life = ($uroven<8) ? (mt_rand(1,10) + get_bonus($odolnost,1)) : 2;
		break;
		case 1:
			$life = ($uroven<8) ? (mt_rand(1,6) + 2 + get_bonus($odolnost,1)) : 2;
		break;
		case 2:
			$life = ($uroven<8) ? (mt_rand(1,6) + 1 + get_bonus($odolnost,1)) : 1;
		break;
		default:
			$life = ($uroven<8) ? (mt_rand(1,6) + get_bonus($odolnost,1)) : 1;
		break;
	}
	if($life < 1) {
		$life = 1;
	}
}
	if ($magyDo) {
		mysql_query ("UPDATE 3_herna_postava_drd SET magy = $magy_max, magy_max = $magy_max, zivoty = zivoty_max+$life, zivoty_max = zivoty_max+$life, uroven=uroven+$uroven_navic WHERE id='$id'");
	}
	else {
		mysql_query ("UPDATE 3_herna_postava_drd SET zivoty = zivoty_max+$life, zivoty_max = zivoty_max+$life, uroven=uroven+$uroven_navic WHERE id='$id'");
	}
}

function level_down($id,$uroven) {
	global $postava;
	$magyDo = false;
	if ($uroven > 1) {
		if (is_object($postava)) {
			$magy_max = get_magy($postava->povolani,$postava->uroven-1,$postava->obratnost,$postava->inteligence);
			$magyDo = true;
		}
		if ($magyDo) {
			mysql_query ("UPDATE 3_herna_postava_drd SET uroven=uroven-1, magy_max = $magy_max, magy = $magy_max WHERE id='$id'");
		}
		else {
			mysql_query ("UPDATE 3_herna_postava_drd SET uroven=uroven-1 WHERE id='$id'");
		}
	}
}

function bojeschopnost ($z_max,$odol){
	if ($odol < 6) {
		return "Počet životů pro vyřazení: ".floor($z_max/4)." | postih za méně, než ".floor($z_max/3)."životů je -3";
	}
	elseif ($odol < 12) {
		return "Počet životů pro vyřazení: ".floor($z_max/6)." | postih za méně, než ".floor($z_max/3)."životů je -2";
	}
	elseif ($odol < 17) {
		return "Počet životů pro vyřazení: ".floor($z_max/8)." | postih za méně, než ".floor($z_max/3)."životů je -1";
	}
	elseif ($odol < 22) {
		return "Počet životů pro vyřazení: 1 | postih za méně, než ".floor($z_max/3)."životů je 0";
	}
	else {
		return "";
	}
}

?>
