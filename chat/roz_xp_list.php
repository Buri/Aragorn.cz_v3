<?php 

$config["hodnosti"]=array("Bezzemek","Vidlák","Páže","Voják","Důstojník","Rytíř");

function getRozXPTableData($who) {
 /**
  *  returns array with:
  *   - xp table    
  **/   
  $config["dbtablerozxpstatsmed"]='3_roz_xp_stats';
  $config["dbtablearagusers"]='3_users';
  $query = "SELECT t1.login,t2.level,t2.sum,t2.timestamp FROM ".$config["dbtablerozxpstatsmed"]." AS t2 INNER JOIN ".$config["dbtablearagusers"]." as t1 ON t1.id=t2.uid ORDER BY t2.level DESC,t2.sum DESC,t2.timestamp DESC"; 
  $result = mysql_query($query);
  if (!$result) {
    die("dotaz ".$query." se nepodarilo provest: ".mysql_error());
  }  
  return $result;    
}

function getRozXPAuditData($who) {
 /**
  *  returns array with $who's xp, who added them and why
  **/   
  $config["dbtablerozxp"]='3_roz_xp';
  $config["dbtablearagusers"]='3_users';
  $query = "SELECT t1.login,t2.count,t2.admin,t2.comment,t2.timestamp FROM ".$config["dbtablerozxp"]." AS t2 INNER JOIN ".$config["dbtablearagusers"]." as t1 ON t1.id=t2.player WHERE t1.login='".$who."'";
  $result = mysql_query($query);
  if (!$result) {
    die("dotaz ".$query." se nepodarilo provest: ".mysql_error());
  }
  return $result;   
}

function getRozXPPos($who,$table) {
/**
 *  find $who in table a get his position
 **/ 
  $userposition=0;
  $userlevel=0;
  $i=1;
  $j=1;
  $level=6;
  while ($row = mysql_fetch_array($table)) {
    while ($level!=$row[1]) {
      $level--;
      $i=1;
    }
    $i++;
    $j++;
    if ($row[0]==$who) {
      $userposition=$i;
      $userlevel=$level;
    }    
  }
  $result["userlevel"]=$userlevel;
  $result["positionincategory"]=$userposition;
  $result["position"]=$j;
  return $result;   
}

function printRozXPHeraldry($userlevel) {
/**
 *  print heraldry according to position
 **/  
   return "<img src='/graphic/svg/erb".$userlevel.".svg' id='heraldiclevel' width='100' heigth='105'>"; 
}

function printRozXPPos($userposition,$who) {
/**
 *  print position
 **/
global $config;
if ($userposition["positionincategory"]!=0)
  return "<table id='rank'><tr><td>Hodnost:</td> <td> ".$config["hodnosti"][$userposition["userlevel"]]."</td></tr>\n<tr><td>Celkové pořadí v žebříčku: </td><td> ".$userposition["position"]." místo</td></tr>\n <tr><td> Ve své kategorii: </td> <td>".$userposition["positionincategory"]." místo</td></tr>\n</table>\n";  
else return "<p>Hráč ".$who." nebyl hodnocen</p>";
}


function printRozXPTable($table) {
/**
 *  print XP ladder
 **/ 
  global $config;
  $level=6;
  $i=0;
  $ladder="<h2>Žebříček:</h2>\n";
  while ($row = mysql_fetch_array($table)) {
    while ($level!=$row[1]) {
      $level--;
      if ($level==5) {$ladder.="</table>\n";}
      $ladder.= "<table><tr><th colspan='4' class='level'>Hodnost ".$config["hodnosti"][$level]."</th></tr>\n <tr><th>#</th><th>login</th><th>získané XP</th><th>naposledy aktualizováno</th></tr>\n";
      $i=1;
    }
    $ladder.="<tr><td>".$i."</td><td><a href=\"roz_xp_list.php?who=".$row[0]."\">".$row[0]."</a></td><td>".$row[2]."</td><td>".$row[3]."</td></tr>\n";
    $i++; 
  }
  $ladder.= "</table>"; 
  return $ladder;
}

function printRozXPAudit($table) {
/**
 *  find $who in table a get his position
 **/ 
 
  $result = "<table>\n<tr><th>kdy</th><th>kdo je přidal</th><th># XP</th><th>komentář</th></tr>";
  while ($row = mysql_fetch_array($table)) {
    $result .= "<tr><td>".$row[4]."</td><td>".$row[2]."</td><td>".$row[1]."</td><td>".$row[3]."</td></tr>\n";  
  }
  $result .= "</table>";  
  return $result;
}


function printHeader($title) {
return '<!DOCTYPE html> 
<html lang="cs-cz" dir="ltr"> 
   <head> 
      <title>'.$title.'</title> 
      <meta charset="UTF-8">    
</head> 
<body> 
<style>
body {padding-left: 5%; padding-right: 5%;}
td {text-align: center;}
tr:nth-child(even) {background: #DDD}
tr:nth-child(odd) {background: #FFF}
table {width: 500px; border-collapse: collapse; margin-top: 10px;}
th.level {font-size: 16px;text-align:left; border-bottom: 1px solid;}

html {background-color: #dfdbd2;}
body {background-color: #3c3b37; color: #dfdbd2; width: 510px; padding-top: 20px; padding-bottom: 15px; margin: auto; border-radius: 15px; margin-top:10px; margin-bottom: 10px; border: 3px solid #eb6e39;box-shadow: 7px 4px 2px #999; border-right: 2px solid #eb6e39; border-bottom: 2px solid #eb6e39;}
a, a:visited {color: #eb6e39; text-decoration: none;}
a:hover {color: #fb7e49;}
tr:nth-child(even) {background: #3f3f39;}
tr:nth-child(odd) {background: #3c3b37;}
tr:hover {color: #efebe2;}
h1,h2,p,td,th {text-shadow: 2px 2px 2px #000; font-family: Tahoma,Arial,lucida,sans-serif}
h1 {font-size:22px;}
h2 {font-size:18px;}
p,td,th {font-size:14px;}
img#heraldiclevel {float: right; padding-top:20px;}
table#rank {width: 400px;}
table#rank td {text-align: left;}
table#rank td+td {text-align: center;}
</style>';
}

function printTitle($title) {
  return "<h1>".$title."</h1>";
}

function printFooter() {
  return "</body>\n</html>";
}

function doXP($who,$mode) {
  echo "doing XP for ". $who ." in mode ".$mode;
  if ($mode=="full") {
    $title="Aragorn.cz - zkušenosti v rozcestí";
    $result=getRozXPTableData($who);
    $position=getRozXPPos($who,$result);  
    echo printHeader($title);
    echo printRozXPHeraldry($position["userlevel"]);
    echo printTitle($title);
    echo printRozXPPos($position,$who);
    mysql_data_seek($result, 0);
    echo printRozXPTable($result);
    echo printFooter();
  }
  elseif ($mode=="audit") {
    $title="Aragorn.cz - ".$who." audit zkušeností v rozcestí";
    $result=getRozXPAuditData($who);  
    echo printHeader($title);
    echo printTitle($title);
    echo printRozXPAudit($result);
    echo printFooter();
  }
  elseif ($mode=="onlypos") {
    $result=getRozXPTableData($who);
    $position=getRozXPPos($who,$result);
    return printRozXPPos($position,$who); 
  }
  else {
    $title="Aragorn.cz - žebříček zkušeností v rozcestí";//bez pozice
    $result=getRozXPTableData($who);   
    echo printHeader($title);
    echo printTitle($title);
    echo printRozXPTable($result);
    echo printFooter();  
  }
  mysql_free_result($result);
}

require_once $_SERVER['DOCUMENT_ROOT']."/db/conn.php";
$mode='';
$who='';
if (isSet($_SESSION["login"])) {
  $who=$_SESSION["login"];
  $mode="full";
}
if (isSet($_GET["who"])) {
  $who=mysql_real_escape_string($_GET["who"]);
  $mode="audit";
}
$requesturl=explode("?",$_SERVER['REQUEST_URI']);
if ($requesturl[0]=="/chat/roz_xp_list.php") {  
  doXP($who,$mode);
}

?>
