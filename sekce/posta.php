<h2 class='h2-head'><a href='/<?php echo $link;?>/' title='Pošta'>Pošta</a></h2>
<h3><a href='/<?php echo $link;?>/' title='Pošta'>Mé zprávy</a></h3>
<p class='submenu'><a href="/posta/" class='permalink' title='Nová verze pošty'>Nová pošta</a> <a href="javascript: del()" class='permalink' title='Smazat označené'>Smazat označené</a> <a href="javascript: conf('/<?php echo $link;?>/?akce=posta-deleteall')" class='permalink' title='Smazat vše'>Smazat vše</a></p>

<?php
$uU = array();
if(intval($_GET['ok']) == 1 || isSet($_GET['to']) > 0){

  $display = "block";

}else{

  $display = "none";

}

if (isSet($_GET['error'])){

switch ($_GET['error']){

case 1:
  $error = "Nebyl vyplněn příjemce zprávy.";
break;

case 2:
  $error = "Nebyla zadána zpráva.";
break;

case 3:
  $error = "Příjemce(i) neexistuje.";
break;

case 4:
  $error = "V pole pro příjemce se nachází Váš nick.";
break;

}

info($error);
}elseif (isSet($_GET['ok'])){

switch ($_GET['ok']){

case 1:
  $ok = "Zpráva v pořádku odeslána.";
break;

case 2:
  $ok = "Vybrané zprávy smazány.";
break;

case 3:
  $ok = "Pošta vyprázdněna.";
break;

}

ok($ok);

}

?>
<div id='0' style='display: <?php echo $display; ?>'>
<div class='f-top'></div>
<div class='f-middle'>
<form action='/posta/?akce=posta-send-new' name='form_for_new' id='form_for_new' method='post' class='f fd' onsubmit="return checkForNew('posta','to',['us','mess'],false);">
<fieldset>
<div>Nová zpráva <a href="javascript: hide(0)" class='permalinkb flink' title='Zavřít'>Zavřít</a></div>
<label><span>Komu</span><input type='text' name='us' id='to' value='<?php echo _htmlspec(stripslashes($_GET['to'])); ?>' size='20' maxlength='200' /></label>
<?php
$friendsListS = mysql_query("SELECT u.login FROM 3_friends AS f, 3_users AS u WHERE u.id = f.fid AND f.uid = $_SESSION[uid] ORDER BY login ASC");
if (mysql_num_rows($friendsListS)>0) {
	echo "<div><a href=\"javascript:hide('friends-list')\" title=\"Zobrazit/skrýt seznam přátel\">Přátelé</a><div id='friends-list' style='display:none'>\n";
	$friends = array();
	while ($friend = mysql_fetch_row($friendsListS)) {
		$friends[] = "<a href=\"javascript:rep('"._htmlspec($friend[0])."')\">$friend[0]</a>";
	}
	echo join(", ", $friends);
	echo "</div></div>";
}
?>
<label><span>Zpráva</span><textarea rows='8' name='mess' id='km' /></textarea><span><a href='javascript: vloz_tag("b")'><img src='/system/editor/bold.jpg' alt='Tučně' title='Tučně' /></a> <a href='javascript: vloz_tag("i")'><img src='/system/editor/kur.jpg' alt='Kurzívou' title='Kurzívou' /></a> <a href='javascript: vloz_tag("u")' alt='Podtrhnout' title='Podtrhnout'><img src='/system/editor/und.jpg' /></a> <a href='javascript: editor(4)' alt='Odkaz' title='Odkaz'><img src='/system/editor/link.jpg' /></a> <a href='javascript: editor(5)' alt='Obrázek' title='Obrázek'><img src='/system/editor/pict.jpg' /></a></span></label>
<input id='button' type='submit' value='Odeslat zprávu' />
</fieldset>
</form>
</div>
<div class='f-bottom'></div>
</div>

<?php
$cP = mysql_fetch_row( mysql_query ("SELECT count(*) FROM 3_post WHERE oid = $_SESSION[uid]") );
$aC = $cP[0];

if ($aC > 0){

    if (!isSet($_GET['index'])){
       $index = 1;
    }else{
       $index = $_GET['index'];
    }

      $from = ($index - 1) * $postPC; //od kolikate polozky zobrazit
?>
<p class='strankovani'><?php echo make_pages($aC, $postPC, $index); ?></p>

<?php
//select zprav a adresata
$sel_mess = mysql_query ("SELECT p.*, u.login, u.login_rew, u.level, u.ico FROM 3_post AS p, 3_users AS u WHERE p.oid = $_SESSION[uid] AND p.fid = u.id ORDER BY p.date DESC LIMIT $from, $postPC ");
$uC = mysql_num_rows($sel_mess);

  $c = 0;
  
//select prijemce
$sel_tid = mysql_query ("SELECT p.*, u.login, u.login_rew, u.level, u.ico FROM 3_post AS p, 3_users AS u WHERE p.oid = $_SESSION[uid] AND p.tid = u.id ORDER BY p.date DESC LIMIT $from, $postPC ");

while ($pTid = mysql_fetch_object($sel_tid)){
  
  if ($pTid->login !== $_SESSION['login']){
  $tId[] = "<span".sl($pTid->level, 2)."><a href=\"javascript: rep('$pTid->login')\" title='Napsat zprávu'>$pTid->login</a></span>";
    }else{
  $tId[] = "<span".sl($pTid->level, 2)."><a href='/uzivatele/$pTid->login_rew/' title='Profil uživatele'>$pTid->login</a></span>";
  }
}

while ($pT = mysql_fetch_object($sel_mess)){

    if (($c+1) == $uC){
        $st = " style='margin: 0'";
    }else{
        $st = "";
    }
    
    if ($pT->login != $_SESSION['login']){
  $varN = "<span".sl($pT->level, 2)."><a href=\"javascript: rep('$pT->login')\" title='Napsat zprávu'>$pT->login</a></span>";
      $varR = "";
    }else{
  $varN = "<span".sl($pT->level, 2)."><a href='/uzivatele/$pT->login_rew/' title='Profil uživatele'>$pT->login</a></span>";
 
           //indikace prectenych zprav
          $iR = mysql_query ("SELECT r FROM 3_post WHERE id = $pT->id+1");
          $oR = mysql_fetch_row($iR);
            if($oR[0] > 0 || mysql_num_rows($iR) < 1){
              $varR = "<span id='rp'>přečteno</span>";
            }else{
              $varR = "<span id='unp'>nepřečteno</span>";
            }
  }
  //neprectena
  if ($pT->r < 1){
    $uU[] = $pT->id;
    $unR = " unr";
  }else{
    $unR = "";  
  }
?>

  <table class='commtb' cellspacing='0'<?php echo $st;?>>
    <tr><td class='c1' colspan='2' ><?php echo $varN; ?> uživateli <?php echo $tId[$c];?> - <?php echo sdh($pT->date);?> <input type='checkbox' id='<?php echo $pT->id; ?>' /> <a href="javascript: hide('<?php echo "h$pT->id" ?>')" title='Schovat'><img src='/system/ruzne/arrow.gif' title='Schovat' alt='Schovat' /></a></td></tr>
    <tr id='h<?php echo $pT->id ?>'>
    <td class='c2'><a href='/uzivatele/<?php echo $pT->login_rew;?>/' title='Profil uživatele'><img src='http://s1.aragorn.cz/i/<?php echo $pT->ico;?>' alt='Profil uživatele' title='Profil uživatele' /></a><br /><?php echo $varR; ?></td>
    <td class='c3'>
      <p class='c4<?php echo $unR ; ?>'>
        <?php echo spit($pT->text, 1)."\n";?>
      </p>
    </td>
    </tr>
  </table>

<?php
  $c++;
    $js[] = "'$pT->id'";
}

//precteni zprav
if (count ($uU) > 0){

$mU = join(",",$uU);
    mysql_query ("UPDATE 3_post SET r = 1 WHERE id IN ($mU)");
}

    $jsR = join(",",$js); //retezec pro js
?>

<p class='strankovani'><?php echo make_pages($aC, $postPC, $index); ?></p>
<?php
}
?>

<script language='JavaScript' type='text/javascript'>
function rep(to){
    if(document.getElementById(0).style.display == "none"){
      document.getElementById(0).style.display = "block"
  }
  document.getElementById('to').value = to
}

function del(){
pole = new Array()
pc = 0
  ids = new Array(<?php echo $jsR; ?>)
    idsl = ids.length
  for(i=0; i < idsl; i++){
    if(document.getElementById(ids[i]).checked == true){
      pole[pc] = ids[i]
      pc++
    }
  }
if (pole.length > 0){
  vymaz = pole.join(",")
  window.location.href = "/<?php echo $link;?>/?akce=posta-delete&ids="+vymaz
}else{
  alert("Žádné vybrané položky ke smazání")
}
}

<?php
//zprava ze pratel
if (isSet($_GET['friend'])){
  echo "rep('$_GET[friend]')";
}
?>
</script>
