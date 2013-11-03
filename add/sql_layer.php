<?php
/**
* BLOG:CMS: PHP/MySQL Personal Content Management System
* http://blogcms.com/
* http://forum.blogcms.com/
*
* 2003-2004, (c) Radek HULAN
* http://hulan.info/
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
**/

define ('_MYSQL40',0);
define ('_MYSQL41',1);
define ('_SQLITE', 2);
define ('_PGSQL' , 3);

$SQL_DATABASE='./../system/test.db'; // nazev souboru SQLite
$SQL_TYPE=_SQLITE; // typ databaze
$SQL_PREFIX=''; // prefix tabulek

/**
 * Connects to mysql, mysqli, sqlite DB
 */
function sql_connect($select = true) {
  global $SQL_HOST, $SQL_USER, $SQL_PASSWORD, $SQL_DATABASE, $SQL_PORT, $activedb, $SQL_TYPE;
  $activedb = false;
  switch ($SQL_TYPE) {
    case _MYSQL40:
      $activedb = @mysql_connect($SQL_HOST, $SQL_USER, $SQL_PASSWORD);
      if (!$activedb) die('Could not connect do MySQL 4.0 database.');
      if ($select) sql_select_db($SQL_DATABASE);
      break;
    case _MYSQL41:
      if (function_exists('mysqli_connect'))
      $activedb = @mysqli_connect($SQL_HOST, $SQL_USER, $SQL_PASSWORD);
      // try old mysql extension if mysqli fails
      if (!$activedb) {
          $activedb = @mysql_connect($SQL_HOST, $SQL_USER, $SQL_PASSWORD);
          if ($activedb) $SQL_TYPE = 0;
      }
      if (!$activedb) die('Could not connect do MySQL 4.1 database.');
      if ($select) sql_select_db($SQL_DATABASE);
      break;
    case _SQLITE:
      if (!is_readable($SQL_DATABASE)) die('Unable to open database \''.$dbname.'\' for reading. Permission denied.');
      if (!is_writable($SQL_DATABASE))  die('Unable to open database \''.$dbname.'\' for writing. Permission denied.');
      $activedb = true;
      if ($select) sql_select_db($SQL_DATABASE);
      break;
    case _PGSQL:
      if (!isset($SQL_PORT)) $SQL_PORT="5432";
      $conn_string = "host=$SQL_HOST port=$SQL_PORT dbname=$SQL_DATABASE user=$SQL_USER password=$SQL_PASSWORD";
      $activedb = @pg_connect($conn_string);
      if (!$activedb) die('Could not connect to PostgreSQL database.');
    default:
      die('sql_connect');
  }
  // disable DB if not connected
  if (!$activedb) $SQL_TYPE = -1;
}

/**
 * Creates a new database
 */
function sql_create_db($dbname){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      sql_query("CREATE DATABASE $dbname");
      break;
    case _MYSQL41:
      sql_query("CREATE DATABASE $dbname");
      break;
    case _SQLITE:
      if (!file_exists($dbname)) {
          @touch($dbname);
          @chmod($dbname, 0666);
      }
      if (!file_exists($dbname)) die('Unable to create new database \''.$dbname.'\'. Permission denied.');
      if (!is_readable($dbname)) die('Unable to open database \''.$dbname.'\' for reading. Permission denied.');
      if (!is_writable($dbname)) die('Unable to open database \''.$dbname.'\' for writing. Permission denied.');
      sql_connect(false);
      break;
    case _PGSQL:
      sql_query("CREATE DATABASE $dbname");
      break;
    default:
      die('sql_create_db');
}
}

/**
 * Selects active DB
 */
function sql_select_db($dbname) {
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      @mysql_select_db($dbname,$activedb)
          or
          die('Could not select MySQL 4.0 database: '.mysql_error($activedb));
      break;
    case _MYSQL41:
      @mysqli_select_db($activedb,$dbname)
          or
          die('Could not select MySQL 4.1 database: '. mysqli_error($activedb));
      break;
    case _SQLITE:
      $activedb = @sqlite_popen($dbname, 0666, $sqlite_error);
      if (!$activedb) die('Could not open SQLite database.');
      sqlite_busy_timeout($activedb,1000);
      sqlite_create_function($activedb,'UNIX_TIMESTAMP','strtotime',1);
      sqlite_create_function($activedb,'NOW','time',0);
      sqlite_create_function($activedb,'DAYOFMONTH','sql_day',1);
      sqlite_create_function($activedb,'MONTH','sql_month',1);
      sqlite_create_function($activedb,'YEAR','sql_year',1);
      sqlite_create_function($activedb,'SUBSTRING','substr',3);
      break;
    case _PGSQL:
      // no need to do anything
      break;
    default:
      die('sql_select_db');
}
}


function sql_day($time) {
  return date("d",strtotime($time));
}
function sql_month($time) {
  return date("m",strtotime($time));
}
function sql_year($time) {
  return date("Y",strtotime($time));
}

/**
 * Returns a prefixed table name
 */
function sql_table($name) {
  global $SQL_PREFIX;
  if ($SQL_PREFIX)
      return $SQL_PREFIX . $name;
  else
      return $name;
}

/**
 * Disconnects from SQL server
 */
function sql_disconnect() {
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      @mysql_close($activedb);
      unset($activedb);
      break;
    case _MYSQL41:
      @mysqli_close($activedb);
      unset($activedb);
      break;
    case _SQLITE:
      @sqlite_close($activedb);
      unset($activedb);
      break;
    case _PGSQL:
      @pg_close($activedb);
      unset($activedb);
      break;
    default:
      die('sql_disconnect');
}
}

/**
* executes an SQL query
*/
function sql_query($query, $option = MYSQLI_STORE_RESULT) {
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      $result = @mysql_query($query,$activedb)
          or
          die ("Invalid query: ".htmlspecialchars($query)." <br><br>Error: <b>".sql_error()."</b>");
      break;
    case _MYSQL41:
      if (!isset($option)) $option=MYSQLI_STORE_RESULT;
      $result = @mysqli_query($activedb,$query,$option)
          or
          die ("Invalid query: ".htmlspecialchars($query)." <br><br>Error: <b>".sql_error()."</b>");
      break;
    case _SQLITE:
      $query = preg_replace('/`(\w+)`/','$1',$query);
      $result = @sqlite_query($activedb,$query)
          or
          die ("Invalid query: ".htmlspecialchars($query)." <br><br>Error: <b>".sql_error()."</b>");
      break;
    case _PGSQL:
      $result = @pg_query($activedb,$query)
          or
          die ("Invalid query: ".htmlspecialchars($query)." <br><br>Error: <b>".sql_error()."</b>");
      global $_pg;
      $_pg = &$result;
      break;
    default:
      die('sql_query');
}
return $result;
}

/**
 * Shows SQL DB error message
 */
function sql_error() {
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return @mysql_error($activedb);
      break;
    case _MYSQL41:
      return @mysqli_error($activedb);
      break;
    case _SQLITE:
      return @sqlite_error_string(@sqlite_last_error($activedb));
      break;
    case _PGSQL:
      return @pg_last_error($activedb);
      break;
    default:
      die('sql_error');
}
}

/**
 * Disconnects from SQL server
 */
function sql_close() {
sql_disconnect();
}

/**
 * Fetch resultset as an object
 */
function sql_fetch_object(&$resource){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return @mysql_fetch_object($resource);
      break;
    case _MYSQL41:
      return @mysqli_fetch_object($resource);
      break;
    case _SQLITE:
      return @sqlite_fetch_object($resource);
      break;
    case _PGSQL:
      return @pg_fetch_object($resource);
      break;
    default:
      die('sql_fetch_object');
}
}

/**
 * Fetch resultset as an array (key - field names and row numbers)
 */
function sql_fetch_array(&$resource){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return @mysql_fetch_array($resource,MYSQL_BOTH);
      break;
    case _MYSQL41:
      return @mysqli_fetch_array($resource,MYSQLI_BOTH);
      break;
    case _SQLITE:
      return @sqlite_fetch_array($resource,SQLITE_BOTH);
      break;
    case _PGSQL:
      return @pg_fetch_array($resource, NULL, PGSQL_BOTH);
      break;
    default:
      die('sql_fetch_array');
}
}

/**
 * Fetch resultset as an array (key - field names)
 */
function sql_fetch_assoc(&$resource){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return @mysql_fetch_assoc($resource);
      break;
    case _MYSQL41:
      return @mysqli_fetch_assoc($resource);
      break;
    case _SQLITE:
      return @sqlite_fetch_array($resource,SQLITE_ASSOC);
      break;
    case _PGSQL:
      return @pg_fetch_array($resource, NULL, PGSQL_ASSOC);
      break;
    default:
      die('sql_fetch_assoc');
}
}

/**
 * Fetch resultset as an array (key - row numbers)
 */
function sql_fetch_row(&$resource){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return @mysql_fetch_row($resource);
      break;
    case _MYSQL41:
      return @mysqli_fetch_row($resource);
      break;
    case _SQLITE:
      return @sqlite_fetch_array($resource,SQLITE_NUM);
      break;
    case _PGSQL:
      return @pg_fetch_row($resource);
      break;
    default:
      die('sql_fetch_row');
}
}

/**
 * Returns number of rows for resultset
 */
function sql_num_rows(&$resource){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return @mysql_num_rows($resource);
      break;
    case _MYSQL41:
      return @mysqli_num_rows($resource);
      break;
    case _SQLITE:
      return @sqlite_num_rows($resource);
      break;
    case _PGSQL:
      return @pg_num_rows($resource);
      break;
    default:
      die('sql_num_rows');
}
}

/**
 * Frees from memory resultset
 */
function sql_free_result(&$resource){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      @mysql_free_result($resource);
      unset($resource);
      break;
    case _MYSQL41:
      @mysqli_free_result($resource);
      unset($resource);
      break;
    case _SQLITE:
      unset($resource);
      break;
    case _PGSQL:
      @pg_free_result($resource);
      unset($resource);
      break;
    default:
      die('sql_free_result');
}
}

/**
 * Returns autoincrement id of last INSERT INTO statement
 */
function sql_insert_id(){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return @mysql_insert_id();
      break;
    case _MYSQL41:
      return @mysqli_insert_id($activedb);
      break;
    case _SQLITE:
      return @sqlite_last_insert_rowid($activedb);
      break;
    case _PGSQL:
      global $_pg;
      return @pg_last_oid($_pg);
      break;
    default:
      die('sql_insert_id');
}
}

/**
 * Returns number of fields in a resultset
 */
function sql_num_fields(&$result){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return @mysql_num_fields($result);
      break;
    case _MYSQL41:
      return @mysqli_num_fields($result);
      break;
    case _SQLITE:
      return @sqlite_num_fields($result);
      break;
    case _PGSQL:
      return @pg_num_fields($result);
      break;
    default:
      die('sql_num_fields');
}
}

/**
 * Returns number of rows affected by query
 */
function sql_affected_rows(){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return @mysql_affected_rows($activedb);
      break;
    case _MYSQL41:
      return @mysqli_affected_rows($activedb);
      break;
    case _SQLITE:
      return @sqlite_changes($activedb);
      break;
    case _PGSQL:
      global $_pg;
      return @pg_affected_rows($_pg);
      break;
    default:
      die('sql_affected_rows');
}
}

/**
 * Returns field attributes
 */
function sql_fetch_field(&$resource){
global $activedb, $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return @mysql_fetch_field($resource);
      break;
    case _MYSQL41:
      return @mysqli_fetch_field($resource);
      break;
    case _SQLITE:
      return array();
      break;
    case _PGSQL:
      return array();
      break;
    default:
      die('sql_fetch_field');
}
}

/**
 * Returns escaped string for query
 */
function sql_escape($text){
global $SQL_TYPE, $activedb;
switch ($SQL_TYPE) {
    case _MYSQL40:
      if (is_callable('mysql_real_escape_string'))
          return mysql_real_escape_string($text,$activedb);
      else
          return addslashes($text);
      break;
    case _MYSQL41:
      if (is_callable('mysqli_real_escape_string'))
          return mysqli_real_escape_string($activedb,$text);
      else
          return addslashes($text);
      break;
    case _SQLITE:
      return sqlite_escape_string($text);
      break;
    case _PGSQL:
      return pg_escape_string($text);
      break;
    default:
      die('sql_escape');
}
}

/**
 * Unescapes string
 */
function sql_unescape($text){
global $SQL_TYPE;
switch ($SQL_TYPE) {
    case _MYSQL40:
      return stripslashes($text);
      break;
    case _MYSQL41:
      return stripslashes($text);
      break;
    case _SQLITE:
      return $text;
      break;
    case _PGSQL:
      return $text;
      break;
    default:
      die('sql_unescape');
}
}

//sql_create_db($SQL_DATABASE);

// pripojeni k DB
//sql_connect(true);

// shutdown funkce
//register_shutdown_function('sql_disconnect');

// vlastni kod
//$query = sql_query( ('create table') );
//while ( $obj = sql_fetch_object($query) ) {
  // zpracovani vysledku
//}
//sql_free_result($query);

?>