<?php
header("Access-Control-Allow-Headers: Origin, Authorization, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Origin: *");

include('config.php');
include('query_generator.php');

# 1. Figure out what the input schema looks like
$REQUEST_SCHEMA = $OGD_SCHEMA;
$UPPER = "NO APP ID";
if (isset($_REQUEST["app_id"])) {
  $upper = strtoupper($_REQUEST["app_id"]);
  // error_log("The app id in upper-case is: ".$upper);
  $logger_games = array("BACTERIA",   "BALLOON",  "CRYSTAL",    "CYCLE_CARBON", "CYCLE_NITROGEN", "CYCLE_WATER",
                        "EARTHQUAKE", "JOWILDER", "LAKELAND",   "MAGNET",       "WAVES",          "WIND");
  $ogd_games    = array("AQUALAB",    "ICECUBE",  "MASHOPOLIS", "PENGUINS");
  if (in_array($upper, $logger_games)) {
    $REQUEST_SCHEMA = $LOGGER_SCHEMA;
  }
  elseif (in_array($upper, $ogd_games)) {
    $REQUEST_SCHEMA = $OGD_SCHEMA;
  }
}

# 2. Make the db connection before we go to the trouble of generating query.
$conn = mysqli_connect($servername, $username, $password, $db);
if(!$conn) {die("FAIL: Could not connect to the database.\nError message: " . mysqli_connect_error());}

# 3. Generate the query data from raw input data.
$data = json_decode(base64_decode($_POST["data"]));
if(!is_array($data)) { $d = $data; $data = array(); array_push($data,$d); }
$query = generateQueryString($REQUEST_SCHEMA, $upper, $data, $conn);
$n_rows = count($data);
// error_log("The query is: ".$query);

# 4. Send the query itself. Log errors if failed.
if($n_rows > 0) {
  $result = mysqli_query($conn,$query);
  if (!$result) {
    $sql_err = "Query for ".$UPPER." failed with error: ".mysqli_error($conn);
    error_log($sql_err);
    die("FAIL: ".$sql_err);
  }
}
else {error_log("Didn't perform query, n_rows is ".$n_rows);}
die("SUCCESS: ".$query);

?>
