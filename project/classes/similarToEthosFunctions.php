<?php

//stubs

function eeWorkspaceName()
{
    return "triggerHelper";
};
// to do update all pass through functions so arguments match the php functions
function eePHP_copy($x)
{ //copy($x);
}
function eePHP_dir($x)
{ //dir($x);
}
function eePHP_dirname($x)
{ //dirname($x);
}
function eePHP_file($x)
{ //file($x);
}
function eePHP_file_exists($x)
{ //file_exists($x);
}
function eePHP_file_get_contents($x)
{ //file_get_contents($x);
    return file_get_contents($x);
}
function eePHP_file_put_contents($x)
{ //file_put_contents($x);
}
function eePHP_fopen($x)
{ //fopen($x);
}
function eePHP_ini_alter($x)
{ //ini_alter($x);
}
function eePHP_ini_get($x)
{ //ini_get($x);
}
function eePHP_ini_set($x)
{ //ini_set($x);
}
function eePHP_list($x)
{ //list($x);
}
function eePHP_mkdir($x)
{ //mkdir($x);
}
function eePHP_opendir($x)
{ //opendir($x);
}
function eePHP_readfile($x)
{ //readfile($x);
}
function eePHP_rename($x)
{ //rename($x);
}
function eePHP_rmdir($x)
{ //rmdir($x);
}
function eePHP_scandir($x)
{ //scandir($x);
}
function eePHP_tempnam($x)
{ //tempnam($x);
}
function eePHP_touch($x)
{ //touch($x);
}
function eePHP_unlink($x)
{ //unlink($x);
}
// live functions
function eeGetEthosSessionToken()
{
    $response = new stdClass;
    if (empty($_ENV['token']) || time() >= $_ENV['tokenExpires'] ) {
        $method = "POST";
        $url = "https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/auth";
        $requestHeaders = array(
            'Accept: application/json',
            "Content-Type: application/json",
            "Cache-Control: no-cache",
            "Authorization: Bearer {$_ENV['API_KEY']}"
        );
        $response = callCurl($method, $url, $requestHeaders);
        if (empty($response->result) || $response->error || $response->result == '{"message":"Invalid API KEY"}') {
            $msg = "error getting token: " . json_encode($response, JSON_PRETTY_PRINT);
            error_log($msg);
        } else {
            $tenant = json_decode(base64_decode(explode(".", $response->result)[1]));
            
            $_ENV['tokenExpires'] = $tenant->exp -30;
            $_ENV['token'] = $response->result;    
            $_ENV['ethosTenant'] = $tenant->tenant->alias??"error";    

            $testOrProd = $tenant->tenant->label??"";
            $testOrProd = (empty($tenant->tenant->label)) ? "" : $tenant->tenant->label." " ;
            print "Using Ethos {$testOrProd}Tenant: {$tenant->tenant->name} " .PHP_EOL. str_repeat("-",85).PHP_EOL;
            
        } 
    } else {
        $response->result = $_ENV['token'];
    }

    return  $response->result;
}



/**
 * @param mixed $token
 * @return mixed
 */
function ethosGetAppConfig()
{
    $token = eeGetEthosSessionToken();
    $method = "GET";
    $url = "https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/appconfig";
    $requestHeaders = array(
        'Accept: application/json',
        "Authorization: Bearer $token"
    );

    return callCurl($method, $url, $requestHeaders);
}

function ethosGetChangeNotifications()
{
    $token = eeGetEthosSessionToken();
    $method = "GET";
    $url = "https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/consome";
    $requestHeaders = array(
        'Accept: application/vnd.hedtech.change-notifications.v2+json',
        "Authorization: Bearer $token"
    );
    return callCurl($method, $url, $requestHeaders);
}

/**
 * ethosGetAvailableResources
 *
 * @return object
 */
function ethosGetAvailableResources()
{
    $token = eeGetEthosSessionToken();
    $method = "GET";
    $url = "https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/admin/available-resources";
    $requestHeaders = array(
        "Authorization: Bearer $token",
    );
    return callCurl($method, $url, $requestHeaders);
}


function ethosGetErrors()
{
    $token = eeGetEthosSessionToken();
    $method = "POST";
    $url = "https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/errors";
    $requestHeaders = array(
        "Authorization: Bearer $token",
        "Accept: application/vnd.hedtech.errors.v2+json"
    );
    return callCurl($method, $url, $requestHeaders);
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

    return callCurl($method, $url, $requestHeaders);
}


/**
 * eeGetEthosDataModelByFilter
 *
 * @param  mixed $resource
 * @param  mixed $criteria
 * @param  mixed $version
 * @param  mixed $token
 * @param  mixed $useCache
 * @return mixed
 */
function eeGetEthosDataModelByFilter($resource, $criteria, $version = null, $token = null, $useCache = true)
{
    $token = eeGetEthosSessionToken();
    $version = $version ?? "application/json";
    $headers = [];
    $method = "GET";
    $filter = "";
    if (!empty($criteria->criteria)) {
        foreach ($criteria as $key => $item) {
            $delim = (empty($filter)) ? "?" : "&";
            $filter = "{$delim}criteria=" . urlencode(json_encode($item));
            break;
        }
    }
    if (!empty($criteria->limit)) {
        $delim = (empty($filter)) ? "?" : "&";
        $filter = "{$delim}offset={$criteria->offset}&limit={$criteria->limit}";
    }
    $filter = $filter;
    $url = "https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/api/$resource$filter";
    $requestHeaders = array(
        'Accept: application/json',
        "Content-Type:$version",
        "Authorization: Bearer $token"
    );

    return callCurl($method, $url, $requestHeaders);
}

function eeGetUserFromEthos($credentialType, $credential)
{
    $filter = new stdClass;
    $filter->criteria = new stdClass;
    $filter->criteria->credentials[] = (object) array("type"=>$credentialType,"value"=>$credential);
    $response = eeGetEthosDataModelByFilter("persons",$filter);
    $response->dataObj = $response->dataObj[0]??new stdClass;
    return $response;
};
function eeGetEthosDataModel($resource, $id, $version = null, $token = null, $useCache = true)
{

    $version = $version ?? "application/json";
    $method = "GET";
    $url = "https://integrate.elluciancloud.{$_ENV['ETHOS_REGION']}/api/$resource/$id";
    $requestHeaders = array(
        'Accept: application/json',
        "Content-Type:$version",
        "Authorization: Bearer " . eeGetEthosSessionToken()
    );
    return callCurl($method, $url, $requestHeaders);
}


/**
 * @param mixed $response
 * @return object
 */
function processResponse($response)
{
    $dataObj = json_decode($response->result);
    $errors = $dataObj->errors ?? "";
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
        'count' =>  $count ??"",
        'totalCount' => $response->headers["x-total-count"][0] ?? "",
        'maxPageSize' => $response->headers["x-max-page-size"][0] ?? ""
    );
    $result->triggerHelper = new stdClass;
    $result->triggerHelper->graphCount = $graphCount ?? "";
    $response->requestHeaders = $response->requestHeaders ?? array();
    foreach ($response->requestHeaders as $key => &$item) {
        $parts = explode(":", $item);
        if ($parts[0] == "Authorization") {
            $item = $parts[0] . ":Bearer {{token}}";
        }
    }
    if (!empty($result->totalCount)) {
        print "     returned $result->count of $result->totalCount records in {$response->info['total_time']} seconds" . PHP_EOL;
    } else {
        print "     returned response in {$response->info['total_time']} seconds" . PHP_EOL;
    }
    print '     using request headers: ' . PHP_EOL . "          " . implode(PHP_EOL . "          ", $response->requestHeaders) . PHP_EOL;
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


/**
 * callCurl
 *
 * @param  mixed $method
 * @param  mixed $url
 * @param  mixed $requestHeaders
 * @return mixed
 */
function callCurl(string $method, string $url, array $requestHeaders)
{
    $headers = array();
    if (substr($url, -4) != "auth") {
        echo "$method $url" . PHP_EOL;
        if ($url != urldecode($url)) {
            echo "    (decoded: " . urldecode($url) . ")" . PHP_EOL;
        }
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
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


    //'Content-Type: multipart/form-data',
    $response = new stdClass;
    $response->result = curl_exec($ch);
    $response->error = curl_error($ch);
    $response->info = curl_getinfo($ch);
    $response->headers = $headers;
    $response->requestHeaders = $requestHeaders;
    curl_close($ch);
    if (substr($url, -4) == "auth") {
        return $response;
    } else {
        return processResponse($response);
    }
}
