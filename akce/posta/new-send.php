<?php
//poslani zpravy uzivateli/um
$error = 0;
mb_internal_encoding("UTF-8");

if (!$LogedIn) {
	header("Location: $inc");
	exit;
}

if (mb_strlen($_POST['us']) < 1 ){
	$error = 1;
}elseif (mb_strlen(trim($_POST['mess'])) < 1 ){
	$error = 2;
}else{

	$us = explode(",", trim(trim($_POST['us']), ","));
	$usC = count($us);

	for ($i=0;$i < $usC;$i++) {
		if (trim(mb_strToLower($us[$i])) == $_SESSION['login']) continue;
		$usJ[] = addslashes(do_seo($us[$i]));
	}
	$usJ = array_unique($usJ);

	$usQ = join("','",$usJ);

	$qCS = mysql_query ("SELECT id,login FROM 3_users WHERE login_rew IN ('$usQ') AND reg_code = 0 AND id > 1 ORDER BY login ASC");
	$qC = mysql_num_rows($qCS);

	if ($qC > 0) {

		// zjisteni loginu
		$Logins = $UIDs = array();
		$whisStav = "";
		while($loginer = mysql_fetch_row($qCS)){
			$UIDs[] = $loginer[0];
			$Logins[] = $loginer[1];
			$whisStav .= "0";
		}
//		mysql_free_result($qCS);
		$whis = join(",",$UIDs);

		// vytvoreni textu zpravy
		$text = strtr(editor($_POST['mess']),$changeToXHTML);
		$hash = addslashes(md5($text));
		$messId = 0;
		$jeHashS = mysql_query("SELECT id,content FROM 3_post_text WHERE hash = '$hash' ORDER BY id ASC");
		if ($jeHashS && mysql_num_rows($jeHashS)>0){
			while($jeHash = mysql_fetch_row($jeHashS)) {
				if (trim($jeHash[1]) == trim($text)) {
					$messId = $jeHash[0];
					break;
				}
			}
		}

		mysql_query("LOCK TABLES 3_post_text WRITE, 3_post_new WRITE;");
		if ($messId == 0) {
			$text = addslashes($text);
			mysql_query("INSERT INTO 3_post_text (hash, content) VALUES ('$hash','$text')");
			$messId = mysql_insert_id();
		}

		if ($qC == 1) {
			mysql_query("INSERT INTO 3_post_new (mid,fid,tid,cas,stavfrom,stavto) VALUES ($messId,$_SESSION[uid],$UIDs[0],$time,'1','0')");
			if (isset($AragornCache)) {
				$AragornCache->delVal("post-unread:$UIDs[0]");
			}
		}
		else {
			mysql_query("INSERT INTO 3_post_new (mid,fid,tid,whis,whisstav,cas,stavfrom,stavto) VALUES ($messId,$_SESSION[uid],0,'$whis','$whisStav',$time,'1','3')");
			$parentId = mysql_insert_id();
			$sqlI = array();
			for($i=0;$i<count($UIDs);$i++){
				if (isset($AragornCache)) {
					$AragornCache->delVal("post-unread:$UIDs[$i]");
				}
				$sqlI[] = "($parentId,$messId,$_SESSION[uid],$UIDs[$i],$time,'3','0')";
			}
			mysql_query('INSERT INTO 3_post_new (parent,mid,fid,tid,cas,stavfrom,stavto) VALUES '.join(",", $sqlI));
		}
		mysql_query("UNLOCK TABLES");

		// odeslani kazdemu loginu
		for($i=0;$i<count($UIDs);$i++){
			//upozorneni na postu, pokud je prijemce na chatu
			$vCh = mysql_query ("SELECT rid FROM 3_chat_users WHERE uid = $UIDs[$i]");
			while($oCh = mysql_fetch_object($vCh)){
				$text = addslashes("Máte novou poštu (od $_SESSION[login]) <small>&raquo;<a href='/posta/konverzace/$_SESSION[login_rew]/' title='Otevřít poštu' target='_blank'>otevřít Poštolku</a></small>.");
				if($oCh->rid > 1){
					mysql_query ("INSERT INTO 3_chat_mess (uid, rid, wh, time, text) VALUES (0, $oCh->rid, $UIDs[$i], $time, '$text')");
				}else{
					$text = "Máte novou poštu (od $_SESSION[login]) <small>&raquo;<a href='/posta/konverzace/$_SESSION[login_rew]/' title='Otevřít konverzaci' target='_blank'>otevřít Poštolku</a></small>.";
					ajaxChatInsertSystemWhisper($text, $oCh->rid, $UIDs[$i], $Logins[$i]);
				}
			}
		}

	}else{
		$error = 3;
	}

}

$addUrl = "";
if ($slink != "") {
  $addUrl = $slink."/";
}
//redirect pri chybe / uspesny redirect
if ($error > 0){
	header ("Location:$inc/$link/$addUrl?error=$error&_t=$time");
	exit;
}else{
	header ("Location:$inc/$link/$addUrl?ok=1&_t=$time");
	exit;
}
?>
