<?php
header("Access-Control-Allow-Origin: *");

include('config.php');

$conn = mysqli_connect($servername, $username, $password, $db);
if(!$conn) die("Connection failed: " . mysqli_connect_error());

$query = "CREATE TABLE log (".
"id INT(32) NOT NULL PRIMARY KEY AUTO_INCREMENT, ".
"app_id VARCHAR(32) NOT NULL DEFAULT \"UNDEFINED\", ".
"app_id_fast ENUM(\"UNDEFINED\",\"WAVES\",\"CRYSTAL\") NOT NULL DEFAULT \"UNDEFINED\", ".
"app_version INT(32) NOT NULL DEFAULT 0, ".
"session_id VARCHAR(32) DEFAULT NULL, ".
"persistent_session_id VARCHAR(32) DEFAULT NULL, ".
"level INT(32) NOT NULL DEFAULT 0, ".
"event ENUM(\"BEGIN\",\"COMPLETE\",\"SUCCEED\",\"FAIL\",\"CUSTOM\",\"UNDEFINED\") DEFAULT \"UNDEFINED\", ".
"event_custom INT(32) NOT NULL DEFAULT 0, ".
"event_data_simple INT(32) NOT NULL DEFAULT 0, ".
"event_data_complex TEXT DEFAULT NULL, ".
"client_time TIMESTAMP NOT NULL, ".
"client_time_ms INT(32) NOT NULL DEFAULT 0, ".
"server_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, ".
"req_id BIGINT(64) NOT NULL DEFAULT 0, ".
"session_n BIGINT(64) NOT NULL DEFAULT 0, ".
"http_user_agent TEXT DEFAULT NULL ".
");";

mysqli_query($conn, $query);

mysqli_query($conn,"ALTER TABLE log ADD INDEX \"app_version\" (\"app_id_fast\",\"app_version\");");
mysqli_query($conn,"ALTER TABLE log ADD INDEX \"app_session\" (\"app_id_fast\",\"session_id\");");

echo $query;
?>
