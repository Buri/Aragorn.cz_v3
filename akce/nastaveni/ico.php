<?php
//nahrani ikonky na server
if (isset($_SESSION['login']) && $_SESSION['uid'] > 0) {

$type = ereg_replace("^.+\.(.+)$","\\1",$_FILES["ico"]["name"]);

$ico_n = Rand(1,9).Rand(1,9).Rand(1,9)."_".$_SESSION['uid'].".".$type;

move_uploaded_file ($_FILES["ico"]["tmp_name"], "./system/icos/$ico_n");

if (strlen ($_FILES["ico"]["name"]) < 3){
  $error = 1;
}elseif ( format_test($_FILES["ico"]["type"]) > 0 ){
  $error = 2;
}elseif( ico_size("./system/icos/$ico_n") > 0 ){
  $error = 3;
}elseif( ico_dat($_FILES["ico"]["size"]) > 0 ){
  $error = 4;
}else{
  
  $uIco = mysql_fetch_object( mysql_query("SELECT ico FROM 3_users WHERE id = '$_SESSION[uid]'") );
  //neni-li ikona defaultni, smaze se stara
  if ($uIco->ico != "default.jpg"){
    @unlink("./system/icos/$uIco->ico");
  }

  mysql_query ("UPDATE 3_users SET ico = '$ico_n' WHERE id = '$_SESSION[uid]'");
}

}
else {
	$error = 1;
}

//redirect pri chybe / uspesny redirect
if ($error>0){
    //smazani
  if ($ico_n != "default.jpg") {
    @unlink("./system/icos/$ico_n");
  }
  Header ("Location:$inc/nastaveni/osobni/?error=$error");
}else{
  Header ("Location:$inc/nastaveni/osobni/?ok=1");
}
exit;
?>
