<?php
header("Access-Control-Allow-Origin: *");

include('config.php');

$conn = mysqli_connect($servername, $username, $password, $db);
if(!$conn) die("Connection failed: " . mysqli_connect_error());

//per dump
if(isset($_GET["app_id"]))                $app_id                = mysqli_real_escape_string($conn,$_GET["app_id"]);                       else return;
if(isset($_GET["app_version"]))           $app_version           = filter_var($_GET["app_version"],           FILTER_SANITIZE_NUMBER_INT); else return;
if(isset($_GET["session_id"]))            $session_id            = filter_var($_GET["session_id"],            FILTER_SANITIZE_NUMBER_INT); else return;
if(isset($_GET["persistent_session_id"])) $persistent_session_id = filter_var($_GET["persistent_session_id"], FILTER_SANITIZE_NUMBER_INT); else return;
$http_user_agent = mysqli_real_escape_string($conn,$_SERVER["HTTP_USER_AGENT"]);

$query = "INSERT INTO log (".
  "app_id,".
  "app_id_fast,".
  "app_version,".
  "session_id,".
  "persistent_session_id,".
  "level,".
  "event,".
  "event_custom,".
  "event_data_simple,".
  "event_data_complex,".
  "client_time,".
  "server_time,".
  "http_user_agent".
  ") VALUES";

$data = json_decode($_POST["data"]);
if(!is_array($data)) { $d = $data; $data = array(); array_push($data,$d); }
$n_rows = count($data);
for($i = 0; $i < $n_rows; $i++)
{
  $datum = $data[$i];
  $level = 0;
  $event = "UNDEFINED";
  $event_custom = 0;
  $event_data_simple = 0;
  $event_data_complex = NULL;
  $client_time = date();

  if(isset($datum["level"]))              $level              = filter_var($datum["level"],             FILTER_SANITIZE_NUMBER_INT);
  if(isset($datum["event"]))              $event              = filter_var($datum["event"],             FILTER_SANITIZE_NUMBER_INT);
  //optional
  if(isset($datum["event_custom"]))       $event_custom       = filter_var($datum["event_custom"],      FILTER_SANITIZE_NUMBER_INT);
  if(isset($datum["event_data_simple"]))  $event_data_simple  = filter_var($datum["event_data_simple"], FILTER_SANITIZE_NUMBER_INT);
  if(isset($datum["event_data_complex"])) $event_data_complex = mysqli_real_escape_string($conn,$datum["event_data_complex"]);
  if(isset($datum["client_time"]))        $client_time        = mysqli_real_escape_string($conn,$datum["client_time"]);

  $query .=
    "(".
    "\"".$app_id."\",".
    "\"".$app_id."\",".
    "\"".$app_version."\",".
    "\"".$session_id."\",".
    "\"".$persistent_session_id."\",".
    "\"".$level."\",".
    "\"".$event."\",".
    "\"".$event_custom."\",".
    "\"".$event_data_simple."\",".
    (!is_null($event_data_complex) ? "\"".$event_data_complex."\"," : "NULL,").
    "\"".$client_time."\",".
    "CURRENT_TIMESTAMP,".
    "\"".$http_user_agent."\"".
    ")";
  if($i < $n_rows-1) $query .= ",";
}

if($n_rows > 0) mysqli_query($conn,$query);

echo $query;
echo "SUCCESS";
?>
