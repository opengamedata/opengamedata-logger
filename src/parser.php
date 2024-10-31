<?php
  $LOGGER_SCHEMA = "LOGGER";
  $OGD_SCHEMA    = "OPENGAMEDATA";

   /**
    * An array of event data bodies, containing event_name, event_data, and similar columns.
    * @var array
    */
   function dataToArray($data) {
      # if the data was a single event, turn into a length-1 array.
      if (!is_array($data)) {
         $d = $data;
         $data = array();
         array_push($data, $d);
      }
      return $data;
   }


?>
