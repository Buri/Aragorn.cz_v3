<?php

if (function_exists("domxml_new_doc")) {
	include_once("xml_dom-old.php");
} else {
	include_once("xml_dom-new.php");
}

?>
