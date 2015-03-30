<?php
require_once "nusoap.php";
$client = new nusoap_client("prices.wsdl", true);

$error = $client->getError();
if ($error) {
    echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
}
 
//$result = $client->call("getPrices", array("category" => "all"));
$result = $client->call("calPrice", array("sourcevalue" => "10000" , "sourcecurr" => "JPY" , "targetcurr" => "USD"));

if ($client->fault) {
    echo "<h2>Fault</h2><pre>";
    print_r($result);
    echo "</pre>";
}else {
    $error = $client->getError();
    if ($error) {
        echo "<h2>Error</h2><pre>" . $error . "</pre>";
    }else {
        echo "<h2>Prices</h2><pre>";
        echo $result;
        echo "</pre>";
    }
}

/*
echo "<h2>Debuginfo<h2>";
echo "<h3>Request</h3>";
echo "<pre>" . htmlspecialchars($client->request, ENT_QUOTES) . "</pre>";
echo "<h3>Response</h3>";
echo "<pre>" . htmlspecialchars($client->response, ENT_QUOTES) . "</pre>";
*/

?>