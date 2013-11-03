<?php

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-type: text/plain; charset=UTF-8");
$fp = fopen('./xplog.txt', 'r');
fpassthru($fp);