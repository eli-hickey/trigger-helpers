<?php
require_once "/vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable("/project");
$dotenv->load();
function eeGetEthosSessionToken() {
    //$_ENV["API_KEY"]
    $ch = curl_init("https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/auth");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Cache-Control: no-cache", "Authorization: Bearer " . $_ENV["API_KEY"]));
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    if (empty($response->result) ||$response->error || $response->result == '{"message":"Invalid API KEY"}') {
       $msg = "error getting token: " . json_encode($response,JSON_PRETTY_PRINT);
       error_log($msg);
    }
    curl_close($ch);
    return  $response->result;

}

function test(){return "foo";}


function eeGetEthosDataModel($resource, $id, $version = null, $token = null, $useCache = true)
{
    $version = $version??"application/json";
    $ch = curl_init("https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/api/$resource/$id");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        "Content-Type:$version",
        "Authorization: Bearer ". eeGetEthosSessionToken())
    );
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    curl_close($ch);
    return  processResponse($response);
}

function eeGetEthosDataModelByFilter($resource, $criteria, $version = null, $token = null, $useCache = true)
{
    $token = eeGetEthosSessionToken();
    $version = $version??"application/json";
    $ch = curl_init("https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/api/$resource/$criteria");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        "Content-Type:$version",
        "Authorization: Bearer $token" )
    );
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    curl_close($ch);
    return  processResponse($response);
}

/**
 * @param mixed $token
 * @return mixed
 */
function ethosGetAppConfig($token){


        $ch = curl_init('https://integrate.elluciancloud.com/appconfig');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            "Authorization: Bearer $token")
        );
        //'Content-Type: multipart/form-data',
        $response = new stdClass;
        $response->result = curl_exec($ch);
        $response->error = curl_error($ch);
        $response->info = curl_getinfo($ch);
        curl_close($ch);
        return processResponse($response);

}

function ethosGetChangeNotifications($token){


    $ch = curl_init('https://integrate.elluciancloud.com/consume');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/vnd.hedtech.change-notifications.v2+json',
        "Authorization: Bearer $token")
    );
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
        $response->result = curl_exec($ch);
        $response->error = curl_error($ch);
        $response->info = curl_getinfo($ch);
        curl_close($ch);
        return  processResponse($response);

}

function ethosGetAvailableResources($token){


    $ch = curl_init('https://integrate.elluciancloud.com/admin/available-resources');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer $token")
    );
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
        $response->result = curl_exec($ch);
        $response->error = curl_error($ch);
        $response->info = curl_getinfo($ch);
        curl_close($ch);
        return  processResponse($response);

}


function ethosGetErrors($token){


    $ch = curl_init('https://integrate.elluciancloud.com/errors');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer $token",
        "Accept: application/vnd.hedtech.errors.v2+json")
    );
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    curl_close($ch);
    return  processResponse($response);

}

function processResponse($response){

$result = (object) array(
    'statusCode' => 403,
    'statusMessage' => 'Forbidden',
    'headers' => null,
    'dataStr' => null,
    'dataObj' => json_decode($response->result),
    'version' => null,
    'isError' => true,
    'errorMessage' => 'No Ellucian Ethos auth token - Is your Ethos API Key correct?',
    'count' => 0,
    'totalCount' => null,
    'maxPageSize' => null);

    return $result;


}
