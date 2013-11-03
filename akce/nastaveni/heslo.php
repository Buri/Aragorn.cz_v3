<?php
// zmena hesla
if (isset($_SESSION['login']) && $_SESSION['uid'] > 0) {

$uC = mysql_fetch_row( mysql_query ("select count(*) from 3_users where id = '$_SESSION[uid]' and pass = '".md5($_POST['old_pass'])."' ") );

if (strlen($_POST['pass']) < 5){
  $error = 5;
}elseif (strlen($_POST['pass2']) < 5){
  $error = 6;
}elseif ($_POST['pass'] !== $_POST['pass2']){
  $error = 7;
}elseif ($uC[0] < 1){
  $error = 8;
}else{
  mysql_query ("update 3_users set pass = '".md5($_POST['pass'])."' where id = '$_SESSION[uid]'");
}

}
else {
	$error = 5;
}
//redirect pri chybe / uspesny redirect
if (isSet($error)){
  Header ("Location:$inc/nastaveni/osobni/?error=$error");
}else{
    Header ("Location:$inc/nastaveni/osobni/?ok=2");
}
exit;
?>
