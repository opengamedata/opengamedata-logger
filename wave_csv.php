<?php
header("Access-Control-Allow-Headers: Origin, Authorization, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Origin: *");

include('config.php');

$conn = mysqli_connect($servername, $username, $password, $db);
if(!$conn) die("Connection failed: " . mysqli_connect_error());

echo "id,app_id,app_version,session_id,persistent_session_id,level,event,event_data_simple,amplitude_left,wavelength_left,offset_left,amplitude_right,wavelength_right,offset_right,begin_closeness,end_closeness,slider,wave,begin_val,end_val,min_val,max_val,correct_val,question,answered,answer,client_time,server_time,req_id,session_n,http_user_agent\n";

$result = mysqli_query($conn,"SELECT * FROM log WHERE app_id_fast = 'WAVES';");
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
{
  $data = json_decode($row["event_data_complex"]);

  $line = "";
  $line .= $row["id"].","; //id
  $line .= $row["app_id_fast"].","; //app_id
  $line .= $row["app_version"].","; //app_version
  $line .= $row["session_id"].","; //session_id
  $line .= $row["persistent_session_id"].","; //persistent_session_id
  $line .= $row["level"].","; //level

  $event = $row["event"];
  if($event == "CUSTOM") $event = $data->event_custom;

  $line .= $event.","; //event
  $line .= $row["event_data_simple"].","; //event_data_simple

  if($event == "BEGIN")
  {
    $line .= ","; //amplitude_left
    $line .= ","; //wavelength_left
    $line .= ","; //offset_left
    $line .= ","; //amplitude_right
    $line .= ","; //wavelength_right
    $line .= ","; //offset_right
    $line .= ","; //begin_closeness
    $line .= ","; //end_closeness
    $line .= ","; //slider
    $line .= ","; //wave
    $line .= ","; //begin_val
    $line .= ","; //end_val
    $line .= ","; //min_val
    $line .= ","; //max_val
    $line .= ","; //correct_val
    $line .= ","; //question
    $line .= ","; //answered
    $line .= ","; //answer
  }
  else if($event == "COMPLETE")
  {
    $line .= $data->amplitude_left.","; //amplitude_left
    $line .= $data->wavelength_left.","; //wavelength_left
    $line .= $data->offset_left.","; //offset_left
    $line .= $data->amplitude_right.","; //amplitude_right
    $line .= $data->wavelength_right.","; //wavelength_right
    $line .= $data->offset_right.","; //offset_right
    $line .= $data->closeness.","; //begin_closeness
    $line .= $data->closeness.","; //end_closeness
    $line .= ","; //slider
    $line .= ","; //wave
    $line .= ","; //begin_val
    $line .= ","; //end_val
    $line .= ","; //min_val
    $line .= ","; //max_val
    $line .= ","; //correct_val
    $line .= ","; //question
    $line .= ","; //answered
    $line .= ","; //answer
  }
  else if($event == "SUCCEED")
  {
    $line .= $data->amplitude_left.","; //amplitude_left
    $line .= $data->wavelength_left.","; //wavelength_left
    $line .= $data->offset_left.","; //offset_left
    $line .= $data->amplitude_right.","; //amplitude_right
    $line .= $data->wavelength_right.","; //wavelength_right
    $line .= $data->offset_right.","; //offset_right
    $line .= $data->closeness.","; //begin_closeness
    $line .= $data->closeness.","; //end_closeness
    $line .= ","; //slider
    $line .= ","; //wave
    $line .= ","; //begin_val
    $line .= ","; //end_val
    $line .= ","; //min_val
    $line .= ","; //max_val
    $line .= ","; //correct_val
    $line .= ","; //question
    $line .= ","; //answered
    $line .= ","; //answer
  }
  else if($event == "FAIL")
  {
    $line .= $data->amplitude_left.","; //amplitude_left
    $line .= $data->wavelength_left.","; //wavelength_left
    $line .= $data->offset_left.","; //offset_left
    $line .= $data->amplitude_right.","; //amplitude_right
    $line .= $data->wavelength_right.","; //wavelength_right
    $line .= $data->offset_right.","; //offset_right
    $line .= $data->closeness.","; //begin_closeness
    $line .= $data->closeness.","; //end_closeness
    $line .= ","; //slider
    $line .= ","; //wave
    $line .= ","; //begin_val
    $line .= ","; //end_val
    $line .= ","; //min_val
    $line .= ","; //max_val
    $line .= ","; //correct_val
    $line .= ","; //question
    $line .= ","; //answered
    $line .= ","; //answer
  }
  else if($event == "SLIDER_MOVE_RELEASE")
  {
    $line .= ","; //amplitude_left
    $line .= ","; //wavelength_left
    $line .= ","; //offset_left
    $line .= ","; //amplitude_right
    $line .= ","; //wavelength_right
    $line .= ","; //offset_right
    $line .= $data->begin_closeness.","; //begin_closeness
    $line .= $data->end_closeness.","; //end_closeness
    $line .= $data->slider.","; //slider
    $line .= $data->wave.","; //wave
    $line .= $data->begin_val.","; //begin_val
    $line .= $data->end_val.","; //end_val
    $line .= $data->min_val.","; //min_val
    $line .= $data->max_val.","; //max_val
    $line .= $data->correct_val.","; //correct_val
    $line .= ","; //question
    $line .= ","; //answered
    $line .= ","; //answer
  }
  else if($event == "ARROW_MOVE_RELEASE")
  {
    $line .= ","; //amplitude_left
    $line .= ","; //wavelength_left
    $line .= ","; //offset_left
    $line .= ","; //amplitude_right
    $line .= ","; //wavelength_right
    $line .= ","; //offset_right
    $line .= $data->closeness.","; //begin_closeness
    $line .= $data->closeness.","; //end_closeness
    $line .= $data->slider.","; //slider
    $line .= $data->wave.","; //wave
    $line .= $data->begin_val.","; //begin_val
    $line .= $data->end_val.","; //end_val
    $line .= ","; //min_val
    $line .= ","; //max_val
    $line .= $data->correct_val.","; //correct_val
    $line .= ","; //question
    $line .= ","; //answered
    $line .= ","; //answer
  }
  else if($event == "QUESTION_ANSWER")
  {
    $line .= ","; //amplitude_left
    $line .= ","; //wavelength_left
    $line .= ","; //offset_left
    $line .= ","; //amplitude_right
    $line .= ","; //wavelength_right
    $line .= ","; //offset_right
    $line .= ","; //begin_closeness
    $line .= ","; //end_closeness
    $line .= ","; //slider
    $line .= ","; //wave
    $line .= ","; //begin_val
    $line .= ","; //end_val
    $line .= ","; //min_val
    $line .= ","; //max_val
    $line .= ","; //correct_val
    $line .= $data->question.","; //question
    $line .= $data->answered.","; //answered
    $line .= $data->answer.","; //answer
  }

  $line .= $row["event_data_complex"].",";
  $line .= $row["client_time"].":".$row["client_time_ms"].",";
  $line .= $row["server_time"].",";
  $line .= $row["req_id"].",";
  $line .= $row["session_n"].",";
  $line .= $row["http_user_agent"].",";

  echo $line;
}
mysqli_free_result($result);

?>
