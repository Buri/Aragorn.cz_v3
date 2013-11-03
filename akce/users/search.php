<?php

if (strlen ($_POST['user']) > 0){
$sUs = do_seo($_POST['user']);

$search = mysql_fetch_row( mysql_query ("SELECT count(*) FROM 3_users WHERE login_rew = '$sUs' AND reg_code = 0") );

if ($search[0] > 0){
  $ok = 1;
}else{
  $error = 2;
}
}else{
  $error = 1;
}

//redirect pri chybe / uspesny redirect
if (isSet($error)){
  Header ("Location:$inc/uzivatele/?error=$error");
}else{
    Header ("Location:$inc/uzivatele/$sUs/");
}
exit;
?>
