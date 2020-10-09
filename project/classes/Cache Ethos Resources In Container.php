<?php
function localcacheResource($resource, $version, $filterIn, $path)
{
    localEwfLogMsg("Before Get Data for " . $resource, "debug");
    $r_records = localGetAllEthosDatafromResource($resource, $version, $filterIn);
    localEwfLogMsg("After Get Data for " . $resource, "debug");
    if (empty($r_records) || !is_array($r_records)) {
        localEwfLogMsg("Empty or not an array" . $resource, "debug");
        return;
    }
    //check if count is way less that what is allready stored
    $rCount = count($r_records);
    $r_records = serialize($r_records);
    $filename = $path . $resource . ".dat";
    localEwfLogMsg("about to save file: " . $filename, "debug");
    $fpcNumberofBytesWrittenorFalse = file_put_contents($filename, $r_records);
    localEwfLogMsg("Saved filename: " . $filename . " response:" . $fpcNumberofBytesWrittenorFalse, "debug");
}
function localEwfLogMsg($msg, $type = "info")
{
    error_log($msg);
}

function localGetAllEthosDatafromResource($resource, $version, $filterIn)
{
    $token = eeGetEthosSessionToken();
    $totalRecords = 30;
    $batchSize = 500;
    $response = array();
    for ($i = 0; $i < $totalRecords; $i = $i + $batchSize) {
        unset($curResponse);
        // $filter = "offset=$i&limit=" . $batchSize;
        $filter = new stdClass;
        $filter->offset = $i;
        $filter->limit = $batchSize;
        if (!empty($filterIn)) {
            $filter .= "&" . $filterIn;
        }
        $curResponse = eeGetEthosDataModelByFilter($resource, $filter, $version, $token);
        if ($curResponse->isError || $curResponse->count == 0) {
           localEwfLogMsg($resource . " error: " . json_encode($curResponse->errorMessage));
            break; //
        }
        if ($i == 0) {
            $batchSize = $curResponse->maxPageSize;
            $totalRecords = $curResponse->totalCount;
            localEwfLogMsg("getting $totalRecords records from: " . $resource . " " . $curResponse->version);
        }
        if (is_array($response) && is_array($curResponse->dataObj)) {
            $response = array_merge($response, $curResponse->dataObj);
        } else {
            localEwfLogMsg($resource . " error array not found ");
        }
    } // end of pagination
    foreach ($response as $key => $data) {
        if (isset($data->id)) {
            $allData[$data->id] = $data;
        }
    }
   
    //$allData["cacheInfo"] ="This data was generated on ".  date('c') ." in ".$tenantInfo;
    return $allData??"";
}

function cacheResources(array $resources)
{
    $token = eeGetEthosSessionToken();
    $customer = $_ENV['ethosTenant'];
    $path = '/project/ethosCache/' . $customer;
    ini_set("memory_limit", "1700M");
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
    foreach ($resources as $key => $resource) {
        localcacheResource($resource, "", "", $path . "/");
    }

}
