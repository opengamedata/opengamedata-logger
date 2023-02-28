<?php
header("Access-Control-Allow-Headers: Origin, Authorization, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Origin: *");

include('config.php');
include('query_generator.php');

# 1. Figure out what the input schema looks like
$REQUEST_SCHEMA = $OGD_SCHEMA;
$db = "opengamedata";
$UPPER = "NO APP ID";
if (isset($_REQUEST["app_id"])) {
  $UPPER = strtoupper($_REQUEST["app_id"]);
  // error_log("The app id in upper-case is: ".$upper);
  $logger_games = array("BACTERIA",   "BALLOON",  "CRYSTAL",    "CYCLE_CARBON", "CYCLE_NITROGEN", "CYCLE_WATER",
                        "EARTHQUAKE", "JOWILDER", "LAKELAND",   "MAGNET",       "WAVES",          "WIND");
  $ogd_games    = array("AQUALAB",    "ICECUBE", "JOURNALISM", "MASHOPOLIS", "PENGUINS");
  if (in_array($UPPER, $logger_games)) {
    $REQUEST_SCHEMA = $LOGGER_SCHEMA;
    $db = "logger";
  }
  elseif (in_array($UPPER, $ogd_games)) {
    $REQUEST_SCHEMA = $OGD_SCHEMA;
    $db = "opengamedata";
  }
}

# 2. Make the db connection before we go to the trouble of generating query.
$conn = mysqli_connect($servername, $username, $password, $db);
if(!$conn) {die("FAIL: Could not connect to the database.\nError message: " . mysqli_connect_error());}

# 3. Generate the query data from raw input data.
$data = json_decode(base64_decode($_POST["data"]));
if(!is_array($data)) { $d = $data; $data = array(); array_push($data,$d); }

# 4. Send the query itself. Log errors if failed.
if(count($data) > 0) {
  $query = generateQueryString($REQUEST_SCHEMA, $UPPER, $data, $conn);
  $result = mysqli_query($conn,$query);
  if (!$result) {
    $sql_err = "Query for ".$UPPER." failed with error: ".mysqli_error($conn);
    // error_log("The query is: ".$query);
    error_log($sql_err);
    die("FAIL: ".$sql_err);
  }
}
else {error_log("Didn't perform query, n_rows is <= 0");}
die("SUCCESS: ".$query);

?>
