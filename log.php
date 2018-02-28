<?php
header("Access-Control-Allow-Origin: *");

include('config.php');

$conn = mysqli_connect($servername, $username, $password, $db);
if(!$conn) die("Connection failed: " . mysqli_connect_error());

if(isset($_GET["app_id"]))             $app_id             = mysqli_real_escape_string($conn,$_GET["app_id"]);                   else return;
if(isset($_GET["app_version"]))        $app_version        = filter_var($_GET["app_version"],       FILTER_SANITIZE_NUMBER_INT); else return;
if(isset($_GET["session_id"]))         $session_id         = filter_var($_GET["session_id"],        FILTER_SANITIZE_NUMBER_INT); else return;
if(isset($_GET["level"]))              $level              = filter_var($_GET["level"],             FILTER_SANITIZE_NUMBER_INT); else return;
if(isset($_GET["event"]))              $event              = filter_var($_GET["event"],             FILTER_SANITIZE_NUMBER_INT); else return;
//optional
if(isset($_GET["event_custom"]))       $event_custom       = filter_var($_GET["event_custom"],      FILTER_SANITIZE_NUMBER_INT);
if(isset($_GET["event_data_simple"]))  $event_data_simple  = filter_var($_GET["event_data_simple"], FILTER_SANITIZE_NUMBER_INT);
if(isset($_GET["event_data_complex"])) $event_data_complex = mysqli_real_escape_string($conn,$_GET["event_data_complex"]);

mysqli_query($conn, "INSERT INTO log (".
  "app_id,".
  "app_id_fast,".
  "app_version,".
  "session_id,".
  "level,".
  "event,".
  (isset($event_custom)       ? "event_custom,"       : "").
  (isset($event_data_simple)  ? "event_data_simple,"  : "").
  (isset($event_data_complex) ? "event_data_complex," : "").
  "time".
  ") VALUES (".
  "\"".$app_id."\",".
  "\"".$app_id."\",".
  "\"".$app_version."\",".
  "\"".$session_id."\",".
  "\"".$level."\",".
  "\"".$event."\",".
  (isset($event_custom)       ? "\"".$event_custom."\","       : "").
  (isset($event_data_simple)  ? "\"".$event_data_simple."\","  : "").
  (isset($event_data_complex) ? "\"".$event_data_complex."\"," : "").
  "CURRENT_TIMESTAMP".
  ");");

?>
