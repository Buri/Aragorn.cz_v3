<?php
if ($_GET['add'] != $_SESSION['uid'] && $_GET['del'] != $_SESSION['uid']){

if ($_GET['add'] > 0 && $_GET['add'] != $_SESSION['uid']){

  $cI = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_friends WHERE uid = $_SESSION[uid] AND fid = '".addslashes($_GET['add'])."'") );
  $cU = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_users WHERE id = '".addslashes($_GET['add'])."'") );

if ($cU[0] < 1){
  $error = 1;
}elseif($cI[0] > 0){
  $error = 2;
}else{
  mysql_query ("INSERT INTO 3_friends (uid, fid) VALUES ($_SESSION[uid], '".addslashes($_GET['add'])."')");
  $ok = 1;
}

}elseif ($_GET['del'] > 0 && $_GET['del'] != $_SESSION['uid']){

  $cI = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_friends WHERE uid = $_SESSION[uid] AND fid = '".addslashes($_GET['del'])."'") );
  $cU = mysql_fetch_row ( mysql_query ("SELECT COUNT(*) FROM 3_users WHERE id = '".addslashes($_GET['del'])."'") );

if ($cU[0] < 1){
  $error = 1;
}elseif($cI[0] < 1){
  $error = 3;
}else{
  mysql_query ("DELETE FROM 3_friends WHERE uid = $_SESSION[uid] AND fid = '".addslashes($_GET['del'])."'");
  $ok = 2;
}

}
else {
  $error = 4;
}
}else{
  $error = 4;
}

if (isSet($ok)){
Header ("Location:$inc/$link/$slink/?ok=$ok");
}else{
Header ("Location:$inc/$link/$slink/?error=$error");
}

exit;
?>
