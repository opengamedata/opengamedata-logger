<?php
header("Access-Control-Allow-Headers: Origin, Authorization, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Origin: *");

include('config.php');
include('query_generator.php');
include('utilities.php');
# 1. Figure out what the input schema looks like
$REQUEST_SCHEMA = $OGD_SCHEMA;
$UPPER = "NO APP ID";
if (isset($_REQUEST["app_id"])) {
  $UPPER = strtoupper($_REQUEST["app_id"]);
  // error_log("The app id in upper-case is: ".$upper);
  $logger_games = array("BACTERIA",   "BALLOON",  "CRYSTAL",    "CYCLE_CARBON", "CYCLE_NITROGEN", "CYCLE_WATER",
                        "EARTHQUAKE", "JOWILDER", "LAKELAND",   "MAGNET",       "WAVES",          "WIND");
  $ogd_games    = array("AQUALAB",    "ICECUBE", "JOURNALISM", "MASHOPOLIS", "PENGUINS", "THERMOVR",
                        "TRANSFORMATION_QUEST");
  if (in_array($UPPER, $logger_games)) {
    $REQUEST_SCHEMA = $LOGGER_SCHEMA;
  }
  elseif (in_array($UPPER, $ogd_games)) {
    $REQUEST_SCHEMA = $OGD_SCHEMA;
  }
}

# 2. Make the db connection before we go to the trouble of generating query.
$conn = mysqli_connect($servername, $username, $password, $db);
if (!$conn) {
  die("FAIL: Could not connect to the database.\nError message: " . mysqli_connect_error());
}

# 3. Generate the query data from raw input data.
$data = json_decode(base64_decode($_POST["data"]));
if (!is_array($data)) {
  $d = $data;
  $data = array();
  array_push($data, $d);
}

# 4. Send the query itself. Log errors if failed.
if (count($data) > 0) {
  $query = generateQueryString($REQUEST_SCHEMA, $UPPER, $data, $conn);
  $result = mysqli_query($conn, $query);
  if ($result) {
    # 5. Make flask connection after connecting to db
    $loggerData = combineParaAndBody($_REQUEST, $data[0]);
    // $current_time = microtime(true);
    // $current_time_milliseconds = round($current_time * 1000);
    // error_log("beforeTime:".$current_time_milliseconds);
    connectFlask("all-game", $loggerData);
    if ($REQUEST_SCHEMA == $OGD_SCHEMA) {
      // $loggerData = combineParaAndBody($_REQUEST, $data[0]);
      // // $current_time = microtime(true);
      // // $current_time_milliseconds = round($current_time * 1000);
      // // error_log("beforeTime:".$current_time_milliseconds);
      // connectFlask("all-game", $loggerData);
      // // $next_time = microtime(true);
      // // $next_time_milliseconds = round($next_time * 1000);
      // // error_log("afterTime:".$next_time_milliseconds);

      // // error_log("timeDiff".($next_time_milliseconds-$current_time_milliseconds)/1000);
      // // connectFlask($_REQUEST["app-id"], $loggerData);
    } else {
      error_log("Warning: Got an old-logger data format");
    }
    // error_log("log.php test flaskApiUrl and loggerData \nflaskApiUrl: " . $flaskApiUrl . "\nloggerData: " . $loggerData);
  } else {
    $sql_err = "Query for " . $UPPER . " failed with error: " . mysqli_error($conn);
    // error_log("The query is: ".$query);
    error_log($sql_err);
    die("FAIL: ".$sql_err);
  }
} else {
  error_log("Didn't perform query, n_rows is <= 0");
}

die("SUCCESS: " . $query);
?>