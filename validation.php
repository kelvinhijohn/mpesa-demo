<?php
    header("Content-Type: application/json");

    // get the M-PESA input stream. 
    $mpesaResponse = file_get_contents('php://input');

    $jsonMpesaResponse = json_decode($mpesaResponse, true);

    $amount = $jsonMpesaResponse['TransAmount'];

    if ($amount == 50 || $amount == 100 || $amount == 200) {
        $response = '{
            "ResultCode": 0,
            "ResultDesc": "Accepted"
        }';
        
        echo $response;

        // log the response
        $logFile = "AA.txt";
        $log = fopen($logFile, "a");
        fwrite($log, $mpesaResponse);
        fclose($log);
    
    }else {
        $response = '{
            "ResultCode": 1,
            "ResultDesc": "Rejected"
        }';
        
        echo $response;
        
        // log the response
        $logFile = "RR.txt";
        $log = fopen($logFile, "a");
        fwrite($log, $mpesaResponse);
        fclose($log);
        
        info($mpesaResponse);

    }    
?>