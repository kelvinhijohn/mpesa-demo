<?php
require_once 'messages.php';
header("Content-Type: application/json");

// get response from M-PESA Stream
$mpesaResponse = file_get_contents('php://input');

//database connection
$db = mysqli_connect('', '', '', '');
// Check connection
if (!$db){ 
        die("Connection failed: " . mysqli_connect_error());
}


//decode the json file
$jsonMpesaResponse = json_decode($mpesaResponse, true); // decode to save to database
$transactiontype= mysqli_real_escape_string($db,$jsonMpesaResponse['TransactionType']);
$transid=mysqli_real_escape_string($db,$jsonMpesaResponse['TransID']); 
$transtime= mysqli_real_escape_string($db,$jsonMpesaResponse['TransTime']); 
$transamount= mysqli_real_escape_string($db,$jsonMpesaResponse['TransAmount']); 
$businessshortcode=  mysqli_real_escape_string($db,$jsonMpesaResponse['BusinessShortCode']); 
$billrefno=  mysqli_real_escape_string($db,$jsonMpesaResponse['BillRefNumber']); 
$invoiceno=  mysqli_real_escape_string($db,$jsonMpesaResponse['InvoiceNumber']);
$orgaccountbalance=   mysqli_real_escape_string($db,$jsonMpesaResponse['OrgAccountBalance']);
$thirdpartyid=  mysqli_real_escape_string($db,$jsonMpesaResponse['ThirdPartyTransID']);
$msisdn=  mysqli_real_escape_string($db,$jsonMpesaResponse['MSISDN']); 
$firstname=mysqli_real_escape_string($db,$jsonMpesaResponse['FirstName']); 
$middlename=mysqli_real_escape_string($db,$jsonMpesaResponse['MiddleName']); 
$lastname=mysqli_real_escape_string($db,$jsonMpesaResponse['LastName']); 

$sql="INSERT INTO mobile_payments(TransactionType, TransID, TransTime, TransAmount, BusinessShortCode, BillRefNumber, InvoiceNumber, OrgAccountBalance, ThirdPartyTransID, MSISDN,  FirstName, MiddleName, LastName)
        VALUES('$transactiontype', '$transid', '$transtime', '$transamount', '$businessshortcode', '$billrefno', '$invoiceno', '$orgaccountbalance', '$thirdpartyid', '$msisdn',  '$firstname', '$middlename', '$lastname')";


if (mysqli_query($db,$sql)) { 
        $response = '{
                "ResultCode": 0, 
                "ResultDesc": "Confirmation Received Successfully" 
        }';

        // log the response
        $logFile = "M_PESAConfirmationResponse.txt";
        // write to file
        $log = fopen($logFile, "a");
        fwrite($log, $mpesaResponse);
        fclose($log);
        echo $response;
        //End of log
        
        //initiate info function
        info($mpesaResponse);


}else {

        $error = mysqli_error($db);
        $errLog = fopen('error.txt', 'a');
        fwrite($errLog, $error);
        fclose($errLog);
        $logFailedTransaction = fopen('failedTransaction.txt', 'a');
        fwrite($logFailedTransaction, json_encode($jsonMpesaResponse));
        fclose($logFailedTransaction);
}

mysqli_close($db);
?>
