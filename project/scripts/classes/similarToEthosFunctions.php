<?php
require_once "/vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable("/project");
$dotenv->load();
//stubs
function eeGetUserFromEthos($credentialType,$credential){return "";};
function eeWorkspaceName() {return "triggerHelper";};
// to do update all pass through functions so arguments match the php functions
function eePHP_copy($x)
{//copy($x);
}
function eePHP_dir($x)
{//dir($x);
}
function eePHP_dirname($x)
{//dirname($x);
}
function eePHP_file($x)
{//file($x);
}
function eePHP_file_exists($x)
{//file_exists($x);
}
function eePHP_file_get_contents($x)
{//file_get_contents($x);
    return "";
}
function eePHP_file_put_contents($x)
{//file_put_contents($x);
}
function eePHP_fopen($x)
{//fopen($x);
}
function eePHP_ini_alter($x)
{//ini_alter($x);
}
function eePHP_ini_get($x)
{//ini_get($x);
}
function eePHP_ini_set($x)
{//ini_set($x);
}
function eePHP_list($x)
{//list($x);
}
function eePHP_mkdir($x)
{//mkdir($x);
}
function eePHP_opendir($x)
{//opendir($x);
}
function eePHP_readfile($x)
{//readfile($x);
}
function eePHP_rename($x)
{//rename($x);
}
function eePHP_rmdir($x)
{//rmdir($x);
}
function eePHP_scandir($x)
{//scandir($x);
}
function eePHP_tempnam($x)
{//tempnam($x);
}
function eePHP_touch($x)
{//touch($x);
}
function eePHP_unlink($x)
{//unlink($x);
}
// live functions
function eeGetEthosSessionToken()
{
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
    if (empty($response->result) || $response->error || $response->result == '{"message":"Invalid API KEY"}') {
        $msg = "error getting token: " . json_encode($response, JSON_PRETTY_PRINT);
        error_log($msg);
    }
    curl_close($ch);
    return  $response->result;
}

function test()
{
    return "foo";
}






/**
 * @param mixed $token
 * @return mixed
 */
function ethosGetAppConfig($token)
{


    $ch = curl_init('https://integrate.elluciancloud.com/appconfig');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        "Authorization: Bearer $token"
    ));
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    curl_close($ch);
    return processResponse($response);
}

function ethosGetChangeNotifications($token)
{


    $ch = curl_init('https://integrate.elluciancloud.com/consume');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/vnd.hedtech.change-notifications.v2+json',
        "Authorization: Bearer $token"
    ));
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    curl_close($ch);
    return  processResponse($response);
}

function ethosGetAvailableResources($token)
{


    $ch = curl_init('https://integrate.elluciancloud.com/admin/available-resources');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer $token"
    ));
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    curl_close($ch);
    return  processResponse($response);
}


function ethosGetErrors($token)
{


    $ch = curl_init('https://integrate.elluciancloud.com/errors');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer $token",
        "Accept: application/vnd.hedtech.errors.v2+json"
    ));
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    curl_close($ch);
    return  processResponse($response);
}



function eeGetEthosDataByGraphQLQuery($query)
{

    $body = (object) [
        "query" => $query
    ];
    $bodyString = json_encode($body);
    json_decode($bodyString);
    $token = eeGetEthosSessionToken();
    $headers = [];
    $method = "POST";
    $url = "https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/graphql";
    echo "$method $url" . PHP_EOL;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HEADERFUNCTION,
        function ($curl, $header) use (&$headers) {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
                return $len;

            $headers[strtolower(trim($header[0]))][] = trim($header[1]);

            return $len;
        }
    );
    $requestHeaders = array(
        "Content-Type: application/json",
        "Accept: application/json",
        "Authorization: Bearer $token",
        "Content-Length: " . strlen($bodyString)
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders );
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    $response->headers = $headers;
    $response->requestHeaders = $requestHeaders;
    curl_close($ch);
    return  processResponse($response);
}

function eeGetEthosDataModelByFilter($resource, $criteria, $version = null, $token = null, $useCache = true)
{
    $token = eeGetEthosSessionToken();
    $version = $version ?? "application/json";
    $headers = [];
    $method = "GET";
    $filter = "";
    if (!empty($criteria)) {
        foreach ($criteria as $key => $item) {
            $filter = "?criteria=" . urlencode(json_encode($item));
            break;
        }
    }
    $url = "https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/api/$resource$filter";

    echo "$method $url" . PHP_EOL;
    if ($url != urldecode($url)) {
        echo "    (decoded: " . urldecode($url) . ")".PHP_EOL;
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HEADERFUNCTION,
        function ($curl, $header) use (&$headers) {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
                return $len;

            $headers[strtolower(trim($header[0]))][] = trim($header[1]);

            return $len;
        }
    );
    $requestHeaders = array(
        'Accept: application/json',
        "Content-Type:$version",
        "Authorization: Bearer $token"
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER,$requestHeaders );
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    $response->headers = $headers;
    $response->requestHeaders = $requestHeaders;
    curl_close($ch);
    return  processResponse($response);
}
function eeGetEthosDataModel($resource, $id, $version = null, $token = null, $useCache = true)
{
    $headers = [];
    $version = $version ?? "application/json";
    $method = "GET";
    $url = "https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/api/$resource/$id";
    echo "$method $url" . PHP_EOL;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HEADERFUNCTION,
        function ($curl, $header) use (&$headers) {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
                return $len;

            $headers[strtolower(trim($header[0]))][] = trim($header[1]);

            return $len;
        }
    );
    $requestHeaders = array(
        'Accept: application/json',
        "Content-Type:$version",
        "Authorization: Bearer " . eeGetEthosSessionToken()
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    $response->headers = $headers;
    $response->requestHeaders = $requestHeaders;
    curl_close($ch);
    return  processResponse($response);
}


/**
 * @param mixed $response
 * @return object
 */
function processResponse($response)
{
    $dataObj = json_decode($response->result);
    $errors = $dataObj->errors??"";
    if (!empty($dataObj) && is_array($dataObj) && empty($errors)) {
        $count = count($dataObj) ?? "";
    }
    if (!empty($dataObj) && is_object($dataObj && empty($dataObj->errors))) {
        $edges = findEdges($dataObj);
        $graphCount = count($edges) ?? "";
    }
    $errors = $dataObj->errors ?? "";
    $errors = $errors ?? $response->error ?? "";
    $result = (object) array(
        'statusCode' => $response->info["http_code"],
        'statusMessage' => '',
        'dataStr' => $response->result,
        'dataObj' => $dataObj,
        'responseObject' => $dataObj,
        'version' => $response->headers["x-media-type"][0] ?? "",
        'isError' => !empty($errors),
        'errorMessage' => $errors ?? "",
        'count' =>  "$count",
        'totalCount' => $response->headers["x-total-count"][0] ?? "",
        'maxPageSize' => $response->headers["x-max-page-size"][0] ?? ""
    );
    $result->triggerHelper = new stdClass;
    $result->triggerHelper->graphCount = $graphCount ?? "";
    $response->requestHeaders = $response->requestHeaders??array();
    foreach ($response->requestHeaders as $key => &$item) {
       $parts = explode(":",$item);
       if ($parts[0] == "Authorization") {
           $item = $parts[0].":Bearer {{token}}";
       }
    }
    if (!empty($result->totalCount)) {
        print "     returned $result->count of $result->totalCount records in {$response->info['total_time']} seconds" . PHP_EOL;
    } else {
        print "     returned response in {$response->info['total_time']} seconds" . PHP_EOL;
    }
    print '     using request headers: '.PHP_EOL."          ".implode(PHP_EOL."          ",$response->requestHeaders).PHP_EOL;
    return $result;
    //xth - custom to this trigger helper process.  Do not reference these properties inside trigger code
    // todo pull out just the version from $response->headers["x-media-type"][0]
    // add statusMessage, isError boo and count

}



function findEdges($obj)
{
    foreach ($obj as $key => $item) {
        if ($key == "edges") {
            return $item;
        } else {
            return findEdges($item);
        }
    }
}
