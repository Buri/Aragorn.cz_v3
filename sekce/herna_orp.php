<?php echo "<?"."xml version=\"1.0\" encoding=\"UTF-8\""."?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Untitled</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type='text/css'>
.plusminus {
	padding: 2px;
	text-decoration: none;
}
select {
	width: 150px;
}
option {
	width: 300px;
}

#frm div span {
	width: 300px;
	float:left;
	clear: left;
	text-align:right;
	margin-right: 5px;
  padding-right: 5px;
}
#frm div {
	font-size: 120%;
  clear: both;
  display: block;
  margin: 1px 0 0 0;
  background-color: #e3e3e3;
}
#frm div label {
}
#frm input {
	border-collapse: collapse;
	border: none;
	padding: 0;
	margin: 0 5px;
}
#frm .button {
	width: 150px;
  background-color: #ccc;
  font-weight: bold;
  color: black;
  margin: 0;
}
</style>
<script type="text/javascript">
<!--
function sender(f) {
	var s = document.getElementById("frm").elements;
	for (var a = 0; a < s.length; a++) {
		if (s[a].tagName.toUpperCase() == "SELECT" && s[a].options.length>1) {
			var o = s[a].options;
			var t = "";
			for (var i = 0; i < o.length; i++) {
				if ( i > 0 ) {
					t += "&";
				}
				t += encodeURIComponent(o[i].text);
			}
			var p = s[a];
			while(p.tagName.toUpperCase() != "DIV" && p.parentNode) p = p.parentNode;
			var y = p.getElementsByTagName("INPUT");
			y[0].value = t;
		}
		else if (s[a].tagName.toUpperCase() == "SELECT" && s[a].options.length<2) {
		  alert("Pro typ 'výběr z možností' musíte vytvořit nějaké možnosti.");
		  s[a].focus();
		  return false;
		}
		else if (s[a].tagName.toUpperCase() == "INPUT" && s[a].name.substr(0,6) == "random") {
			var p = s[a];
			while(p.tagName.toUpperCase() != "DIV" && p.parentNode) p = p.parentNode;
			var y = p.getElementsByTagName("INPUT");
			if (y.length == 5) {
				if (isNaN(y[3].value) || isNaN(y[4].value) || y[3].value.length == 0 || y[4].value.length == 0) {
					alert("Pro typ 'náhodné číslo' musíte zadat číselně dolní a horní mez.");
					if (isNaN(y[3].value) || y[3].value.length == 0) y[3].focus();
					else y[4].focus();
					return false;
				}
				else {
					y[0].value = y[3].value + ':' + y[4].value;
				}
			}
			else {
				alert("Pro typ políčka 'náhodné číslo' musíte zadat číselně horní a dolní mez.");
				return false;
			}
		}
	}
	f.submit();
	return false;
}

function additem(wh) {
	at = prompt("Text nové položky","");
	if (at) {
		x = document.getElementById(wh);
		a = document.createElement("option");
		a.innerHTML = at;
		//aa = document.createTextNode();
		x.appendChild(a);
	}
	return false;
}

function removeitem(wh) {
	var s = document.getElementById(wh);
	if (s.hasChildNodes()) {
		var e = s.options[s.selectedIndex];
		if (s.selectedIndex>0) {
			s = s.removeChild(e);
		}
	}
	return false;
}
//-->
</script>
</head>
<body>
<?php

$arr = "nazev pole>s>e>prvni>druha>treti"; // u deniku + ">hodnota";
$arr2 = "nazev pole>r>a>10>20";
$arr3 = "nazev pole>n>n";
$arr3 = "nazev pole>t>v";

/*
	field NAME > TYPE[a-rea,t-ext,n-umber,r-andom,s-elect] > EDITability+VIEWibility(see below) [+ > other[random:min>max,select:chioce1>choice2>choiceX] ]

a = editable + visible (all)
e = editable + invisible (editable)

v = uneditable + visible (visible)
n = uneditable + invisible (nothing)

*/

function orp_typ_pole($pole,$name) {
	$chb = $edt = "";
	$pole = explode(">",$pole);
	if ($pole[2]=="a" || $pole[2]=="v") {
		$chb = " checked";
	}
	if ($pole[2]=="a" || $pole[2]=="e") {
	  $edt = "checked";
	}
	$chb = "<label for='v-$name-id'>veřejné:</label><input id='v-$name-id' type='checkbox' value='on' name='v-$name'$chb />";
	$chb .= " | <label for='e-$name-id'>úpravy:</label><input id='e-$name-id' type='checkbox' value='on' name='e-$name'$edt />";

	switch ($pole[1]) {
	case "t":
		return "<span>$pole[0]</span> | $chb | krátký text";
	break;
	case "a":
		return "<span>$pole[0]</span> | $chb | delší text";
	break;
	case "s":
		for ($i=3;$i<count($pole);$i++) {
			$t .= "<option>".$pole[$i]."</option>";
		}
		$t = "<input type='hidden' name='h-$name-s' value='' /><span>$pole[0]</span> | $chb | <label for='select-$name'>výběr:</label> <select id='select-$name' name='select-$name'><option>- - - - -</option>" . $t;
		$t .= "</select><a href='#' class='plusminus' title='přidat možnost' onclick='additem(\"select-$name\");return false;'>+</a>/<a href='#' title='odebrat vybranou možnost' onclick='removeitem(\"select-$name\");return false;' class='plusminus'>&minus;</a>";
		return $t;
	break;
	case "n":
		return "$pole[0] | číslo | ".$chb;
	break;
	case "r":
		return "<input type='hidden' name='random-$name' value='' /><span>$pole[0]</span> | $chb | náhodné číslo | od <input type='text' value='$pole[3]' name='h-$name-low' size='5' /> do <input type='text' value='$pole[4]' name='h-$name-high' size='5' />";
	break;
	}
	return "";
}

echo "<form name='frm' id='frm' method='post' onsubmit='return sender(this)'>\n";
echo "<div>".orp_typ_pole($arr,"nazev")."</div>\n";
echo "<div>".orp_typ_pole($arr2,"nazevX")."</div>\n";
echo "<div>".orp_typ_pole($arr3,"nazevY")."</div>\n";
echo "<div style='clear:both'><input type='submit' class='button' value='Odeslat' /></div>\n";
echo "</form>
</body>
</html>";
exit;

if ($hItem->typ!='1') {
	echo "<div class='art'><p class='t-a-c'>Nastavení ORP je přístupné jen v jeskyních se systémem Open Role Play</p></div>\n";
}
else {
	if ($allow == "pj" || $allow == "hrac") {
	}
	else {
		info("Tato sekce je přístupná jen hráčům a majiteli této jeskyně.");
	}
}
?>
</body>
</html>
