<?php

mysql_connect('localhost', 'buri', 'tajneheslododb');
mysql_select_db('aragorncz01');
$total = 15557;
$i = 0;
$q = mysql_query('SELECT mail FROM 3_users');
$domains = array();
while($row = mysql_fetch_object($q)){
$perc = $i / $total * 100;
/*if(round($perc) % 10 == 0)
echo round($perc) . "<br/>\n";*/
$d = strtolower(substr($row->mail, strpos($row->mail, '@') + 1));
if(empty($domains[$d]))
$domains[$d] = 1;
else
$domains[$d]++;
$i++;
}
arsort($domains);
?>
<table>
<?php
//print_r($domains);

/*foreach($domains as $dom => $count)*/
while(list($dom,$count) = each($domains))
echo "<tr><td>$dom</td><td>$count</td></tr>\n";

?>
</table>