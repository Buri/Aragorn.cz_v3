<?php

function isRozAdmin($uid) {
  $config["dbtablearagchatadmins"]="3_chat_admin";
  $query = "SELECT id FROM `".$config["dbtablearagchatadmins"]."` WHERE uid='".$uid."' AND typ='1'";
  $result = mysql_query($query);
  $rowcount = 0;
  if ($result) $rowcount=mysql_num_rows($result);
  if ($rowcount==0) 
    return false;
  else 
    return true;   
}

function isAdmin($uid) {;
  $config["dbtablearagusers"]='3_users';
  $query="SELECT id FROM `".$config["dbtablearagusers"]."` WHERE level>2 AND id=".$uid;
  $result = mysql_query($query);
  $rowcount = 0;
  if ($result) $rowcount=mysql_num_rows($result);
  if ($rowcount==0) 
    return false;
  else 
    return true;   
}

function isProgrammer($login) {
  $config["programmers"]=array("apophis","buri the great");
  if (in_array(strtolower($login),$config["programmers"])) 
    return true;
  else 
    return false;   
}

?>
