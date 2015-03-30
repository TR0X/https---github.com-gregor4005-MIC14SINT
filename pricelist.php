<?php
require_once "nusoap.php";

function getXML($url){
	if($fp = curl_init($url)){
		$xml = file_get_contents($url);
		file_put_contents("./eurofxref-daily.xml", $xml); 
	}else{
		echo "no XML available - locale file will be used";
	}
}

function getPrices($category){
	getXML("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
	$rates = simplexml_load_file("eurofxref-daily.xml");
	if($category == "all"){
		foreach ($rates->Cube->Cube->Cube as $Cube){
			$attributes = $Cube -> attributes();
			$array = array($attributes['currency'] , ",") ;
			$returnarray .= implode($array);
		}
	return $returnarray;
	}
}

function calPrice($sourcevalue, $sourcecurr, $targetcurr){
	getXML("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
	$rates = simplexml_load_file("eurofxref-daily.xml");
	
	foreach ($rates->Cube->Cube->Cube as $Cube){
		$attributes = $Cube -> attributes();
		if($attributes['currency'] == $sourcecurr){
			$sourceprice = $attributes['rate'];
		}
		if($attributes['currency'] == $targetcurr){
			$targetprice = $attributes['rate'];
		}
	}
	return (((float) $sourcevalue * (float) $targetprice) / (float) $sourceprice);
}

$server = new nusoap_server();
$server->configureWSDL("pricelist", "urn:pricelist");

$server->register("getPrices",
    array("category" => "xsd:string"),
    array("return" => "xsd:string"),
    "urn:pricelist",
    "urn:pricelist#getPrices",
    "rpc",
    "encoded",
    "Get a list of all prices");

$server->register("calPrice",
    array("sourcevalue" => "xsd:string" , "sourcecurr" => "xsd:string", "targetcurr" => "xsd:string"),
    array("return" => "xsd:string"),
    "urn:pricelist",
    "urn:pricelist#calPrice",
    "rpc",
    "encoded",
    "Calculte the targetvalue");
 
$server->service($HTTP_RAW_POST_DATA);
?>