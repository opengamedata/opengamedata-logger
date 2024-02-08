<?php

// connect to flask app with flask api url
// send json package to flask app
function connectFlask($AppID, $jsonPackage)
{
    include('config.php');
    $flaskApiUrl = $flaskURL . $AppID;
    $timeout = 0.1;
    $ch = curl_init($flaskApiUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonPackage)
        )
    );

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPackage);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 50); // DEPLOYMENT CHANGE
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1); //timeout in seconds
    $response = curl_exec($ch);

    // check for cURL errors
    if (curl_errno($ch)) {
        error_log( 'cURL error when attempting to communicate with Monitor API: ' . curl_error($ch) );
    } else {
        error_log( 'Response from Monitor API: ' . $response );
    }

    // close cURL session
    curl_close($ch);
}

// given <parameter array from $_REQUEST> AND <body object from $data>
// iterate these two and return a combined <jsonPackage> including each item of both
function combineParaAndBody($paraArray, $bodyObject)
{
    $jsonPackage = array();

    foreach ($paraArray as $key => $value) {
        $jsonPackage[$key] = $value;
    }

    foreach ($bodyObject as $key => $value) {
        $jsonPackage[$key] = $value;
    }

    // remove the elements you do not want to send through log.php to flaskapp
    unset($jsonPackage["data"]); // contains long string of encoded data
    unset($jsonPackage["remote_addr"]);
    unset($jsonPackage["http_user_agent"]);

    return json_encode($jsonPackage);
}
?>