<?php
//postnuti prispevku do komentare users

if ($LogedIn == true && $id > 0 && $id != $_SESSION['uid'] && $link == "uzivatele") {

//prazdny - vymaz komentare
if ($_POST['mess'] === ""){

  mysql_query ("DELETE FROM 3_u_comm WHERE cid = '$id' AND uid = $_SESSION[uid]");

}else{

$vc = mysql_fetch_row( mysql_query ("SELECT count(*) FROM 3_u_comm WHERE cid = '$id' AND uid = $_SESSION[uid]") );

$text = addslashes(strtr(editor(mb_substr($_POST["mess"],0,500)),$changeToXHTML));

if ($vc[0] > 0){

  mysql_query ("UPDATE 3_u_comm SET uid = '$_SESSION[uid]', cid = '$id', text = '$text' WHERE cid = '$id' AND uid = $_SESSION[uid]");
	Header ("Location:$inc/$link/$slink/?$time#comm-$_SESSION[login_rew]");
	exit;

}else{

  mysql_query ("INSERT INTO 3_u_comm (uid, cid, text) VALUES ($_SESSION[uid], '$id', '$text')");

}


}

}

Header ("Location:$inc/$link/$slink/?$time#comm-$_SESSION[login_rew]");
exit;
