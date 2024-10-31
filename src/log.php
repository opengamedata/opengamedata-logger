<?php
header("Access-Control-Allow-Headers: Origin, Authorization, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Origin: *");

include('config.php');
include('parser.php');
include('query_generator.php');
include('monitor.php');
# 1. Figure out what the input schema looks like, defaulting to full OGD schema.
$APP_ID = $_REQUEST["app_id"] ?? "NO APP ID";
$REQUEST_SCHEMA = schemaFromAppID($APP_ID);

# 2. Make the db connection before we go to the trouble of generating query.
$conn = mysqli_connect($servername, $username, $password, $db);
if (!$conn) {
  die("FAIL: Could not connect to the database.\nError message: " . mysqli_connect_error());
}

# 3. Generate the query data from raw input data.
$data = json_decode(base64_decode($_POST["data"]));
$data = dataToArray($data);

if (count($data) > 0) {
  # 4. Send the query itself. Log errors if failed.
  $query = generateQueryString($REQUEST_SCHEMA, $APP_ID, $data, $conn);
  $result = mysqli_query($conn, $query);
  if ($result) {
    // syslog(LOG_NOTICE, "Successfully inserted event(s) to database");
  } else {
    $sql_err = "Query for ".$APP_ID." failed with error: ".mysqli_error($conn);
    error_log($sql_err);
    die("FAIL: ".$sql_err);
  }
  # 5. Send event to flask monitor after sending to db
  if ($monitorEnabled) {
    SendToMonitor($_REQUEST, $data);
    $end_time_milliseconds = round(microtime(true) * 1000);
    // if ($REQUEST_SCHEMA != $OGD_SCHEMA) {
    //   syslog(LOG_WARNING, "Warning: Got an old-logger data format");
    // }
    }
} else {
  error_log("Didn't perform query, n_rows is <= 0");
}
die("SUCCESS: " . $query);
?>