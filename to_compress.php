<?php
session_start();
$allowed = false;
if (isset($_SESSION['login']) && isset($_SESSION['uid'])){
	if ($_SESSION['login'] == "apophis"){
		$allowed = true;
	}
}
if (!$allowed) {
	die("Jen pro povolane lidi!");
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; " />
    <title></title>
  </head>
  <style type="text/css">
  /* <![CDATA[ */
  	body{font-size:10px;font-family: Tahoma, Arial, lucida, sans-serif;}
    a {color:red;display:block;line-height:10px;margin:5px 0 0 0;text-decoration: none;}
    a:active{color:blue;}
    a:hover{color:#CA0000;text-decoration: underline;}
    a.done{font-weight:bold;}
  /* ]]> */
  </style>
  <script charset="utf-8" src="js/mootools.js" type="text/javascript"></script>
  <script charset="utf-8" type="text/javascript">
  /* <![CDATA[ */
    window.addEvent('domready',function(){
			$$('a').addEvent('click',function(event){
				if (event) {
					event = new Event(event);
					event.stop();
				}
				new Request({
					url:this.get('href'),
					async:true,
					method:'get',
					onRequest:function(){
						this.removeEvent('click').set('href','#');
						this.appendText(' ...');
					}.bind(this),
					onSuccess:function(responseText, responseXML){
						if (responseText) {
							this.appendText(' :'+responseText+":");
						}
						this.appendText(' done').addClass('done');
						if (!isNaN(responseText)){
							responseXML = this.getNext();
							if (responseXML) {
								responseXML.fireEvent('click',false);
							}
						}
					}.bind(this)
				}).send();
				return false;
			});
		});
  /* ]]> */
  </script>
  <body>
<?php
	$doDecompress = 0;
	if (isset($_GET['w'])) $doDecompress = $_GET['w'];
	if ($doDecompress > 0) $doDecompress = 1;

	$noOutputBuffer = 1;

	echo "decompress: $doDecompress<br /><br />";

	include "./db/conn.php";
	if (isset($_GET['s'])) {
		$s = intval($_GET['s']);
		$c = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_comm_$s"));
	}
	else {
		$c = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_clanky"));
		$s = "clanky";
	}
//	$c = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_post_text"));
//	$c = mysql_fetch_row(mysql_query("SELECT count(*) FROM 3_clanky WHERE compressed = '$doDecompress'"));

	for($i=0;$i<$c[0];$i+=100){
		echo "<a href='compress_do.php?w=$doDecompress&amp;s=$s&amp;i=$i'>".($i+1)." - ".($i+100)."</a>\n";
//		echo "<a href='compress_do.php?w=$doDecompress&amp;s=$_GET[s]&amp;i=$i'>".($i+1)." - ".($i+1000)."</a>\n";
//		echo "<a href='compress_do.php?w=$doDecompress&amp;s=clanky&amp;i=$i'>".($i+1)." - ".($i+1000)."</a>\n";
	}

?>
    
  </body>
</html>