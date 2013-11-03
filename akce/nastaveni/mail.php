<?php
// zmena mailu
$_mail = addslashes($_POST['mail']);
$uC = mysql_fetch_row( mysql_query ("SELECT count(*) FROM 3_users where id != $_SESSION[uid] and mail = '$_mail'") );

if ($uC[0] > 0){
  $error = 9;
}else{
  mysql_query ("UPDATE 3_users SET mail = '".$_mail."' WHERE id = $_SESSION[uid]");
}

//redirect pri chybe / uspesny redirect
if (isSet($error)){
  Header ("Location:$inc/nastaveni/osobni/?error=$error");
}else{
    Header ("Location:$inc/nastaveni/osobni/?ok=3");
}
exit;
?>
