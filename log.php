<?php
header("Access-Control-Allow-Headers: Origin, Authorization, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Origin: *");

include('config.php');

# Define some stuff

$MYSQL_SCHEMAS = array("LOGGER", "OPENGAMEDATA");

function generateQueryColumns($schema) {
  global $MYSQL_SCHEMAS;
  switch ($schema) {
    case $MYSQL_SCHEMAS[0]:
      return "INSERT INTO log (".
        "app_id,".
        "app_id_fast,".
        "app_version,".
        "session_id,".
        "persistent_session_id,".
        "player_id,".
        "level,".
        "event,".
        "event_custom,".
        "event_data_simple,".
        "event_data_complex,".
        "client_time,".
        "client_time_ms,".
        "server_time,".
        "remote_addr,".
        "req_id,".
        "session_n,".
        "http_user_agent".
        ") VALUES";
      break;
    case $MYSQL_SCHEMAS[1]:
      return "INSERT INTO ".$_REQUEST['app_id']." (".
        "session_id,".
        "user_id,".
        "user_data,".
        "client_time,".
        "client_time_ms,".
        "client_offset,".
        "server_time,".
        "event_name,".
        "event_data,".
        "event_source,".
        "game_state,".
        "app_version,".
        "app_branch,".
        "log_version,".
        "event_sequence_index,".
        "remote_addr,".
        "http_user_agent".
        ") VALUES";
      break;
    default:
      error_log("Got case of ".$schema." != ".$MYSQL_SCHEMAS[0]." and != ".$MYSQL_SCHEMAS[1]);
  }
}

function generateQueryValues($schema, $datum, $conn) {
  global $MYSQL_SCHEMAS;
  switch ($schema) {
    case $MYSQL_SCHEMAS[0]:
      return generateLoggerValues($datum, $conn);
      break;
    case $MYSQL_SCHEMAS[1]:
      return generateOGDValues($datum, $conn);
      break;
  }
}

function generateLoggerValues($datum, $conn) {
  //per dump
  if(isset($_REQUEST["app_id"]))                $app_id                = mysqli_real_escape_string($conn,$_REQUEST["app_id"]);                       else die("No app_id");
  if(isset($_REQUEST["app_version"]))           $app_version           = filter_var($_REQUEST["app_version"],           FILTER_SANITIZE_NUMBER_INT); else die("No app_version");
  if(isset($_REQUEST["session_id"]))            $session_id            = filter_var($_REQUEST["session_id"],            FILTER_SANITIZE_NUMBER_INT); else die("No session_id");
  if(isset($_REQUEST["persistent_session_id"])) $persistent_session_id = filter_var($_REQUEST["persistent_session_id"], FILTER_SANITIZE_NUMBER_INT);
  if(isset($_REQUEST["player_id"]))             $player_id             = preg_replace("/[^a-zA-Z0-9]+/", "", $_REQUEST["player_id"]);
  if(isset($_REQUEST["req_id"]))                $req_id                = filter_var($_REQUEST["req_id"], FILTER_SANITIZE_NUMBER_INT);
  $remote_addr = $_SERVER["REMOTE_ADDR"];
  $http_user_agent = mysqli_real_escape_string($conn,$_SERVER["HTTP_USER_AGENT"]);

  $level = 0;
  $event = "UNDEFINED";
  $event_custom = 0;
  $event_data_simple = 0;
  $event_data_complex = NULL;
  $client_time = date("M d Y H:i:s");
  $client_time_ms = 0;

  if(isset($datum->level))              $level              = filter_var($datum->level,             FILTER_SANITIZE_NUMBER_INT);
  if(isset($datum->event))              $event              = mysqli_real_escape_string($conn,$datum->event);
  //optional
  if(isset($datum->event_custom))       $event_custom       = filter_var($datum->event_custom,      FILTER_SANITIZE_NUMBER_INT);
  if(isset($datum->event_data_simple))  $event_data_simple  = filter_var($datum->event_data_simple, FILTER_SANITIZE_NUMBER_INT);
  if(isset($datum->event_data_complex)) $event_data_complex = mysqli_real_escape_string($conn,$datum->event_data_complex);
  if(isset($datum->client_time))
  {
    $client_time = mysqli_real_escape_string($conn,$datum->client_time);
    // $client_time is a string like "2019-02-20 17:21:05.493Z"
    $ct_len = strlen($client_time);
    $ct_dot = strrpos($client_time,".");
    if ($ct_dot) {
      // drop ".493Z" for the DATETIME, and extract 493 for separate column
      $client_time_ms = substr($client_time, $ct_dot + 1, $ct_len - ($ct_dot + 1) - 1);
      $client_time    = substr($client_time, 0, $ct_dot);
    } else {
      $client_time_ms = 0;
    }
  }
  if(isset($datum->session_n))          $session_n          = filter_var($datum->session_n, FILTER_SANITIZE_NUMBER_INT);
  return "(".
    "\"".$app_id."\",".
    "\"".$app_id."\",".
    "\"".$app_version."\",".
    "\"".$session_id."\",".
    "\"".$persistent_session_id."\",".
    "\"".$player_id."\",".
    "\"".$level."\",".
    "\"".$event."\",".
    "\"".$event_custom."\",".
    "\"".$event_data_simple."\",".
    (!is_null($event_data_complex) ? "\"".$event_data_complex."\"," : "NULL,").
    "\"".$client_time."\",".
    "\"".$client_time_ms."\",".
    "CURRENT_TIMESTAMP,".
    "\"".$remote_addr."\",".
    "\"".$req_id."\",".
    "\"".$session_n."\",".
    "\"".$http_user_agent."\"".
    ")";
}

function generateOGDValues($datum, $conn) {
  // Items from $_REQUEST: session_id, user_id, user_data, app_version, app_branch, log_version, 
  // Items from $datum: client_time, client_offset, event_name, event_data, game_state, event_sequence_index
  //per dump
  if(isset($_REQUEST["session_id"])) {
    $session_id = filter_var($_REQUEST["session_id"], FILTER_SANITIZE_NUMBER_INT);
  } else { die("No session_id"); }

  if(isset($_REQUEST["user_id"])) {
    $user_id = preg_replace("/[^a-zA-Z0-9]+/", "", $_REQUEST["user_id"]);
  } else { $user_id = NULL; }

  if(isset($_REQUEST["user_data"])) {
    $user_data = mysqli_real_escape_string($conn, $_REQUEST["user_data"]);
  } else { $user_data = NULL; }

  if(isset($datum->client_time))
  {
    $client_time = mysqli_real_escape_string($conn,$datum->client_time);
    // $client_time is a string like "2019-02-20 17:21:05.493Z"
    $ct_len = strlen($client_time);
    $ct_dot = strrpos($client_time,".");
    if ($ct_dot) {
      // drop ".493Z" for the DATETIME, and extract 493 for separate column
      $client_time_ms = substr($client_time, $ct_dot + 1, $ct_len - ($ct_dot + 1) - 1);
      $client_time = substr($client_time, 0, $ct_dot);
    } else {
      $client_time_ms = 0;
    }
  } else {
    $client_time = date("M d Y H:i:s");
    $client_time_ms = 0;
  }

  if(isset($datum->client_offset)) {
    $client_offset = mysqli_real_escape_string($conn,$datum->client_offset);
  } else {$client_offset = "00:00:00";}

  if(isset($datum->event_name)) {
    $event_name = mysqli_real_escape_string($conn,$datum->event_name);
  } else { die("No event_name"); }

  if(isset($datum->event_data)) {
    $event_data = mysqli_real_escape_string($conn,$datum->event_data);
  } else { $event_data = NULL; }

  if(isset($datum->game_state)) {
    $game_state = mysqli_real_escape_string($conn,$datum->game_state);
  } else { $game_state = NULL; }

  if(isset($_REQUEST["app_version"])) {
    $app_version = filter_var($_REQUEST["app_version"], FILTER_SANITIZE_NUMBER_INT);
  } else { die("No app_version"); }

  if(isset($_REQUEST["app_branch"])) {
    $app_branch = preg_replace("/[^a-zA-Z0-9-_]+/", "", $_REQUEST["app_branch"]);
  } else { $app_branch = NULL; }

  if(isset($_REQUEST["log_version"])) {
    $log_version = filter_var($_REQUEST["log_version"], FILTER_SANITIZE_NUMBER_INT);
  } else { die("No log_version"); }

  if(isset($datum->event_sequence_index)) {
    $event_sequence_index  = filter_var($datum->event_sequence_index, FILTER_SANITIZE_NUMBER_INT);
  } else { die("No event_sequence_index"); }

  $http_user_agent = mysqli_real_escape_string($conn,$_SERVER["HTTP_USER_AGENT"]);

  return "(".
    "\"".$session_id."\",".
    "\"".$user_id."\",".
    "\"".$user_data."\",".
    "\"".$client_time."\",".
    "\"".$client_time_ms."\",".
    "\"".$client_offset."\",".
    "CURRENT_TIMESTAMP,".
    "\"".$event_name."\",".
    (!is_null($event_data) ? "\"".$event_data."\"," : "NULL,").
    "\"GAME\",".
    "\"".$game_state."\",".
    "\"".$app_version."\",".
    "\"".$app_branch."\",".
    "\"".$log_version."\",".
    "\"".$event_sequence_index."\",".
    "\"".$_SERVER["REMOTE_ADDR"]."\",".
    "\"".$http_user_agent."\"".
    ")";
}

# Actual code
if (isset($_REQUEST["app_id"])) {
  $upper = strtoupper($_REQUEST["app_id"]);
  $ogd_games = array("AQUALAB", "ICECUBE", "MASHOPOLIS", "PENGUINS");
  if (in_array($upper, $ogd_games)) {
    $db = "opengamedata";
  }
}
$conn = mysqli_connect($servername, $username, $password, $db);
if(!$conn) {die("Connection failed: " . mysqli_connect_error());}

$REQUEST_SCHEMA = "";

if ($db == "logger") {
  // remove 'false' below to enable switching on AQUALAB and not others.
  $REQUEST_SCHEMA = $MYSQL_SCHEMAS[0];
}
elseif ($db == "opengamedata") {
  $REQUEST_SCHEMA = $MYSQL_SCHEMAS[1];
}
else {
  $REQUEST_SCHEMA = $MYSQL_SCHEMAS[1];
}

$query = generateQueryColumns($REQUEST_SCHEMA);

$data = json_decode(base64_decode($_POST["data"]));
if(!is_array($data)) { $d = $data; $data = array(); array_push($data,$d); }
$n_rows = count($data);
for($i = 0; $i < $n_rows; $i++)
{
  $query .= generateQueryValues($REQUEST_SCHEMA, $data[$i], $conn);
  if($i < $n_rows-1) {
    $query .= ",";
  }
}

if($n_rows > 0) {
  $result = mysqli_query($conn,$query);
  if (!$result) {
    error_log("Query failed with error: ".mysqli_error($conn));
  }
}
else {error_log("Didn't perform query, n_rows is ".$n_rows);}
die($query);

?>
