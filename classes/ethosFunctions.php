<?php
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
        return  $response;

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
        return  $response;

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
        return  $response;

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
    return  $response;

}
