<?php

class Aragorn_Memcache_Class {
	
	var $mc; // memcache object

	function __construct(){
	}

	function initialize($server, $port) {

		$memcached = new Memcache;
		$memcached->connect($server, $port);

		$this->mc = $memcached;

	}

	function incVal($k, $v = 1, $expire = 1296000) { // helper function
		if (!$this->mc) {
			return false;
		}
		$ret = $this->mc->increment($k);
		if ($ret === false) {
			return $this->setVal($k, $v, $expire);
		}
		else {
			return $ret;
		}
	}

	function getVal($k) { // helper function
		if (!$this->mc) {
			return false;
		}
		return $this->mc->get($k);
	}

	function delVal($k) { // helper function
		if (!$this->mc) {
			return false;
		}
		return $this->mc->delete($k);
	}

	function setVal($k, $v, $expire = 1296000) { // helper function
		if (!$this->mc) {
			return false;
		}
		return $this->mc->set($k, $v, false, $expire);
	}

	function replaceVal($k, $v, $expire = 1296000) { // helper function
		if (!$this->mc) {
			return false;
		}
		$ret = $this->mc->replace($k, $v, false, $expire);
		if ($ret === false) {
			return $this->mc->set($k, $v, false, $expire);
		}
		return $ret;
	}

	function getIcoSizes($icoFile, $dir = 'icos') {
		if (!$icoFile) {
			return;
		}
		$w = $this->getVal('sizes:system/'.$dir.'/'.$icoFile);
		if ($w === false) {
			$w = @getimagesize('./system/'.$dir.'/'.$icoFile);
			if (!$w) {
				$w = " ";
			}
			else {
				$w = $w[3];
			}
			$this->setVal('sizes:system/'.$dir.'/'.$icoFile, $w, 3600);
		}

		return $w;

	}

}

$AragornCache = new Aragorn_Memcache_Class();
$AragornCache->initialize('127.0.0.1', 11211);
