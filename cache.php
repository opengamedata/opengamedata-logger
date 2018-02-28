<?php
header("Access-Control-Allow-Origin: *");

include('config.php');

$conn = mysqli_connect($servername, $username, $password, $db);
if(!$conn) die("Connection failed: " . mysqli_connect_error());

mysqli_query($conn, "UPDATE log SET app_id_fast = app_id;");

);

?>
