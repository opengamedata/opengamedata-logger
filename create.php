<?php
header("Access-Control-Allow-Origin: *");

include('config.php');

$conn = mysqli_connect($servername, $username, $password, $db);
if(!$conn) die("Connection failed: " . mysqli_connect_error());

mysqli_query($conn, "CREATE TABLE log (".
"id INT(32) NOT NULL PRIMARY KEY AUTO INCREMENT,".
"app_id VARCHAR(32) NOT NULL DEFAULT \"UNDEFINED\",".
"app_id_fast ENUM(\"UNDEFINED\") NOT NULL DEFAULT \"UNDEFINED\",".
"app_version INT(32) NOT NULL DEFAULT 0,".
"session_id INT(64) NOT NULL DEFAULT 0,".
"persistent_session_id INT(64) NOT NULL DEFAULT 0,".
"level INT(32) NOT NULL DEFAULT 0,".
"event ENUM(\"BEGIN\",\"COMPLETE\",\"SUCCEED\",\"FAIL\",\"CUSTOM\",\"UNDEFINED\") DEFAULT \"UNDEFINED\",".
"event_custom INT(32) NOT NULL DEFAULT 0,".
"event_data_simple INT(32) NOT NULL DEFAULT 0,".
"event_data_complex VARCHAR(32) DEFAULT NULL,".
"client_time TIMESTAMP NOT NULL,".
"server_time TIMESTAMP NOT NULL DEFAULT CURRENT TIMESTAMP,".
"http_user_agent VARCHAR(256) DEFAULT NULL"
";");

mysqli_query($conn,"ALTER TABLE log ADD INDEX \"app_version\" (\"app_id_fast\",\"app_version\");");
mysqli_query($conn,"ALTER TABLE log ADD INDEX \"app_session\" (\"app_id_fast\",\"session_id\");");

);

?>
