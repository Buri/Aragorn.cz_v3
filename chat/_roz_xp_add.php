<?php 
require "../db/conn.php";
// osetreni vstupnich parametru

$xpcount=-1;
if (isSet($_GET["xpcount"]) && is_numeric($_GET["xpcount"]) && ($_GET["xpcount"]>=0) && ($_GET["xpcount"]<6) ) $xpcount=$_GET["xpcount"];

$xpto=-1;
if (isSet($_GET["xpto"]) && is_numeric($_GET["xpto"])) $xpto=$_GET["xpto"];

$xpwhy='';
if (isSet($_GET["xpwhy"])) $xpwhy=mysql_real_escape_string($_GET["xpwhy"]);

// číslo správce rozcestí získáme ze session
$uid=-1;
if (isSet($_SESSION["uid"])) $uid=$_SESSION["uid"];

$login="";
if (isSet($_SESSION["login"])) $login=$_SESSION["login"];

//  a ověříme, že přihlášený uživatel je oprávněný přidávat XP
if ($uid==-1 || $login=='') { 
  echo "Uživatel není přihlášený";
  // poslat IP adresu do poštolky? Může to být bot? Sem by se žádný bot neměl dostat, protože se mu vůbec "nezobrazí" předchozí obrazovka
}
else {  
  include_once("../hater_custom_functions.php");
  if (isRozAdmin($uid) || isAdmin($uid) || isProgrammer($login)) {

    if ($xpcount!=-1 && $xpto!=-1 && $xpwhy!='') {
      
      // uložíme záznam o hodnocení
      $config["dbtablerozxp"]='3_roz_xp';
      $query = "INSERT INTO ".$config["dbtablerozxp"]." (player,count,admin,comment) VALUES('".$xpto."','".$xpcount."','".$login."','".$xpwhy."')";  
      $result = mysql_query($query);      
      if (!$result) {
        echo "uložení záznamu se nezdařilo: ". mysql_error();
      }
      else {  
        // upravíme statistiku uživatele
        $config["dbtablerozxpstatsmed"]='3_roz_xp_stats';
        $query = "SELECT m0,m1,m2,m3,m4,m5,level,sum FROM ".$config["dbtablerozxpstatsmed"]." WHERE uid='".$xpto."'";
        $result = mysql_query($query);
        $rownumber=0;
        if ($result) $rownumber=mysql_num_rows($result);
        if ($rownumber==0) $query = "INSERT INTO ".$config["dbtablerozxpstatsmed"]." (uid,m".$xpcount.",level,sum) VALUES(".$xpto.",'1','".$xpcount."','".$xpcount."')";  
        else {
          $row = mysql_fetch_row($result);
          $newvalue = $row[$xpcount] + 1;
          $newlevel=$row[6];          
          if ($newvalue > $row[$newlevel]) {
            $newlevel=$xpcount;
          }     
          $sum = $row[7] + $xpcount;
          $query = "UPDATE ".$config["dbtablerozxpstatsmed"]." SET m".$xpcount."='".$newvalue."', level='".$newlevel."', sum='".$sum."', timestamp=NOW() WHERE uid='".$xpto."'";
        }
        $result = mysql_query($query);
        if ($result) echo "OK - uživateli ".$_GET["xplogin"]." bylo přidáno ".$xpcount." XP";
        else echo "uložení záznamu se zdařilo, ale neprovedla se aktualizace statistik: ".mysql_error()." požádejte o přepočtení statistik uživatele ".$login; 
      }
    }
    else echo "Špatně zadané vstupní parametry";
  
  }
  else {
    // pokud se to nepodaří, jedná se o bezpečnostní incident, někdo zkouší přidat XP "načerno", zaslat poštolku adminům?
    echo "K těmto úpravám nemáte oprávnění a uživatelské rozhraní Vám je ani nenabízí. Čas incidentu, Vaše přihlašovací jméno, IP adresa a veškeré dostupné údaje byly právě předány správcům systému.";
  }
}


?>
