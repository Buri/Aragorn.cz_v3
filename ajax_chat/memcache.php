<?php // ěščřžýá UTF8 check
define("THE_URL","/ajax_chat/memcache.php");

ob_start();
session_start();

include "../db/dibi.min.php";
include "../db/credentials.php";

dibi::connect(array(
	'driver'   => 'mysqli',
	'host'     => $se1,
	'username' => $us1,
	'password' => $pa1,
	'database' => $db1,
	'charset'  => 'utf8',
));

class mc_chat {
	
	var $chan; // channel
	var $chanType; // channel type
	var $chanNumber; // channel number
	var $mc; // memcache object
	var $ret = 5; // retention - how many messages are loaded (max-min=retention) 
	var $body; // body of the whole response
	var $users; // users in room
	var $time;
	var $firstLine;
	var $uid;

	function __construct(){
		$this->time = time();
		$this->firstLine = "";
		$this->body = array();
		if (!isset($_GET['rid'])) {
			$this->endChat('Nebylo urceno ID mistnosti.');
		}
	}

	function initialize($server, $port, $channelNumber, $channelType="chat", $retention=5) {

		if (isset($_SESSION['uid'])){
			$this->uid = $_SESSION['uid'];
		}
		else {
			$this->uid = 0;
		}
		$memcached = new Memcache;
		$memcached->connect($server, $port) or die ('{"error":"Chyba pripojeni se serverem."}');

		$this->mc = $memcached;

		$this->chan = $channelType."-".$channelNumber;
		$this->chanNumber = $channelNumber;
		$this->chanType = $channelType;

		$this->ret = $retention;

		$this->retrieveUsers();
	}

	function isGet($ar=false) { // helper IO function
		if ($ar) {
			if (is_array($ar)) {
				$a = count($ar);
				for($i = 0 ; $i < $a; $i++) {
					if (isset($_GET[$ar[$i]])) {
						return true;
					}
				}
			}
			else {
				if (isset($_GET[$ar])) {
					return true;
				}
			}
		}
		return false;
	}

	function incVal($k) { // helper function
		return $this->mc->increment("$this->chan:$k");
	}

	function getVal($k) { // helper function
		return $this->mc->get("$this->chan:$k");
	}

	function delVal($k) { // helper function
		return $this->mc->delete("$this->chan:$k");
	}

	function setVal($k,$v) { // helper function
		return $this->mc->set("$this->chan:$k", $v, false, 1296000);
	}

	function resetChat() {
		$reset = false;
		$users = $this->mc->getVal("users");
		if ($users){
			if (strlen($users)==0){
				$reset = true;
			}
		}
		else {
			$reset = true;
		}
		if ($reset) {
			$this->deleteAllMessages();
			$this->setVal("min:posted",1);
			$this->setVal("max:posted",1);
		}
	}

	function timeoutUser($a) {
		if (!$this->uid){
			$this->endChat($a);
		}
		else {
			$t = $this->getUser();
			if ($t) {
				$this->delVal("user:".$this->uid);
				unset($this->users[$this->uid]);
				$this->setVal('users',join("|",$this->users));
				// dibi::query("UPDATE [3_".$this->chanType."_users] SET [odesel] = 1 WHERE [rid] = %i AND [uid] = %i",array($this->chanNumber, $_SESSION['uid'] ) );
			}
			$this->endChat($a);
		}
	}

	function retrieveUsers() {
		$this->users = $this->getVal('users');
		if(!$this->users){
			$this->users = array();
		}
		else {
			$this->users = explode("|",$this->users);
			$this->users = array_combine($this->users, $this->users);
		}
	}

	function getUser() {
		return $this->getVal("user:".$this->uid);
	}

	function credentials() {
		$q = false;
		$t = false;
		$this->reEnterRoom();
		if ($this->uid > 0) {
			$q = $this->getUser();
			if ($q && isset($this->users[$this->uid])) {
				$t = intval($q) + ($_SESSION['lvl'] < 2 ? 900 : 1500);
			}
			else {
				$q = false;
			}
		}
		if (!$q && $th3is->uid > 0) {
			$q = dibi::query("SELECT [timestamp] FROM [3_".$this->chanType."_users] WHERE [odesel] = 0 AND [rid] = %i ", $this->chanNumber, " AND [uid] = %i ", $this->uid);
			if ($q) {
				$t = intval($q->fetchSingle());
				if ($t > 0) {
					$t += ($_SESSION['lvl'] < 2 ? 900 : 1500);
				}
				else {
					$t = false;
					$q = 'Uzivatel neni v mistnosti. A001.';
					$t = $this->time;
				}
			}
			else {
				$q = 'Uzivatel neni v mistnosti. B002.';
				$t = false;
			}
		}
		else {
			$this->reEnterRoom();
			$this->timeoutUser('Uzivatel nebyl nenalezen v mistnosti.');
		}

		if ($t) {
			if ($t < $this->time) {
				$this->timeoutUser('Vyprsel casovy limit.');
			}
			else {
				$this->user = $_SESSION;
			}
		}
		else {
			$this->timeoutUser($q);
		}
	}

	function reEnterRoom() {
		if (!isset($this->users[$this->uid]) && $this->uid > 0 && $this->isGet('enter') && !$this->isGet(array("messages","people","sender"))) {
			$u = $this->getUser();
			if (!$u) {
				$this->setVal('user:'.$this->uid,$this->time);
			}
			if (!isset($this->users[$this->uid])) {
				$this->users[$this->uid] = $this->uid;
				$this->setVal('users',join("|",$this->users));
			}
			header("Location: ".THE_URL."?rid=".$this->chanNumber);
		}
		else {
			header("Location: ".THE_URL."?rid=".$this->chanNumber);
		}
	}

	function loadMessages( $from=0 ) { // loads all messages from min to max, or uses @param from (int)
		$max = (int)$this->getVal("max:posted");
		$min = (int)$this->getVal("min:posted");
		$messages = array();
		for ( $i = max(0,$min); $i <= $max; $i++ ) {
			$m = $this->getVal("msg:$i");
			if ($m)
				$messages[$i] = $m;
		}
		return $messages;
	}

	function deleteOneMessage( $one = false ) { // deletes all messages from min to max, or uses @param one (int)
		if ( $one !== false) {
			$one = intval($one);
			return $this->deleteMessages($one);
		}
		return false;
	}

	function deleteMessages($one=false) { // deletes all messages from min to max, or uses @param one (int)
		if ($one !== false){
			$one = intval($one);
			return $this->delVal("msg:$one");
		}
		else {
			$max = (int)$this->getVal("max:posted");
			$min = (int)$this->getVal("min:posted");
			$messages = array();
			for ( $i = max(0,$min); $i <= $max; $i++ ) {
				$m = $this->delVal("msg:$i");
				if ($m)
					$messages[$i] = $m;
			}
			return true;
		}
		return $messages;
	}

	function makeText($uid,$fromName,$color,$tid,$toName,$type,$text) { // makes a string from all the inputed datas
		return "$tid|$uid|$fromName|$toName|$color|$type|$time|$text";
	}

	function unMakeText($txt) { // retrieves all data from the @param text (string) given 
		return explode("|",$txt,7);
	}

	function addMessage($txt) {
		$id = (int)$this->incVal("max:posted");
		if ( !$id ) {
			$id = 1;
			$this->setVal("max:posted",1);
		}
		$this->setVal("msg:$id", $txt);
		if ( $id >= $this->ret ) {
			if ( !$this->incVal("min:posted") )
				$this->setVal("min:posted", 1);
		}
	}

	function runApp() {
		if (isset($_GET['rid'])) {
			if (!isset($_GET['ajax'])) {
				$this->prepareHTML();
				$this->printAll();
			}
			else {
				if (isset($_GET['messages'])) {
					if ($_GET['messages'] == '1') {
						$this->lastMessagesAction();
					}
				}
				if (isset($_GET['sender'])) {
					if ($_GET['sender'] == '1') {
						$this->senderAction();
					}
				}
				if (isset($_GET['people'])) {
					if ($_GET['people'] == '1') {
						$this->peopleAction();
					}
				}
				$this->defaultAction();
			}
		}
		else {
			$this->noRoom();
		}
	}

	function noRoom() {
		$this->endChat("Nebylo urceno ID mistnosti.");
		exit;
	}

	function endChat($t) {
		if ($t) {
			echo '{"error":"'.addslashes($t).'"}';
		}
		else {
			echo '{"error":"Nespecifikovana chyba."}';
		}
		exit;
	}

	function lastMessagesAction() {
		if (isset($_GET['last'])){
			$last = intval($_GET['last']);
		}
		else {
			$last = 0;
		}
		$this->body['lastMessages'] = $this->loadMessages($last);
	}

	function retrieveText() {
	}

	function retrieveKomu() {
		$to = array(0,0);
		if (isset($_POST['komu'])){
			$to[0] = intval($_POST['komu']);
			$to[1] = $this->getVal('user:'.$to[0]);
			if (!$to[1]) {
				//
			}
		}
		return $to;
	}

	function senderAction() {
		if (isset($_POST['text'])){
			$to = $this->retrieveKomu();
			$text = $this->retrieveText();
			$this->addMessage($this->makeText($_SESSION['uid'],$_SESSION['login'],$this->user->color,$to[0],$to[1],0,$text));
		}
	}

	function peopleAction() {
		$usersSource = $this->getVal('users');
		if ($usersSource) {
			$this->body['memoredUsers'] = $usersSource;
			$usersSource = explode("|", $usersSource);
			$users = array();
			foreach($usersSource as $k=>$v) {
				$k = $this->getVal('user:'.$v);
				if ($k) {
					$users[$v] = $k;
				}
			}
			$this->body['users'] = $users;
		}
		else {
			$this->body['users'] = array("empty");
		}
	}

	function defaultAction() {
		$this->printAll();
	}

	function prepareHTML() {
		header("Content-type:text/html;charset=utf-8");
		header("Vary: Accept-Encoding");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", time()-3600*4) . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0");
		header("Pragma: no-cache");
		$this->firstLine = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$this->body = "<html>
<head>
	<title>{TITLE}</title>
</head>
<body>
	<iframe src='/ajax_chat/memcache.php?rid=$this->chanNumber&amp;ajax=1&amp;messages=1' width='800' height='50'></iframe>
	<iframe src='/ajax_chat/memcache.php?rid=$this->chanNumber&amp;ajax=1&amp;people=1' width='800' height='50'></iframe>
	<p><a href='/ajax_chat/memcache.php?rid=$this->chanNumber&amp;enter=1'>enter memcache room</a></p>
</body>
</html>
";
	}

	function printAll() {
		if (is_array($this->body)) {
			echo json_encode($this->body);
		}
		else {
			echo "".$this->body."";
		}
	}

	function printStats() {
		print_r($this->mc->getExtendedStats());
	}
}

$keep_messages = 200; // how many messages to preload
$serverMemcache = '127.0.0.1';

$chat = new mc_chat();
$chat->initialize($serverMemcache, 11211, (int)$_GET['rid'], "chat", $keep_messages);

print_r($chat);
exit;

$chat->credentials();
$chat->runApp();

/*
*/
?> 